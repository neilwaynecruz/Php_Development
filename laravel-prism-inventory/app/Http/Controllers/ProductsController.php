<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\SettingsService;

class ProductsController extends Controller
{
    public function index(Request $request, SettingsService $settings)
    {
        $role = (string) session('role', 'user');
        $isAdmin = ($role === 'admin');

        // Per-user filter key
        $user = (string) session('user', 'guest');
        $filterKey = 'filters.products.' . $user;

        // Clear saved filters explicitly
        if ($request->boolean('clear')) {
            session()->forget($filterKey);
            return redirect()->route('products.index');
        }

        // Load any previously saved filters
        $saved = session($filterKey) ?? ['q' => '', 'cat' => '', 'view' => 'active'];

        // Detect incoming filters
        $incomingQ    = $request->query('q');
        $incomingCat  = $request->query('cat');
        $incomingView = $request->query('view');

        // Compose current filters, preserving saved q/cat when only view is toggled
        $q    = trim((string) ($incomingQ   ?? $saved['q']   ?? ''));
        $cat  = trim((string) ($incomingCat ?? $saved['cat'] ?? ''));
        $view = in_array(($incomingView ?? $saved['view'] ?? 'active'), ['active','archived'], true)
            ? ($incomingView ?? $saved['view'] ?? 'active')
            : 'active';

        // Persist filters whenever any of q/cat/view are present
        if ($request->query->has('q') || $request->query->has('cat') || $request->query->has('view')) {
            session([
                $filterKey => [
                    'q'         => $q,
                    'cat'       => $cat,
                    'view'      => $view,
                    'updated_at'=> now()->toDateTimeString(),
                ],
            ]);
        }

        $is_archived_view = ($view === 'archived');

        // Settings (dynamic)
        $lowThreshold    = $settings->getInt('low_stock_threshold', 10);
        $showTotalToUser = $settings->getBool('show_total_to_user', true);

        $records_per_page = 23;
        $current_page = max(1, (int) $request->integer('page', 1));

        $where = $is_archived_view ? "is_archived = 1" : "is_archived = 0";
        $params = [];

        if ($q !== '') {
            // SEARCH also in SKU
            $where .= " AND (name LIKE ? OR category LIKE ? OR sku LIKE ?)";
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        if ($cat !== '') {
            $where .= " AND category LIKE ?";
            $params[] = "%$cat%";
        }

        $sumSql = "SELECT COUNT(*) AS total_products,
                          COALESCE(SUM(quantity * price), 0) AS total_value,
                          SUM(CASE WHEN quantity <= ? THEN 1 ELSE 0 END) AS low_count
                   FROM products WHERE $where";
        $sumRow = DB::selectOne($sumSql, array_merge([$lowThreshold], $params));
        $totalProducts = (int) $sumRow->total_products;
        $totalValue    = (float) $sumRow->total_value;
        $lowCount      = (int) $sumRow->low_count;

        $total_pages = max(1, (int) ceil($totalProducts / $records_per_page));
        $current_page = min($current_page, $total_pages);
        $offset = ($current_page - 1) * $records_per_page;

        $listSql = "SELECT * FROM products WHERE $where ORDER BY product_id DESC LIMIT ? OFFSET ?";
        $rows = DB::select($listSql, array_merge($params, [$records_per_page, $offset]));

        // Search & Update (by ID) support for the Edit modal
        $searchResult = null;
        $searchId = session()->getOldInput('search-id');
        if (is_numeric($searchId) && (int)$searchId > 0) {
            $searchRow = DB::selectOne(
                "SELECT product_id, sku, barcode, name, category, quantity, price, image_path
                 FROM products WHERE product_id = ?",
                [(int)$searchId]
            );
            if ($searchRow) {
                $searchResult = [
                    'product_id' => (int) $searchRow->product_id,
                    'sku'        => (string) ($searchRow->sku ?? ''),
                    'barcode'    => (string) ($searchRow->barcode ?? ''),
                    'name'       => (string) $searchRow->name,
                    'category'   => (string) $searchRow->category,
                    'quantity'   => (int) $searchRow->quantity,
                    'price'      => (float) $searchRow->price,
                    'image_path' => (string) ($searchRow->image_path ?? ''),
                ];
            }
        }

        return view('products.index', compact(
            'rows','isAdmin','lowThreshold','totalProducts','totalValue','lowCount',
            'current_page','total_pages','q','cat','view','is_archived_view','searchResult',
            'showTotalToUser'
        ));
    }

    public function create(Request $request)
    {
        if ((string) session('role') !== 'admin') {
            $msg = '<div class="alert alert-danger">Access denied: You do not have permission to modify products.</div>';
            if ($request->expectsJson()) return response()->json(['ok'=>false,'message'=>$msg], 403);
            session()->flash('message', $msg); return back();
        }

        $name     = trim((string)$request->input('pName',''));
        $category = trim((string)$request->input('pCategory',''));
        $qtyRaw   = $request->input('pQty');
        $qty      = is_numeric($qtyRaw) ? (int)$qtyRaw : null;
        $priceRaw = $request->input('pPrice', null);
        $price    = is_numeric($priceRaw) ? (float)$priceRaw : null;

        $sku      = trim((string) $request->input('pSku', ''));
        $barcode  = trim((string) $request->input('pBarcode', ''));

        $errors = [];
        if ($name === '') $errors[] = "Product name is required.";
        if ($category === '') $errors[] = "Category is required.";
        if ($qty === null || !is_numeric($qty) || (int)$qty < 0) $errors[] = "Quantity must be a non-negative integer.";
        if ($price === null || !is_numeric($price) || (float)$price < 0) $errors[] = "Price must be a non-negative number.";

        // Enforce unique SKU if provided
        if ($sku !== '') {
            $existingSku = DB::selectOne("SELECT product_id FROM products WHERE sku = ?", [$sku]);
            if ($existingSku) {
                $errors[] = "SKU is already used by product ID ".(int)$existingSku->product_id.".";
            }
        }

        // Image validation (optional – keep whatever you already had)
        $imagePath = null;
        if ($request->hasFile('pImage')) {
            if (!$request->file('pImage')->isValid()) {
                $errors[] = "Uploaded image is invalid.";
            } else {
                $request->validate([
                    'pImage' => ['image','max:2048'],
                ]);
            }
        }

        if ($errors) {
            $msg = '<div class="alert alert-danger">'.implode("<br>", $errors).'</div>';
            if ($request->expectsJson()) return response()->json(['ok'=>false,'message'=>$msg], 422);
            session()->flash('message', $msg); return back()->withInput();
        }

        if ($request->hasFile('pImage')) {
            $stored = $request->file('pImage')->store('products', 'public');
            $imagePath = $stored;
        }

        DB::insert(
            "INSERT INTO products (sku, barcode, name, category, quantity, price, image_path)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $sku !== '' ? $sku : null,
                $barcode !== '' ? $barcode : null,
                $name,
                $category,
                (int)$qty,
                (float)$price,
                $imagePath
            ]
        );
        $newId = DB::getPdo()->lastInsertId();

        $logDetails = "Added: $name";
        if ($sku !== '') {
            $logDetails .= " (SKU: $sku)";
        }
        if ($imagePath) {
            $logDetails .= " (with image)";
        }
        $this->logActivity('create', (int)$newId, $logDetails);

        $msg = '<div class="alert alert-success">Product added!</div>';
        if ($request->expectsJson()) return response()->json(['ok'=>true,'message'=>$msg]);
        session()->flash('message', $msg); return back();
    }

