<x-layout.default title="Creación dinámica de campos a demanda.">

    <x-container class=" mb-5">
        <x-main-heading>
            Creación dinámica de campos a demanda.
        </x-main-heading>
{{--
    Expeimento: De un conjunto dado de opciones brindar la posibilidad
    de seleccionarlas, de modo que al seleccionar una, se limite el número
    de opciones disponibles, y se pueda seguir seleccionando opciones
    siempre y cuando hayan opciones disponibles.
--}}

        {{-- Parent component --}}
        <div x-data="dinamicFields.getRootData()" x-init="initialize"
            class=" max-w-screen-md mx-auto border rounded my-4 p-4">
            <h3 class="my-4 font-semibold">Selecciona tus opciones</h3>
            <template x-for="attribute in attributes">

                {{-- data-set wrapper --}}
                <div x-bind:data-attribute="attribute" class="border border-gray-400 rounded my-4 p-4">

                    {{-- Child component --}}
                    <div x-data="dinamicFields.getSelectData()" x-init="initialize">
                        <h4 class=" my-2 italic" x-text="dataset.attribute"></h4>
                        <div x-bind:id="dataset.attribute" class="flex">
                            <select name="sourceAttributes" id="sourceAttributes" x-model="selectedOption"
                                class=" focus:outline-none py-2 px-4 m-2 rounded-sm border flex-grow-0 w-52">
                                <template x-for="option in myOptions" x-bind:key="option.code">
                                    <option x-bind:value="option.code" x-text="option.label"></option>
                                </template>
                            </select>

                            {{-- Conditional inputs --}}
                            <div class="flex flex-col justify-center">
                                <template x-if="ownedOption.type == 'text'">
                                    <div>
                                        <span>Entrada de texto</span>
                                        <input type="text" name="texto" id="texto" class="border px-3 py-2 m-2">
                                    </div>
                                </template>
                                <template x-if="ownedOption.type == 'date'">
                                    <div>
                                        <span>Entrada de fecha</span>
                                        <input type="date" name="date" id="date" class="border px-3 py-2 m-2">
                                    </div>
                                </template>
                                <template x-if="ownedOption.type == 'number'">
                                    <div>
                                        <span>Entrada de número</span>
                                        <input type="number" name="xs" id="xs" class="border px-3 py-2 m-2">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <button x-on:click="agregarAttributo" x-bind:disabled="count >= optionsLength"
                class="bg-blue-500 text-white font-semibold rounded px-4 py-2 disabled:cursor-default disabled:pointer-events-none disabled:opacity-50 focus:outline-none">
                Agregar Attributo
            </button>
            <button x-on:click="quitarAttributo" x-bind:disabled="count <= 0"
                class="border border-blue-500 text-blue-500 mx-2 font-semibold rounded px-4 py-2 disabled:cursor-default disabled:pointer-events-none disabled:opacity-50 focus:outline-none">
                Quitar Attributo
            </button>
        </div>

    </x-container>

    @push('foot-script')
    @verbatim
    <script>
        /**
 * depends on:
 * - EventRoom.js
 * - SharedOptionsComponent.js
 */

const testOptions = [
    {
        code: "title",
        label: "Título",
        order: 0,
        type: 'text'
    }, {
        code: "abstractNote",
        label: "Resumen",
        order: 1,
        type: 'text'
    }, {
        code: "publicationTitle",
        label: "Nombre de Revista",
        order: 2,
        type: 'text'
    }, {
        code: "publisher",
        label: "Editorial",
        order: 3,
        type: 'text'
    }, {
        code: "volume",
        label: "Volume",
        order: 4,
        type: 'number'
    }, {
        code: "date",
        label: "Fecha de publicación",
        order: 5,
        type: 'date'
    }, {
        code: "place",
        label: "Ciudad",
        order: 6,
        type: 'text'
    }
];
const myEventRoom = new EventRoom();

dinamicFields = new DinamicSelectComponent(myEventRoom, testOptions);

    </script>
    @endverbatim
    @endpush
</x-layout.default>
