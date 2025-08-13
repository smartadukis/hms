<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a test user and authenticate
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_list_invoices()
    {
        Invoice::factory()->count(3)->create();

        $response = $this->get(route('invoices.index'));
        $response->assertStatus(200);
        $response->assertViewIs('invoices.index');
    }

    public function test_can_store_invoice_with_items()
    {
        $patient = Patient::factory()->create();

        $payload = [
            'patient_id' => $patient->id,
            'items' => [
                ['description' => 'Consultation', 'amount' => 50],
                ['description' => 'Lab Test', 'amount' => 100]
            ]
        ];

        $response = $this->post(route('invoices.store'), $payload);
        $response->assertRedirect(route('invoices.index'));

        $this->assertDatabaseHas('invoices', [
            'patient_id' => $patient->id,
            'total_amount' => 150
        ]);
        $this->assertDatabaseCount('invoice_items', 2);
    }

    public function test_can_update_invoice()
    {
        $patient = Patient::factory()->create();
        $invoice = Invoice::factory()->create(['patient_id' => $patient->id]);
        InvoiceItem::factory()->count(2)->create(['invoice_id' => $invoice->id]);

        $payload = [
            'patient_id' => $patient->id,
            'status' => 'paid',
            'payment_method' => 'cash',
            'items' => [
                ['description' => 'Updated Service', 'amount' => 200]
            ]
        ];

        $response = $this->put(route('invoices.update', $invoice), $payload);
        $response->assertRedirect(route('invoices.index'));

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
            'total_amount' => 200
        ]);
        $this->assertDatabaseCount('invoice_items', 1);
    }

    public function test_can_delete_invoice()
    {
        $invoice = Invoice::factory()->create();

        $response = $this->delete(route('invoices.destroy', $invoice));
        $response->assertRedirect();
        $this->assertModelMissing($invoice);
    }
}
