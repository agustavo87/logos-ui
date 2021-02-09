@props([
    'base' => 'underline', 
    'active' => 'text-blue-500 hover:text-blue-400', 
    'inactive' => 'text-gray-800',
    'disabled' => 0,
    'button' => 0,
    'dontDisable' => false
])

@php
    if ($dontDisable) {
      $isDisabled = false;
    } else {
      // Disabled: if the location and href are the same and are not a button. Or is specified as disabled.
      $isDisabled =  ( ( url()->current() == url( $attributes->get('href') ) ) && !$button ) || $disabled;
    }
    $cClass = $base . ' ' . ($isDisabled ? $inactive : $active);
    $attributes = $attributes->merge(['class' => $cClass]);
    $attributes = $isDisabled
      ? $attributes->filter(fn ($value, $key) => $key != 'href')
      : $attributes;
    $attributes = $button && $isDisabled ? $attributes->merge(['disabled' => 'disabled']) : $attributes
@endphp


@if ($button)
  <button {{ $attributes }}>
    {{ $slot }}
  </button>
@else
  <a {{ $attributes }}>
    {{ $slot }}
  </a>
@endif

