
@props([
  'base' => 'block px-2 py-1 mt-1 sm:mt-0 sm:ml-2 font-semibold rounded', 
  'active' => 'text-white opacity-75 cursor-pointer hover:bg-gray-800 hover:opacity-100 active:opacity-100 active:bg-gray-700',
  'inactive' => 'text-white cursor-default'
])

<x-link
  {{ $attributes }}
  base="{{ $base }}"
  active="{{ $active }}"
  inactive="{{ $inactive }}" 
>
{{ $slot }}
</x-link>
