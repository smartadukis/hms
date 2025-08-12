<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\User;
use Faker\Factory as Faker;
use Carbon\Carbon;

class JournalEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get random finance/admin users (or any users for now)
        $userIds = User::pluck('id')->toArray();

        for ($i = 1; $i <= 30; $i++) {
            $creatorId = $faker->randomElement($userIds);

            // Create the journal entry
            $entry = JournalEntry::create([
                'entry_date' => $faker->dateTimeBetween('-6 months', 'now'),
                'reference'  => strtoupper($faker->bothify('REF-####')),
                'description'=> $faker->sentence(),
                'approved'   => $faker->boolean(70), // 70% approved
                'created_by' => $creatorId,
            ]);

            // Random number of lines: min 2 (one debit, one credit), max 4
            $lineCount = $faker->numberBetween(2, 4);
            $totalDebit = 0;
            $totalCredit = 0;

            for ($l = 1; $l <= $lineCount; $l++) {
                // Last line should balance the total
                if ($l === $lineCount) {
                    $debit = max($totalCredit - $totalDebit, 0);
                    $credit = max($totalDebit - $totalCredit, 0);
                } else {
                    if ($faker->boolean) {
                        $debit = $faker->numberBetween(100, 500);
                        $credit = 0;
                    } else {
                        $debit = 0;
                        $credit = $faker->numberBetween(100, 500);
                    }
                }

                $totalDebit += $debit;
                $totalCredit += $credit;

                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id'       => $faker->numberBetween(1, 10), // adjust to your actual chart_of_accounts IDs
                    'debit'            => $debit,
                    'credit'           => $credit,
                    'narration'        => $faker->sentence(),
                ]);
            }
        }
    }
}
