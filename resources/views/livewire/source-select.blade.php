<div
    x-data="modalCitation"
    x-show="display" x-on:{{ $listen }}.window="handleInvocation"
    x-on:source-mounted.window="tab = $event.detail"
    x-on:keydown.escape.window="handleEscape"
    class="fixed z-10 inset-0 flex flex-col justify-center items-center"
    x-ref="root"
    x-transition.opacity
>
    <div class="absolute inset-0 bg-gray-700 opacity-40"></div>

    {{-- modal container --}}
    {{-- <template x-if="showModal"> --}}
        <div 
            x-show="showModal" 
            x-transition
            x-on:transitioned="handleEndTransition($event, $dispatch)"
            x-on:transitioncancel="handleEndTransition($event, $dispatch)"
            class="relative max-w-lg w-full px-2 py-2" 
        >
            {{-- modal --}}
            <div class=" relative bg-white rounded-xl w-full shadow-xl"
                 @click.outside="cancel"
            >
            <ul class="flex gap-2 px-5 pt-5 text-sm">
                <li>
                    <button 
                        x-on:click="tab = 'select'" 
                        x-bind:disabled="tab == 'select'"
                        x-bind:class="tab == 'select' ?  'bg-gray-100' : ' text-black text-opacity-80 hover:bg-blue-100 active:bg-white'"
                        class="py-1 px-2 ml-2 rounded-t focus:outline-none disabled:cursor-default"
                    >
                        Seleccionar
                    </button>
                </li>
                <li>
                    <button 
                        x-on:click="$wire.emit('sourceNew')" 
                        x-bind:disabled="tab == 'new'"
                        x-bind:class="tab == 'new' ? 'bg-gray-100' : 'text-black text-opacity-80 hover:bg-blue-100 active:bg-white'"
                        class="py-1 px-2 rounded-t focus:outline-none disabled:cursor-default"
                    >
                        Nueva
                    </button>
                </li>
                <li>
                    <button 
                        x-bind:disabled="tab == 'edit'"
                        x-bind:class="tab == 'edit' ? 'bg-gray-100' : 'invisible'"
                        class="py-1 px-2 rounded-t focus:outline-none disabled:cursor-default"
                    >
                        Editar
                    </button>
                </li>
            </ul>
            <div class="relative">
                {{-- TODO: extraer a un componente --}}
                <div class="px-5 pt-0 relative" x-bind:class="tab != 'select' ? 'invisible' : ''">
                    <label for="title"></label>
                    <x-sources.select-table  wire:model="sources"
                        x-on:selection-change="selectionChanged($event.detail)"
                        x-on:input:title.debounce.500m="$wire.set('searchFields.title', $event.detail)"
                        x-on:input:key.debounce.500m="$wire.set('searchFields.key', $event.detail)"
                        x-on:order-change="$wire.set('asc', $event.detail)"
                        x-on:source-edit="$wire.emit('sourceEdit', $event.detail);"
                        :max-rows="$maxRows"
                    />
                </div>

                <div class="absolute inset-0"
                     x-bind:class="(tab != 'new' && tab != 'edit') ? 'invisible' : ''"
                     x-on:source-new:save="sourceSave($event.detail)"
                >
                    <livewire:source-new />
                </div>
            </div>
            {{-- Loader --}}
            <div class="my-2 px-5 h-5 flex items-center">
                <div x-data="{loading:false}"
                     x-bind:class="{'invisible': !loading}"
                     x-on:lw:message-change.window="loading = $event.detail.loading"
                >
                    <div class="flex items-center" >
                        <span class="ring-loader-xs"></span>
                        <span class=" ml-1 text-gray-600 loader-text">Procesando...</span>
                    </div>
                </div>
            </div>


                <div class="mt-3 bg-gray-100 px-5 rounded-b-xl pt-3 pb-4 flex justify-end items-center">
                    {{-- <x-form.button title="Edit Source" class="mr-2 disabled:cursor-default disabled:pointer-events-none disabled:opacity-50 "
                                   x-on:click="edit" x-bind:disabled="!selected_id"
                    >
                        Edit
                    </x-form.button> --}}
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

        function modalCitation(options) {
                return {
                    tab: 'select',
                    selected: null,
                    display: false,
                    showModal: false,
                    ui: null,
                    selectionChanged: function (key) {
                        this.selected = key
                    },
                    respond: a => console.log(a),
                    handleInvocation: function (e) {
                        this.respond = e.detail.resolve,
                        this.ui = e.detail.ui;
                        this.show();
                    },
                    show: function () {
                        this.showModal = true;
                        this.display = true;
                        this.$nextTick(() => {
                            document.dispatchEvent(new CustomEvent('source-select:start', {bubbles: true}))
                        });
                    },
                    newReference: function () {
                        this.tab = 'new'
                    },
                    init: function () {
                        this.$watch('tab', (value) => {
                            this.$refs.root.dispatchEvent(
                                new CustomEvent('source-select:tab-change', {bubbles:true, detail:value})
                            )
                        })
                    },
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
                    solve: function () {
                        if (this.tab == 'select') {
                            this.display = false;
                            this.respond(this.selected);
                            this.close()
                        }
                            this.$refs.root.dispatchEvent(
                                new CustomEvent('source-select:solve', {bubbles:true})
                            )

                    },
                    cancel: function () {
                        this.close()
                        this.respond(null);
                    },
                    sourceSave: function (result) {
                        console.log('source saved: ', result);
                    },
                    close: function () {
                        this.display = false,
                        this.showModal = false;
                        this.selected = null
                        this.$wire.call('flush')
                        this.$el.dispatchEvent(new CustomEvent('source-select:reset', {bubbles:true}))

                    },
                    handleEscape: function ($dispatch) {
                        if (this.showModal) {
                            this.cancel();
                        }
                    },
                    handleEndTransition: function ($event, $dispatch) {
                        if (!this.display) {
                            this.clean()
                        }
                    },
                    clean: function () {
                        this.tab = 'select'
                    }
                }
            }
    </script>
</div>
