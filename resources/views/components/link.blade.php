@props([
    'base' => 'underline', 
    'active' => 'text-blue-500 hover:text-blue-400', 
    'inactive' => 'text-gray-800' 
])

@php
    $current =  url()->current() == url($attributes->get('href'));
    $cClass = $base . ' ' . ($current ? $inactive : $active);
    $attributes = $attributes->merge(['class' => $cClass]);
    $attributes = $current
      ? $attributes->filter(fn ($value, $key) => $key != 'href')
      : $attributes;
@endphp

<a {{ $attributes }}>
  {{ $slot }}
</a>