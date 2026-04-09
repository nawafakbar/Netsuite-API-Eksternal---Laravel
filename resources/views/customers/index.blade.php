@extends('layouts.app')

@section('title', 'Purchase Orders')
@section('subtitle', 'List of all Purchase Orders from NetSuite')

@section('content')
<div class="space-y-4">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Total Cutomers</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_users'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-gray-700">All Customer</h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    Showing {{ $Customer->count() }} of {{ $total }} records
                </p>
            </div>
            <form method="GET" action="{{ route('customers.index') }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search vendor or PO..." 
                       class="pl-4 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 w-64"/>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Search</button>
                @if($search)
                    <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg">Clear</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-400 uppercase tracking-wide">
                        <th class="px-6 py-3 text-left">Company Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Phone</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($Customer as $cs)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-700">{{ $cs['companyName'] }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $cs['email'] }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $cs['phone'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">No purchase orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lastPage > 1)
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">
                Showing Page {{ $page }} of {{ $lastPage }} (Total {{ $total }} records)
            </p>
            <div class="flex items-center gap-1">

                {{-- First & Previous --}}
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
                @php
                    $start = max(1, $page - 2);
                    $end = min($lastPage, $page + 2);
                @endphp

                @for($i = $start; $i <= $end; $i++)
                <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                class="px-3 py-1.5 text-xs rounded-lg border transition
                {{ $i == $page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    {{ $i }}
                </a>
                @endfor

                {{-- Next & Last --}}
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