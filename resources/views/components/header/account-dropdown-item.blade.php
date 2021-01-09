
@props([
    'base' => 'block px-4 py-2 text-gray-800', 
    'active' => 'hover:bg-indigo-500 hover:text-white',
    'inactive' => 'cursor-default font-semibold'
  ])
  
  <x-link
    {{ $attributes }}
    base="{{ $base }}"
    active="{{ $active }}"
    inactive="{{ $inactive }}" 
  >
  {{ $slot }}
  </x-link>
  