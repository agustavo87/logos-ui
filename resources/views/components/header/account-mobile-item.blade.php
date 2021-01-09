@props([
    'base' => 'block mt-1 text-white opacity-75',
    'active' => 'hover:opacity-100',
    'inactive' => 'cursor-default opacity-100'
])

<x-link 
    base="{{ $base }}" 
    active="{{ $active }}" 
    inactive="{{ $inactive }}"
    {{ $attributes }}
>
    {{ $slot }}
</x-link>