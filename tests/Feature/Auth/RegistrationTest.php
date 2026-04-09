<?php

namespace Tests\Feature\Auth;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+1-555-123-4567',
            'company' => 'Softivus Labs',
            'project_title' => 'New Client Portal',
            'project_description' => 'Need a Laravel + Vue platform with status tracking.',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'company' => 'Softivus Labs',
        ]);
        $this->assertDatabaseHas('projects', [
            'title' => 'New Client Portal',
            'status' => ProjectStatus::PENDING_REVIEW->value,
        ]);
        $this->assertSame(1, Project::query()->count());
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
