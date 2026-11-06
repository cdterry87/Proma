<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    /**
     * Livewire injects its progress bar styles on every page without a nonce,
     * even with the bar disabled. A Livewire upgrade can change this hash, and
     * the only effect of a stale one is a console error.
     */
    private const string LIVEWIRE_PROGRESS_BAR_STYLE_HASH = 'sha256-pGJX2ep4lEKEyrpEvHlS59CGIQoVGpThTaZjnS53LQE=';

    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Vite::useCspNonce();

        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        if ($this->shouldSendContentSecurityPolicy($response)) {
            $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy($nonce));
        }

        return $response;
    }

    /**
     * The Vite dev server and the framework's debug error page both need
     * resources that a strict policy would block.
     */
    private function shouldSendContentSecurityPolicy(Response $response): bool
    {
        if (Vite::isRunningHot()) {
            return false;
        }

        return ! ($response->isServerError() && config('app.debug'));
    }

    private function contentSecurityPolicy(string $nonce): string
    {
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}'",
            "style-src 'self' 'nonce-{$nonce}' '".self::LIVEWIRE_PROGRESS_BAR_STYLE_HASH."'",
            "img-src 'self' data:",
            "font-src 'self'",
            "connect-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]);
    }
}
