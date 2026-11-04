<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_the_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_guest_can_view_the_staff_login_form(): void
    {
        $this->get('/login')->assertOk()->assertSee('Staff portal')->assertSee('Username');
    }

    public function test_active_staff_can_sign_in_and_open_the_dashboard(): void
    {
        User::create([
            'username' => 'cashier1',
            'full_name' => 'Cashier One',
            'password' => 'correct-horse-battery',
            'is_active' => true,
        ]);

        $this->post('/login', [
            'username' => 'cashier1',
            'password' => 'correct-horse-battery',
        ])->assertRedirect('/dashboard');

        $this->get('/dashboard')->assertOk()->assertSee('Cashier One');
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::create([
            'username' => 'cashier1',
            'full_name' => 'Cashier One',
            'password' => 'correct-horse-battery',
            'is_active' => true,
        ]);

        $this->from('/login')->post('/login', [
            'username' => 'cashier1',
            'password' => 'incorrect-password',
        ])->assertRedirect('/login')->assertSessionHasErrors('username');
    }

    public function test_inactive_staff_cannot_sign_in(): void
    {
        User::create([
            'username' => 'inactive',
            'full_name' => 'Inactive Staff',
            'password' => 'correct-horse-battery',
            'is_active' => false,
        ]);

        $this->from('/login')->post('/login', [
            'username' => 'inactive',
            'password' => 'correct-horse-battery',
        ])->assertRedirect('/login')->assertSessionHasErrors('username');
    }

    public function test_staff_can_log_out(): void
    {
        $staff = User::create([
            'username' => 'cashier1',
            'full_name' => 'Cashier One',
            'password' => 'correct-horse-battery',
            'is_active' => true,
        ]);

        $this->actingAs($staff)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->get('/dashboard')->assertRedirect('/login');
    }
}
