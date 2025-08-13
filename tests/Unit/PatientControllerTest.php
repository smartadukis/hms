<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a default authenticated user
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_displays_patients_index()
    {
        $this->actingAs($this->user)
             ->get(route('patients.index'))
             ->assertStatus(200)
             ->assertViewIs('patients.index');
    }

    /** @test */
    public function it_creates_a_new_patient()
    {
        $this->actingAs($this->user);

        $data = [
            'name'        => 'John Doe',
            'phone'       => '1234567890',
            'email'       => 'john@example.com',
            'gender'      => 'Male',
            'dob'         => '1990-01-01',
            'blood_group' => 'O+',
            'address'     => '123 Street',
        ];

        $response = $this->post(route('patients.store'), $data);

        $response->assertRedirect(route('patients.index'));
        $this->assertDatabaseHas('patients', ['name' => 'John Doe']);
    }

    /** @test */
    public function it_edits_a_patient()
    {
        $patient = Patient::factory()->create();

        $this->actingAs($this->user)
             ->get(route('patients.edit', $patient))
             ->assertStatus(200)
             ->assertViewIs('patients.edit');
    }

    /** @test */
    public function it_updates_a_patient()
    {
        $patient = Patient::factory()->create(['phone' => '1112223333']);

        $data = [
            'name'        => 'Jane Doe',
            'phone'       => '4445556666',
            'email'       => 'jane@example.com',
            'gender'      => 'Female',
            'dob'         => '1992-05-05',
            'blood_group' => 'A+',
            'address'     => '456 Avenue',
        ];

        $this->actingAs($this->user)
             ->put(route('patients.update', $patient), $data)
             ->assertRedirect(route('patients.index'));

        $this->assertDatabaseHas('patients', ['name' => 'Jane Doe']);
    }

    /** @test */
    public function it_deletes_a_patient()
    {
        $patient = Patient::factory()->create();

        $this->actingAs($this->user)
             ->delete(route('patients.destroy', $patient))
             ->assertRedirect();

        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
    }
}
