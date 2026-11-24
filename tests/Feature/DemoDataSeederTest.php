<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::create([
            'username' => 'manager',
            'full_name' => 'POS Manager',
            'password' => 'manager-password',
            'is_active' => true,
        ]);
    }

    public function test_demo_seeder_creates_a_catalog_and_history_once_with_stock_reconciled(): void
    {
        (new DemoDataSeeder())->run();

        $this->assertDatabaseCount('products', 11);
        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('sales', 8);
        $this->assertDatabaseHas('sales', [
            'quantity' => 2,
            'unit_price' => '185.00',
            'total_price' => '370.00',
        ]);
        $this->assertSame(18, Product::where('name', 'Caffe Latte (12 oz)')->value('stock_quantity'));
        $this->assertSame(11, Product::where('name', 'Butter Croissant')->value('stock_quantity'));

        (new DemoDataSeeder())->run();

        $this->assertDatabaseCount('products', 11);
        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('sales', 8);
        $this->assertSame(18, Product::where('name', 'Caffe Latte (12 oz)')->value('stock_quantity'));
    }

    public function test_demo_seeder_does_not_add_sample_sales_to_a_database_with_sales(): void
    {
        $staff = User::where('username', 'manager')->firstOrFail();
        $product = Product::create([
            'name' => 'Existing product',
            'price' => '50.00',
            'stock_quantity' => 7,
            'is_active' => true,
        ]);
        Sale::create([
            'product_id' => $product->id,
            'sold_by' => $staff->id,
            'quantity' => 1,
            'unit_price' => '50.00',
            'total_price' => '50.00',
        ]);

        (new DemoDataSeeder())->run();

        $this->assertDatabaseCount('sales', 1);
        $this->assertSame(7, $product->fresh()->stock_quantity);
        $this->assertDatabaseCount('products', 12);
        $this->assertDatabaseCount('customers', 5);
    }
}
