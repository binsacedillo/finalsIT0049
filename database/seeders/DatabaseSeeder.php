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
        // in the local .env file. Never commit real credentials to the repository.
        $username = config('pos.admin_username');
        $password = config('pos.admin_password');

        if ($username && $password) {
            User::updateOrCreate(
                ['username' => $username],
                [
                    'full_name' => config('pos.admin_name', 'POS Administrator'),
                    'password' => $password,
                    'is_active' => true,
                ],
            );
        }
    }
}
