<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogsController extends Controller
{
    /**
     * List activity logs with optional filters and pagination.
     * Accepts POST (from the filter form) and GET (querystring).
     *
     * View expects: rows, current_page, total_pages, total_records, filterUser, filterAction
     */
    public function index(Request $request)
    {
        // Read filters from POST first, then GET (so both methods work)
        $filterUser   = trim((string) $request->input('user', $request->query('u', '')));
        $filterAction = trim((string) $request->input('action', $request->query('action', '')));

        $allowedActions = ['login','logout','create','update','delete','archive','restore'];

        $where  = "1 = 1";
        $params = [];

        if ($filterUser !== '') {
            $where .= " AND username LIKE ?";
            $params[] = "%{$filterUser}%";
        }

        if ($filterAction !== '' && in_array($filterAction, $allowedActions, true)) {
            $where .= " AND action = ?";
            $params[] = $filterAction;
        }

        // Pagination
        $perPage = 25;
        $current_page = max(1, (int) $request->integer('page', 1));

        // Count total
        $countSql = "SELECT COUNT(*) AS total FROM activity_logs WHERE {$where}";
        $countRow = DB::selectOne($countSql, $params);
        $total_records = (int) ($countRow->total ?? 0);

        $total_pages = max(1, (int) ceil($total_records / $perPage));
        $current_page = min($current_page, $total_pages);
        $offset = ($current_page - 1) * $perPage;

        // List page
        $listSql = "SELECT log_id, username, action, product_id, details, created_at
                    FROM activity_logs
                    WHERE {$where}
                    ORDER BY log_id DESC
                    LIMIT ? OFFSET ?";

        $rows = DB::select($listSql, array_merge($params, [$perPage, $offset]));

        return view('logs.index', compact(
            'rows',
            'current_page',
            'total_pages',
            'total_records',
            'filterUser',
            'filterAction'
        ));
    }

    /**
     * Stream CSV export for logs, respecting filters.
     * Filters accepted via querystring: u (username contains), action, from, to.
     */
    public function exportCsv(Request $request)
    {
        $u      = trim((string) $request->query('u', ''));
        $action = trim((string) $request->query('action', ''));
        $from   = trim((string) $request->query('from', '')); // optional YYYY-MM-DD
        $to     = trim((string) $request->query('to', ''));   // optional YYYY-MM-DD

        $where  = "1 = 1";
        $params = [];

        if ($u !== '')      { $where .= " AND username LIKE ?";      $params[] = "%{$u}%"; }
        if ($action !== '') { $where .= " AND action = ?";            $params[] = $action; }
        if ($from !== '')   { $where .= " AND DATE(created_at) >= ?"; $params[] = $from; }
        if ($to !== '')     { $where .= " AND DATE(created_at) <= ?"; $params[] = $to; }

        // Use log_id to match your schema and Blade
        $sql = "SELECT log_id, username, action, product_id, details, created_at
                FROM activity_logs
                WHERE {$where}
                ORDER BY log_id DESC";

        $filename = 'logs_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($sql, $params) {
            $out = fopen('php://output', 'w');

            // CSV header
            fputcsv($out, ['Log ID', 'Username', 'Action', 'Product ID', 'Details', 'Created At']);

            foreach (DB::cursor($sql, $params) as $r) {
                fputcsv($out, [
                    (int) ($r->log_id ?? 0),
                    (string) ($r->username ?? ''),
                    (string) ($r->action ?? ''),
                    is_null($r->product_id) ? '' : (int) $r->product_id,
                    (string) ($r->details ?? ''),
                    (string) ($r->created_at ?? ''),
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}