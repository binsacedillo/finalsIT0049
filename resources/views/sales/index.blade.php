@extends('layouts.app')

@section('title', 'Sales history')
@section('heading', 'Sales history')

@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm text-slate-500">Review completed transactions and the staff who recorded them.</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight">Sales history</h2>
        </div>
        <a href="{{ route('sales.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Record sale</a>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-5 py-3 font-semibold">Transaction</th><th class="px-5 py-3 font-semibold">Product</th><th class="px-5 py-3 font-semibold">Customer</th><th class="px-5 py-3 font-semibold">Staff</th><th class="px-5 py-3 text-right font-semibold">Qty</th><th class="px-5 py-3 text-right font-semibold">Total</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($sales as $sale)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4"><span class="block font-medium text-slate-900">#{{ $sale->id }}</span><span class="mt-1 block text-xs text-slate-500">{{ $sale->created_at->format('M j, Y · g:i A') }}</span></td>
                            <td class="px-5 py-4"><span class="font-medium text-slate-900">{{ $sale->product?->name ?? 'Product removed' }}</span><span class="mt-1 block text-xs text-slate-500">₱{{ number_format((float) $sale->unit_price, 2) }} each</span></td>
                            <td class="px-5 py-4 text-slate-600">{{ $sale->customer?->full_name ?? 'Walk-in / deleted customer' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $sale->staff?->full_name ?? 'Staff unavailable' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums">{{ number_format($sale->quantity) }}</td>
                            <td class="px-5 py-4 text-right font-semibold tabular-nums">₱{{ number_format((float) $sale->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-14 text-center"><p class="font-medium text-slate-700">No sales recorded yet</p><p class="mt-1 text-sm text-slate-500">Completed transactions will appear here.</p><a href="{{ route('sales.create') }}" class="mt-4 inline-block text-sm font-semibold text-indigo-700 hover:text-indigo-900">Record the first sale</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($sales->hasPages()) <div class="border-t border-slate-100 px-5 py-4">{{ $sales->links() }}</div> @endif
    </div>
@endsection
