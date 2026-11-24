<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create the initial staff account only when credentials are supplied
        // through environment configuration. Never commit real credentials.
        $username = config('pos.admin_username');
        $password = config('pos.admin_password');

        if ($username && $password) {
            User::firstOrCreate(
                ['username' => $username],
                [
                    'full_name' => config('pos.admin_name', 'POS Administrator'),
                    'password' => $password,
                    'is_active' => true,
                ],
            );
        }

        if (config('pos.demo_data_enabled')) {
            $this->call(DemoDataSeeder::class);
        }
    }
}
