<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="mt-7 space-y-5">
    @csrf
    @if ($method !== 'POST') @method($method) @endif
    <div>
        <label for="name" class="mb-2 block text-sm font-medium">Product name</label>
        <input id="name" name="name" value="{{ old('name', $product?->name) }}" required maxlength="100" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
        @error('name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="price" class="mb-2 block text-sm font-medium">Price (₱)</label>
            <input id="price" name="price" type="number" min="0.01" step="0.01" value="{{ old('price', $product?->price) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            @error('price') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="stock_quantity" class="mb-2 block text-sm font-medium">Stock quantity</label>
            <input id="stock_quantity" name="stock_quantity" type="number" min="0" step="1" value="{{ old('stock_quantity', $product?->stock_quantity ?? 0) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            @error('stock_quantity') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
    </div>
    <div>
        <label for="image" class="mb-2 block text-sm font-medium">Product image <span class="font-normal text-slate-400">(JPG, PNG, or WebP; up to 2 MB)</span></label>
        @if ($product?->image)
            <img src="{{ $product->image_url }}" alt="Current {{ $product->name }} image" class="mb-3 size-20 rounded-xl object-cover">
        @endif
        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-indigo-700">
        @error('image') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('products.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">{{ $buttonText }}</button>
    </div>
</form>
