<?php

namespace Tests\Feature\Api;

use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectStatusUpdatedNotification;
use App\Services\IdObfuscator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_projects_index_requires_authentication(): void
    {
        $this->getJson(route('api.projects.index'))
            ->assertUnauthorized()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_authenticated_user_only_receives_their_projects_with_encrypted_identifiers(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $firstProject = Project::factory()->for($firstUser)->create();
        Project::factory()->for($secondUser)->create();

        Sanctum::actingAs($firstUser);

        $response = $this->getJson(route('api.projects.index'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Projects fetched successfully.')
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);

        $payload = $response->json('data');
        $this->assertIsArray($payload);
        $this->assertCount(1, $payload);

        $encryptedProjectId = $payload[0]['id'] ?? null;
        $this->assertIsString($encryptedProjectId);
        $this->assertNotSame((string) $firstProject->id, $encryptedProjectId);

        $decodedProjectId = app(IdObfuscator::class)->decode($encryptedProjectId);
        $this->assertSame($firstProject->id, $decodedProjectId);
    }

    public function test_user_cannot_view_project_that_does_not_belong_to_them(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $project = Project::factory()->for($owner)->create();

        Sanctum::actingAs($otherUser);

        $encryptedProjectId = app(IdObfuscator::class)->encode($project->id);

        $this->getJson(route('api.projects.show', $encryptedProjectId))
            ->assertForbidden()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'You are not allowed to access this project.');
    }

    public function test_show_endpoint_returns_standard_envelope_with_encrypted_project_id(): void
    {
        $owner = User::factory()->create();
        $project = Project::factory()->for($owner)->create();

        Sanctum::actingAs($owner);

        $encryptedProjectId = app(IdObfuscator::class)->encode($project->id);

        $response = $this->getJson(route('api.projects.show', $encryptedProjectId))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Project fetched successfully.')
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'status',
                ],
            ]);

        $decodedProjectId = app(IdObfuscator::class)->decode((string) $response->json('data.id'));
        $this->assertSame($project->id, $decodedProjectId);
    }

    public function test_client_can_post_message_and_receives_standard_envelope(): void
    {
        $owner = User::factory()->create();
        $project = Project::factory()
            ->for($owner)
            ->create([
                'status' => ProjectStatus::APPROVED,
            ]);

        Sanctum::actingAs($owner);

        $encryptedProjectId = app(IdObfuscator::class)->encode($project->id);

        $response = $this->postJson(route('api.projects.messages.store', $encryptedProjectId), [
            'message' => 'Need timeline confirmation.',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Message sent successfully.')
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'project_id',
                    'message',
                ],
            ]);

        $this->assertDatabaseHas('project_messages', [
            'project_id' => $project->id,
            'sender_id' => $owner->id,
            'message' => 'Need timeline confirmation.',
        ]);

        $decodedProjectId = app(IdObfuscator::class)->decode((string) $response->json('data.project_id'));
        $this->assertSame($project->id, $decodedProjectId);
    }

    public function test_admin_can_update_project_status_over_api_with_standard_envelope(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);
        $owner = User::factory()->create([
            'role' => UserRole::CLIENT,
        ]);
        $project = Project::factory()->for($owner)->create([
            'status' => ProjectStatus::PENDING_REVIEW,
        ]);

        Sanctum::actingAs($admin);

        $encryptedProjectId = app(IdObfuscator::class)->encode($project->id);

        $response = $this->patchJson(route('api.admin.projects.update-status', $encryptedProjectId), [
            'status' => ProjectStatus::APPROVED->value,
            'status_notes' => 'Approved from API.',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Project status updated successfully.')
            ->assertJsonPath('data.status', ProjectStatus::APPROVED->value);

        $decodedProjectId = app(IdObfuscator::class)->decode((string) $response->json('data.id'));
        $this->assertSame($project->id, $decodedProjectId);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'status' => ProjectStatus::APPROVED->value,
            'reviewed_by' => $admin->id,
        ]);

        Notification::assertSentTo($owner, ProjectStatusUpdatedNotification::class);
    }

    public function test_admin_status_update_validation_uses_standard_error_envelope(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);
        $owner = User::factory()->create([
            'role' => UserRole::CLIENT,
        ]);
        $project = Project::factory()->for($owner)->create([
            'status' => ProjectStatus::PENDING_REVIEW,
        ]);

        Sanctum::actingAs($admin);

        $encryptedProjectId = app(IdObfuscator::class)->encode($project->id);

        $this->patchJson(route('api.admin.projects.update-status', $encryptedProjectId), [
            'status_notes' => 'Missing required status field.',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed.')
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => ['status'],
            ]);
    }

    public function test_invalid_encrypted_id_returns_standard_error_envelope(): void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);

        $this->getJson(route('api.projects.show', 'invalid-encrypted-id'))
            ->assertStatus(400)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Invalid identifier supplied.');
    }
}
