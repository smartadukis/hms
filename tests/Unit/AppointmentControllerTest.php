<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_appointments()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        Appointment::factory()->count(3)->create();

        $response = $this->get(route('appointments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('appointments');
    }

    public function test_store_creates_appointment()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $patient = Patient::factory()->create();
        $doctor = User::factory()->create(['role' => 'doctor']);

        $response = $this->post(route('appointments.store'), [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'scheduled_at' => now()->addDay(),
            'notes' => 'Test appointment notes'
        ]);

        $response->assertRedirect(route('appointments.index'));
        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'notes' => 'Test appointment notes'
        ]);
    }

    public function test_update_edits_appointment()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $appointment = Appointment::factory()->create();

        $response = $this->put(route('appointments.update', $appointment), [
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'scheduled_at' => now()->addDays(2),
            'notes' => 'Updated notes',
            'status' => 'completed'
        ]);

        $response->assertRedirect(route('appointments.index'));
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'notes' => 'Updated notes',
            'status' => 'completed'
        ]);
    }

    public function test_destroy_deletes_appointment()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $appointment = Appointment::factory()->create();

        $response = $this->delete(route('appointments.destroy', $appointment));

        $response->assertRedirect(route('appointments.index'));
        $this->assertDatabaseMissing('appointments', [
            'id' => $appointment->id
        ]);
    }
}
