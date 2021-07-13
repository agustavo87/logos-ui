<x-layout.default title="Selección dinámica de distintas opciones de un conjunto finito.">

<x-container class=" mb-5">
    <x-main-heading>
        Selección dinámica de distintas opciones de un conjunto finito.
    </x-main-heading>
{{--
    Expeimento: De un conjunto dado de opciones brindar la posibilidad
    de seleccionarlas, de modo que al seleccionar una, se limite el número
    de opciones disponibles, y se pueda seguir seleccionando opciones
    siempre y cuando hayan opciones disponibles.
--}}

{{-- Parent component --}}
<div x-data="{attributes: ['attribute-1', 'attribute-2', 'attribute-3']}" class=" max-w-screen-md mx-auto border rounded my-4 p-4">
    <h3 class="my-4 font-semibold">Selecciona tus opciones</h3>
    <template x-for="attribute in attributes">

        {{-- data-set wrapper --}}
        <div x-bind:data-attribute="attribute" class="border border-gray-400 rounded m-4 p-4">

            {{-- Child component --}}
            <div x-data="mySharedOptions.getData()" x-init="initialize">
                <h4 class=" my-2 italic" x-text="dataset.attribute"></h4>
                <div x-bind:id="dataset.attribute" class=" w-64 flex">
                    <select name="sourceAttributes" id="sourceAttributes"
                            x-model="selectedOption" class=" focus:outline-none flex-grow py-2 px-4 m-2 rounded-sm border"
                    >
                        <template x-for="option in myOptions" x-bind:key="option.code">
                            <option x-bind:value="option.code" x-text="option.label"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>
    </template>
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
        order: 0
    }, {
        code: "abstractNote",
        label: "Resumen",
        order: 1
    }, {
        code: "publicationTitle",
        label: "Nombre de Revista",
        order: 2
    }, {
        code: "publisher",
        label: "Editorial",
        order: 3
    }, {
        code: "date",
        label: "Fecha de publicación",
        order: 4
    }, {
        code: "place",
        label: "Ciudad",
        order: 4
    }
];
const myEventRoom = new EventRoom();
const mySharedOptions = new DinamicSharedOptionsComponent(testOptions, myEventRoom);

function datasetableComponent() {
    return {
        dataset: {
            attribute: ''
        },
        initialize: function () {
            this.dataset = Object.assign({}, this.$el.parentElement.dataset);
        }
    }
}

</script>
@endverbatim
@endpush
</x-layout.default>
