<div class="fixed inset-0 z-10 flex flex-row justify-center items-center" 
    x-data="editSource({
        data: @entangle('data').defer,
        schema: @entangle('sourceSchema').defer
    })" 
    x-show="display" x-init="init($dispatch)"
    x-on:{{$listen}}.window="handleInvokation">

    {{-- dark background --}}
    <template x-if="withBg">
        <div class="fixed inset-0 bg-gray-700 opacity-40"> </div>
    </template>

    {{-- modal --}}
    <div class="rounded-lg bg-white relative w-2/3" x-show="showModal" @click.away="cancel"
        x-on:keydown.escape.window="handleEscape">
        <div class="py-5 px-5 w-full">
            <div>
                <h3>Autores</h3>
                <livewire:creators-edit :source="$source" />
            </div>
            <div>
                <x-form.select name="schema" label="Tipo" x-model="sourceSchema">
                    @foreach ($supportedSchemas as $schemaTag => $schemaName)
                        <option value="{{$schemaTag}}" {{ $schemaName === $sourceSchema ? 'selected' : ''}}> {{ $schemaName}} </option>
                    @endforeach
                </x-form.select>
            </div>
            <div class=" h-64 overflow-y-auto" x-on:data-change.debounce.750ms="$wire.set('data', $event.detail)">
                <x-logos.citation-book-filler />
                <x-logos.citation-article-filler />
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
                display: true,
                showModal: true,
                data: entangles.data,
                sourceSchema: entangles.schema,
                withBg: @json($withBg),

                resolve: r => console.log(r),

                init: function ($dispatch) {
                    this.$nextTick(() => $dispatch('set-schema', {
                        schema: this.sourceSchema,
                        data: this.data
                    }))
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

                handleInvokation: function(event) {
                    this.resolve = event.detail.resolve,
                    this.withBg = event.detail.withBg,
                    this.showModal = true;
                    this.display = true;
                },

                solve: function() {
                    this.display = false;
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
</div>