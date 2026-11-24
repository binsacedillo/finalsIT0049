@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    <section aria-labelledby="dashboard-welcome" class="overflow-hidden rounded-2xl bg-indigo-950 p-6 text-white shadow-sm sm:p-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="max-w-2xl">
                <p class="text-sm font-medium text-indigo-200">Welcome back, {{ auth()->user()->full_name }}</p>
                <h2 id="dashboard-welcome" class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Your coffeehouse at a glance.</h2>
                <p class="mt-3 text-sm leading-6 text-indigo-100/75">Check today's sales, recent activity, and items that need a restock.</p>
                <p class="mt-4 text-xs font-medium text-indigo-200">{{ $reportNow->format('l, F j, Y') }} <span aria-hidden="true">&middot;</span> {{ '@' . auth()->user()->username }}</p>
            </div>
            <a href="{{ route('sales.create') }}" class="dashboard-primary-action inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-indigo-950 shadow-sm transition hover:bg-indigo-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                Record a sale
            </a>
        </div>
    </section>

    <section class="mt-7" aria-labelledby="today-overview">
        <div class="mb-3 flex items-end justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Today</p>
                <h2 id="today-overview" class="mt-1 text-xl font-semibold tracking-tight">At a glance</h2>
            </div>
            <span class="text-xs text-slate-500">{{ config('pos.reporting_timezone', 'Asia/Manila') }}</span>
        </div>

        <dl class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:p-5">
                <dt class="text-sm font-medium text-amber-700">Sales today</dt>
                <dd class="mt-3 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 sm:text-3xl">&#8369;{{ number_format((float) $salesTodayTotal, 2) }}</dd>
                <p class="mt-1 text-xs text-slate-600">Recorded transactions</p>
            </div>
            <div class="rounded-2xl border border-slate-300 bg-white p-4 shadow-sm sm:p-5">
                <dt class="text-sm font-medium text-slate-600">Transactions today</dt>
                <dd class="mt-3 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 sm:text-3xl">{{ number_format($transactionsToday) }}</dd>
                <p class="mt-1 text-xs text-slate-500">Sales completed today</p>
            </div>
            <div class="rounded-2xl border border-slate-300 bg-white p-4 shadow-sm sm:p-5">
                <dt class="text-sm font-medium text-slate-600">Active products</dt>
                <dd class="mt-3 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 sm:text-3xl">{{ number_format($activeProducts) }}</dd>
                <p class="mt-1 text-xs text-slate-500">Available in the catalog</p>
            </div>
            <div class="rounded-2xl border border-slate-300 bg-white p-4 shadow-sm sm:p-5">
                <dt class="text-sm font-medium text-slate-600">Out of stock</dt>
                <dd class="mt-3 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 sm:text-3xl">{{ number_format($outOfStockProducts) }}</dd>
                <p class="mt-1 text-xs text-slate-500">Active items with zero quantity</p>
            </div>
        </dl>
    </section>

    <section class="mt-7 grid gap-4 lg:grid-cols-3" aria-label="Recent sales and inventory">
        <div class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
                <div>
                    <h2 class="font-semibold tracking-tight">Recent sales</h2>
                    <p class="mt-1 text-xs text-slate-500">The latest recorded transactions</p>
                </div>
                <a href="{{ route('sales.index') }}" class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50 hover:text-indigo-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">View history</a>
            </div>

            @forelse ($recentSales as $sale)
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-4 last:border-b-0 sm:px-5">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-900">{{ $sale->product?->name ?? 'Product removed' }}</p>
                        <p class="mt-1 truncate text-xs text-slate-600">
                            {{ $sale->customer?->full_name ?? 'Walk-in sale' }}
                            <span aria-hidden="true">&middot;</span>
                            {{ $sale->quantity }} {{ \Illuminate\Support\Str::plural('item', $sale->quantity) }}
                            <span aria-hidden="true">&middot;</span>
                            {{ $sale->staff?->full_name ?? 'Staff unavailable' }}
                        </p>
                        <time class="mt-1 block text-xs text-slate-500" datetime="{{ $sale->created_at->toIso8601String() }}">{{ $sale->created_at->copy()->setTimezone($reportingTimezone)->format('M j, g:i A') }}</time>
                    </div>
                    <p class="shrink-0 text-sm font-semibold tabular-nums text-slate-900">&#8369;{{ number_format((float) $sale->total_price, 2) }}</p>
                </div>
            @empty
                <div class="px-5 py-10 text-center">
                    <p class="font-medium text-slate-800">No sales recorded yet</p>
                    <p class="mt-1 text-sm text-slate-500">Your completed transactions will appear here.</p>
                    <a href="{{ route('sales.create') }}" class="mt-4 inline-flex min-h-11 items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Record the first sale</a>
                </div>
            @endforelse
        </div>

        <aside class="rounded-2xl border border-slate-300 bg-white p-4 shadow-sm sm:p-5" aria-labelledby="stock-watchlist-heading">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 id="stock-watchlist-heading" class="font-semibold tracking-tight">Stock watchlist</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ $lowStockThreshold > 0 ? 'Active items with '.$lowStockThreshold.' or fewer units' : 'Active items with no stock' }}</p>
                </div>
                <a href="{{ route('products.index') }}" aria-label="View product catalog" class="inline-flex size-10 shrink-0 items-center justify-center rounded-lg text-indigo-700 hover:bg-indigo-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7 7 7-7 7"/></svg>
                </a>
            </div>

            @forelse ($stockAlerts as $product)
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 py-3 last:border-b-0">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-slate-800">{{ $product->name }}</p>
                        @if ($product->stock_quantity === 0)
                            <span class="mt-1 inline-flex rounded-full bg-rose-50 px-2 py-0.5 text-xs font-medium text-rose-700">Out of stock</span>
                        @else
                            <span class="mt-1 inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">Low stock</span>
                        @endif
                    </div>
                    <p class="shrink-0 text-sm font-semibold tabular-nums text-slate-700">{{ number_format($product->stock_quantity) }} left</p>
                </div>
            @empty
                <div class="mt-5 rounded-xl bg-indigo-50 px-4 py-5 text-center">
                    <p class="text-sm font-medium text-indigo-900">Stock levels look good</p>
                    <p class="mt-1 text-xs leading-5 text-slate-600">No active products are at or below the watchlist threshold.</p>
                </div>
            @endforelse
        </aside>
    </section>

    <section class="mt-7" aria-labelledby="quick-actions-heading">
        <div class="mb-3">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Shortcuts</p>
            <h2 id="quick-actions-heading" class="mt-1 text-xl font-semibold tracking-tight">Quick actions</h2>
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
            <a href="{{ route('products.create') }}" class="flex min-h-16 items-center gap-3 rounded-2xl border border-slate-300 bg-white p-4 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-indigo-400 hover:bg-indigo-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <span class="grid size-9 place-items-center rounded-lg bg-indigo-50 text-indigo-700" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg></span>
                Add a product
            </a>
            <a href="{{ route('customers.create') }}" class="flex min-h-16 items-center gap-3 rounded-2xl border border-slate-300 bg-white p-4 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-indigo-400 hover:bg-indigo-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <span class="grid size-9 place-items-center rounded-lg bg-indigo-50 text-indigo-700" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m16 0v-2a4 4 0 0 0-3-3.87M14 3.13a4 4 0 0 1 0 7.75M14 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/></svg></span>
                Add a customer
            </a>
            <a href="{{ route('staff.index') }}" class="flex min-h-16 items-center gap-3 rounded-2xl border border-slate-300 bg-white p-4 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-indigo-400 hover:bg-indigo-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <span class="grid size-9 place-items-center rounded-lg bg-indigo-50 text-indigo-700" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m16 0v-2a4 4 0 0 0-3-3.87M14 3.13a4 4 0 0 1 0 7.75M14 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/></svg></span>
                Manage staff
            </a>
        </div>
    </section>
@endsection
