<x-layout.alpine-2 title="Problema/Solución pasar datos entre componentes anidados">

<x-container class=" mb-5">
    <x-main-heading>
        Problema/Solución pasar datos entre componentes anidados.
    </x-main-heading>
{{--
    Problema #1: Pasar datos de un componente a un componente hijo

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


{{--
    Experimento: Pasar datos a componentes hijos dentro de un bucle x-for
    de Alpine.js
--}}

<div  x-data="{mensajes: ['mensaje uno', 'mensaje dos', 'mensaje tres']}"
      class=" max-w-screen-md my-4 p-4 rounded-sm border"
>
    <template x-for="mensaje in mensajes">
        <div x-bind:data-mensaje="mensaje">
            <div x-data="repeatedChildLegacy()" x-init="initialize"
                 class="border border-gray-500 m-2 p-2"
            >
                <p>Mi mensaje es: <span class="italic" x-text="dataset.mensaje"></span></p>
            </div>
        </div>
    </template>
</div>
<script>
    function repeatedChildLegacy() {
        return {
            dataset: {
                mensaje: ''
            },
            initialize: function () {
                this.dataset = Object.assign({}, this.$el.parentElement.dataset);
            }
        }
    }
</script>

</x-container>
</x-layout.alpine-2>
