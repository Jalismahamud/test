<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (InsufficientStockException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success'      => false,
                    'error'        => 'INSUFFICIENT_STOCK',
                    'message'      => $e->getMessage(),
                    'product_name' => $e->productName,
                ], 422);
            }
        });

        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => 'VALIDATION_ERROR',
                    'message' => 'Validation failed.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => 'UNAUTHENTICATED',
                    'message' => 'Unauthenticated.',
                ], 401);
            }
            return redirect()->route('login');
        });

        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => 'NOT_FOUND',
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

        $this->reportable(function (Throwable $e) {
            Log::error($e->getMessage(), [
                'user_id' => auth()->id(),
                'url'     => request()->url(),
                'trace'   => $e->getTraceAsString(),
            ]);
        });
    }
}
