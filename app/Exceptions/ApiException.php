<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiException extends Exception
{
    protected int $status = 400;

    public function render(Request $request): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
            ], $this->status);
        }

        return $this->webRedirectResponse();
    }

    protected function webRedirectResponse(): RedirectResponse
    {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', $this->getMessage());
    }
}
