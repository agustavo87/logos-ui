<div
    class="fixed inset-0 z-10 flex flex-row justify-center items-center"
    x-data="editSource({
        data: @entangle('data').defer,
        schema: @entangle('sourceSchema').defer,
        source_id: @entangle('source_id').defer
    })"
    x-show="display" x-init="init($dispatch)"
    x-on:{{ $listen }}.window="handleInvocation($event, $dispatch)">

    {{-- dark background --}}
    <template x-if="withBg">
        <div class="fixed inset-0 bg-gray-700 opacity-40"> </div>
    </template>

    {{-- modal --}}
    <div class="source-edit-modal rounded-lg bg-white relative"
        x-show="showModal" @click.away="cancel"
        x-on:keydown.escape.window="handleEscape">
        <div class="py-5 px-5 w-full relative" >
            {{-- Loading Message --}}
            <div class="absolute bg-gray-100 text-gray-700 rounded-t-lg inset-0 flex justify-center items-center" x-show="!data_loaded">
                Cargando...
            </div>
            <div :class="{
                'visible': data_loaded,
                'invisible': !data_loaded
            }">
                <div>
                    <h3 class="font-medium">Creadores</h3>
                    <livewire:creators-edit  :source="$source" />
                </div>
                <div>
                    <select name="schema" label="Tipo" x-model="sourceSchema" class="border rounded border-gray-400 text-sm px-2 py-1 focus:outline-none">
                        @foreach ($supportedSchemas as $schemaTag => $schemaName)
                            <option value="{{$schemaTag}}" {{ $schemaName === $sourceSchema ? 'selected' : ''}}> {{ $schemaName}} </option>
                        @endforeach
                    </select>
                </div>
                <div class=" h-64 overflow-y-auto" x-on:data-change.debounce.750ms="$wire.set('data', $event.detail)">
                    <x-logos.citation-book-filler />
                    <x-logos.citation-article-filler />
                </div>
            </div>
        </div>
        <div class="py-5 px-5 w-full">
            <x-form.button @click="solve">Add</x-form.button>
            <x-form.button wire:click="save">Guardar</x-form.button>
        </div>
    </div>

    <script>
        function editSource(entangles) {
            return {
                sourceSchema: entangles.schema,
                data: entangles.data,
                source_id: entangles.source_id,
                withBg: @json($withBg),
                display: false,
                showModal: false,
                data_loaded: false,
                ui: null,
                resolve: r => console.log(r),

                init: function ($dispatch) {
                    this.$watch('sourceSchema', v => {
                        this.schemaChange(v, $dispatch)
                    })
                },

                schemaChange: function (schema, $dispatch) {
                    $dispatch('set-schema', {
                        schema: schema,
                        data: this.data
                    })
                },

                handleInvocation: function(event, $dispatch ) {
                    this.data_loaded = false;
                    this.resolve = event.detail.resolve;
                    this.withBg = event.detail.withBg;
                    this.display = true;
                    this.showModal = true;
                    this.$wire.setSource(event.detail.source_id)
                        .then( (r) => {
                            $dispatch('set-schema', {
                                schema: this.sourceSchema,
                                data: this.data
                            });
                            this.data_loaded = true;
                        });
                },

                solve: function() {
                    this.display = false;
                    this.showModal = false;
                    this.resolve('ok')
                },

                cancel: function() {
                    this.display = false;
                    this.showModal = false;
                    this.resolve(null)
                },

                handleEscape: function() {
                    if (this.showModal) {
                        this.cancel();
                    }
                }
            }
        }
    </script>
    @push('head-script')
        <style>
            .source-edit-modal {
                width: 580px;
            }
        </style>
    @endpush
</div>
