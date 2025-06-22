<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create('en_US');
        $userIds = User::pluck('id')->toArray();

        for ($i = 0; $i < 75; $i++) {
            Patient::create([
                'name' => $faker->name,
                'phone' => $faker->unique()->phoneNumber,
                'email' => $faker->unique()->safeEmail,
                'gender' => $faker->randomElement(['male', 'female']),
                'dob' => $faker->dateTimeBetween('-90 years', '-18 years')->format('Y-m-d'),
                'address' => $faker->address,
                'created_by' => $faker->randomElement($userIds)
            ]);
        }
    }
}
