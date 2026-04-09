<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ProjectMessageStoreRequest;
use App\Services\Web\StoreProjectMessageService;
use Illuminate\Http\RedirectResponse;

class ProjectMessageController extends Controller
{
    public function __construct(
        private readonly StoreProjectMessageService $service,
    ) {
    }

    public function store(ProjectMessageStoreRequest $request): RedirectResponse
    {
        $this->service->handle($request->validated(), $request->user());

        return redirect()->back()->with('success', 'Message sent.');
    }
}
