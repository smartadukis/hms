<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use App\Models\Patient;
use App\Models\Medication;
use Faker\Factory as Faker;

class PrescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $doctors = User::where('role', 'doctor')->pluck('id')->toArray();
        $patients = Patient::pluck('id')->toArray();
        $medications = Medication::pluck('id')->toArray();
        $users = User::pluck('id')->toArray();

        if (empty($doctors) || empty($patients) || empty($medications)) {
            $this->command->warn('Please ensure doctors, patients, and medications are seeded before running PrescriptionSeeder.');
            return;
        }

        for ($i = 0; $i < 30; $i++) {
            $prescription = Prescription::create([
                'doctor_id' => $faker->randomElement($doctors),
                'patient_id' => $faker->randomElement($patients),
                'created_by' => $faker->randomElement($users),
                'notes' => $faker->boolean(70) ? $faker->sentence() : null,
            ]);

            $numItems = rand(1, 4);
            for ($j = 0; $j < $numItems; $j++) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medication_id' => $faker->randomElement($medications),
                    'dosage_quantity' => $faker->numberBetween(1, 3),
                    'dosage_unit' => $faker->randomElement(['tablet', 'ml', 'capsule']),
                    'frequency' => $faker->randomElement(['once daily', 'twice daily', 'every 8 hours']),
                    'duration' => $faker->randomElement(['5 days', '7 days', '10 days']),
                    'instructions' => $faker->boolean(60) ? $faker->sentence() : null,
                ]);
            }
        }
    }
}
