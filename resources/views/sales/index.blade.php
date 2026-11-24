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

    <div class="mt-6 space-y-3 md:hidden">
        @forelse ($sales as $sale)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500">Transaction #{{ $sale->id }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $sale->created_at->copy()->setTimezone(config('pos.reporting_timezone', 'Asia/Manila'))->format('M j, Y · g:i A') }}</p>
                    </div>
                    <p class="shrink-0 text-right text-lg font-semibold tabular-nums">₱{{ number_format((float) $sale->total_price, 2) }}</p>
                </div>
                <dl class="mt-4 grid grid-cols-2 gap-x-3 gap-y-3 border-t border-slate-100 pt-3 text-sm">
                    <div class="col-span-2 min-w-0"><dt class="text-xs text-slate-500">Product</dt><dd class="mt-1 break-words font-medium">{{ $sale->product?->name ?? 'Product removed' }}</dd><dd class="text-xs text-slate-500">₱{{ number_format((float) $sale->unit_price, 2) }} each</dd></div>
                    <div class="min-w-0"><dt class="text-xs text-slate-500">Customer</dt><dd class="mt-1 break-words">{{ $sale->customer?->full_name ?? 'Walk-in / deleted' }}</dd></div>
                    <div class="min-w-0"><dt class="text-xs text-slate-500">Staff</dt><dd class="mt-1 break-words">{{ $sale->staff?->full_name ?? 'Unavailable' }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Quantity</dt><dd class="mt-1 tabular-nums">{{ number_format($sale->quantity) }}</dd></div>
                </dl>
            </article>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-12 text-center"><p class="font-medium text-slate-700">No sales recorded yet</p><p class="mt-1 text-sm text-slate-500">Completed transactions will appear here.</p><a href="{{ route('sales.create') }}" class="mt-4 inline-block min-h-11 py-3 text-sm font-semibold text-indigo-700 hover:text-indigo-900">Record the first sale</a></div>
        @endforelse
    </div>

    <div class="mt-6 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-5 py-3 font-semibold">Transaction</th><th class="px-5 py-3 font-semibold">Product</th><th class="px-5 py-3 font-semibold">Customer</th><th class="px-5 py-3 font-semibold">Staff</th><th class="px-5 py-3 text-right font-semibold">Qty</th><th class="px-5 py-3 text-right font-semibold">Total</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($sales as $sale)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4"><span class="block font-medium text-slate-900">#{{ $sale->id }}</span><span class="mt-1 block text-xs text-slate-500">{{ $sale->created_at->copy()->setTimezone(config('pos.reporting_timezone', 'Asia/Manila'))->format('M j, Y · g:i A') }}</span></td>
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
        @if ($sales->hasPages()) <div class="border-t border-slate-100 px-4 py-4 sm:px-5">{{ $sales->links() }}</div> @endif
    </div>
    @if ($sales->hasPages()) <div class="mt-4 md:hidden">{{ $sales->links() }}</div> @endif
@endsection
