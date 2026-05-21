@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
$alignmentClasses = match ($align) {
    'left' => 'dropdown-menu-start',
    'top' => 'dropup',
    default => 'dropdown-menu-end',
};

// Width isn't directly supported by BS dropdown-menu, but we can keep the width class if needed or use standard BS sizes.
// For now, let's stick to standard BS dropdown behavior or add a custom style if really necessary.
@endphp

<div class="dropdown" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="dropdown-menu show position-absolute {{ $alignmentClasses }} {{ $contentClasses }}"
            style="display: none; min-width: {{ $width == '48' ? '12rem' : 'auto' }};"
            @click="open = false">
        {{ $content }}
    </div>
</div>
