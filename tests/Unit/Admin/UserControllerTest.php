<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_user_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
    }

    /** @test */
    public function admin_can_view_user_details()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $this->actingAs($admin);

        $response = $this->get(route('admin.users.show', $user));
        $response->assertStatus(200);
        $response->assertViewIs('admin.users.show');
    }

    /** @test */
    public function admin_can_edit_a_user_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'staff']);
        $this->actingAs($admin);

        $response = $this->put(route('admin.users.update', $user), [
            'role' => 'doctor',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertEquals('doctor', $user->fresh()->role);
    }

    /** @test */
    public function admin_cannot_demote_self_from_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->put(route('admin.users.update', $admin), [
            'role' => 'staff',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('admin', $admin->fresh()->role);
    }

    /** @test */
    public function admin_can_delete_other_users_but_not_self()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $this->actingAs($admin);

        // Delete another user
        $this->delete(route('admin.users.destroy', $user))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // Attempt self-deletion
        $this->delete(route('admin.users.destroy', $admin))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
