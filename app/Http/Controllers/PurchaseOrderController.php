<?php

namespace App\Http\Controllers;

use App\Services\NetSuiteService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    protected $ns;

    public function __construct(NetSuiteService $ns)
    {
        $this->ns = $ns;
    }

    public function index(Request $request)
    {
        $response = $this->ns->get('purchaseorder');
        $allOrders = collect($response['data'] ?? []);

        // Search logic khusus PO
        $search = $request->get('search', '');
        if ($search) {
            $allOrders = $allOrders->filter(function($po) use ($search) {
                return str_contains(strtolower($po['po_number'] ?? ''), strtolower($search)) ||
                       str_contains(strtolower($po['vendor'] ?? ''), strtolower($search)) ||
                       str_contains(strtolower($po['status'] ?? ''), strtolower($search));
            });
        }

        // Stats khusus PO
        $allData = collect($response['data'] ?? []);
        $stats = [
            'total_orders' => $allData->count(),
            'total_value'  => $allData->sum('total'),
            'fully_billed' => $allData->where('status', 'Fully Billed')->count(),
        ];

        // Manual Pagination
        $perPage     = 10;
        $page        = $request->get('page', 1);
        $total       = $allOrders->count();
        $lastPage    = max(1, ceil($total / $perPage));
        $page        = max(1, min($page, $lastPage));
        $purchaseOrders = $allOrders->slice(($page - 1) * $perPage, $perPage)->values();

        return view('purchaseorders.index', compact('purchaseOrders', 'stats', 'search', 'page', 'lastPage', 'total', 'perPage'));
    }

    public function show($id)
    {
        $response      = $this->ns->get('purchaseorder', $id);
        $purchaseOrder = $response['data'] ?? [];
        return view('purchaseorders.show', compact('purchaseOrder'));
    }
}