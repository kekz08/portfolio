<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Project\StoreProjectUpdateRequest;
use App\Http\Resources\Api\ProjectUpdateResource;
use App\Services\Api\ProjectUpdateService;
use Illuminate\Http\JsonResponse;

class ProjectUpdateController extends Controller
{
    public function __construct(
        private readonly ProjectUpdateService $service,
    ) {
    }

    public function store(StoreProjectUpdateRequest $request): JsonResponse
    {
        $update = $this->service->handle($request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Project update created successfully.',
            'data' => (new ProjectUpdateResource($update))->response()->getData(true)['data'] ?? null,
        ]);
    }
}
