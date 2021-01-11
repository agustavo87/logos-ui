@props([
    'name','label',
    'errorStyle' => 'text-red-500 text-sm',
    ])
<div class="flex flex-col">
    <x-form.label :for=$name>{{ $label }}: </x-form.label>
    <x-form.input 
        {{ $attributes->merge([
            'name' => $name,
            'id' => $name,
            'value' => old($name),
            'aria-label' => $label,
        ]) }} 
    />
    @error($name)
        <div class="{{ $errorStyle }}"> {{ $message }} </div>
    @enderror
    

</div>