<div {{ $attributes->whereDoesntStartWith('wire') }}>
    <table class="sources-table w-full table-fixed border border-separate border-gray-300 rounded-t-md"
           x-data="
            alpSelectTable({
                entangles : {
                    sources: @entangle( $attributes->wire('model') ),
                },
                maxRows: {{ $maxRows }}
            })
           "
    >
        <thead>
            <tr class="ml-2">
                <th class="w-2/6">
                    <div class="flex flex-row items-stretch text-gray-500">
                        <label for="key" class="flex-none py-2 bg-gray-100 px-2 rounded-l-md">
                            <x-icons.lupa  class="w-3 h-3 fill-current" />
                        </label>
                        <input type="text" autocomplete="off" placeholder="key" name="key" id="key" x-ref="key"
                            class=" flex-grow px-1 focus:outline-none focus:shadow-inner border rounded-r-md border-gray-100 text-sm w-0"
                            x-model="key"
                            x-on:input="$dispatch('input:key', $event.target.value)"
                        >
                    </div>
                </th>
                <th class="w-4/6">
                    <div class="flex flex-row items-stretch text-gray-500">
                        <label for="title" class=" flex-none py-2 bg-gray-100 px-2 rounded-l-md">
                            <x-icons.lupa  class="w-3 h-3 fill-current" />
                        </label>
                        <input type="text" autocomplete="off" id="title" placeholder="title"
                            class="flex-grow px-1 focus:outline-none focus:shadow-inner border rounded-r-md border-gray-100 text-sm  w-0"
                            x-model="title"
                            x-on:input="$dispatch('input:title', $event.target.value)"
                        >
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <template x-for="i in 8" x-bind:key="i">
                <tr x-bind:class="{'cursor-pointer hover:bg-indigo-200': sources[i-1] }">
                    <td class="text-sm px-2 py-1 border-b border-gray-100">
                        <span x-text="sources[i-1] ? sources[i-1].key : '&nbsp;' "></span>
                    </td>
                    <td class="text-sm px-2 py-1 border-b border-gray-100 text-ellipsis">
                        <span x-text="sources[i-1] ? sources[i-1].attributes.title : '&nbsp;' "></span>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

@once
@push('head-script')

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('alpSelectTable', (options) => {
            return {
                sources: options.entangles.sources,
                key: '',
                title: '',
                maxRows: options.maxRows
            }
        })
    })
</script>

@endpush
@endonce
</div>
