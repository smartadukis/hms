<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\User;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = Account::pluck('id')->toArray();
        $invoices = Invoice::pluck('id')->toArray();
        $users    = User::pluck('id')->toArray();

        for ($i = 1; $i <= 50; $i++) {
            DB::table('transactions')->insert([
                'date'         => now()->subDays(rand(0, 180))->toDateString(),
                'type'         => rand(0, 1) ? 'income' : 'expense',
                'account_id'   => $accounts[array_rand($accounts)],
                'amount'       => rand(50, 5000) + (rand(0, 99) / 100),
                'description'  => 'Seeded transaction ' . $i,
                'invoice_id'   => rand(0, 3) ? ($invoices ? $invoices[array_rand($invoices)] : null) : null,
                'receipt_path' => null, // no file uploads for seed
                'created_by'   => $users[array_rand($users)],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}
