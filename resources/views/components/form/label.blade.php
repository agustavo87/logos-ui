@props([
  'padding' => 'px-1 pt-4'
])

<label
  {{ $attributes->merge(['class' => 'text-gray-700 m-0 ' . $padding]) }}
>
 {{ $slot }}
</label>
