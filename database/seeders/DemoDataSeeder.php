<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    /**
     * Add fictional coffeehouse records without overwriting existing catalog data.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $staff = User::query()
                ->where('username', config('pos.admin_username'))
                ->where('is_active', true)
                ->first()
                ?? User::query()->where('is_active', true)->orderBy('id')->first();

            if (! $staff) {
                return;
            }

            $products = [];

            foreach ($this->products() as $attributes) {
                $product = Product::firstOrCreate(
                    ['name' => $attributes['name']],
                    [
                        'price' => $attributes['price'],
                        'stock_quantity' => $attributes['stock_quantity'],
                        'is_active' => true,
                    ],
                );

                $products[$attributes['name']] = $product;
            }

            $customers = [];

            foreach ($this->customers() as $attributes) {
                $customers[$attributes['email']] = Customer::firstOrCreate(
                    ['email' => $attributes['email']],
                    [
                        'full_name' => $attributes['full_name'],
                        'phone' => null,
                    ],
                );
            }

            // Never mix generated sample transactions into a database that
            // already has sales. This also makes Render restarts safe.
            if (Sale::query()->exists()) {
                return;
            }

            foreach ($this->sales() as [$productName, $customerEmail, $quantity, $daysAgo, $hour, $minute]) {
                $product = $products[$productName];

                // Do not create fictional sales against pre-existing products,
                // or products that have since been archived or sold out.
                if (! $product->wasRecentlyCreated || ! $product->is_active || $product->stock_quantity < $quantity) {
                    continue;
                }

                $unitPrice = (string) $product->getRawOriginal('price');
                $totalCents = $this->decimalToCents($unitPrice) * $quantity;

                $product->decrement('stock_quantity', $quantity);

                Sale::create([
                    'product_id' => $product->id,
                    'customer_id' => $customerEmail ? $customers[$customerEmail]->id : null,
                    'sold_by' => $staff->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $this->centsToDecimal($totalCents),
                    'created_at' => now()->subDays($daysAgo)->setTime($hour, $minute),
                ]);
            }
        });
    }

    /** @return list<array{name: string, price: string, stock_quantity: int}> */
    private function products(): array
    {
        return [
            ['name' => 'House Blend Beans (250 g)', 'price' => '575.00', 'stock_quantity' => 24],
            ['name' => 'Dark Roast Beans (250 g)', 'price' => '610.00', 'stock_quantity' => 18],
            ['name' => 'Caffe Latte (12 oz)', 'price' => '185.00', 'stock_quantity' => 20],
            ['name' => 'Caramel Macchiato (12 oz)', 'price' => '205.00', 'stock_quantity' => 16],
            ['name' => 'Cold Brew (16 oz)', 'price' => '195.00', 'stock_quantity' => 22],
            ['name' => 'Matcha Latte (12 oz)', 'price' => '210.00', 'stock_quantity' => 14],
            ['name' => 'Butter Croissant', 'price' => '115.00', 'stock_quantity' => 12],
            ['name' => 'Blueberry Muffin', 'price' => '135.00', 'stock_quantity' => 10],
            ['name' => 'Reusable Tumbler', 'price' => '650.00', 'stock_quantity' => 8],
            ['name' => 'Ceramic Mug', 'price' => '420.00', 'stock_quantity' => 10],
            ['name' => 'Seasonal Pour-over Kit', 'price' => '395.00', 'stock_quantity' => 4],
        ];
    }

    /** @return list<array{full_name: string, email: string}> */
    private function customers(): array
    {
        return [
            ['full_name' => 'Alex Rivera', 'email' => 'alex.rivera@example.com'],
            ['full_name' => 'Jamie Santos', 'email' => 'jamie.santos@example.com'],
            ['full_name' => 'Taylor Reyes', 'email' => 'taylor.reyes@example.com'],
            ['full_name' => 'Morgan Cruz', 'email' => 'morgan.cruz@example.com'],
            ['full_name' => 'Casey Lim', 'email' => 'casey.lim@example.com'],
        ];
    }

    /** @return list<array{string, ?string, int, int, int, int}> */
    private function sales(): array
    {
        return [
            ['Caffe Latte (12 oz)', 'alex.rivera@example.com', 2, 0, 9, 15],
            ['Butter Croissant', 'alex.rivera@example.com', 1, 0, 9, 15],
            ['Cold Brew (16 oz)', 'jamie.santos@example.com', 1, 1, 14, 40],
            ['Caramel Macchiato (12 oz)', 'taylor.reyes@example.com', 1, 2, 10, 5],
            ['House Blend Beans (250 g)', null, 1, 3, 12, 10],
            ['Blueberry Muffin', 'morgan.cruz@example.com', 2, 4, 8, 50],
            ['Matcha Latte (12 oz)', 'casey.lim@example.com', 1, 5, 16, 20],
            ['Reusable Tumbler', 'taylor.reyes@example.com', 1, 6, 11, 30],
        ];
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
