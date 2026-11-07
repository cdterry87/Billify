<?php

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

function shareFieldError(string $name, string $message): void
{
    view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag([$name => [$message]])));
}

it('renders a button by default and a link when given an href', function () {
    $this->blade('<x-ui.button>Save</x-ui.button>')
        ->assertSee('<button', false)
        ->assertSee('type="button"', false)
        ->assertSee('btn--primary', false);

    $this->blade('<x-ui.button href="/bills" variant="secondary">Bills</x-ui.button>')
        ->assertSee('<a', false)
        ->assertSee('href="/bills"', false)
        ->assertDontSee('<button', false);
});

it('names icon-only buttons for screen readers', function () {
    $this->blade('<x-ui.button icon="x" label="Close" />')
        ->assertSee('aria-label="Close"', false)
        ->assertSee('btn--icon', false);
});

it('ties a field label to its control', function () {
    $this->blade('<x-ui.field name="form.name" label="Name" required hint="Short." />')
        ->assertSee('for="form-name"', false)
        ->assertSee('id="form-name"', false)
        ->assertSee('aria-required="true"', false)
        ->assertSee('aria-describedby="form-name-hint"', false)
        ->assertDontSee('aria-invalid', false);
});

it('marks a field invalid and links its error', function () {
    shareFieldError('email', 'Enter a valid email.');

    $this->blade('<x-ui.field name="email" label="Email" />')
        ->assertSee('aria-invalid="true"', false)
        ->assertSee('aria-describedby="email-error"', false)
        ->assertSee('Enter a valid email.');
});

it('renders select options and placeholder', function () {
    $this->blade('<x-ui.field name="c" label="C" as="select" placeholder="Pick" :options="$options" />', ['options' => ['a' => 'Alpha']])
        ->assertSee('<option value="">Pick</option>', false)
        ->assertSee('<option value="a">Alpha</option>', false);
});

it('escapes user-supplied values', function () {
    $this->blade('<x-ui.stat-tile label="L" :value="$value" />', ['value' => '<script>x</script>'])
        ->assertDontSee('<script>x</script>', false);

    $this->blade('<x-ui.chart-bar title="T" :bars="$bars" />', ['bars' => [['label' => '<b>x</b>', 'value' => 1, 'display' => '1']]])
        ->assertDontSee('<b>x</b>', false);
});

it('scales chart bars against the largest value', function () {
    $this->blade('<x-ui.chart-bar title="T" :bars="$bars" />', ['bars' => [
        ['label' => 'A', 'value' => 200, 'display' => '$200', 'tone' => 'positive'],
        ['label' => 'B', 'value' => 50, 'display' => '$50', 'tone' => 'negative'],
    ]])
        ->assertSee('--bar-height: 100%', false)
        ->assertSee('--bar-height: 25%', false)
        ->assertSee('chart__bar--positive', false)
        ->assertSee('$200');
});

it('allows bars without a tone', function () {
    $this->blade('<x-ui.chart-bar title="T" :bars="$bars" />', ['bars' => [['label' => 'A', 'value' => 5, 'display' => '$5']]])
        ->assertSee('--bar-height: 100%', false)
        ->assertDontSee('chart__bar--', false);
});

it('shows an empty state instead of dividing by zero', function () {
    $this->blade('<x-ui.chart-bar title="T" :bars="$bars" />', ['bars' => [['label' => 'A', 'value' => 0, 'display' => '$0']]])
        ->assertSee('Nothing to show yet.')
        ->assertDontSee('--bar-height', false);

    $this->blade('<x-ui.chart-bar title="T" />')->assertSee('Nothing to show yet.');
});

it('renders modals as native dialogs', function () {
    $this->blade('<x-ui.modal name="edit" title="Edit bill">Body</x-ui.modal>')
        ->assertSee('<dialog', false)
        ->assertSee('aria-labelledby="edit-title"', false)
        ->assertSee('Edit bill')
        ->assertSee('Body');
});

it('passes a flashed toast to the client safely', function () {
    session()->flash('toast', ['type' => 'success', 'message' => '<img src=x>']);

    $this->blade('<x-ui.toasts />')
        ->assertSee('aria-live="polite"', false)
        ->assertDontSee('<img src=x>', false);
});

it('renders every icon', function (string $name) {
    $this->blade('<x-ui.icon :name="$name" />', ['name' => $name])->assertSee('<svg', false);
})->with(fn () => collect(glob(dirname(__DIR__, 3).'/resources/views/components/ui/icons/*.blade.php'))
    ->map(fn (string $path) => basename($path, '.blade.php'))
    ->all());
