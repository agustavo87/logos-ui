<x-layout.default title="Problema/Solución pasar datos entre componentes anidados">

<x-container class=" mb-5">
    <x-main-heading>
        Problema/Solución pasar datos entre componentes anidados.
    </x-main-heading>
{{--
    Problema: Pasar datos de un componente a un componente hijo

    Solución: Se utiliza el dataset de un nodo padre del componente hijo.
    Al inicializarlo, se utiliza la referencia $el, y se accede al dataset
    del nodo padre, y se actualizan los datos del componente hijo.

    Los datos del componente hijo no responden a los cambios del padre.
--}}

<div class=" max-w-screen-md mx-auto border border-gray-400 p-4 m-4 rounded-md">
    {{-- root component --}}
    <form x-data="{title:'Prueba de pasar datos', propA:'Soy un sub-mensage', propB:'Soy otro sub-mensaje'}" class="flex flex-col">
        <h3 x-text="title" class=" font-semibold py-4"></h3>

        <div x-bind:data-prop-a="propA" x-bind:data-prop-b="propB">
            <div x-data="inheritProps()" x-init="initialize">
                <ul>
                    <li>Mensaje 1: <span x-text="dataset.propA"></span></li>
                    <li>Mensaje 2: <span x-text="dataset.propB"></span></li>
                </ul>
                 <br>

            </div>
        </div>
        </template>
<script>
function inheritProps() {
    return {
        dataset: {
            propA:'',
            propB:''
        },
        initialize: function () {
            this.dataset =  Object.assign({}, this.$el.parentElement.dataset);
        }
    }
}
</script>
    </form>
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
const mySharedOptions = new SharedOptionsComponent(testOptions, myEventRoom);

</script>
@endverbatim
@endpush
</x-layout.default>
