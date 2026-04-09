<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeContentUpdateRequest;
use App\Models\HomeContent;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class HomeContentController extends Controller
{
    public function edit(): Response
    {
        $this->authorize('manage-projects');

        return Inertia::render('Admin/HomeContent/Edit', [
            'content' => HomeContent::resolvedContent(),
        ]);
    }

    public function update(HomeContentUpdateRequest $request): RedirectResponse
    {
        $this->authorize('manage-projects');

        HomeContent::storeContent($request->validated('content'));

        return redirect()
            ->route('admin.home-content.edit')
            ->with('success', 'Home page content updated successfully.');
    }

    public function reset(): RedirectResponse
    {
        $this->authorize('manage-projects');

        HomeContent::storeContent(HomeContent::defaultContent());

        return redirect()
            ->route('admin.home-content.edit')
            ->with('success', 'Home page content has been reset to defaults.');
    }
}
