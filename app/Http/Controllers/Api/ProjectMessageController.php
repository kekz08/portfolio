<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Project\StoreProjectMessageRequest;
use App\Http\Resources\Api\ProjectMessageResource;
use App\Services\Api\ProjectMessageService;
use Illuminate\Http\JsonResponse;

class ProjectMessageController extends Controller
{
    public function __construct(
        private readonly ProjectMessageService $service,
    ) {
    }

    public function store(StoreProjectMessageRequest $request): JsonResponse
    {
        $message = $this->service->handle($request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => (new ProjectMessageResource($message))->response()->getData(true)['data'] ?? null,
        ]);
    }
}
