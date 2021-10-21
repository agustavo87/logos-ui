@props(['key'])
<div class="flex flex-row gap-2 items-baseline ml-1 pb-1 px-2">
    <label for="source-key" class="flex-grow-0 text-gray-600 text-sm">{{ __('sources.key') }}</label>
    <div class="flex flex-col flex-grow">
        <input
            value="{{$key}}"
            type="text" id="source-key" name="source-key"
            autocomplete="off"
            {{ $attributes}}
        >
        {{ $slot }}
    </div>
</div>
