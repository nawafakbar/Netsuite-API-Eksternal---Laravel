@extends('layouts.app')

@section('title', 'Purchase Order #' . ($purchaseOrder['po_number'] ?? ''))
@section('subtitle', 'Purchase Order Detail')

@section('content')
<div class="space-y-5">

    <a href="{{ route('purchaseorders.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Purchase Orders
    </a>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Purchase Order #{{ $purchaseOrder['po_number'] }}</h2>
                <p class="text-sm text-gray-400 mt-1">NetSuite ID: {{ $purchaseOrder['netsuite_id'] }}</p>
            </div>
            @php
                $statusColor = match($purchaseOrder['status'] ?? '') {
                    'Fully Billed'    => 'bg-green-50 text-green-700',
                    'Pending Bill'    => 'bg-orange-50 text-orange-700',
                    'Pending Receipt' => 'bg-yellow-50 text-yellow-700',
                    'Closed'          => 'bg-gray-100 text-gray-500',
                    default           => 'bg-blue-50 text-blue-600'
                };
            @endphp
            <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium {{ $statusColor }}">
                {{ $purchaseOrder['status'] ?? '-' }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Vendor</p>
                <p class="text-sm font-medium text-gray-800 mt-1">{{ $purchaseOrder['vendor'] ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Date</p>
                <p class="text-sm font-medium text-gray-800 mt-1">{{ $purchaseOrder['date'] ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Currency</p>
                <p class="text-sm font-medium text-gray-800 mt-1">EUR</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Subsidiary</p>
                <p class="text-sm font-medium text-gray-800 mt-1">{{ $purchaseOrder['subsidiary'] ?? '-' }}</p>
            </div>
        </div>

        @if(isset($purchaseOrder['memo']) && $purchaseOrder['memo'])
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Memo</p>
            <p class="text-sm text-gray-600 mt-1">{{ $purchaseOrder['memo'] }}</p>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-700">Line Items</h3>
            <span class="text-xs text-gray-400">{{ count($purchaseOrder['line_items'] ?? []) }} Items</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-400 uppercase tracking-wide">
                        <th class="px-6 py-3 text-left">Item</th>
                        <th class="px-6 py-3 text-left">Description</th>
                        <th class="px-6 py-3 text-center">Ordered</th>
                        <th class="px-6 py-3 text-center text-blue-600">Received</th>
                        <th class="px-6 py-3 text-center">Billed</th>
                        <th class="px-6 py-3 text-right">Unit Price</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($purchaseOrder['line_items'] ?? [] as $line)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $line['item'] }}</td>
                        <td class="px-6 py-4 text-gray-500 max-w-xs truncate" title="{{ $line['description'] }}">
                            {{ $line['description'] ?: '-' }}
                        </td>
                        <td class="px-6 py-4 text-center text-gray-700">{{ $line['qty_ordered'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold {{ $line['qty_received'] >= $line['qty_ordered'] ? 'text-green-600' : 'text-blue-600' }}">
                                {{ $line['qty_received'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500">{{ $line['qty_billed'] }}</td>
                        <td class="px-6 py-4 text-right text-gray-700">€{{ number_format($line['unit_price'], 2) }}</td>
                        <td class="px-6 py-4 text-right font-medium text-gray-800">€{{ number_format($line['amount'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">No line items found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="flex flex-col items-end space-y-1.5 text-sm">
                <div class="flex gap-12">
                    <span class="text-gray-500">Total Purchase</span>
                    <span class="font-bold text-gray-900 w-28 text-right">€{{ number_format($purchaseOrder['total'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection