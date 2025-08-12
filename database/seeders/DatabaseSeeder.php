<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\PrescriptionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{

    //$this->call(PatientSeeder::class);
    //$this->call(PrescriptionSeeder::class);
    //$this->call(UserSeeder::class);
    //$this->call(MedicationSeeder::class);
    //$this->call(AccountSeeder::class);
    //$this->call(JournalEntrySeeder::class);
    $this->call(TransactionSeeder::class);
    // Create a super admin user if it doesn't exist
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
