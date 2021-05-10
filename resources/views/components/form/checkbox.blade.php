@props(['name','label'])
<div>
    <x-form.input
    {{ $attributes->merge([
        'name' => $name,
        'id' => $name,
        'aria-label' => $label,
        'type' => 'checkbox',
        ]) }} />
    <x-form.label :for=$name>{{ $label }}</x-form.label>
</div>