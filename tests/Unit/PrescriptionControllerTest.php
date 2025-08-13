<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Models\User;
use App\Models\Patient;
use App\Models\Medication;
use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Http\Controllers\PrescriptionController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PrescriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;
    protected $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new PrescriptionController();

        // Create a test doctor user
        $this->doctor = User::factory()->create(['role' => 'doctor']);
        $this->actingAs($this->doctor);
    }

    /** @test */
    public function index_returns_filtered_prescriptions_for_doctor()
    {
        Prescription::factory()->count(2)->create(['doctor_id' => $this->doctor->id]);
        Prescription::factory()->create(); // not assigned to doctor

        $request = new Request();
        $response = $this->controller->index($request);

        $this->assertArrayHasKey('prescriptions', $response->getData());
        $this->assertEquals(2, $response->getData()['prescriptions']->count());
    }

    /** @test */
    public function store_creates_prescription_and_items()
    {
        $patient = Patient::factory()->create();
        $medication = Medication::factory()->create();

        $request = Request::create('', 'POST', [
            'patient_id' => $patient->id,
            'notes' => 'Test notes',
            'medications' => [
                [
                    'medication_id' => $medication->id,
                    'dosage_quantity' => 2,
                    'dosage_unit' => 'tablet',
                    'frequency' => 'daily',
                    'duration' => '5 days',
                    'instructions' => 'After meals',
                ]
            ]
        ]);

        $response = $this->controller->store($request);

        $this->assertDatabaseHas('prescriptions', [
            'patient_id' => $patient->id,
            'doctor_id' => $this->doctor->id,
        ]);

        $this->assertDatabaseHas('prescription_items', [
            'medication_id' => $medication->id,
            'dosage_quantity' => 2,
        ]);
    }

    /** @test */
    public function update_replaces_prescription_items()
    {
        $prescription = Prescription::factory()->create(['doctor_id' => $this->doctor->id]);
        $medication = Medication::factory()->create();
        $patient = Patient::factory()->create();

        $request = Request::create('', 'PUT', [
            'patient_id' => $patient->id,
            'notes' => 'Updated notes',
            'medications' => [
                [
                    'medication_id' => $medication->id,
                    'dosage_quantity' => 1,
                    'dosage_unit' => 'capsule',
                    'frequency' => 'twice daily',
                    'duration' => '7 days',
                    'instructions' => null,
                ]
            ]
        ]);

        $this->controller->update($request, $prescription);

        $this->assertDatabaseHas('prescriptions', ['notes' => 'Updated notes']);
        $this->assertDatabaseHas('prescription_items', [
            'medication_id' => $medication->id,
            'dosage_quantity' => 1,
        ]);
    }

    /** @test */
    public function destroy_deletes_prescription()
    {
        $prescription = Prescription::factory()->create(['doctor_id' => $this->doctor->id]);

        $this->controller->destroy($prescription);

        $this->assertDatabaseMissing('prescriptions', ['id' => $prescription->id]);
    }
}
