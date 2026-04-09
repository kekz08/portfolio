<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectStatusUpdatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProjectDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_only_sees_projects_belonging_to_their_account(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $firstProject = Project::factory()->for($firstUser)->create([
            'title' => 'First User Project',
            'status' => ProjectStatus::PENDING_REVIEW,
        ]);

        Project::factory()->for($secondUser)->create([
            'title' => 'Second User Project',
            'status' => ProjectStatus::COMPLETED,
        ]);

        $this->actingAs($firstUser)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->has('projects', 1)
                ->where('projects.0.id', $firstProject->id)
                ->where('projects.0.title', 'First User Project')
            );
    }

    public function test_admin_can_update_project_status_and_notify_owner(): void
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

        $this->actingAs($admin)
            ->patch(route('admin.projects.update-status', $project), [
                'status' => ProjectStatus::APPROVED->value,
                'status_notes' => 'Approved by admin.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'status' => ProjectStatus::APPROVED->value,
            'reviewed_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'project_id' => $project->id,
            'action' => 'status_updated',
            'old_status' => ProjectStatus::PENDING_REVIEW->value,
            'new_status' => ProjectStatus::APPROVED->value,
        ]);

        Notification::assertSentTo($owner, ProjectStatusUpdatedNotification::class);
    }
}

