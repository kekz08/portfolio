<?php

namespace App\Http\Middleware;

use App\Models\HomeContent;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $resolvedContent = HomeContent::resolvedContent();
        $footer = is_array($resolvedContent['footer'] ?? null) ? $resolvedContent['footer'] : [];

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'phone' => $user->phone,
                    'company' => $user->company,
                    'role' => $user->role?->value,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
            'site' => [
                'footer' => [
                    'brand' => isset($footer['brand']) && is_string($footer['brand']) ? $footer['brand'] : 'Portify',
                    'tagline' => isset($footer['tagline']) && is_string($footer['tagline']) ? $footer['tagline'] : 'Designed with Vue + Tailwind',
                ],
            ],
        ];
    }
}
