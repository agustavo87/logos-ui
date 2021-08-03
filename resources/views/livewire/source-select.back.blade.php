<div
    x-data="modalCitation()"
    x-show="display" x-on:{{ $listen }}.window="handleInvocation"
    x-on:keydown.escape.window="handleEscape"
    class="fixed z-10 inset-0 flex flex-col justify-center items-center">

    <div class="absolute inset-0 bg-gray-700 opacity-40"></div>

    {{-- modal container --}}
    {{-- <template x-if="showModal"> --}}
        <div class="relative max-w-lg w-full px-2 py-2" x-show="showModal">
            {{-- modal --}}
            <div class=" relative bg-white rounded-xl w-full shadow-xl"
                 @click.outside="cancel"
            >
                <div class="px-5 pt-6">
                    <table class="sources-table w-full table-fixed border border-separate border-gray-300 rounded-t-md">
                        <thead>
                            <tr class="ml-2">
                                <th class="w-2/6">
                                    <div class="flex flex-row items-stretch text-gray-500">
                                        <label for="key" class=" flex-none py-2 bg-gray-100 px-2 rounded-l-md">
                                            <x-icons.lupa  class="w-3 h-3 fill-current" />
                                        </label>
                                        <input type="text" autocomplete="off" id="key" placeholder="key"
                                               wire:model.debounce.500ms="searchFields.key" x-ref="key"
                                               class=" flex-grow px-1 focus:outline-none focus:shadow-inner border rounded-r-md border-gray-100 text-sm w-0"
                                        >
                                    </div>
                                </th>
                                <th class=" w-4/6">
                                    <div class="flex flex-row items-stretch text-gray-500">
                                        <label for="title" class=" flex-none py-2 bg-gray-100 px-2 rounded-l-md">
                                            <x-icons.lupa  class="w-3 h-3 fill-current" />
                                        </label>
                                        <input type="text" autocomplete="off" id="title" placeholder="title"
                                               wire:model.debounce.500ms="searchFields.title"
                                               class=" flex-grow px-1 focus:outline-none focus:shadow-inner border rounded-r-md border-gray-100 text-sm  w-0"
                                        >
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($row = 1; $row <= $max_rows; $row++)
                                @if($row <= $sources->count())
                                    <?php $source = $sources[$row-1] ?>
                                    <tr data-key="{{ $source->key }}" data-id="{{ $source->id }}"
                                        x-on:mouseup="seleccionar"
                                        class="cursor-pointer hover:bg-indigo-200" x-bind:class="{'bg-indigo-100' : selected === '{{ $source->key }}'}"
                                    >
                                        <td class="text-sm px-2 py-1 border-b border-gray-100">
                                            {{ $source->key }}
                                        </td>
                                        <td class="text-sm px-2 py-1 border-b border-gray-100 text-ellipsis" title="{{$source->data['title']}}">
                                            {{ $source->data['title'] }}
                                        </td>
                                    </tr>
                                @else
                                    @if ($sources->count())
                                        <tr>
                                            <td class="text-sm px-2 py-1 border-b border-gray-100">
                                                &nbsp;
                                            </td>
                                            <td class="text-sm px-2 py-1 border-b border-gray-100">
                                                &nbsp;
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="text-sm px-2 py-1 border-b border-gray-100 text-gray-500 text-center" colspan="2">
                                                @if($row == 1) <i> {{ __('logos.no-records') }}</i> @endif &nbsp;
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endfor
                        </tbody>
                    </table>
                    {{-- pagination --}}
                    <div class="px-1">
                        <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
                            @if ($sources->hasPages() && $sources->count())
                                <span>
                                    {{-- Previous Page Link --}}
                                    @if ($sources->onFirstPage())
                                        <span
                                            class="relative inline-flex items-center text-sm font-medium text-gray-400 cursor-default leading-5 rounded-md">
                                            {!! __('pagination.previous') !!}
                                        </span>
                                    @else
                                        <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev"
                                            class="relative inline-flex items-center text-sm font-medium text-gray-700 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                            {!! __('pagination.previous') !!}
                                        </button>
                                    @endif
                                </span>
                                @if ($sources->count())
                                    <span class="text-xs leading-5 tracking-widest font-medium text-gray-600">
                                        {{ "{$sources->currentPage()}:{$sources->lastPage()}" }}
                                    </span>
                                @endif
                                <span>
                                    {{-- Next Page Link --}}
                                    @if ($sources->hasMorePages())
                                        <button wire:click="nextPage" wire:loading.attr="disabled" rel="next"
                                            class="relative inline-flex items-center text-sm font-medium text-gray-700  leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                            {!! __('pagination.next') !!}
                                        </button>
                                    @else
                                        <span
                                            class="relative inline-flex items-center text-sm font-medium text-gray-400 cursor-default leading-5 rounded-md">
                                            {!! __('pagination.next') !!}
                                        </span>
                                    @endif
                                </span>
                            @else
                                <span>&nbsp;</span>
                            @endif
                        </nav>
                    </div>
                </div>
                <div class="mt-3 bg-gray-100 px-5 rounded-b-xl pt-3 pb-4 flex justify-end items-center">
                    {{-- <x-form.button title="Edit Source" class="mr-2 disabled:cursor-default disabled:pointer-events-none disabled:opacity-50 "
                                   x-on:click="edit" x-bind:disabled="!selected_id"
                    >
                        Edit
                    </x-form.button> --}}
                    {{-- <button x-on:click="newReference" title=" {{ __('ui.new') }}"
                        class="h-9 w-9 flex justify-center items-center bg-green-200 text-blue-900 rounded-full mr-2"
                    >
                        <x-icons.cross class="w-5 h-5" />
                    </button> --}}
                    <x-form.button x-on:click="solve"  class="mr-2">
                        {{ __('ui.insert') }}
                    </x-form.button>
                    <x-form.button x-on:click="cancel" replace
                        class="bg-gray-500 font-bold py-2 px-4 rounded-lg text-white focus:outline-none hover:bg-gray-400 active:bg-gray-600"
                    >
                        {{ __('ui.cancel') }}
                    </x-form.button>
                </div>
            </div>
        </div>
    {{-- </template> --}}

    <script>
        function modalCitation() {
                return {
                    selected: null,
                    selected_id: null,
                    display: false,
                    showModal: false,
                    ui: null,
                    seleccionar: function (e) {
                        this.selected = e.currentTarget.dataset.key
                        this.selected_id = e.currentTarget.dataset.id
                    },
                    respond: a => console.log(a),
                    handleInvocation: function (e) {
                        console.log(e.detail)
                        this.respond = e.detail.resolve,
                        this.ui = e.detail.ui;
                        this.show();
                    },
                    show: function () {
                        this.showModal = true;
                        this.display = true;
                        this.$nextTick(() => {
                            this.$refs.key.value = '';
                            this.$refs.key.focus()
                        });
                    },
                    solve: function () {
                        this.display = false;
                        this.respond(this.selected);
                        this.close()
                    },
                    // newReference: function () {
                    //     this.showModal = false;
                    //     this.ui.dialogGet('source-edit', {
                    //         withBg:false,
                    //         ui:this.ui,
                    //         source_id: null
                    //     })
                    //         .then((r) => {
                    //             console.log(r);
                    //             this.showModal = true;
                    //         })
                    // },
                    // edit: function() {
                    //     this.showModal = false;
                    //     this.ui.dialogGet('source-edit', {
                    //         withBg: false,
                    //         ui: this.ui,
                    //         source_id: this.selected_id
                    //     })
                    //         .then((r) => {
                    //             console.log(r);
                    //             this.showModal = true;
                    //         })
                    // },
                    cancel: function () {
                        console.log('source-select canceling')
                        this.close()
                        this.respond(null);
                    },
                    close: function () {
                        this.display = false,
                        this.showModal = false;
                        this.$wire.resetPage();
                        this.$wire.reiniciarFields()
                        this.selected = ''
                        this.selected_id = null
                    },
                    handleEscape: function () {
                        if (this.showModal) {
                            this.cancel();
                        }
                    }
                }
            }
    </script>
</div>