    public function update(Request $request)
    {
        if ((string) session('role') !== 'admin') {
            $msg = '<div class="alert alert-danger">Access denied: You do not have permission to modify products.</div>';
            if ($request->expectsJson()) return response()->json(['ok'=>false,'message'=>$msg], 403);
            session()->flash('message', $msg); return back();
        }

        $id       = (int) $request->input('pid');
        $name     = trim((string)$request->input('name',''));
        $category = trim((string)$request->input('category',''));
        $qty      = $request->input('quantity');
        $price    = $request->input('price');

        $sku      = trim((string) $request->input('sku', ''));
        $barcode  = trim((string) $request->input('barcode', ''));

        $errors = [];
        if ($name === '') $errors[] = "Product name is required.";
        if ($category === '') $errors[] = "Category is required.";
        if (!is_numeric($qty) || (int)$qty < 0) $errors[] = "Quantity must be a non-negative integer.";
        if (!is_numeric($price) || (float)$price < 0) $errors[] = "Price must be a non-negative number.";

        if ($sku !== '') {
            $existingSku = DB::selectOne("SELECT product_id FROM products WHERE sku = ? AND product_id <> ?", [$sku, $id]);
            if ($existingSku) {
                $errors[] = "SKU is already used by product ID ".(int)$existingSku->product_id.".";
            }
        }

        if ($request->hasFile('image')) {
            if (!$request->file('image')->isValid()) {
                $errors[] = "Uploaded image is invalid.";
            } else {
                $request->validate([
                    'image' => ['image','max:2048'],
                ]);
            }
        }

        if ($errors) {
            $msg = '<div class="alert alert-danger">'.implode("<br>", $errors).'</div>';
            if ($request->expectsJson()) return response()->json(['ok'=>false,'message'=>$msg], 422);
            session()->flash('message', $msg); return back()->withInput();
        }

        // existing image
        $oldRow = DB::selectOne("SELECT image_path FROM products WHERE product_id = ?", [$id]);
        $oldImage = $oldRow->image_path ?? null;
        $newImagePath = $oldImage;

        $imageUpdated = false;
        $imageRemoved = false;

        if ($request->boolean('remove_image') && !$request->hasFile('image')) {
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $newImagePath = null;
            $imageRemoved = true;
        }

        if ($request->hasFile('image')) {
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $stored = $request->file('image')->store('products', 'public');
            $newImagePath = $stored;
            $imageUpdated = true;
            $imageRemoved = false;
        }

        DB::update(
            "UPDATE products
             SET sku = ?, barcode = ?, name = ?, category = ?, quantity = ?, price = ?, image_path = ?
             WHERE product_id = ?",
            [
                $sku !== '' ? $sku : null,
                $barcode !== '' ? $barcode : null,
                $name,
                $category,
                (int)$qty,
                (float)$price,
                $newImagePath,
                $id
            ]
        );

        $logDetails = "Updated: $name";
        if ($sku !== '') {
            $logDetails .= " (SKU: $sku)";
        }
        if ($imageUpdated) {
            $logDetails .= " (updated image)";
        } elseif ($imageRemoved) {
            $logDetails .= " (removed image)";
        }
        $this->logActivity('update', $id, $logDetails);

        $msg = '<div class="alert alert-success">Product updated!</div>';
        if ($request->expectsJson()) return response()->json(['ok'=>true,'message'=>$msg]);
        session()->flash('message', $msg); return back();
    }

