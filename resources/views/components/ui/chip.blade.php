@props(['tone' => null])

<span {{ $attributes->class(['chip', 'chip--'.$tone => filled($tone)]) }}>{{ $slot }}</span>
