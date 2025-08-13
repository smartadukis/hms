<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Medication;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MedicationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an authenticated user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_displays_medications_list()
    {
        Medication::factory()->count(3)->create();

        $response = $this->get(route('medications.index'));

        $response->assertStatus(200);
        $response->assertViewHas('medications');
    }

    /** @test */
    public function it_can_store_a_new_medication()
    {
        $data = Medication::factory()->make()->toArray();

        $response = $this->post(route('medications.store'), $data);

        $response->assertRedirect(route('medications.index'));
        $this->assertDatabaseHas('medications', ['name' => $data['name']]);
    }

    /** @test */
    public function it_can_update_a_medication()
    {
        $medication = Medication::factory()->create();

        $data = ['name' => 'Updated Name'] + $medication->toArray();

        $response = $this->put(route('medications.update', $medication), $data);

        $response->assertRedirect(route('medications.index'));
        $this->assertDatabaseHas('medications', ['name' => 'Updated Name']);
    }

    /** @test */
    public function it_can_delete_a_medication()
    {
        $medication = Medication::factory()->create();

        $response = $this->delete(route('medications.destroy', $medication));

        $response->assertRedirect();
        $this->assertDatabaseMissing('medications', ['id' => $medication->id]);
    }

    /** @test */
    public function it_can_restock_a_medication()
    {
        $medication = Medication::factory()->create(['quantity' => 5]);

        $response = $this->post(route('medications.restock', $medication), ['quantity' => 3]);

        $response->assertRedirect(route('medications.index'));
        $this->assertDatabaseHas('medications', ['quantity' => 8]);
    }
}
