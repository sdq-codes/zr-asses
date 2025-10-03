<?php

namespace App\Exceptions;

use App\Support\Enums\responses\InternalResponseCodes;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Exceptions that should not be reported to logs/bug trackers.
     */
    protected $dontReport = [
        AuthorizationException::class,
        AuthenticationException::class,
        ApplicationAccessDeniedException::class,
        CustomValidationFailed::class,
        DeletingFailedException::class,

        HttpException::class,


        ModelNotFoundException::class,
        MethodNotAllowedHttpException::class,

        UnauthorizedUserException::class,
        ValidationException::class,


    ];

    /**
     * Exception to HTTP response mapping.
     */
    protected $exceptionMap = [
        ModelNotFoundException::class => [
            'status' => 404,
            'code' => InternalResponseCodes::NOT_FOUND,
            'title' => 'Resource not found',
        ],
        NotFoundHttpException::class => [
            'status' => 404,
            'code' => InternalResponseCodes::NOT_FOUND,
            'title' => 'Endpoint not found',
        ],
        MethodNotAllowedHttpException::class => [
            'status' => 405,
            'code' => InternalResponseCodes::HTTP_ERROR,
            'title' => 'This method is not allowed for this endpoint.',
        ],
        AuthenticationException::class => [
            'status' => 401,
            'code' => InternalResponseCodes::HTTP_ERROR,
            'title' => 'Authentication required.',
        ],
        AuthorizationException::class => [
            'status' => 403,
            'code' => InternalResponseCodes::HTTP_ERROR,
            'title' => 'You do not have permission to perform this action.',
        ],
        ApplicationAccessDeniedException::class => [
            'status' => 403,
            'code' => InternalResponseCodes::HTTP_ERROR,
            'title' => 'You do not have permission to perform this action.',
        ],
        UnauthorizedUserException::class => [
            'status' => 403,
            'code' => InternalResponseCodes::HTTP_ERROR,
            'title' => 'Unauthorized user.',
        ],
        DeletingFailedException::class => [
            'status' => 409,
            'code' => InternalResponseCodes::EXCEPTION,
            'title' => 'Unable to delete resource.',
        ],
        CustomValidationFailed::class => [
            'status' => 422,
            'code' => InternalResponseCodes::VALIDATION_FAILED,
            'title' => 'Custom validation failed.',
        ],
        ResourceNotFoundException::class => [
            'status' => 404,
            'code' => InternalResponseCodes::NOT_FOUND,
            'title' => 'Requested resource could not be found.',
        ],
    ];

    /**
     * Report or log an exception.
     * @throws Throwable
     */
    public function report(Throwable $e): void
    {
        if (app()->bound('sentry') && $this->shouldReport($e)) {
            $this->enrichSentryContext($e);
            app('sentry')->captureException($e);
        }

        return;
    }

    /**
     * Render an exception into an HTTP response.
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse
    {
        // Handle HttpException with custom status codes
        if ($e instanceof HttpException) {
            $status = $e->getStatusCode();
            $response = $this->buildErrorResponse(
                $status,
                InternalResponseCodes::HTTP_ERROR,
                $this->sanitizeMessage($e->getMessage()) ?: 'HTTP error occurred.',
                $request
            );

            return response()->json($response, $status);
        }

        // Handle HttpException with custom status codes
        if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
            $mapping = $this->exceptionMap[NotFoundHttpException::class] ?? [
                'status' => 404,
                'code'   => InternalResponseCodes::NOT_FOUND,
                'title'  => 'Not Found',
            ];

            $response = $this->buildErrorResponse(
                $mapping['status'],
                $mapping['code'],
                $mapping['title'],
                $request
            );

            return response()->json($response, $mapping['status']);
        }

        // Handle ValidationException with field errors
        if ($e instanceof ValidationException) {
            $response = $this->buildErrorResponse(
                422,
                InternalResponseCodes::VALIDATION_FAILED,
                'Validation failed.',
                $request,
                ['source' => $e->errors()]
            );

            return response()->json($response, 422);
        }

        // Map known exceptions
        $exceptionClass = get_class($e);
        if (isset($this->exceptionMap[$exceptionClass])) {
            $mapping = $this->exceptionMap[$exceptionClass];
            $response = $this->buildErrorResponse(
                $mapping['status'],
                $mapping['code'],
                $mapping['title'],
                $request
            );

            return response()->json($response, $mapping['status']);
        }

        // Default error handling
        $status = 500;
        $title = app()->environment('production')
            ? 'An unexpected error occurred.'
            : $this->sanitizeMessage($e->getMessage());

        $response = $this->buildErrorResponse(
            $status,
            InternalResponseCodes::EXCEPTION,
            $title,
            $request
        );

        // Log the error response for debugging
        if ($status === 500) {
            logger()->error('Unhandled exception returned to client', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace_id' => $response['errors'][0]['meta']['trace_id'] ?? null,
            ]);
        }

        return response()->json($response, $status);
    }

    /**
     * Build a standardized error response.
     */
    protected function buildErrorResponse(
        int $status,
        string $code,
        string $title,
        Request $request,
        array $extra = []
    ): array {
        $error = array_merge([
            'id' => (string) Str::uuid(),
            'status' => $status,
            'code' => $code,
            'title' => $title,
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'trace_id' => $request->header('X-Trace-ID') ?? $request->header('X-Requests-ID'),
                'path' => $request->path(),
            ],
        ], $extra);

        return ['errors' => [$error]];
    }

    /**
     * Sanitize exception message to prevent information disclosure.
     */
    protected function sanitizeMessage(string $message): string
    {
        // Remove potential sensitive patterns
        $patterns = [
            '/\/home\/.*?\//',  // File paths
            '/password[\'"]?\s*[:=]\s*[\'"]?[^\s\'"]+/i',  // Passwords
            '/api[_-]?key[\'"]?\s*[:=]\s*[\'"]?[^\s\'"]+/i',  // API keys
            '/token[\'"]?\s*[:=]\s*[\'"]?[^\s\'"]+/i',  // Tokens
        ];

        $sanitized = preg_replace($patterns, '[REDACTED]', $message);

        // Truncate if too long
        return Str::limit($sanitized, 200);
    }

    /**
     * Enrich Sentry context with request data.
     */
    protected function enrichSentryContext(Throwable $e): void
    {
        if (!app()->bound('sentry')) {
            return;
        }

        $sentry = app('sentry');

        // Add user context if authenticated
        if (auth()->check()) {
            $sentry->configureScope(function ($scope) {
                $scope->setUser([
                    'id' => auth()->id(),
                    'email' => auth()->user()->email ?? null,
                ]);
            });
        }

        // Add request context
        if (request()) {
            $sentry->configureScope(function ($scope) {
                $scope->setContext('request', [
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'ip' => request()->ip(),
                ]);
            });
        }
    }
}
