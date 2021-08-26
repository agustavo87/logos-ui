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
                        <input type="search" autocomplete="off" placeholder="key" name="key" id="key" x-ref="key"
                            class=" flex-grow px-1 focus:outline-none focus:shadow-inner focus:border-blue-400 border rounded-r-md border-gray-100 text-sm w-0"
                            x-model="key"
                            x-on:input="$dispatch('input:key', $event.target.value)"
                            x-on:source-select:start.window="$el.focus()"
                        >
                        <button class="flex-none py-2 bg-gray-100 px-2 hover:opacity-80 focus:outline-none active:opacity-100"
                            x-on:click="orderChanged($dispatch)"
                        >
                            <x-icons.sort-direction class="w-3 h-3 fill-current" />
                        </button>
                    </div>
                </th>
                <th class="w-4/6">
                    <div class="flex flex-row items-stretch text-gray-500">
                        <label for="title" class=" flex-none py-2 bg-gray-100 px-2 rounded-l-md">
                            <x-icons.lupa  class="w-3 h-3 fill-current" />
                        </label>
                        <input type="search" autocomplete="off" id="title" placeholder="title"
                            class="flex-grow px-1 focus:outline-none focus:shadow-inner focus:border-blue-400 border rounded-r-md border-gray-100 text-sm  w-0"
                            x-model="title"
                            x-on:input="$dispatch('input:title', $event.target.value)"
                        >
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <template x-for="i in maxRows" x-bind:key="i">
            <tr x-bind:class="{'cursor-pointer hover:bg-indigo-100': sources[i-1], 'bg-indigo-100': selected == $el.dataset.key}"
                    x-on:click="sourceSelected($dispatch)" x-bind:data-key="sources[i-1] ? sources[i-1].key : null "
                    x-bind:title="sources[i-1] ? sources[i-1].attributes.title : '&nbsp;' "
                >
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
                asc: true,
                maxRows: options.maxRows,
                selected: '',
                sourceSelected: function ($dispatch) {
                    this.selected = this.$el.dataset.key
                    $dispatch('selection-change', this.selected)
                },
                orderChanged: function($dispatch) {
                    this.asc = !this.asc
                    $dispatch('order-change', this.asc)
                },
                init: function () {
                    document.addEventListener('source-select:reset', () => this.reset())
                },
                reset: function() {
                    this.selected = ''
                    this.title = ''
                    this.asc= true
                    this.key= ''
                }
            }
        })
    })
</script>

@endpush
@endonce
</div>
