<?php

namespace App\Http\Controllers;

use App\Services\NetSuiteService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $ns;

    public function __construct(NetSuiteService $ns)
    {
        $this->ns = $ns;
    }

    public function index(Request $request)
    {
        $response = $this->ns->get('customer');
        $allOrders = collect($response['data'] ?? []);

        // Search logic khusus PO
        $search = $request->get('search', '');
        if ($search) {
            $allOrders = $allOrders->filter(function($po) use ($search) {
                return str_contains(strtolower($po['companyName'] ?? ''), strtolower($search)) ||
                       str_contains(strtolower($po['email'] ?? ''), strtolower($search)) ||
                       str_contains(strtolower($po['phone'] ?? ''), strtolower($search));
            });
        }

        // Stats khusus Customer
        $allData = collect($response['data'] ?? []);
        $stats = [
            'total_users' => $allData->count(),
        ];

        // Manual Pagination
        $perPage     = 10;
        $page        = $request->get('page', 1);
        $total       = $allOrders->count();
        $lastPage    = max(1, ceil($total / $perPage));
        $page        = max(1, min($page, $lastPage));
        $Customer = $allOrders->slice(($page - 1) * $perPage, $perPage)->values();

        return view('customers.index', compact('Customer', 'stats','search', 'page', 'lastPage', 'total', 'perPage'));
    }

}