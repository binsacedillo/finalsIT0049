<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        $sales = Sale::query()
            ->with(['product:id,name', 'customer:id,full_name', 'staff:id,full_name'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'stock_quantity']);
        $customers = Customer::query()->orderBy('full_name')->get(['id', 'full_name', 'email']);
        $hasAvailableStock = $products->contains(fn (Product $product) => $product->stock_quantity > 0);

        return view('sales.create', compact('products', 'customers', 'hasAvailableStock'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->where('is_active', true)],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:2147483647'],
        ], [
            'product_id.exists' => 'Select an active product.',
        ]);

        $sale = DB::transaction(function () use ($data, $request): Sale {
            $product = Product::query()->whereKey($data['product_id'])->lockForUpdate()->first();

            if (! $product || ! $product->is_active) {
                throw ValidationException::withMessages(['product_id' => 'That product is no longer available. Choose an active product.']);
            }

            if ($data['quantity'] > $product->stock_quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Only {$product->stock_quantity} unit(s) of {$product->name} are currently in stock.",
                ]);
            }

            $unitPrice = (string) $product->getRawOriginal('price');
            $unitCents = $this->decimalToCents($unitPrice);
            $maxTotalCents = 9_999_999_999;

            if ($unitCents > intdiv($maxTotalCents, (int) $data['quantity'])) {
                throw ValidationException::withMessages([
                    'quantity' => 'The sale total exceeds the maximum amount supported by the sales record.',
                ]);
            }

            $totalCents = $unitCents * (int) $data['quantity'];

            $product->decrement('stock_quantity', (int) $data['quantity']);

            return Sale::create([
                'product_id' => $product->id,
                'customer_id' => $data['customer_id'] ?? null,
                'sold_by' => $request->user()->id,
                'quantity' => (int) $data['quantity'],
                'unit_price' => $unitPrice,
                'total_price' => $this->centsToDecimal($totalCents),
            ]);
        }, attempts: 3);

        return redirect()->route('sales.index')->with('status', "Sale #{$sale->id} recorded successfully.");
    }

    private function decimalToCents(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '00');
        $fraction = str_pad(substr($fraction, 0, 2), 2, '0');

        return ((int) $whole * 100) + (int) $fraction;
    }

    private function centsToDecimal(int $cents): string
    {
        return intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
}
