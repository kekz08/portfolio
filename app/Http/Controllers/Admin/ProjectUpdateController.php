<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ProjectUpdateStoreRequest;
use App\Services\Web\StoreProjectUpdateService;
use Illuminate\Http\RedirectResponse;

class ProjectUpdateController extends Controller
{
    public function __construct(
        private readonly StoreProjectUpdateService $service,
    ) {
    }

    public function store(ProjectUpdateStoreRequest $request): RedirectResponse
    {
        $this->service->handle($request->validated(), $request->user());

        return redirect()->back()->with('success', 'Project progress updated successfully.');
    }
}
