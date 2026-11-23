@extends('layouts.app')

@section('title', 'Record sale')
@section('heading', 'Record sale')

@section('content')
    <div class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,1fr)_340px] xl:gap-6">
        <section>
            <p class="text-sm text-slate-500">Choose an active product and quantity to complete a transaction.</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight">New sale</h2>

            <form method="POST" action="{{ route('sales.store') }}" class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-8">
                @csrf
                <div>
                    <label for="product_id" class="mb-2 block text-sm font-medium">Product</label>
                    <select id="product_id" name="product_id" required class="min-h-12 w-full min-w-0 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        <option value="">Choose a product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id) @disabled($product->stock_quantity < 1)>
                                {{ $product->name }} · ₱{{ number_format((float) $product->price, 2) }} · {{ $product->stock_quantity }} in stock
                            </option>
                        @endforeach
                    </select>
                    @error('product_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    @if (! $hasAvailableStock) <p class="mt-2 text-sm text-amber-700">No active products are in stock. Update inventory before recording a sale.</p> @endif
                </div>
                <div class="mt-5">
                    <label for="customer_id" class="mb-2 block text-sm font-medium">Customer <span class="font-normal text-slate-400">(optional)</span></label>
                    <select id="customer_id" name="customer_id" class="min-h-12 w-full min-w-0 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        <option value="">Walk-in customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->full_name }} · {{ $customer->email }}</option>
                        @endforeach
                    </select>
                    @error('customer_id') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="mt-5 max-w-xs">
                    <label for="quantity" class="mb-2 block text-sm font-medium">Quantity</label>
                    <input id="quantity" name="quantity" type="number" min="1" step="1" value="{{ old('quantity', 1) }}" required class="min-h-12 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    @error('quantity') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="mt-7 border-t border-slate-100 pt-5">
                    <button @disabled(! $hasAvailableStock) class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-300 sm:w-auto">Complete sale</button>
                    <a href="{{ route('sales.index') }}" class="mt-2 inline-flex min-h-11 items-center text-sm font-medium text-slate-600 hover:text-slate-900 sm:ml-3">View sales history</a>
                </div>
            </form>
        </section>

        <aside class="h-fit rounded-2xl border border-indigo-100 bg-indigo-50/70 p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-700">Sale notes</p>
            <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                <li class="flex gap-2"><span class="font-semibold text-indigo-600">01</span><span>The total is calculated from the current product price.</span></li>
                <li class="flex gap-2"><span class="font-semibold text-indigo-600">02</span><span>Inventory is reduced only after the sale is accepted.</span></li>
                <li class="flex gap-2"><span class="font-semibold text-indigo-600">03</span><span>Sales cannot exceed the available stock.</span></li>
            </ul>
        </aside>
    </div>
@endsection
