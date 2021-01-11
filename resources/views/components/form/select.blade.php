@props(['name', 'label'])
<x-form.label :for=$name> {{$label}} </x-form.label>
<select 
  {{ $attributes->merge([
    'name' => $name,
    'id' => $name,
    'aria-label' => $label,
    'class' => 'p-2 text-red-100leading-tight border border-gray-400 rounded-sm focus:outline-none',
  ]) }}
>
  {{ $slot }}
</select>