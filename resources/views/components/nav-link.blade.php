@props(['active'])

@php
$classes = ($active ?? false)
            ? 'nav-link active fw-bold border-bottom border-primary border-2'
            : 'nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
