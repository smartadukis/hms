<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $types = ['Asset', 'Liability', 'Income', 'Expense', 'Equity'];

        for ($i = 0; $i < 30; $i++) {
            Account::create([
                'name' => $faker->company . ' Account',
                'code' => $faker->unique()->numerify('1###'),
                'type' => $faker->randomElement($types),
                'description' => $faker->sentence(6),
                'is_active' => $faker->boolean(90),
            ]);
        }
    }
}

