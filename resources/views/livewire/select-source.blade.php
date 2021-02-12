<div x-data="modalCitation()" x-on:{{ $listen }}.window="handleInvocation" 
    x-show="display" class="fixed z-10 inset-0 flex flex-col justify-center items-center">
    {{-- @foreach ($sources as $source)
       <p>{{ $source->key}}</p>
    @endforeach --}}

        <div class="absolute inset-0 bg-gray-700 opacity-40"></div>
        {{-- modal container --}}
        <div class="relative max-w-lg w-full px-2 py-2">
            {{-- modal --}}
            <div class="relative bg-white rounded-xl w-full shadow-xl"  x-show="display" @click.away="cancel" >
                <div class="px-5 pt-6">
                    <table class=" w-full table-fixed border border-separate border-gray-300 rounded-t-md">
                        <thead>
                            <tr class="ml-2">
                                <th class="w-2/6">
                                        <div class="flex flex-row items-stretch text-gray-500">
                                            <label for="key" class=" flex-none py-2 bg-gray-100 px-2 rounded-l-md">
                                                <svg class="w-3 h-3 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                                </svg>
                                            </label >
                                            <input type="text" autocomplete="off" 
                                                id="key"
                                                class=" flex-grow px-1 focus:outline-none focus:shadow-inner border rounded-r-md border-gray-100 text-sm w-0"
                                                placeholder="key"
                                                >
                                        </div>
                                </th>
                                <th class=" w-4/6">
                                    <div class="flex flex-row items-stretch text-gray-500">
                                        <label for="key" class=" flex-none py-2 bg-gray-100 px-2 rounded-l-md">
                                            <svg class="w-3 h-3 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                            </svg>
                                        </label >
                                        <input type="text" autocomplete="off" 
                                            id="title" 
                                            class=" flex-grow px-1 focus:outline-none focus:shadow-inner border rounded-r-md border-gray-100 text-sm  w-0"
                                            placeholder="title"
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sources as $source)
                            <tr class=" cursor-pointer hover:bg-gray-100" data-key="{{ $source->key }}" x-on:click="seleccionar" :class="{'bg-indigo-100 hover:bg-indigo-100' : selected === '{{ $source->key }}'}" >
                                <td class="text-sm px-2 py-1 border-b border-gray-100">{{ $source->key }}</td>
                                <td class="text-sm px-2 py-1 border-b border-gray-100" title="{{$source->data['title']}}">{{  \Illuminate\Support\Str::limit($source->data['title'], 35) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-sm px-2 text-center font-bold text-gray-400 py-4"> No se encontraron registros.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{-- pagination --}}
                    <div class="px-1">
                        @if ($sources->hasPages())
                            <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
                                <span>
                                    {{-- Previous Page Link --}}
                                    @if ($sources->onFirstPage())
                                        <span class="relative inline-flex items-center text-sm font-medium text-gray-400 cursor-default leading-5 rounded-md">
                                            {!! __('pagination.previous') !!}
                                        </span>
                                    @else
                                        <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="relative inline-flex items-center text-sm font-medium text-gray-700 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                            {!! __('pagination.previous') !!}
                                        </button>
                                    @endif
                                </span>

                                <span class="text-sm item-center">
                                    {{ "{$sources->currentPage()}:{$sources->lastPage()}" }}
                                </span>
                    
                                <span>
                                    {{-- Next Page Link --}}
                                    @if ($sources->hasMorePages())
                                        <button wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="relative inline-flex items-center text-sm font-medium text-gray-700  leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                            {!! __('pagination.next') !!}
                                        </button>
                                    @else
                                        <span class="relative inline-flex items-center text-sm font-medium text-gray-400 cursor-default leading-5 rounded-md">
                                            {!! __('pagination.next') !!}
                                        </span>
                                    @endif
                                </span>
                            </nav>
                        @endif
                    </div>
                </div>
                <div class="mt-3 bg-gray-100 px-5 rounded-b-xl pt-3 pb-4 flex justify-end">
                    <x-form.button @click="solve" class="mr-2">Agregar</x-form.button >
                    <x-form.button @click="cancel" class="bg-gray-500 font-bold py-2 px-4 rounded-lg text-white focus:outline-none hover:bg-gray-400 active:bg-gray-600" replace>Cancelar</x-form.button >
                </div>
            </div>
        </div>
    <script>
        console.log(@json($sources))
    </script>
    <script>
        function modalCitation() {
           return {
               selected: null,
               seleccionar: function (e) {
                console.log(e.currentTarget.dataset['key']);
                this.selected = e.currentTarget.dataset['key']
               },
               display: true,
               respond: a => console.log(a),
               handleInvocation: function (e) {
                   this.respond = e.detail.resolve
                   
                   this.display = true;
               },
               solve: function () {
                   this.display = false;
                   // this.selected = 'a12'; // test selected
                   this.respond(this.selected);
                   this.$wire.resetPage();
               },
               cancel: function () {
                   this.display = false;
                   this.respond(null);
                   this.$wire.resetPage();
               }
           }
       }
    </script>
</div>