<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        // Límite general por IP
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return $this->buildResponse($key, $maxAttempts);
        }

        // Límite específico para búsquedas
        if ($request->is('*/buscar*') || $request->is('*/search*')) {
            $searchKey = 'search_' . $key;
            if (RateLimiter::tooManyAttempts($searchKey, 30)) { // 30 búsquedas por minuto
                return response()->json([
                    'error' => 'Demasiadas búsquedas. Intenta de nuevo en unos minutos.',
                    'retry_after' => RateLimiter::availableIn($searchKey)
                ], 429);
            }
            RateLimiter::hit($searchKey, 60);
        }

        // Límite para importaciones
        if ($request->is('*/import*')) {
            $importKey = 'import_' . $key;
            if (RateLimiter::tooManyAttempts($importKey, 5)) { // 5 importaciones por hora
                return response()->json([
                    'error' => 'Demasiadas importaciones. Intenta de nuevo en una hora.',
                    'retry_after' => RateLimiter::availableIn($importKey)
                ], 429);
            }
            RateLimiter::hit($importKey, 3600);
        }

        // Límite para estadísticas
        if ($request->is('*/estadisticas*') || $request->is('*/analytics*')) {
            $statsKey = 'stats_' . $key;
            if (RateLimiter::tooManyAttempts($statsKey, 120)) { // 120 consultas por minuto
                return response()->json([
                    'error' => 'Demasiadas consultas de estadísticas. Intenta de nuevo en unos minutos.',
                    'retry_after' => RateLimiter::availableIn($statsKey)
                ], 429);
            }
            RateLimiter::hit($statsKey, 60);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Resolve request signature.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        if ($user = $request->user()) {
            return sha1($user->getAuthIdentifier());
        }

        if ($route = $request->route()) {
            return sha1($route->getDomain() . '|' . $request->ip());
        }

        return sha1($request->ip());
    }

    /**
     * Create a 'too many attempts' response.
     */
    protected function buildResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = $this->getTimeUntilNextAttempt($key);

        if ($retryAfter) {
            return response()->json([
                'error' => 'Demasiadas solicitudes. Intenta de nuevo en ' . $retryAfter . ' segundos.',
                'retry_after' => $retryAfter
            ], 429);
        }

        return response()->json([
            'error' => 'Demasiadas solicitudes. Intenta de nuevo más tarde.',
        ], 429);
    }

    /**
     * Get the number of seconds until the next retry.
     */
    protected function getTimeUntilNextAttempt(string $key): ?int
    {
        return RateLimiter::availableIn($key);
    }

    /**
     * Calculate the number of remaining attempts.
     */
    protected function calculateRemainingAttempts(string $key, int $maxAttempts): int
    {
        return RateLimiter::remaining($key, $maxAttempts);
    }

    /**
     * Add the limit header information to the given response.
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts): Response
    {
        return $response->header('X-RateLimit-Limit', $maxAttempts)
                       ->header('X-RateLimit-Remaining', $remainingAttempts);
    }
}
