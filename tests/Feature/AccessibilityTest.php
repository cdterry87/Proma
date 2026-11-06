<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

function shareFieldError(string $field): void
{
    view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag([$field => 'Something is wrong.'])));
}

beforeEach(function () {
    view()->share('errors', new ViewErrorBag);
});

it('names icon-only buttons and leaves other buttons to their text [D-34]', function () {
    expect(Blade::render('<x-button icon="x" label="Close" />'))->toContain('aria-label="Close"')
        ->and(Blade::render('<x-button icon="x" href="/" label="Home" />'))->toContain('aria-label="Home"')
        ->and(Blade::render('<x-button>Save</x-button>'))->not->toContain('aria-label')
        ->and(Blade::render('<x-button href="/">Open</x-button>'))->not->toContain('aria-label');
});

it('builds dialogs on the native element with a heading as their name [D-34]', function () {
    $html = Blade::render('<x-dialog name="edit" title="Edit">Body</x-dialog>');

    expect($html)->toContain('<dialog')
        ->toContain('aria-labelledby="edit-title"')
        ->toContain('id="edit-title"')
        ->not->toContain('type="checkbox"');
});

it('links and announces errors on every kind of field', function (string $field) {
    shareFieldError('thing');

    $html = Blade::render($field);

    expect($html)->toContain('id="thing-error"')
        ->toContain('role="alert"')
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="thing-error"');
})->with([
    'input' => '<x-input name="thing" label="Thing" />',
    'select' => '<x-select name="thing" label="Thing"></x-select>',
    'textarea' => '<x-textarea name="thing" label="Thing"></x-textarea>',
    'checkbox' => '<x-checkbox name="thing" label="Thing" />',
    'toggle' => '<x-toggle name="thing" label="Thing" />',
]);

it('marks nothing as invalid without an error', function () {
    expect(Blade::render('<x-toggle name="thing" label="Thing" />'))->not->toContain('aria-invalid')
        ->not->toContain('aria-describedby')
        ->toContain('role="switch"');
});

it('gives toasts an icon for each severity so color is not the only signal', function () {
    $this->actingAs(User::factory()->create());

    $response = $this->get('/settings')->assertOk();

    foreach (['info', 'success', 'warning', 'error'] as $severity) {
        $response->assertSeeHtml("toast__icon--{$severity}");
    }
});

it('gives focus somewhere to land in both layouts', function () {
    $this->get('/login')->assertSeeHtml('id="main" tabindex="-1"');

    $this->actingAs(User::factory()->create())
        ->get('/settings')
        ->assertSeeHtml('id="main" class="shell__content" tabindex="-1"');
});

it('writes no inline styles outside email templates [D-35]', function () {
    $offenders = collect(File::allFiles(resource_path('views')))
        ->reject(fn ($file) => str_starts_with($file->getRelativePathname(), 'mail'))
        ->filter(fn ($file) => preg_match('/\sstyle=/', $file->getContents()) === 1)
        ->map(fn ($file) => $file->getRelativePathname())
        ->values()
        ->all();

    expect($offenders)->toBe([]);
});

it('serves its own font and loads none from elsewhere [D-36]', function () {
    $css = collect(File::allFiles(resource_path('css')))->map(fn ($file) => $file->getContents())->implode("\n");

    expect($css)->toContain('inter-variable-latin.woff2')
        ->not->toContain('fonts.googleapis.com');

    $this->get('/login')->assertDontSee('fonts.googleapis.com');
});

it('keeps hard-coded colors in the token file', function () {
    $offenders = collect(File::allFiles(resource_path('css')))
        ->reject(fn ($file) => $file->getFilename() === 'tokens.css')
        ->filter(fn ($file) => preg_match('/#[0-9a-fA-F]{3,8}\b|\b(rgb|hsl|oklch)a?\(/', $file->getContents()) === 1)
        ->map(fn ($file) => $file->getRelativePathname())
        ->values()
        ->all();

    expect($offenders)->toBe([]);
});
