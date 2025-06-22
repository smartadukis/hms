<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('en_CA');

        // for ($i = 0; $i < 30; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                //'email_verified_at' => now(),
                'password' => Hash::make('password'), // or bcrypt('password')
                'remember_token' => \Str::random(10),
                'address' => $faker->address,
                'role' => $faker->randomElement(['staff', 'doctor', 'nurse', 'receptionist', 'lab_staff', 'pharmacist', 'accountant']),

            ]);
                // Create 5 Doctor users
        User::factory()->count(5)->create([
            'role' => 'doctor',
        ]);

        // Create 7 Nurse users
        User::factory()->count(15)->create([
            'role' => 'nurse',
        ]);

        // Create 3 Receptionist users
        User::factory()->count(3)->create([
            'role' => 'receptionist',
        ]);

        // Create 4 Laboratory Staff users
        User::factory()->count(4)->create([
            'role' => 'lab_staff',
        ]);

        // Create 2 Pharmacist users
        User::factory()->count(2)->create([
            'role' => 'pharmacist',
        ]);

        // Create 3 Accountant users
        User::factory()->count(3)->create([
            'role' => 'accountant',
        ]);
            
       // }
    }
}


