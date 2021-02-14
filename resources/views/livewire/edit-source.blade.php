<div class="fixed inset-0 z-10 flex flex-row justify-center items-center" x-data="editSource()" x-show="display"
    x-on:{{$listen}}.window="handleInvokation">

    {{-- dark background --}}
    <template x-if="withBg">
        <div class="fixed inset-0 bg-gray-700 opacity-40"> </div>
    </template>

    <div class="rounded-lg bg-white relative w-2/3" x-show="showModal" @click.away="cancel"
        x-on:keydown.escape.window="handleEscape">
        <div class="py-5 px-5 w-full">
            @isset($source->id)
            <ul>
                <li>key: {{ $source->key }}</li>
                <li>type: {{ $source->type }}</li>
                <li>schema: {{ $source->schema }}</li>
                <li>
                    data:
                    <code>
                            {{ json_encode($source->data, JSON_PRETTY_PRINT) }}
                        </code>
                </li>
            </ul>
            @endisset
            <code>
                {{ json_encode($data, JSON_PRETTY_PRINT) }}
            </code>
            <div x-on:cambio.debounce.750ms="$wire.set('data', $event.detail)">
                <div x-data="sourceFiller()">
                    <div>
                        <input type="hidden" x-ref="secretary">
                        <input type="text" name="title" placeholder="title" class="border w-36 px-3 py-2"
                            @input.stop="handleInput($event, $dispatch)">
                        <input type="text" name="year" placeholder="year"  class="border w-36 px-3 py-2"
                            @input.stop="handleInput($event, $dispatch)">
                        <input type="text" name="editorial" placeholder="editorial"  class="border w-36 px-3 py-2"
                            @input.stop="handleInput($event, $dispatch)">
                        <input type="text" name="city" placeholder="city"  class="border w-36 px-3 py-2"
                            @input.stop="handleInput($event, $dispatch)">
                    </div>
                    <button @click="handleInput($dispatch)" class=" border">Probar</button>
                    <span x-text="data.title"></span>
                    <script>
                        function sourceFiller() {
                            return {
                                data: {
                                    title: null,
                                    year: null,
                                    editorial: null,
                                    city: null
                                },
                                handleInput: function ($e, $d) {
                                    this.data[$e.target.name] = $e.target.value
                                    $d('cambio', this.data);
                                }
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
        <div class="py-5 px-5 w-full">
            <x-form.button @click="solve">Add</x-form.button>
        </div>
    </div>

    <script>
        console.log(@json($source))
        function editSource() {
            return {
                display: true,
                showModal: true,
                withBg: @json($withBg),
                resolve: r => console.log(r),
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
</div>