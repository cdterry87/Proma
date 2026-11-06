<?php

declare(strict_types=1);

it('sends security headers', function () {
    $this->get('/login')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});

it('sends a nonce based content security policy without unsafe sources [D-35]', function () {
    $policy = $this->get('/login')->headers->get('Content-Security-Policy');

    expect($policy)
        ->toContain("script-src 'self' 'nonce-")
        ->toContain("frame-ancestors 'none'")
        ->not->toContain('unsafe-inline')
        ->not->toContain('unsafe-eval');
});

it('uses the policy nonce on the inline theme script', function () {
    $response = $this->get('/login');

    preg_match("/'nonce-([^']+)'/", $response->headers->get('Content-Security-Policy'), $matches);

    $response->assertSee('nonce="'.$matches[1].'"', false);
});
