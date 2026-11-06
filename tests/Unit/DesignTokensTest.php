<?php

declare(strict_types=1);

/**
 * @return array<string, array<string, string>>
 */
function themeTokens(): array
{
    $css = file_get_contents(dirname(__DIR__, 2).'/resources/css/base/tokens.css');

    $blocks = [
        'light' => preg_match('/\[data-theme=\'light\'\] \{(.*?)\n\}/s', $css, $light) ? $light[1] : '',
        'dark' => preg_match('/\[data-theme=\'dark\'\] \{(.*?)\n\}/s', $css, $dark) ? $dark[1] : '',
    ];

    return array_map(function (string $block): array {
        preg_match_all('/--color-([a-z-]+):\s*(#[0-9a-f]{6});/', $block, $matches);

        return array_combine($matches[1], $matches[2]);
    }, $blocks);
}

function contrastRatio(string $first, string $second): float
{
    $luminance = function (string $hex): float {
        $channels = array_map(function (string $channel): float {
            $value = hexdec($channel) / 255;

            return $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }, str_split(ltrim($hex, '#'), 2));

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    };

    $values = [$luminance($first), $luminance($second)];

    return (max($values) + 0.05) / (min($values) + 0.05);
}

dataset('themes', ['light', 'dark']);

it('meets AA contrast for text on every surface', function (string $theme) {
    $tokens = themeTokens()[$theme];

    $surfaces = ['bg', 'surface', 'sunken'];
    $texts = ['text', 'text-muted', 'link', 'info-text', 'success-text', 'warning-text', 'error-text'];

    foreach ($surfaces as $surface) {
        foreach ($texts as $text) {
            expect(contrastRatio($tokens[$text], $tokens[$surface]))
                ->toBeGreaterThanOrEqual(4.5, "{$theme}: {$text} on {$surface}");
        }
    }
})->with('themes');

it('meets AA contrast on filled and tinted components', function (string $theme) {
    $tokens = themeTokens()[$theme];

    foreach (['primary', 'secondary', 'neutral', 'info', 'success', 'warning', 'error'] as $name) {
        foreach ([$name, "{$name}-hover"] as $fill) {
            expect(contrastRatio($tokens["on-{$name}"], $tokens[$fill]))
                ->toBeGreaterThanOrEqual(4.5, "{$theme}: on-{$name} on {$fill}");
        }
    }

    foreach (['info', 'success', 'warning', 'error'] as $name) {
        expect(contrastRatio($tokens["{$name}-text"], $tokens["{$name}-surface"]))
            ->toBeGreaterThanOrEqual(4.5, "{$theme}: {$name}-text on {$name}-surface");
    }
})->with('themes');

it('meets 3:1 contrast for controls and focus indicators', function (string $theme) {
    $tokens = themeTokens()[$theme];

    foreach (['bg', 'surface'] as $surface) {
        foreach (['border-strong', 'focus'] as $control) {
            expect(contrastRatio($tokens[$control], $tokens[$surface]))
                ->toBeGreaterThanOrEqual(3.0, "{$theme}: {$control} on {$surface}");
        }
    }
})->with('themes');
