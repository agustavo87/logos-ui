@props(['sourceAttributes', 'typeAttributes'])
<ul x-data="sourceAttributes" wire:ignore {{ $attributes }}>
    <template x-for="attribute in {{ $typeAttributes }}">
        <li>
            <div class="flex flex-col mt-2">
                <label x-bind:for="'attribute.' + attribute.code" x-text="attribute.label" class=" flex-grow-0 text-gray-600 text-sm ml-1"></label>
                <input x-show="attribute.code != 'abstractNote' && attribute.type != 'date'"
                    x-bind:type="type(attribute.type)"
                    x-bind:name="'attribute.' + attribute.code"
                    x-bind:id="'input-' + attribute.code"
                    x-bind:value="{{ $sourceAttributes }}[attribute.code] ? {{ $sourceAttributes }}[attribute.code] : ({{ $sourceAttributes }}[attribute.base] ? {{ $sourceAttributes }}[attribute.base] : null)"
                    x-on:input="{{ $sourceAttributes }}[attribute.code] = $event.target.value"
                    class=" flex-grow border text-sm px-1 py-1 rounded focus:outline-none focus:border-blue-400"
                >

                <template x-if="attribute.type == 'date'">
                    <x-source.date-attribute-input
                        class="flex-grow border text-sm px-1 py-1 rounded focus:outline-none focus:border-blue-400"
                        :source-attributes="$sourceAttributes"
                    />
                </template>
                <textarea x-show="attribute.code == 'abstractNote'"
                    x-bind:name="'attribute.' + attribute.code"
                    x-bind:id="'input-' + attribute.code"
                    x-on:input="{{ $sourceAttributes }}[attribute.code] = $event.target.value"
                    x-bind:value="{{ $sourceAttributes }}[attribute.code] ? {{ $sourceAttributes }}[attribute.code] : ({{ $sourceAttributes }}[attribute.base] ? {{ $sourceAttributes }}[attribute.base] : null)"
                    rows="4"
                    class=" flex-grow border px-2 py-1 rounded text-sm resize-none focus:outline-none focus:border-blue-400"
                ></textarea>
            </div>
        </li>
    </template>
</ul>

@once
    @push('head-script')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('sourceAttributes', () => {
                    return {
                        type: function(typeCode) {
                            switch (typeCode) {
                                case 'text':
                                    return 'text'
                                case 'number':
                                    return 'number'
                                case 'date':
                                    return 'date'
                                default:
                                    return 'text'
                                    break;
                            }
                        }
                    }
                })
            })
        </script>
    @endpush
@endonce
