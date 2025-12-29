<?php

namespace App\Http\Controllers;

use App\Services\RequisitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequisitionsController extends Controller
{
    public function myRequests(Request $request)
    {
        $username = (string) session('user', '');
        if ($username === '') {
            abort(403, 'Not logged in.');
        }

        $status = $request->query('status', 'all');

        $query = DB::table('requisitions')
            ->where('username', $username)
            ->orderByDesc('id');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requisitions = $query->paginate(15);

        return view('requisitions.my', compact('requisitions', 'status'));
    }

    public function showMy(Request $request, int $id)
    {
        $username = (string) session('user', '');
        $req = DB::table('requisitions')->where('id', $id)->first();
        if (!$req || $req->username !== $username) {
            abort(404);
        }

        $items = DB::table('requisition_items as ri')
            ->join('products as p', 'ri.product_id', '=', 'p.product_id')
            ->where('ri.requisition_id', $id)
            ->select('ri.*', 'p.name', 'p.sku', 'p.category', 'p.quantity as current_qty')
            ->get();

        return view('requisitions.my_show', compact('req', 'items'));
    }

    public function createDraft(Request $request, RequisitionService $service)
    {
        $username = (string) session('user', '');
        if ($username === '') {
            abort(403);
        }

        $notes = trim((string) $request->input('notes', ''));
        $id = $service->createDraft($username, $notes !== '' ? $notes : null);

        return redirect()->route('requisitions.my.edit', $id);
    }

    public function saveNotesMy(Request $request, RequisitionService $service, int $id)
    {
        $username = (string) session('user', '');
        $notes    = trim((string) $request->input('notes', ''));

        try {
            // user can only update notes on their own requisitions, service will validate status
            $service->updateNotes($id, $username, $notes !== '' ? $notes : null, false);
            return back()->with('message', '<div class="alert alert-success">Notes updated.</div>');
        } catch (\Throwable $e) {
            return back()->with('message', '<div class="alert alert-danger">'.$e->getMessage().'</div>');
        }
    }
    public function editMy(Request $request, int $id)
    {
        $username = (string) session('user', '');
        $req = DB::table('requisitions')->where('id', $id)->first();
        if (!$req || $req->username !== $username) {
            abort(404);
        }

        $items = DB::table('requisition_items as ri')
            ->join('products as p', 'ri.product_id', '=', 'p.product_id')
            ->where('ri.requisition_id', $id)
            ->select('ri.*', 'p.name', 'p.sku', 'p.category', 'p.quantity as current_qty')
            ->get();

        $allProducts = DB::table('products')
            ->where('is_archived', 0)
            ->orderBy('name')
            ->get(['product_id','sku','name','category','quantity']);

        return view('requisitions.my_edit', compact('req', 'items', 'allProducts'));
    }

    public function addItemMy(Request $request, RequisitionService $service, int $id)
    {
        $username  = (string) session('user', '');
        $productId = (int) $request->input('product_id');
        $qty       = (int) $request->input('quantity');

        try {
            $service->addOrUpdateItem($id, $productId, $qty, $username);
            return redirect()->route('requisitions.my.edit', $id)
                ->with('message', '<div class="alert alert-success">Item saved.</div>');
        } catch (\Throwable $e) {
            return redirect()->route('requisitions.my.edit', $id)
                ->with('message', '<div class="alert alert-danger">'.$e->getMessage().'</div>');
        }
    }

    public function removeItemMy(Request $request, RequisitionService $service, int $id, int $itemId)
    {
        $username = (string) session('user', '');
        try {
            $service->removeItem($itemId, $username);
            return redirect()->route('requisitions.my.edit', $id)
                ->with('message', '<div class="alert alert-success">Item removed.</div>');
        } catch (\Throwable $e) {
            return redirect()->route('requisitions.my.edit', $id)
                ->with('message', '<div class="alert alert-danger">'.$e->getMessage().'</div>');
        }
    }

    public function submitMy(Request $request, RequisitionService $service, int $id)
    {
        $username = (string) session('user', '');
        try {
            $service->submit($id, $username);
            return redirect()->route('requisitions.my.show', $id)
                ->with('message', '<div class="alert alert-success">Request submitted.</div>');
        } catch (\Throwable $e) {
            return redirect()->route('requisitions.my.edit', $id)
                ->with('message', '<div class="alert alert-danger">'.$e->getMessage().'</div>');
        }
    }

    public function cancelMy(Request $request, RequisitionService $service, int $id)
    {
        $username = (string) session('user', '');
        try {
            $service->cancel($id, $username);
            return redirect()->route('requisitions.my.index')
                ->with('message', '<div class="alert alert-success">Request cancelled.</div>');
        } catch (\Throwable $e) {
            return redirect()->route('requisitions.my.show', $id)
                ->with('message', '<div class="alert alert-danger">'.$e->getMessage().'</div>');
        }
    }

    // ADMIN VIEWS

    public function adminIndex(Request $request)
    {
        $status = $request->query('status', 'submitted');
        $user   = trim((string) $request->query('user', ''));

        $query = DB::table('requisitions')->orderByDesc('id');

        if ($status !== 'all') {
            $query->where('status', $status);
        }
        if ($user !== '') {
            $query->where('username', 'LIKE', "%{$user}%");
        }

        $requisitions = $query->paginate(20);

        return view('requisitions.admin_index', compact('requisitions','status','user'));
    }

    public function adminShow(Request $request, int $id)
    {
        $req = DB::table('requisitions')->where('id', $id)->first();
        if (!$req) abort(404);

        $items = DB::table('requisition_items as ri')
            ->join('products as p', 'ri.product_id', '=', 'p.product_id')
            ->where('ri.requisition_id', $id)
            ->select('ri.*', 'p.name', 'p.sku', 'p.category', 'p.quantity as current_qty')
            ->get();

        return view('requisitions.admin_show', compact('req','items'));
    }

    public function adminApprove(Request $request, RequisitionService $service, int $id)
    {
        $admin = (string) session('user', '');
        $notes = trim((string) $request->input('notes', ''));

        try {
            $service->approve($id, $admin, $notes !== '' ? $notes : null);
            return back()->with('message', '<div class="alert alert-success">Approved.</div>');
        } catch (\Throwable $e) {
            return back()->with('message', '<div class="alert alert-danger">'.$e->getMessage().'</div>');
        }
    }

    public function adminReject(Request $request, RequisitionService $service, int $id)
    {
        $admin  = (string) session('user', '');
        $reason = trim((string) $request->input('reason', ''));
        $notes  = trim((string) $request->input('notes', ''));

        try {
            $service->reject(
                $id,
                $admin,
                $reason !== '' ? $reason : null,
                $notes  !== '' ? $notes  : null
            );
            return back()->with('message', '<div class="alert alert-success">Rejected.</div>');
        } catch (\Throwable $e) {
            return back()->with('message', '<div class="alert alert-danger">'.$e->getMessage().'</div>');
        }
    }

    public function adminFulfill(Request $request, RequisitionService $service, int $id)
    {
        $admin = (string) session('user', '');
        try {
            $service->fulfill($id, $admin);
            return back()->with('message', '<div class="alert alert-success">Requisition fulfilled and stock updated.</div>');
        } catch (\Throwable $e) {
            return back()->with('message', '<div class="alert alert-danger">'.$e->getMessage().'</div>');
        }
    }

    public function deleteMy(Request $request, RequisitionService $service, int $id)
    {
        $username = (string) session('user', '');
        if ($username === '') {
            abort(403, 'Not logged in.');
        }

        try {
            // byAdmin = false (user path)
            $service->delete($id, $username, false);

            return redirect()
                ->route('requisitions.my.index')
                ->with('message', '<div class="alert alert-success">Request deleted.</div>');
        } catch (\Throwable $e) {
            return back()->with(
                'message',
                '<div class="alert alert-danger">'.e($e->getMessage()).'</div>'
            );
        }
    }
    public function adminDelete(Request $request, RequisitionService $service, int $id)
    {
        $admin = (string) session('user', '');

        try {
            // byAdmin = true → can delete any requisition
            $service->delete($id, $admin, true);

            return redirect()
                ->route('requisitions.admin.index')
                ->with('message', '<div class="alert alert-success">Requisition deleted.</div>');
        } catch (\Throwable $e) {
            return back()->with(
                'message',
                '<div class="alert alert-danger">'.e($e->getMessage()).'</div>'
            );
        }
    }
}