    public function archive(Request $request)
    {
        $id = (int) $request->input('archive_id');
        DB::update("UPDATE products SET is_archived = 1 WHERE product_id = ?", [$id]);
        $name = $this->fetchProductName($id);
        $this->logActivity('archive', $id, "Archived product: " . ($name ?? "ID $id"));
        $msg = '<div class="alert alert-warning">Product archived.</div>';
        if ($request->expectsJson()) return response()->json(['ok'=>true,'message'=>$msg]);
        session()->flash('message', $msg); return back();
    }

    public function restore(Request $request)
    {
        $id = (int) $request->input('restore_id');
        DB::update("UPDATE products SET is_archived = 0 WHERE product_id = ?", [$id]);
        $name = $this->fetchProductName($id);
        $this->logActivity('restore', $id, "Restored product: " . ($name ?? "ID $id"));
        $msg = '<div class="alert alert-success">Product restored.</div>';
        if ($request->expectsJson()) return response()->json(['ok'=>true,'message'=>$msg]);
        session()->flash('message', $msg);
        return redirect()->route('products.index', ['view' => 'archived']);
    }

    public function reset(Request $request)
    {
        DB::statement("TRUNCATE TABLE products");
        $this->logActivity('delete', null, "reset products table");
        $msg = '<div class="alert alert-warning">All products deleted and ID reset to 1.</div>';
        if ($request->expectsJson()) return response()->json(['ok'=>true,'message'=>$msg]);
        session()->flash('message', $msg); return back();
    }

    public function deletePermanent(Request $request)
    {
        $id = (int) $request->input('delete_id');

        $imgRow = DB::selectOne("SELECT image_path FROM products WHERE product_id = ? AND is_archived = 1", [$id]);
        if ($imgRow && $imgRow->image_path && Storage::disk('public')->exists($imgRow->image_path)) {
            Storage::disk('public')->delete($imgRow->image_path);
        }

        $nameRow = DB::selectOne("SELECT name FROM products WHERE product_id = ? AND is_archived = 1", [$id]);
        $name = $nameRow->name ?? null;

        DB::delete("DELETE FROM products WHERE product_id = ? AND is_archived = 1", [$id]);
        $displayName = $name !== null ? $name : "ID $id";
        $this->logActivity('delete', $id, "Permanently deleted product: $displayName");

        $ok = $this->resequenceProducts();
        $msg = $ok
            ? '<div class="alert alert-danger">Product permanently deleted. IDs resequenced.</div>'
            : '<div class="alert alert-warning">Product deleted but resequence failed. Check logs.</div>';

        if ($request->expectsJson()) return response()->json(['ok'=>true,'message'=>$msg]);
        session()->flash('message', $msg);
        return redirect()->route('products.index', ['view' => 'archived']);
    }

