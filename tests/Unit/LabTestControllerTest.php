<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\LabTest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LabTestControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test lab tests index page is accessible.
     */
    public function test_index_displays_lab_tests()
    {
        $this->actingAs(User::factory()->create());
        LabTest::factory()->count(3)->create();

        $response = $this->get(route('lab-tests.index'));
        $response->assertStatus(200);
        $response->assertViewIs('lab_tests.index');
    }

    /**
     * Test creating a new lab test.
     */
    public function test_store_creates_lab_test()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $patient = Patient::factory()->create();
        $doctor  = User::factory()->create(['role' => 'doctor']);

        $response = $this->post(route('lab-tests.store'), [
            'patient_id' => $patient->id,
            'doctor_id'  => $doctor->id,
            'test_type'  => 'Blood Test',
            'notes'      => 'Urgent case',
        ]);

        $response->assertRedirect(route('lab-tests.index'));
        $this->assertDatabaseHas('lab_tests', [
            'patient_id' => $patient->id,
            'doctor_id'  => $doctor->id,
            'test_type'  => 'Blood Test',
        ]);
    }

    /**
     * Test updating a lab test.
     */
    public function test_update_lab_test()
    {
        $this->actingAs(User::factory()->create());

        $labTest = LabTest::factory()->create(['status' => 'pending']);

        $response = $this->put(route('lab-tests.update', $labTest), [
            'status' => 'completed',
            'result' => 'Positive',
        ]);

        $response->assertRedirect(route('lab-tests.index'));
        $this->assertDatabaseHas('lab_tests', [
            'id'     => $labTest->id,
            'status' => 'completed',
            'result' => 'Positive',
        ]);
    }

    /**
     * Test deleting a lab test.
     */
    public function test_destroy_lab_test()
    {
        $this->actingAs(User::factory()->create());

        $labTest = LabTest::factory()->create();

        $response = $this->delete(route('lab-tests.destroy', $labTest));

        $response->assertRedirect();
        $this->assertDatabaseMissing('lab_tests', ['id' => $labTest->id]);
    }
}
