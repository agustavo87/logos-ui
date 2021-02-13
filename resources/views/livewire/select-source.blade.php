<div x-data="modalCitation()" 
    x-show="display" x-on:{{ $listen }}.window="handleInvocation"
    x-on:keydown.escape.window="handleEscape"
    class="fixed z-10 inset-0 flex flex-col justify-center items-center">

    <div class="absolute inset-0 bg-gray-700 opacity-40"></div>

    {{-- modal container --}}
    {{-- <template x-if="showModal"> --}}
        <div class="relative max-w-lg w-full px-2 py-2" x-show="showModal">
            {{-- modal --}}
            <div class=" relative bg-white rounded-xl w-full shadow-xl"
             @click.away="cancel">
                <div class="px-5 pt-6">
                    <table class="w-full table-fixed border border-separate border-gray-300 rounded-t-md">
                        <thead>
                            <tr class="ml-2">
                                <th class="w-2/6">
                                    <div class="flex flex-row items-stretch text-gray-500">
                                        <label for="key" class=" flex-none py-2 bg-gray-100 px-2 rounded-l-md">
                                            <svg class="w-3 h-3 fill-current" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </label>
                                        <input type="text" autocomplete="off" id="key" x-ref="key"
                                            class=" flex-grow px-1 focus:outline-none focus:shadow-inner border rounded-r-md border-gray-100 text-sm w-0"
                                            placeholder="key" wire:model.debounce.500ms="searchFields.key">
                                    </div>
                                </th>
                                <th class=" w-4/6">
                                    <div class="flex flex-row items-stretch text-gray-500">
                                        <label for="key" class=" flex-none py-2 bg-gray-100 px-2 rounded-l-md">
                                            <svg class="w-3 h-3 fill-current" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </label>
                                        <input type="text" autocomplete="off" id="title"
                                            class=" flex-grow px-1 focus:outline-none focus:shadow-inner border rounded-r-md border-gray-100 text-sm  w-0"
                                            placeholder="title" wire:model.debounce.500ms="searchFields.title" </div> </th>
                                            </tr> </thead> <tbody>
                                        @forelse ($sources as $source)
                            <tr class=" cursor-pointer hover:bg-gray-100" data-key="{{ $source->key }}"
                                x-on:click="seleccionar"
                                :class="{'bg-indigo-100 hover:bg-indigo-100' : selected === '{{ $source->key }}'}">
                                <td class="text-sm px-2 py-1 border-b border-gray-100">{{ $source->key }}</td>
                                <td class="text-sm px-2 py-1 border-b border-gray-100" title="{{$source->data['title']}}">
                                    {{  \Illuminate\Support\Str::limit($source->data['title'], 35) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-sm px-2 text-center font-bold text-gray-400 py-4"> No se
                                    encontraron registros.</td>
                            </tr>
                            @endforelse
                            </tbody>
                    </table>
                    {{-- pagination --}}
                    <div class="px-1">
                        @if ($sources->hasPages() && $sources->count())
                        <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
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
                        </nav>
                        @endif
                    </div>
                </div>
                <div class="mt-3 bg-gray-100 px-5 rounded-b-xl pt-3 pb-4 flex justify-end items-center">
                    <button @click="newReference" title="Agregar"
                    class="h-9 w-9 flex justify-center items-center bg-green-200 text-blue-900 rounded-full mr-2">
                        <svg class="w-5 h-5 fill-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                    <x-form.button @click="solve" class="mr-2">Insertar</x-form.button>
                    <x-form.button @click="cancel"
                        class="bg-gray-500 font-bold py-2 px-4 rounded-lg text-white focus:outline-none hover:bg-gray-400 active:bg-gray-600"
                        replace>Cancelar</x-form.button>
                </div>
            </div>
        </div>
    {{-- </template> --}}
    <script>
        console.log(@json($sources))
    </script>
    <script>
        function modalCitation() {
                return {
                    selected: null,
                    display: false,
                    showModal: false,
                    ui: null, 
                    seleccionar: function (e) {
                        // console.log(e.currentTarget.dataset['key']);
                        this.selected = e.currentTarget.dataset['key']
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
                    newReference: function () {
                        this.showModal = false;
                        this.ui.dialogGet('edit-source', {withBg:false, ui:this.ui})
                            .then((r) => {
                                console.log(r);
                                this.showModal = true;
                            })
                    },
                    cancel: function () {
                        console.log('select-source canceling')
                        this.close()
                        this.respond(null);
                    },
                    close: function () {
                        this.display = false,
                        this.showModal = false;
                        this.$wire.resetPage();
                        this.$wire.reiniciarFields()
                        this.selected = ''
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