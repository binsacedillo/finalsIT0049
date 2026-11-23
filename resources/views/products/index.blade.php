@extends('layouts.app')

@section('title', 'Products')
@section('heading', 'Products')

@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm text-slate-500">Manage your catalog and on-hand inventory.</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight">Product catalog</h2>
        </div>
        <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Add product</a>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex gap-2 overflow-x-auto text-sm">
            @foreach (['active' => 'Active', 'archived' => 'Archived', 'all' => 'All'] as $key => $label)
                <a href="{{ route('products.index', array_filter(['status' => $key, 'q' => $search])) }}" @class(['inline-flex min-h-11 shrink-0 items-center rounded-lg px-3 py-2 font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600', 'bg-indigo-600 text-white shadow-sm hover:bg-indigo-700' => $status === $key, 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => $status !== $key]) @if ($status === $key) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </div>
        <form method="GET" action="{{ route('products.index') }}" class="flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <label for="product-search" class="sr-only">Search products</label>
            <input id="product-search" name="q" value="{{ $search }}" placeholder="Search products" class="min-h-11 w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 sm:w-64">
            <button class="min-h-11 shrink-0 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium hover:bg-slate-50">Search</button>
        </form>
    </div>

    <div class="mt-4 space-y-3 md:hidden">
        @forelse ($products as $product)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex min-w-0 items-start gap-3">
                    @if ($product->image)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="size-12 shrink-0 rounded-lg bg-slate-100 object-cover">
                    @else
                        <span class="grid size-12 shrink-0 place-items-center rounded-lg bg-indigo-50 text-sm font-semibold text-indigo-600">{{ strtoupper(mb_substr($product->name, 0, 1)) }}</span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <h3 class="break-words font-semibold text-slate-900">{{ $product->name }}</h3>
                        <span @class(['mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium', 'bg-emerald-50 text-emerald-700' => $product->is_active, 'bg-slate-100 text-slate-600' => ! $product->is_active])>{{ $product->is_active ? 'Active' : 'Archived' }}</span>
                    </div>
                </div>
                <dl class="mt-4 grid grid-cols-2 gap-3 border-t border-slate-100 pt-3 text-sm">
                    <div><dt class="text-xs text-slate-500">Price</dt><dd class="mt-1 font-semibold tabular-nums">₱{{ number_format((float) $product->price, 2) }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Stock</dt><dd class="mt-1 font-semibold tabular-nums">{{ number_format($product->stock_quantity) }}</dd></div>
                </dl>
                <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-3">
                    <a href="{{ route('products.edit', $product) }}" class="inline-flex min-h-11 items-center rounded-lg border border-slate-200 px-3 text-sm font-medium hover:bg-slate-50">Edit</a>
                    @if ($product->is_active)
                        <form method="POST" action="{{ route('products.archive', $product) }}" onsubmit="return confirm('Archive this product? It will no longer be available for new sales.')">
                            @csrf @method('PATCH')
                            <button class="min-h-11 rounded-lg border border-rose-200 px-3 text-sm font-medium text-rose-700 hover:bg-rose-50">Archive</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('products.restore', $product) }}">
                            @csrf @method('PATCH')
                            <button class="min-h-11 rounded-lg border border-indigo-200 px-3 text-sm font-medium text-indigo-700 hover:bg-indigo-50">Restore</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-12 text-center"><p class="font-medium text-slate-700">No products found</p><p class="mt-1 text-sm text-slate-500">Try a different filter or add a product to get started.</p></div>
        @endforelse
    </div>

    <div class="mt-4 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-5 py-3 font-semibold">Product</th><th class="px-5 py-3 font-semibold">Price</th><th class="px-5 py-3 font-semibold">Stock</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 text-right font-semibold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($product->image)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="size-11 rounded-lg bg-slate-100 object-cover">
                                    @else
                                        <span class="grid size-11 place-items-center rounded-lg bg-indigo-50 text-sm font-semibold text-indigo-600">{{ strtoupper(mb_substr($product->name, 0, 1)) }}</span>
                                    @endif
                                    <span class="font-medium text-slate-900">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 tabular-nums">₱{{ number_format((float) $product->price, 2) }}</td>
                            <td class="px-5 py-4 tabular-nums">{{ number_format($product->stock_quantity) }}</td>
                            <td class="px-5 py-4"><span @class(['rounded-full px-2.5 py-1 text-xs font-medium', 'bg-emerald-50 text-emerald-700' => $product->is_active, 'bg-slate-100 text-slate-600' => ! $product->is_active])>{{ $product->is_active ? 'Active' : 'Archived' }}</span></td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('products.edit', $product) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium hover:bg-slate-50">Edit</a>
                                    @if ($product->is_active)
                                        <form method="POST" action="{{ route('products.archive', $product) }}" onsubmit="return confirm('Archive this product? It will no longer be available for new sales.')">
                                            @csrf @method('PATCH')
                                            <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Archive</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('products.restore', $product) }}">
                                            @csrf @method('PATCH')
                                            <button class="rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50">Restore</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-14 text-center"><p class="font-medium text-slate-700">No products found</p><p class="mt-1 text-sm text-slate-500">Try a different filter or add a product to get started.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($products->hasPages()) <div class="border-t border-slate-100 px-4 py-4 sm:px-5">{{ $products->links() }}</div> @endif
    </div>
    @if ($products->hasPages()) <div class="mt-4 md:hidden">{{ $products->links() }}</div> @endif
@endsection
