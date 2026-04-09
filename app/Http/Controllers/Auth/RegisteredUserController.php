<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\RegistrationService;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly RegistrationService $registrationService,
    ) {
    }

    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Register');
    }

    /**
     * Handle an incoming registration request.
     *
     */
    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $user = $this->registrationService->registerWithProject(
            $request->validated(),
        );

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