    public function search(Request $request)
    {
        return back()->withInput();
    }

    private function logActivity(string $action, $productId, $details)
    {
        $user = (string) session('user', 'unknown');
        DB::insert("INSERT INTO activity_logs (username, action, product_id, details) VALUES (?, ?, ?, ?)", [$user, $action, $productId, $details]);
    }

    private function fetchProductName(int $id): ?string
    {
        $r = DB::selectOne("SELECT name FROM products WHERE product_id = ?", [$id]);
        return $r->name ?? null;
    }

    private function resequenceProducts(): bool
    {
        try {
            $rows = DB::select("SELECT product_id, sku, barcode, name, category, quantity, price, IFNULL(is_archived,0) AS is_archived, image_path FROM products ORDER BY product_id");
            if (!$rows) return true;

            $temp = 'products_backup_' . time();
            DB::statement("RENAME TABLE products TO $temp");
            DB::statement("CREATE TABLE products LIKE $temp");

            foreach ($rows as $r) {
                DB::insert("INSERT INTO products (sku, barcode, name, category, quantity, price, is_archived, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [
                    $r->sku,
                    $r->barcode,
                    $r->name,
                    $r->category,
                    (int)$r->quantity,
                    (float)$r->price,
                    (int)$r->is_archived,
                    $r->image_path,
                ]);
                $newId = DB::getPdo()->lastInsertId();
                if ((int)$r->product_id !== (int)$newId) {
                    DB::update("UPDATE activity_logs SET product_id = ? WHERE product_id = ?", [(int)$newId, (int)$r->product_id]);
                }
            }

            DB::statement("DROP TABLE IF EXISTS $temp");
            return true;
        } catch (\Throwable $e) {
            Log::error("Resequence failed: ".$e->getMessage());
            return false;
        }
    }

    public function exportCsv(Request $request)
    {
        $user = (string) session('user', 'guest');
        $filterKey = 'filters.products.' . $user;
        $saved = session($filterKey) ?? ['q' => '', 'cat' => '', 'view' => 'active'];

        $q    = trim((string) ($request->query('q',    $saved['q']   ?? '')));
        $cat  = trim((string) ($request->query('cat',  $saved['cat'] ?? '')));
        $view = (string) ($request->query('view', $saved['view'] ?? 'active'));
        $is_archived_view = ($view === 'archived');

        $where = $is_archived_view ? "is_archived = 1" : "is_archived = 0";
        $params = [];
        if ($q !== '')  {
            $where .= " AND (name LIKE ? OR category LIKE ? OR sku LIKE ?)";
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        if ($cat !== ''){
            $where .= " AND category LIKE ?";
            $params[] = "%$cat%";
        }

        $sql = "SELECT product_id, sku, barcode, name, category, quantity, price, IFNULL(is_archived,0) AS is_archived
                FROM products
                WHERE $where
                ORDER BY product_id DESC";

        $filename = 'products_' . ($is_archived_view ? 'archived' : 'active') . '_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($sql, $params) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['ID', 'SKU', 'Barcode', 'Name', 'Category', 'Qty', 'Price', 'Total', 'Archived']);

            foreach (DB::cursor($sql, $params) as $r) {
                $id   = (int) $r->product_id;
                $sku  = (string) ($r->sku ?? '');
                $bar  = (string) ($r->barcode ?? '');
                $name = (string) $r->name;
                $cat  = (string) $r->category;
                $qty  = (int) $r->quantity;
                $price= (float) $r->price;
                $arch = ((int) $r->is_archived) ? 'yes' : 'no';
                $total= $qty * $price;

                fputcsv($out, [
                    $id,
                    $sku,
                    $bar,
                    $name,
                    $cat,
                    $qty,
                    number_format($price, 2, '.', ''),
                    number_format($total, 2, '.', ''),
                    $arch
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}