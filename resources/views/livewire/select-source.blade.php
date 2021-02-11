<div x-data="modalCitation()" x-on:{{ $listen }}.window="handleInvocation" x-show="display"
    class="fixed z-10 inset-0 overflow-y-auto">

    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20
        text-center sm:block sm:p-0">

        <div class="fixed inset-0 transition-opacity " x-show="display" 
            :aria-hidden="display">

            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>

        </div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" 
            aria-hidden="true">&#8203;</span>

        {{-- Modal --}}
        <div x-show="display" @click.away="cancel" 
            class="inline-block align-bottom bg-white rounded-lg text-left 
            overflow-hidden shadow-xl transform transition-all sm:my-8 
            sm:align-middle sm:max-w-lg sm:w-full" role="dialog" 
            aria-modal="true" aria-labelledby="modal-headline">
            
            <p class="p-4">Elige una fuente</p>
            <button @click="solve" class="border p-3 m-1">Retornar</button>
            <button @click="cancel" class="border p-3 m-1">Cancelar</button>

        </div>
        {{-- End modal --}}


    </div>

</div>
<script>
    function modalCitation() {
        return {
            key: null,
            display: false,
            respond: null,
            handleInvocation: function (e) {
                this.respond = e.detail.resolve
                this.display = true;
            },
            solve: function () {
                this.display = false;
                this.key = 'a12'; // test key
                this.respond(this.key);
            },
            cancel: function () {
                this.display = false;
                this.respond(null);
            }
        }
    }
</script>

</div>