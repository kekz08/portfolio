<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ProjectStatusUpdateRequest;
use App\Services\Web\AdminProjectQueueViewService;
use App\Services\Web\UpdateProjectStatusService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectManagementController extends Controller
{
    public function __construct(
        private readonly AdminProjectQueueViewService $viewService,
        private readonly UpdateProjectStatusService $statusService,
    ) {
    }

    public function index(): Response
    {
        $this->authorize('manage-projects');

        return Inertia::render('Admin/Projects/Index', $this->viewService->handle());
    }

    public function update(ProjectStatusUpdateRequest $request): RedirectResponse
    {
        $this->statusService->handle($request->validated(), $request->user());

        return redirect()
            ->back()
            ->with('success', 'Project status updated successfully.');
    }
}
