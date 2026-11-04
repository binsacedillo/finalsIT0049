<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleProcessingTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = User::create([
            'username' => 'cashier',
            'full_name' => 'Alex Cashier',
            'password' => 'cashier-password',
            'is_active' => true,
        ]);
        $this->actingAs($this->staff);
    }

    public function test_staff_can_record_a_sale_with_no_customer_and_stock_decreases(): void
    {
        $product = Product::create([
            'name' => 'Desk Lamp',
            'price' => '21.35',
            'stock_quantity' => 8,
            'is_active' => true,
        ]);

        $this->post(route('sales.store'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ])->assertRedirect(route('sales.index'));

        $sale = Sale::firstOrFail();
        $this->assertSame($product->id, $sale->product_id);
        $this->assertNull($sale->customer_id);
        $this->assertSame($this->staff->id, $sale->sold_by);
        $this->assertSame(3, $sale->quantity);
        $this->assertSame('21.35', $sale->unit_price);
        $this->assertSame('64.05', $sale->total_price);
        $this->assertSame(5, $product->fresh()->stock_quantity);

        $this->get(route('sales.index'))->assertOk()->assertSee('Desk Lamp')->assertSee('Alex Cashier')->assertSee('Walk-in / deleted customer');
    }

    public function test_sale_history_shows_the_selected_customer(): void
    {
        $product = Product::create([
            'name' => 'Coffee Mug',
            'price' => '10.00',
            'stock_quantity' => 5,
            'is_active' => true,
        ]);
        $customer = Customer::create([
            'full_name' => 'Taylor Customer',
            'email' => 'taylor@example.com',
        ]);

        $this->post(route('sales.store'), [
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'quantity' => 2,
        ])->assertRedirect(route('sales.index'));

        $this->get(route('sales.index'))->assertOk()->assertSee('Taylor Customer')->assertSee('Coffee Mug')->assertSee('₱20.00');
    }

    public function test_sale_form_shows_active_products_and_an_optional_customer_choice(): void
    {
        Product::create([
            'name' => 'Available item',
            'price' => '5.00',
            'stock_quantity' => 2,
            'is_active' => true,
        ]);
        Product::create([
            'name' => 'Archived item',
            'price' => '6.00',
            'stock_quantity' => 2,
            'is_active' => false,
        ]);
        Customer::create([
            'full_name' => 'Walk-in Option',
            'email' => 'walkin@example.com',
        ]);

        $this->get(route('sales.create'))
            ->assertOk()
            ->assertSee('Available item')
            ->assertDontSee('Archived item')
            ->assertSee('Walk-in customer')
            ->assertSee('Walk-in Option');
    }

    public function test_sale_over_available_stock_is_rejected_without_changing_stock_or_history(): void
    {
        $product = Product::create([
            'name' => 'USB Hub',
            'price' => '450.00',
            'stock_quantity' => 2,
            'is_active' => true,
        ]);

        $this->from(route('sales.create'))->post(route('sales.store'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ])->assertRedirect(route('sales.create'))
            ->assertSessionHasErrors('quantity');

        $this->assertSame(2, $product->fresh()->stock_quantity);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_archived_products_and_invalid_customers_are_rejected(): void
    {
        $product = Product::create([
            'name' => 'Archived item',
            'price' => '5.00',
            'stock_quantity' => 5,
            'is_active' => false,
        ]);

        $this->from(route('sales.create'))->post(route('sales.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect(route('sales.create'))->assertSessionHasErrors('product_id');

        $active = Product::create([
            'name' => 'Active item',
            'price' => '5.00',
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $this->from(route('sales.create'))->post(route('sales.store'), [
            'product_id' => $active->id,
            'customer_id' => 99999,
            'quantity' => 1,
        ])->assertRedirect(route('sales.create'))->assertSessionHasErrors('customer_id');

        $this->assertDatabaseCount('sales', 0);
    }

    public function test_quantity_must_be_a_positive_integer(): void
    {
        $product = Product::create([
            'name' => 'Notebook',
            'price' => '12.00',
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $this->from(route('sales.create'))->post(route('sales.store'), [
            'product_id' => $product->id,
            'quantity' => 0,
        ])->assertRedirect(route('sales.create'))->assertSessionHasErrors('quantity');

        $this->assertSame(5, $product->fresh()->stock_quantity);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_total_must_fit_the_sales_decimal_column(): void
    {
        $product = Product::create([
            'name' => 'High value item',
            'price' => '90000000.00',
            'stock_quantity' => 3,
            'is_active' => true,
        ]);

        $this->from(route('sales.create'))->post(route('sales.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertRedirect(route('sales.create'))->assertSessionHasErrors('quantity');

        $this->assertSame(3, $product->fresh()->stock_quantity);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_guests_cannot_record_or_view_sales(): void
    {
        auth()->logout();

        $this->get(route('sales.create'))->assertRedirect(route('login'));
        $this->get(route('sales.index'))->assertRedirect(route('login'));
    }
}
