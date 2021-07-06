@props([
    'name','label', 'containerStyle' => null,
    'errorStyle' => 'text-red-500 text-sm',
    'labelClass' => '',
    'labelPadding' => null,
    'inputName' => $name
    ])
<div class="flex flex-col" style="{{ $containerStyle }}" >

    <x-form.label :padding=$labelPadding :for=$name class={{$labelClass}}>{{ $label }}: </x-form.label>
    <x-form.input

        {{ $attributes->merge([
            'name' => $name,
            'id' => $name,
            'value' => old($inputName),
            'aria-label' => $label,
        ]) }}
    />
    @error($inputName)
        <div class="{{ $errorStyle }}"> {{ $message }} </div>
    @enderror


</div>
