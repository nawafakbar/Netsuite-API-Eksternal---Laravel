<?php

namespace App\Http\Controllers;

use App\Services\NetSuiteService;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    protected $ns;

    public function __construct(NetSuiteService $ns)
    {
        $this->ns = $ns;
    }

    public function index(Request $request)
    {
        $response = $this->ns->get('salesorder');
        $allOrders = collect($response['data'] ?? []);

        // Search
        $search = $request->get('search', '');
        if ($search) {
            $allOrders = $allOrders->filter(function($so) use ($search) {
                return str_contains(strtolower($so['order_number'] ?? ''), strtolower($search)) ||
                       str_contains(strtolower($so['customer'] ?? ''), strtolower($search)) ||
                       str_contains(strtolower($so['sales_rep'] ?? ''), strtolower($search)) ||
                       str_contains(strtolower($so['status'] ?? ''), strtolower($search)) ||
                       str_contains(strtolower($so['subsidiary'] ?? ''), strtolower($search));
            });
        }

        // Stats (dari semua data, bukan hasil filter)
        $allData = collect($response['data'] ?? []);
        $stats = [
            'total_orders' => $allData->count(),
            'total_value'  => $allData->sum('total'),
            'billed'       => $allData->where('status', 'Billed')->count(),
        ];

        // Pagination manual
        $perPage    = 10;
        $page       = $request->get('page', 1);
        $total      = $allOrders->count();
        $lastPage   = max(1, ceil($total / $perPage));
        $page       = max(1, min($page, $lastPage));
        $salesOrders = $allOrders->slice(($page - 1) * $perPage, $perPage)->values();

        return view('salesorders.index', compact('salesOrders', 'stats', 'search', 'page', 'lastPage', 'total', 'perPage'));
    }

    public function show($id)
    {
        $response   = $this->ns->get('salesorder', $id);
        $salesOrder = $response['data'] ?? [];
        return view('salesorders.show', compact('salesOrder'));
    }
}