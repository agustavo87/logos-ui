<x-layout.alpine-2 title="Problema/Solución pasar datos entre componentes anidados">

<x-container class=" mb-5">
    <x-main-heading>
        x-data inside template in alpine
    </x-main-heading>
    <div x-data="{data:['hola', 'como', 'va', 'mi' , 'gente']}"

    >
        <ul class="  list-disc list-inside">
            <template x-for="word in data">
                <li>
                    <span  x-text="word"></span>
                    <div x-data="{chip: 'this'}">
                        <span x-text="chip"></span>
                    </div>
                </li>
            </template>
        </ul>
    </div>
</x-container>
</x-layout.alpine-2>
