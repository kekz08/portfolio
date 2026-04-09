<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Project\ShowProjectRequest;
use App\Http\Resources\Api\ProjectResource;
use App\Services\Api\ProjectDetailService;
use App\Services\Api\ProjectListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectListingService $listing,
        private readonly ProjectDetailService $detail,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $projects = $this->listing->handle($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Projects fetched successfully.',
            'data' => ProjectResource::collection($projects)->response()->getData(true)['data'] ?? [],
        ]);
    }

    public function show(ShowProjectRequest $request): JsonResponse
    {
        $project = $this->detail->handle($request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Project fetched successfully.',
            'data' => (new ProjectResource($project))->response()->getData(true)['data'] ?? null,
        ]);
    }
}
