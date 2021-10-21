@props(['types', 'updatecb'])

<select x-data="selectSourceType({
        type: @entangle( $attributes->wire('model') ) ,
        updatecb: {{ $updatecb }}
    })"
    x-model="type"
    x-bind:class="{'invisible':{{ $types }} == null}"
    wire:ignore
    {{ $attributes->whereDoesntStartWith('wire:model') }}
>
<template x-for="sType in {{ $types }}">
    <option x-bind:value="sType.code" x-text="sType.label" x-bind:selected="sType.code == type">
    </option>
</template>
</select>

@once
    @push('head-script')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('selectSourceType', function (options) {
                    return {
                        type: options.type,
                        updatecb: options.updatecb,
                        init: function () {
                            this.updatecb(this.type)
                            this.$watch('type', (v) => this.updatecb(v))
                        }
                    }
                })
            })
        </script>
    @endpush
@endonce
