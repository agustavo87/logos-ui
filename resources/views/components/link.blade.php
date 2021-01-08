@props([
    'base' => 'underline', 
    'active' => 'text-blue-500 hover:text-blue-400', 
    'inactive' => 'text-gray-800',
    'disabled' => 0
])

@php
    $isDisabled =  (url()->current() == url($attributes->get('href'))) || $disabled;
    $cClass = $base . ' ' . ($isDisabled ? $inactive : $active);
    $attributes = $attributes->merge(['class' => $cClass]);
    $attributes = $isDisabled
      ? $attributes->filter(fn ($value, $key) => $key != 'href')
      : $attributes;
@endphp

<a {{ $attributes }}>
  {{ $slot }}
</a>