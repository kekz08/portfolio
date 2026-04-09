<?php

namespace Tests\Feature\Api;

use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectStatusUpdatedNotification;
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
            ->assertUnauthorized();
    }

    public function test_authenticated_user_only_receives_their_projects(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $firstProject = Project::factory()->for($firstUser)->create();
        Project::factory()->for($secondUser)->create();

        Sanctum::actingAs($firstUser);

        $this->getJson(route('api.projects.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $firstProject->id)
            ->assertJsonPath('data.0.owner.id', $firstUser->id);
    }

    public function test_user_cannot_view_project_that_does_not_belong_to_them(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $project = Project::factory()->for($owner)->create();

        Sanctum::actingAs($otherUser);

        $this->getJson(route('api.projects.show', $project))
            ->assertForbidden();
    }

    public function test_admin_can_update_project_status_over_api(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);
        $owner = User::factory()->create([
            'role' => UserRole::USER,
        ]);
        $project = Project::factory()->for($owner)->create([
            'status' => ProjectStatus::PENDING_REVIEW,
        ]);

        Sanctum::actingAs($admin);

        $this->patchJson(route('api.admin.projects.update-status', $project), [
            'status' => ProjectStatus::APPROVED->value,
            'status_notes' => 'Approved from API.',
        ])
            ->assertOk()
            ->assertJsonPath('data.status', ProjectStatus::APPROVED->value);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'status' => ProjectStatus::APPROVED->value,
            'reviewed_by' => $admin->id,
        ]);

        Notification::assertSentTo($owner, ProjectStatusUpdatedNotification::class);
    }
}
