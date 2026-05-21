@props(['active'])

@php
$classes = ($active ?? false)
            ? 'nav-link active bg-light border-start border-primary border-4 ps-3'
            : 'nav-link ps-3';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
