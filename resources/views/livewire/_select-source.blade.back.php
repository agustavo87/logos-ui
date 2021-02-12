
<div x-data="modalCitation()" x-on:{{ $listen }}.window="handleInvocation" x-show="display"
    class="fixed z-10 inset-0 overflow-y-auto">
    <div class="flex flex-col justify-center items-center min-h-screen px-7">

        <div class="fixed inset-0 transition-opacity " x-show="display" 
            :aria-hidden="display">

            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>

        </div>

        {{--!-- This element is to trick the browser into centering the modal contents. -->
        <span class="inline-block align-middle h-screen" 
            aria-hidden="true">&#8203;</span> --}}

        <div class="block md:hidden bg-white rounded-lg shadow-xl transform my-8 align-middle max-w-md">
            <div class="mt-6">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <!-- Heroicon name: exclamation -->
                    <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    {{-- <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg> --}}
                </div>
            </div>
            <div class="px-6 py-4">
                <p class=" font-medium text-gray-800">Por favor, haz mas grande la pantalla del navegador, o utiliza un dispositivo con pantalla más grande para poder editar.</p>
            </div>
            <div class=" rounded-b-2xl bg-gray-50 py-3 px-6 flex flex-row-reverse">
                <x-form.button @click="cancel" class="bg-gray-500 font-bold py-2 px-4 rounded-lg text-white focus:outline-none hover:bg-gray-400 active:bg-gray-600" replace>Cancelar</x-form.button >
            </div>
        </div>

        {{-- Modal Solo es visible en tamaño md --}}
        <div x-show="display" @click.away="cancel" 
            {{-- class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"  --}}
            {{-- class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"  --}}
            class="hidden md:inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all my-8 align-middle max-w-2xl w-full"
            role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            {{-- Main Content --}}
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                
                <table class="min-w-full table-fixed">
                    <thead>
                        <tr>
                            <th class="w-1/4  px-1 pt-3 text-center text-sm leading-4 tracking-wieder text-gray-700">
                                Key &downarrow; 
                            </th>
                            <th class=" w-16  px-1 pt-3 text-center text-sm leading-4 tracking-wieder text-gray-700">
                                Year &downarrow; 
                            </th>
                            <th class="w-auto px-1 pt-3 text-center text-sm leading-4 tracking-wieder text-gray-700">
                                Title &downarrow; 
                            </th>
                        </tr>
                        <tr>
                            <td class=" px-1 py-1 whitespace-no-wrap text-sm leading-5">
                                <input type="text" placeholder="Search..." 
                                    class="mt-1 text-sm pl-2 pr-4 rounded-lg border border-gray-400 w-full py-1 focus:outline-none focus:border-blue-400" />
                            </td>
                            <td class=" px-1 py-1 whitespace-no-wrap text-sm leading-5">
                                <input type="text" placeholder="..." 
                                    class="mt-1 text-sm pl-2 pr-4 rounded-lg border border-gray-400 w-full py-1 focus:outline-none focus:border-blue-400" />
                            </td>

                            <td class=" px-1 py-1 whitespace-no-wrap text-sm leading-5">
                                <input type="text" placeholder="Search..." 
                                    class="mt-1 text-sm pl-2 pr-4 rounded-lg border border-gray-400 w-full py-1 focus:outline-none focus:border-blue-400" />
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($sources as $source)
                        <tr class=" cursor-pointer hover:bg-gray-50 border-b border-gray-200" 
                            @click="selected = '{{$source->key}}'" 
                            :class="{'bg-blue-50 hover:bg-blue-100': selected === '{{$source->key}}'}">
                            <td class="px-2 py-2 whitespace-no-wrap text-sm text-gray-900">{{ $source->key }}</td>
                            <td class="px-2 py-2 whitespace-no-wrap text-sm text-gray-900">{{ $source->data['year'] }}</td>
                            <td class="px-2 py-2 whitespace-no-wrap text-sm text-gray-900">{{   \Illuminate\Support\Str::limit($source->data['title'], 50) }} </td>
                        </tr>
                    @empty
                        <div class="w-full text-center">No hay resultados</div>
                        
                    @endforelse
                    </tbody>
                </table>
                    {{ $sources->onEachSide(2)->links() }}
            </div>
            <div class="bg-gray-50 py-3 px-6 flex flex-row-reverse">

                <x-form.button @click="solve" class="ml-1">Agregar</x-form.button >
                <x-form.button @click="cancel" class="bg-gray-500 font-bold py-2 px-4 rounded-lg text-white focus:outline-none hover:bg-gray-400 active:bg-gray-600" replace>Cancelar</x-form.button >

            </div>

        </div>
        {{-- End modal --}}



    </div>

</div>
<script>
    function modalCitation() {
        return {
            selected: null,
            display: true,
            respond: a => console.log(a),
            handleInvocation: function (e) {
                this.respond = e.detail.resolve
                this.display = true;
            },
            solve: function () {
                this.display = false;
                // this.selected = 'a12'; // test selected
                this.respond(this.selected);
            },
            cancel: function () {
                this.display = false;
                this.respond(null);
            }
        }
    }
</script>

</div>