<?php

namespace App\Http\Controllers;

use App\Services\Web\DashboardViewService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardViewService $service,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        return Inertia::render('Dashboard', $this->service->handle($request->user()));
    }
}
