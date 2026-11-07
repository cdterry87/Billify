<?php

use Illuminate\Support\Facades\File;

it('applies the saved theme in the head and offers all three options', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee("localStorage.getItem('billify-theme')", false)
        ->assertSee('value="system"', false)
        ->assertSee('value="light"', false)
        ->assertSee('value="dark"', false);
});

it('renders the app layout with a skip link and main landmark', function () {
    $this->get('/styleguide')
        ->assertOk()
        ->assertSee('Skip to content')
        ->assertSee('id="main"', false)
        ->assertSee('<title>Style guide · '.config('app.name').'</title>', false);
});

it('hides the style guide outside local and testing', function () {
    app()->detectEnvironment(fn () => 'production');

    $this->get('/styleguide')->assertNotFound();
});

it('makes no third-party font or CSS requests', function () {
    $this->get('/styleguide')
        ->assertDontSee('fonts.googleapis.com', false)
        ->assertDontSee('fonts.bunny.net', false);
});

it('uses only hand-written CSS', function () {
    $package = File::json(base_path('package.json'));

    expect(array_keys($package['devDependencies'] ?? []))
        ->not->toContain('tailwindcss', '@tailwindcss/vite', 'sass');

    expect(File::get(base_path('vite.config.js')))->not->toContain('tailwind');
});

it('references only semantic colours outside the tokens file', function () {
    $files = collect(File::allFiles(resource_path('css')))
        ->reject(fn ($file) => $file->getFilename() === 'tokens.css');

    foreach ($files as $file) {
        expect($file->getContents())->not->toMatch('/#[0-9a-f]{3,8}\b|oklch\(|rgba?\(/i');
    }
});
