@props([
    'base' => 'block px-4 py-2 text-gray-200 sm:text-gray-800 focus:outline-none w-full text-left',
    'active' => 'hover:bg-gray-700 sm:hover:bg-indigo-500 hover:text-white',
    'inactive' => 'font-medium cursor-default',
    'disabled' => 0
])

<x-link 
    :base=$base 
    :active=$active 
    :inactive=$inactive
    :disabled=$disabled
    {{ $attributes }}
>
    {{ $slot }}
</x-link>