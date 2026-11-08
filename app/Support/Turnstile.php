<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Cloudflare Turnstile, which checks that a visitor is a person. It is on
 * only when both keys are set.
 */
final class Turnstile
{
    public const string SCRIPT_ORIGIN = 'https://challenges.cloudflare.com';

    private const string VERIFY_URL = self::SCRIPT_ORIGIN.'/turnstile/v0/siteverify';

    /**
     * Cloudflare rejects longer tokens, so they are not worth a request.
     */
    private const int MAX_TOKEN_LENGTH = 2048;

    public static function enabled(): bool
    {
        return filled(config('services.turnstile.site_key')) && filled(config('services.turnstile.secret_key'));
    }

    public static function siteKey(): ?string
    {
        return self::enabled() ? (string) config('services.turnstile.site_key') : null;
    }

    /**
     * Ask Cloudflare whether the token from the widget is valid. Each token
     * works once. A network failure counts as a failed check.
     */
    public function verify(string $token, ?string $ip): bool
    {
        if ($token === '' || strlen($token) > self::MAX_TOKEN_LENGTH) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->post(self::VERIFY_URL, [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ]);
        } catch (ConnectionException) {
            return false;
        }

        return $response->successful() && $response->json('success') === true;
    }
}
