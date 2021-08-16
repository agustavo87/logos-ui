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
                    <label for="title"></label>
                    <x-sources.select-table  wire:model="sources"
                        x-on:selection-change="selectionChanged($event.detail)"
                        x-on:input:title.debounce.500m="$wire.set('searchFields.title', $event.detail)"
                        x-on:input:key.debounce.500m="$wire.set('searchFields.key', $event.detail)"
                        x-on:order-change="$wire.set('asc', $event.detail)"
                    />
                    <div class="my-1 h-5 flex items-center">
                        <div wire:loading>
                            <div class="flex items-center" >
                                <span class="ring-loader-xs"></span>
                                <span class=" ml-1 text-gray-600 loader-text">Procesando...</span>
                            </div>
                        </div>
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
                        this.selected = null
                        this.$wire.call('flush')
                        this.$el.dispatchEvent(new CustomEvent('source-select:reset', {bubbles:true}))
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
