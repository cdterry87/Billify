@props(['type' => null, 'message' => null])

@php
    switch ($type) {
        case 'success':
            $classes = 'bg-green-800 text-green-100 border-green-900';
            break;
        case 'error':
            $classes = 'bg-red-800 text-red-100 border-red-900';
            break;
        case 'warning':
            $classes = 'bg-yellow-800 text-yellow-100 border-yellow-900';
            break;
        case 'info':
            $classes = 'bg-blue-900 text-blue-100 border-blue-950';
            break;
        default:
            $classes = 'bg-zinc-600 text-zinc-100 border-zinc-700';
    }

@endphp

<div {{ $attributes->merge([
    'class' => 'p-4 rounded-lg border ' . $classes,
]) }}>
    {!! $message ?? $slot !!}
</div>
