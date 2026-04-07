@extends('layouts.app')

@section('title', 'Sales Orders')
@section('subtitle', 'List of all Sales Orders from NetSuite')

@section('content')
<div class="space-y-4">

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Total Orders</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_orders'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Total Value</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($stats['total_value'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Billed</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['billed'] }}</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

        <!-- Search Bar -->
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-gray-700">All Sales Orders</h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    Showing {{ $salesOrders->count() }} of {{ $total }} records
                    @if($search) — filtered by "<span class="text-blue-600">{{ $search }}</span>" @endif
                </p>
            </div>
            <form method="GET" action="{{ route('salesorders.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Search orders..."
                           class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64"/>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                    Search
                </button>
                @if($search)
                <a href="{{ route('salesorders.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">
                    Clear
                </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-400 uppercase tracking-wide">
                        <th class="px-6 py-3 text-left">Order #</th>
                        <th class="px-6 py-3 text-left">Customer</th>
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-left">Sales Rep</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($salesOrders as $so)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-blue-600">#{{ $so['order_number'] }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $so['customer'] }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $so['date'] }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $so['sales_rep'] ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColor = match($so['status']) {
                                'Billed'                            => 'bg-green-50 text-green-700',
                                'Pending Fulfillment'               => 'bg-yellow-50 text-yellow-700',
                                'Pending Approval'                  => 'bg-yellow-50 text-yellow-700',
                                'Closed'                            => 'bg-gray-100 text-gray-500',
                                'Cancelled'                         => 'bg-red-50 text-red-600',
                                default                             => 'bg-blue-50 text-blue-600'
                            };

                            $statusLabel = match($so['status']) {
                                'Billed'                            => 'Billed',
                                'Pending Fulfillment'               => 'Pending',
                                'Pending Approval'                  => 'Pending',
                                'Pending Billing'                   => 'Pending',
                                'Closed'                            => 'Closed',
                                'Cancelled'                         => 'Cancel',
                                'Partially Fulfilled'               => 'Partially',
                                default                             => $so['status']
                            };
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-gray-800">
                            ${{ number_format($so['total'], 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('salesorders.show', $so['netsuite_id']) }}"
                               class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium transition">
                                View
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">
                            No sales orders found
                            @if($search)
                            for "<span class="text-gray-600">{{ $search }}</span>"
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($lastPage > 1)
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">
                Page {{ $page }} of {{ $lastPage }}
            </p>
            <div class="flex items-center gap-1">

                {{-- First --}}
                @if($page > 1)
                <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}"
                   class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
                    «
                </a>
                <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}"
                   class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
                    ‹
                </a>
                @endif

                {{-- Page Numbers --}}
                @for($i = max(1, $page - 2); $i <= min($lastPage, $page + 2); $i++)
                <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                   class="px-3 py-1.5 text-xs rounded-lg border transition
                   {{ $i == $page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    {{ $i }}
                </a>
                @endfor

                {{-- Next --}}
                @if($page < $lastPage)
                <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}"
                   class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
                    ›
                </a>
                <a href="{{ request()->fullUrlWithQuery(['page' => $lastPage]) }}"
                   class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
                    »
                </a>
                @endif

            </div>
        </div>
        @endif

    </div>
</div>
@endsection