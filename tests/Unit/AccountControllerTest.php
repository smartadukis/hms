<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_accounts_with_filters()
    {
        Account::factory()->create(['name' => 'Cash Account', 'type' => 'Asset', 'is_active' => true]);
        Account::factory()->create(['name' => 'Loan Account', 'type' => 'Liability', 'is_active' => false]);

        $response = $this->get('/accounts?search=Cash&type=Asset&status=active');

        $response->assertStatus(200);
        $response->assertViewIs('accounting.accounts.index');
        $response->assertSee('Cash Account');
    }

    /** @test */
    public function it_creates_a_new_account()
    {
        $data = [
            'name' => 'Bank Account',
            'code' => 'BA001',
            'type' => 'Asset',
            'is_active' => 1,
            'description' => 'Main bank account'
        ];

        $response = $this->post('/accounts', $data);

        $response->assertRedirect(route('accounts.index'));
        $this->assertDatabaseHas('accounts', ['name' => 'Bank Account', 'code' => 'BA001']);
    }

    /** @test */
    public function it_updates_an_existing_account()
    {
        $account = Account::factory()->create([
            'name' => 'Old Name',
            'code' => 'OLD001',
            'type' => 'Asset',
            'is_active' => true
        ]);

        $data = [
            'name' => 'Updated Name',
            'code' => 'NEW001',
            'type' => 'Liability',
            'is_active' => 0,
            'description' => 'Updated description'
        ];

        $response = $this->put("/accounts/{$account->id}", $data);

        $response->assertRedirect(route('accounts.index'));
        $this->assertDatabaseHas('accounts', ['name' => 'Updated Name', 'code' => 'NEW001']);
    }

    /** @test */
    public function it_deletes_an_account()
    {
        $account = Account::factory()->create();

        $response = $this->delete("/accounts/{$account->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    }
}
