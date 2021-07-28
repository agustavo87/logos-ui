<x-layout.alpine-2 title="Scope experiments">

<x-container class=" mb-5">
    <x-main-heading>
        Experimentos de opciones compartidas
    </x-main-heading>
{{--
    Experimento 1: Opciones compartidas
    Varios componentes comparten opciones disponibles. Cuando en un componente
    se selecciona una opción, esta opción ya no se encuentra disponible en los
    demás.
    El estado de las opciones se actualiza automáticamente al cambiar cada
    compoentene particular.
--}}

<div class=" max-w-screen-md mx-auto border border-gray-400 p-4 m-4 rounded-md">
    <form class="flex flex-col">
        <div x-data="mySharedOptions.getData()" x-init="initialize" id="source-type-select-1" class=" w-52 flex" >
            <select name="sourceAttributes" id="sourceAttributes"
                    x-model="selectedOption" class=" focus:outline-none flex-grow py-2 px-4 m-2 rounded-sm border"
            >
                <template x-for="option in myOptions" x-bind:key="option.code">
                    <option x-bind:value="option.code" x-text="option.label"></option>
                </template>
            </select>
        </div>
        <div x-data="mySharedOptions.getData()" x-init="initialize" id="source-type-select-2"  class=" w-52 flex">
            <select name="sourceAttributes-2" id="sourceAttributes-2"
                    x-model="selectedOption" class=" focus:outline-none flex-grow py-2 px-4 m-2 rounded-sm border"
            >
                <template x-for="option in myOptions" x-bind:key="option.code">
                    <option x-bind:value="option.code" x-text="option.label"></option>
                </template>
            </select>
        </div>
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
        code: "journalArticle",
        label: "Journal Article",
        order: 0
    },
    {
        code: "book",
        label: "Book",
        order: 1
    },
    {
        code: "bookSection",
        label: "Book Section",
        order: 2
    },
    {
        code: "blogPost",
        label: "Blog Post",
        order: 3
    }
];
const myEventRoom = new EventRoom();
const mySharedOptions = new SharedOptionsComponent(testOptions, myEventRoom);

</script>
@endverbatim
@endpush
</x-layout.alpine-2>
