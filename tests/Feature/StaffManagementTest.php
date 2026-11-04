<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $currentStaff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStaff = User::create([
            'username' => 'manager',
            'full_name' => 'POS Manager',
            'password' => 'manager-password',
            'is_active' => true,
        ]);
        $this->actingAs($this->currentStaff);
        Storage::fake('public');
    }

    public function test_staff_can_create_an_account_with_a_hashed_password_and_avatar(): void
    {
        $this->post(route('staff.store'), [
            'username' => 'cashier',
            'full_name' => 'Casey Cashier',
            'password' => 'cashier-password',
            'password_confirmation' => 'cashier-password',
            'avatar' => $this->fakePng('avatar.png'),
        ])->assertRedirect(route('staff.index'));

        $cashier = User::where('username', 'cashier')->firstOrFail();
        $this->assertSame('Casey Cashier', $cashier->full_name);
        $this->assertTrue(Hash::check('cashier-password', $cashier->password));
        Storage::disk('public')->assertExists($cashier->avatar);
        $this->get(route('staff.show', $cashier))->assertOk()->assertSee('Casey Cashier');
    }

    public function test_staff_directory_displays_active_accounts_and_sales_counts(): void
    {
        $this->get(route('staff.index'))
            ->assertOk()
            ->assertSee('POS Manager')
            ->assertSee('Staff directory')
            ->assertSee('0');
    }

    public function test_staff_can_update_profile_and_change_password_without_overwriting_it_when_blank(): void
    {
        $cashier = User::create([
            'username' => 'cashier',
            'full_name' => 'Casey Cashier',
            'password' => 'original-password',
            'is_active' => true,
        ]);
        $oldHash = $cashier->password;

        $this->put(route('staff.update', $cashier), [
            'username' => 'cashier',
            'full_name' => 'Casey Updated',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('staff.show', $cashier));

        $cashier->refresh();
        $this->assertSame('Casey Updated', $cashier->full_name);
        $this->assertSame($oldHash, $cashier->password);

        $this->put(route('staff.update', $cashier), [
            'username' => 'cashier',
            'full_name' => 'Casey Updated',
            'password' => 'new-cashier-password',
            'password_confirmation' => 'new-cashier-password',
        ])->assertRedirect(route('staff.show', $cashier));

        $this->assertTrue(Hash::check('new-cashier-password', $cashier->fresh()->password));
    }

    public function test_staff_creation_validates_unique_username_and_confirmed_password(): void
    {
        $this->from(route('staff.create'))->post(route('staff.store'), [
            'username' => $this->currentStaff->username,
            'full_name' => 'Duplicate User',
            'password' => 'password-one',
            'password_confirmation' => 'password-two',
            'avatar' => UploadedFile::fake()->create('avatar.txt', 5, 'text/plain'),
        ])->assertRedirect(route('staff.create'))->assertSessionHasErrors(['username', 'password', 'avatar']);
    }

    public function test_inactive_staff_loses_access_to_an_existing_session(): void
    {
        $cashier = User::create([
            'username' => 'cashier',
            'full_name' => 'Casey Cashier',
            'password' => 'cashier-password',
            'is_active' => true,
        ]);

        $this->patch(route('staff.status', $cashier))->assertRedirect(route('staff.index', ['status' => 'inactive']));
        $this->assertFalse($cashier->fresh()->is_active);

        $this->actingAs($cashier->fresh())
            ->get(route('dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_staff_with_sales_cannot_be_deleted_but_can_be_deactivated(): void
    {
        $cashier = User::create([
            'username' => 'cashier',
            'full_name' => 'Casey Cashier',
            'password' => 'cashier-password',
            'is_active' => true,
        ]);
        $product = Product::create([
            'name' => 'POS item',
            'price' => '5.00',
            'stock_quantity' => 4,
            'is_active' => true,
        ]);
        $sale = Sale::create([
            'product_id' => $product->id,
            'sold_by' => $cashier->id,
            'quantity' => 1,
            'unit_price' => '5.00',
            'total_price' => '5.00',
        ]);

        $this->from(route('staff.index'))->delete(route('staff.destroy', $cashier))
            ->assertRedirect(route('staff.index'))
            ->assertSessionHasErrors('staff');
        $this->assertDatabaseHas('users', ['id' => $cashier->id]);

        $this->patch(route('staff.status', $cashier))->assertRedirect();
        $this->assertDatabaseHas('sales', ['id' => $sale->id, 'sold_by' => $cashier->id]);
        $this->assertFalse($cashier->fresh()->is_active);
    }

    public function test_staff_without_sales_can_be_deleted_but_signed_in_staff_cannot_delete_themselves(): void
    {
        $cashier = User::create([
            'username' => 'cashier',
            'full_name' => 'Casey Cashier',
            'password' => 'cashier-password',
            'is_active' => true,
        ]);

        $this->from(route('staff.index'))->delete(route('staff.destroy', $this->currentStaff))
            ->assertRedirect(route('staff.index'))
            ->assertSessionHasErrors('staff');
        $this->assertDatabaseHas('users', ['id' => $this->currentStaff->id]);

        $this->delete(route('staff.destroy', $cashier))->assertRedirect(route('staff.index'));
        $this->assertDatabaseMissing('users', ['id' => $cashier->id]);
    }

    public function test_guests_cannot_access_staff_management(): void
    {
        auth()->logout();

        $this->get(route('staff.index'))->assertRedirect(route('login'));
        $this->get(route('staff.create'))->assertRedirect(route('login'));
    }

    private function fakePng(string $filename): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $filename,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')
        );
    }
}
