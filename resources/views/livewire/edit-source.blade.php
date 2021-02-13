<div class="fixed inset-0 z-10 flex flex-row justify-center items-center"
    x-data="editSource()" x-show="display"
    x-on:{{$listen}}.window="handleInvokation"
    
    >

    {{-- dark background --}}
    <template x-if="withBg">
        <div class="fixed inset-0 bg-gray-700 opacity-40"> </div>
    </template>

    <template x-if="showModal">
        <div class="rounded-lg bg-white relative w-2/3" 
            @click.away="cancel"
            x-on:keydown.escape.window="cancel"  >
            <div class="py-5 px-5 w-full">
                <p>Hola</p>
                <x-form.button @click="solve">Add</x-form.button>
            </div>
        </div>
    </template>

<script>
    function editSource() {
        return {
            display: false,
            showModal: false, 
            withBg: @json($withBg),
            resolve: r => console.log(r),
            handleInvokation: function (e) {
                console.log('invoked:', e)
                this.resolve = e.detail.resolve,
                this.withBg = e.detail.withBg,
                this.showModal = true;
                this.display = true;
            },
            solve: function () {
                this.resolve('ok')
                this.display = false;
            },
            cancel: function() {
                this.display = false;
                this.showModal = false;
                console.log('edit-source canceling')
                this.resolve(null)
            }
        }
    }
</script>
</div>
