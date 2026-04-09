<?php

namespace Tests\Feature;

use App\Models\HomeContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomeContentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_receives_default_content_when_unconfigured(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->where('content.sidebar.contact_email', 'chat@portify.com')
                ->where('content.sections.0.id', 'top')
            );
    }

    public function test_admin_can_update_home_content_payload(): void
    {
        $admin = User::factory()->admin()->create();

        $payload = HomeContent::defaultContent();
        $payload['hero']['tagline'] = 'Hi, I am editable from admin';
        $payload['sidebar']['contact_email'] = 'hello@example.com';

        $this->actingAs($admin)
            ->patch(route('admin.home-content.update'), [
                'content' => $payload,
            ])
            ->assertRedirect(route('admin.home-content.edit'));

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->where('content.hero.tagline', 'Hi, I am editable from admin')
                ->where('content.sidebar.contact_email', 'hello@example.com')
            );
    }

    public function test_non_admin_cannot_open_home_content_admin_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.home-content.edit'))
            ->assertForbidden();
    }
}
