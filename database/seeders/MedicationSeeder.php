<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medication;
use App\Models\User;
use Faker\Factory as Faker;

class MedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create('en_US');
        $userIds = \App\Models\User::pluck('id')->toArray();

        $unitOfStrengthOptions = ['mg', 'g', 'mcg', 'IU', 'ml', 'unit', '%'];
        $categoryOptions = ['Tablet', 'Capsule', 'Syrup', 'Injection', 'Cream', 'Drops', 'Patch', 'Spray', 'Suppository', 'Inhaler', 'Others'];
        $dispensingUnitOptions = ['Tablet', 'Capsule', 'ml', 'sachet', 'vial', 'puff', 'drop', 'unit'];

        $usedBarcodes = [];

        for ($i = 0; $i < 100; $i++) {
            $unit = $faker->randomElement($unitOfStrengthOptions);

            // Generate strength according to unit
            $strength = match ($unit) {
                'mg' => $faker->randomFloat(2, 5, 1000),
                'g' => $faker->randomFloat(2, 0.1, 5),
                'mcg' => $faker->randomFloat(2, 10, 5000),
                'IU' => $faker->numberBetween(100, 100000),
                'ml' => $faker->randomFloat(2, 1, 200),
                'unit' => $faker->numberBetween(1, 1000),
                '%' => $faker->randomFloat(2, 0.1, 10),
                default => $faker->randomFloat(2, 1, 100),
            };

            // Avoid duplicate barcodes
            do {
                $barcode = strtoupper($faker->bothify('??#####??'));
            } while (in_array($barcode, $usedBarcodes));

            $usedBarcodes[] = $barcode;

            Medication::create([
                'name' => $faker->unique()->lexify('Med?????'),
                'generic_name' => $faker->word,
                'strength' => $strength,
                'unit_of_strength' => $unit,
                'category' => $faker->randomElement($categoryOptions),
                'dispensing_unit' => $faker->randomElement($dispensingUnitOptions),
                'pack_size' => $faker->randomElement([10, 15, 30, 60, 100]),
                'manufacturer' => $faker->company,
                'barcode_or_ndc' => $barcode,
                'description' => $faker->sentence(),
                'is_controlled' => $faker->boolean(10), // 10% chance
                'requires_refrigeration' => $faker->boolean(5), // 5% chance
                'storage_conditions' => $faker->randomElement(['Store in a cool, dry place', 'Refrigerate at 2-8°C', null]),
                'created_by' => $faker->randomElement($userIds),
            ]);
        }
    }
}
