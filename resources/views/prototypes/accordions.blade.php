@push('head-script')
    <style>
        .accordion {
            max-height: 0;
        }
    </style>
@endpush
<x-layout.default title="Experimentos con acordeon">
    <x-container>
        <x-main-heading> Acordeon con Alpine</x-main-heading>
        <div
            x-data="
                {
                    active:'',
                    activate(k) {this.active = this.active == k ? '' : k},
                    isActive(k)  {return this.active == k},
                    panelHeigt(k, $el) {return this.isActive(k) ? $el.scrollHeight + 'px' : '0px'}
                }
            "
            class="border m-2 p-2"
        >
            <div
                class="flex flex-row justify-between cursor-pointer p-2 bg-gray-50 hover:bg-gray-100
                focus:outline-none rounded-t border border-b-0"
                x-on:click="activate('data-1')"
                x-bind:class="{'bg-gray-100': isActive('data-1') , 'bg-gray-50': !isActive('data-1')}"
            >
                <h3 class="text-xl font-medium text-gray-900">Título Descriptivo</h3>

                    <span
                        class=" text-xl  font-bold px-1 text-gray-800"
                        x-text="isActive('data-1') ? '&#x2212;' : '&#x2b;'"
                    >&#x2b;</span>
            </div>
            <div
                x-cloak
                class="transition-all ease-in-out duration-1000 overflow-hidden bg-white text-gray-800 rounded-b border border-t-0"
                x-bind:style="{'max-height': panelHeigt('data-1', $el)}"

            >
                <div class="p-2">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nesciunt excepturi voluptatibus commodi
                        architecto impedit. Facere ea mollitia sit dolor recusandae molestiae consectetur assumenda ipsa
                        expedita illum, excepturi eum tenetur reprehenderit.</p>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nesciunt excepturi voluptatibus commodi
                        architecto impedit. Facere ea mollitia sit dolor recusandae molestiae consectetur assumenda ipsa
                        expedita illum, excepturi eum tenetur reprehenderit.</p>
                </div>
            </div>
        </div>
    </x-container>
</x-layout.default>
