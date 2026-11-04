<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'active');
        $search = trim((string) $request->query('q', ''));

        $products = Product::query()
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'archived', fn ($query) => $query->where('is_active', false))
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'status', 'search'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedProduct($request);
        $image = $request->file('image');
        unset($data['image']);

        if ($image) {
            $data['image'] = $image->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('status', 'Product added successfully.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedProduct($request);
        $image = $request->file('image');
        unset($data['image']);

        if ($image) {
            $newImage = $image->store('products', 'public');
            $oldImage = $product->image;
            $data['image'] = $newImage;
            $product->update($data);

            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        } else {
            $product->update($data);
        }

        return redirect()->route('products.index')->with('status', 'Product updated successfully.');
    }

    public function archive(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false]);

        return redirect()->route('products.index')->with('status', 'Product archived.');
    }

    public function restore(Product $product): RedirectResponse
    {
        $product->update(['is_active' => true]);

        return redirect()->route('products.index', ['status' => 'archived'])
            ->with('status', 'Product restored.');
    }

    /** @return array{name: string, price: numeric-string|int|float, stock_quantity: int, image?: mixed} */
    private function validatedProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'gt:0', 'decimal:0,2', 'max:99999999.99'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }
}
