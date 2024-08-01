@props(['message'])

<div {{ $attributes->merge([
    'class' => 'text-sm text-red-700 font-bold',
]) }}>
    {{ $message }}
</div>
