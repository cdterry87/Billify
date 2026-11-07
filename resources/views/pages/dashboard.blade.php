<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard')] class extends Component
{
    //
};
?>

{{-- Placeholder until the dashboard pull request lands. --}}
<div class="stack">
    <h1>Hello, {{ auth()->user()->name }}</h1>
    <p class="muted">Your dashboard is on its way.</p>
</div>
