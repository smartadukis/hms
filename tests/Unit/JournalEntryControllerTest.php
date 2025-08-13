<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JournalEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_journal_entries()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        JournalEntry::factory()->count(3)->create(['created_by' => $user->id]);

        $response = $this->get(route('journal.index'));
        $response->assertStatus(200);
        $response->assertViewHas('entries');
    }

    /** @test */
    public function it_can_create_a_balanced_journal_entry()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        $data = [
            'entry_date' => now()->toDateString(),
            'reference' => 'Test Ref',
            'description' => 'Test Desc',
            'lines' => [
                ['account_id' => $account1->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $account2->id, 'debit' => 0, 'credit' => 100],
            ],
        ];

        $response = $this->post(route('journal.store'), $data);

        $response->assertRedirect(route('journal.index'));
        $this->assertDatabaseHas('journal_entries', ['reference' => 'Test Ref']);
        $this->assertDatabaseCount('journal_lines', 2);
    }

    /** @test */
    public function it_rejects_unbalanced_entries()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        $data = [
            'lines' => [
                ['account_id' => $account1->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $account2->id, 'debit' => 0, 'credit' => 50],
            ],
        ];

        $response = $this->post(route('journal.store'), $data);
        $response->assertSessionHasErrors(['balance']);
    }

    /** @test */
    public function it_can_update_a_journal_entry()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        $entry = JournalEntry::factory()->create(['created_by' => $user->id]);
        $entry->lines()->createMany([
            ['account_id' => $account1->id, 'debit' => 100, 'credit' => 0],
            ['account_id' => $account2->id, 'debit' => 0, 'credit' => 100],
        ]);

        $updateData = [
            'lines' => [
                ['account_id' => $account1->id, 'debit' => 200, 'credit' => 0],
                ['account_id' => $account2->id, 'debit' => 0, 'credit' => 200],
            ],
        ];

        $response = $this->put(route('journal.update', $entry), $updateData);
        $response->assertRedirect(route('journal.index'));

        $this->assertDatabaseHas('journal_lines', ['debit' => 200.00]);
    }

    /** @test */
    public function it_can_delete_a_journal_entry()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $entry = JournalEntry::factory()->create(['created_by' => $user->id]);

        $response = $this->delete(route('journal.destroy', $entry));
        $response->assertRedirect();
        $this->assertDatabaseMissing('journal_entries', ['id' => $entry->id]);
    }
}
