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

    <div class="rounded-lg bg-white relative w-2/3" x-show="showModal" @click.away="cancel"
        x-on:keydown.escape.window="handleEscape">
        <div class="py-5 px-5 w-full">
            <code class="text-xs">{{json_encode($sourceSchema)}}</code>
            <div>
                <x-form.select name="schema" label="Tipo" x-model="sourceSchema">
                    @foreach ($supportedSchemas as $schemaTag => $schemaName)
                        <option value="{{$schemaTag}}" {{ $schemaName === $sourceSchema ? 'selected' : ''}}> {{ $schemaName}} </option>
                    @endforeach
                </x-form.select>
            </div>
            <code class="text-xs leading-tight">
                {{ json_encode($data, JSON_PRETTY_PRINT) }}
            </code>

            <div class=" h-64 overflow-y-auto" x-on:cambio.debounce.750ms="$wire.set('data', $event.detail)">
                <x-logos.citation-book-filler />
                <x-logos.citation-article-filler />
                {{-- Libro --}} 
                {{-- <div x-data="sourceFiller('citation.book:0.0.1', {
                    title: null,
                    year: null,
                    editorial: null,
                    city: null
                })" 
                x-on:set-schema.window="handleSetSchema" x-show="display">

                    <h4 class="font-medium">Libro</h4>
                    <div class="flex flex-col items-stretch">
                        <input type="hidden" x-ref="secretary">
                        <input class="border my-1 px-3 py-2"
                            type="text" name="title" placeholder="title" 
                            x-model="data.title"
                            @input.stop="handleInput($event, $dispatch)">
                        <input type="text" name="year" placeholder="year"  
                            class="border my-1 px-3 py-2"
                            x-model="data.year"
                            @input.stop="handleInput($event, $dispatch)">
                        <input type="text" name="editorial" placeholder="editorial"  
                            class="border my-1 px-3 py-2"
                            x-model="data.editorial"
                            @input.stop="handleInput($event, $dispatch)">
                        <input type="text" name="city" placeholder="city"  
                            class="border my-1 px-3 py-2"
                            x-model="data.city"
                            @input.stop="handleInput($event, $dispatch)">
                    </div>
                    
                    
                </div> --}}

                {{-- <div x-data="sourceFiller('citation.article:0.0.1', {
                    title: null,
                    year: null,
                    editorial: null,
                    city: null,
                    journal: null,
                    volume: null,
                    issue: null
                })" 
                x-show="display" x-on:set-schema.window="handleSetSchema">
                    <h4 class="font-medium">Citation Article</h4>
                </div> --}}
            </div> 
        </div> 
        <div class="py-5 px-5 w-full">
            <x-form.button @click="solve">Add</x-form.button>
            <x-form.button wire:click="save">Guardar</x-form.button>
        </div>
        {{-- <div>
            <strong>Source->data:</strong>
            <code class="text-xs">
                {{ json_encode($source->data)}}
            </code>
        </div> --}}
    </div>

    <script>
        function editSource(entangles) {
            return {
                display: true,
                data: entangles.data,
                showModal: true,
                sourceSchema: entangles.schema,
                withBg: @json($withBg),
                resolve: r => console.log(r),
                init: function ($dispatch) {
                    this.$watch('sourceSchema', v => {
                        this.schemaChange(v, $dispatch)
            
                    })
                    this.$nextTick(() => $dispatch('set-schema', {
                        schema: this.sourceSchema,
                        data: this.data
                    }))
                },
                schemaChange: function (schema, $dispatch) {
                    console.log('sc:', schema, 'this', this, 'dispatch', $dispatch)
                    $dispatch('set-schema', {
                        schema: schema,
                        data: this.data
                    })
                },
                handleInvokation: function(e) {
                    console.log('invoked:', e)
                    this.resolve = e.detail.resolve,
                    this.withBg = e.detail.withBg,
                    this.showModal = true;
                    this.display = true;
                },
                solve: function() {
                    this.resolve('ok')
                    this.display = false;
                },
                cancel: function() {
                    this.display = false;
                    this.showModal = false;
                    console.log('edit-source canceling')
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
    {{-- <script>
        function sourceFiller(schema, dataModel) {
            return {
                display: false,
                schema: schema,
                data: dataModel,
                // data: {
                //     title: null,
                //     year: null,
                //     editorial: null,
                //     city: null
                // },
                handleInput: function ($e, $d) {
                    this.data[$e.target.name] = $e.target.value
                    $d('cambio', this.data);
                },
                handleSetSchema: function (e) {
                    console.log('manejando set-schema', e)
                    console.log(e.detail.schema, '===', this.schema, '?')
                    if (e.detail.schema !== this.schema) {
                        this.display = false;
                        return;
                    };
                    this.display = true
                    let data = e.detail.data
                    this.data.title = data.title
                    this.data.year = data.year
                    this.data.editorial = data.editorial
                    this.data.city = data.city
                }
            }
        }
    </script> --}}
</div>