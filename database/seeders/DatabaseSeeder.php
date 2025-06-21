<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    User::firstOrCreate(
        ['email' => 'admin@hms.com'], // unique check
        [
            'name' => 'Super Admin',
            'email' => 'admin@hms.com',
            'phone' => '08000000000',
            'address' => 'Main Office',
            'role' => 'admin',
            'password' => Hash::make('$Super$'),
        ]
    );

    // You can add more dummy staff/users here later
}
}
