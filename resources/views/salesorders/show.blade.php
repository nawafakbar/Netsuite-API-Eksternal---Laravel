@extends('layouts.app')

@section('title', 'Sales Order #' . ($salesOrder['order_number'] ?? ''))
@section('subtitle', 'Sales Order Detail')

@section('content')
<div class="space-y-5">

    <!-- Back Button -->
    <a href="{{ route('salesorders.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Sales Orders
    </a>

    <!-- Header Info -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Sales Order #{{ $salesOrder['order_number'] }}</h2>
                <p class="text-sm text-gray-400 mt-1">NetSuite ID: {{ $salesOrder['netsuite_id'] }}</p>
            </div>
            @php
                $statusColor = match($salesOrder['status'] ?? '') {
                    'Billed' => 'bg-green-50 text-green-700',
                    'Pending Fulfillment' => 'bg-yellow-50 text-yellow-700',
                    'Closed' => 'bg-gray-100 text-gray-500',
                    'Cancelled' => 'bg-red-50 text-red-600',
                    default => 'bg-blue-50 text-blue-600'
                };
            @endphp
            <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium {{ $statusColor }}">
                {{ $salesOrder['status'] ?? '-' }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Customer</p>
                <p class="text-sm font-medium text-gray-800 mt-1">{{ $salesOrder['customer'] ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Date</p>
                <p class="text-sm font-medium text-gray-800 mt-1">{{ $salesOrder['date'] ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Sales Rep</p>
                <p class="text-sm font-medium text-gray-800 mt-1">{{ $salesOrder['sales_rep'] ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Subsidiary</p>
                <p class="text-sm font-medium text-gray-800 mt-1">{{ $salesOrder['subsidiary'] ?? '-' }}</p>
            </div>
        </div>

        @if($salesOrder['memo'])
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Memo</p>
            <p class="text-sm text-gray-600 mt-1">{{ $salesOrder['memo'] }}</p>
        </div>
        @endif
    </div>

    <!-- Line Items -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Line Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-400 uppercase tracking-wide">
                        <th class="px-6 py-3 text-left">Item</th>
                        <th class="px-6 py-3 text-left">Description</th>
                        <th class="px-6 py-3 text-center">Qty</th>
                        <th class="px-6 py-3 text-right">Unit Price</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($salesOrder['line_items'] ?? [] as $line)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $line['item'] }}</td>
                        <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ $line['description'] ?: '-' }}</td>
                        <td class="px-6 py-4 text-center text-gray-700">{{ $line['quantity'] }}</td>
                        <td class="px-6 py-4 text-right text-gray-700">${{ number_format($line['unit_price'], 2) }}</td>
                        <td class="px-6 py-4 text-right font-medium text-gray-800">${{ number_format($line['amount'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">No line items found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="flex flex-col items-end space-y-1.5 text-sm">
                <div class="flex gap-12">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-medium text-gray-800 w-28 text-right">${{ number_format($salesOrder['subtotal'] ?? 0, 2) }}</span>
                </div>
                @if(($salesOrder['discount'] ?? 0) != 0)
                <div class="flex gap-12">
                    <span class="text-gray-500">Discount</span>
                    <span class="font-medium text-red-500 w-28 text-right">${{ number_format($salesOrder['discount'] ?? 0, 2) }}</span>
                </div>
                @endif
                <div class="flex gap-12 pt-2 border-t border-gray-200">
                    <span class="font-semibold text-gray-700">Total</span>
                    <span class="font-bold text-gray-900 w-28 text-right">${{ number_format($salesOrder['total'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection