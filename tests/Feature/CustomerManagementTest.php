<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = User::create([
            'username' => 'staff',
            'full_name' => 'Staff Member',
            'password' => 'staff-password',
            'is_active' => true,
        ]);
        $this->actingAs($this->staff);
    }

    public function test_staff_can_create_view_update_and_delete_a_customer(): void
    {
        $this->post(route('customers.store'), [
            'full_name' => 'Jamie Cruz',
            'email' => 'jamie@example.com',
            'phone' => '09171234567',
        ])->assertRedirect(route('customers.index'));

        $customer = Customer::firstOrFail();
        $this->get(route('customers.show', $customer))->assertOk()->assertSee('Jamie Cruz')->assertSee('jamie@example.com');

        $this->put(route('customers.update', $customer), [
            'full_name' => 'Jamie Santos',
            'email' => 'jamie.santos@example.com',
            'phone' => null,
        ])->assertRedirect(route('customers.show', $customer));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'full_name' => 'Jamie Santos',
            'email' => 'jamie.santos@example.com',
            'phone' => null,
        ]);

        $this->delete(route('customers.destroy', $customer))->assertRedirect(route('customers.index'));
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_customer_fields_are_validated(): void
    {
        $this->from(route('customers.create'))
            ->post(route('customers.store'), [
                'full_name' => str_repeat('x', 101),
                'email' => 'not-an-email',
                'phone' => str_repeat('1', 21),
            ])
            ->assertRedirect(route('customers.create'))
            ->assertSessionHasErrors(['full_name', 'email', 'phone']);

        $this->assertDatabaseCount('customers', 0);
    }

    public function test_deleting_customer_preserves_sales_and_clears_the_customer_reference(): void
    {
        $customer = Customer::create([
            'full_name' => 'History Customer',
            'email' => 'history@example.com',
        ]);
        $product = Product::create([
            'name' => 'Test item',
            'price' => '20.00',
            'stock_quantity' => 5,
            'is_active' => true,
        ]);
        $sale = Sale::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'sold_by' => $this->staff->id,
            'quantity' => 1,
            'unit_price' => '20.00',
            'total_price' => '20.00',
        ]);

        $this->delete(route('customers.destroy', $customer))->assertRedirect(route('customers.index'));

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
        $this->assertDatabaseHas('sales', ['id' => $sale->id, 'customer_id' => null]);
    }

    public function test_guests_cannot_access_customer_management(): void
    {
        auth()->logout();

        $this->get(route('customers.index'))->assertRedirect(route('login'));
        $this->get(route('customers.create'))->assertRedirect(route('login'));
    }
}
