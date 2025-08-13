<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\TransactionController;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Account;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->controller = new TransactionController();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function store_saves_transaction_and_receipt()
    {
        $account = Account::factory()->create();

        $file = UploadedFile::fake()->image('receipt.jpg');

        $request = Request::create('', 'POST', [
            'date' => now()->toDateString(),
            'type' => 'income',
            'account_id' => $account->id,
            'amount' => 150.75,
            'description' => 'Test income',
            'invoice_id' => 'INV123'
        ], [], ['receipt' => $file]);

        $this->controller->store($request);

        $this->assertDatabaseHas('transactions', [
            'type' => 'income',
            'account_id' => $account->id,
            'amount' => 150.75,
            'created_by' => $this->user->id
        ]);

        Storage::disk('public')->assertExists(
            Transaction::first()->receipt_path
        );
    }

    /** @test */
    public function update_replaces_receipt_and_updates_data()
    {
        $account = Account::factory()->create();
        $transaction = Transaction::factory()->create(['account_id' => $account->id]);

        $newFile = UploadedFile::fake()->image('new_receipt.png');

        $request = Request::create('', 'PUT', [
            'date' => now()->toDateString(),
            'type' => 'expense',
            'account_id' => $account->id,
            'amount' => 50.25,
            'description' => 'Updated description',
            'invoice_id' => 'INV999'
        ], [], ['receipt' => $newFile]);

        $this->controller->update($request, $transaction);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'type' => 'expense',
            'amount' => 50.25,
            'description' => 'Updated description'
        ]);

        Storage::disk('public')->assertExists(
            $transaction->fresh()->receipt_path
        );
    }

    /** @test */
    public function destroy_removes_transaction_and_receipt()
    {
        $account = Account::factory()->create();
        $transaction = Transaction::factory()->create([
            'account_id' => $account->id,
            'receipt_path' => 'transactions/old_receipt.jpg'
        ]);

        Storage::disk('public')->put($transaction->receipt_path, 'dummy content');

        $this->controller->destroy($transaction);

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
        Storage::disk('public')->assertMissing('transactions/old_receipt.jpg');
    }

    /** @test */
    public function report_calculates_totals_correctly()
    {
        $account = Account::factory()->create();

        Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'income',
            'amount' => 200
        ]);
        Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'expense',
            'amount' => 50
        ]);

        $request = Request::create('', 'GET', []);
        $view = $this->controller->report($request);
        $data = $view->getData();

        $this->assertEquals(200, $data['totalIncome']);
        $this->assertEquals(50, $data['totalExpense']);
        $this->assertCount(2, $data['transactions']);
    }
}
