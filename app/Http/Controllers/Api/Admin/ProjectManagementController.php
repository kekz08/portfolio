<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Project\UpdateProjectStatusRequest;
use App\Http\Resources\Api\ProjectResource;
use App\Services\Api\AdminProjectListingService;
use App\Services\Api\ProjectStatusUpdateService;
use Illuminate\Http\JsonResponse;

class ProjectManagementController extends Controller
{
    public function __construct(
        private readonly AdminProjectListingService $listing,
        private readonly ProjectStatusUpdateService $statusUpdater,
    ) {
    }

    public function index(): JsonResponse
    {
        $projects = $this->listing->handle();

        return response()->json([
            'success' => true,
            'message' => 'Projects fetched successfully.',
            'data' => ProjectResource::collection($projects)->response()->getData(true)['data'] ?? [],
        ]);
    }

    public function updateStatus(UpdateProjectStatusRequest $request): JsonResponse
    {
        $project = $this->statusUpdater->handle($request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Project status updated successfully.',
            'data' => (new ProjectResource($project))->response()->getData(true)['data'] ?? null,
        ]);
    }
}
