@props(['href', 'label', 'target' => '_self', 'rel' => 'noopener', 'referrerpolicy' => 'no-referrer'])

<a
    {{ $attributes->merge([
        'aria-label' => $label,
        'class' => 'font-bold hover:brightness-75 transition duration-300 ease-in-out',
        'href' => $href,
        'referrerpolicy' => $referrerpolicy,
        'rel' => $rel,
        'target' => $target,
        'title' => $label,
    ]) }}>
    {{ $label }}
</a>
