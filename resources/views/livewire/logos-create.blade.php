@push('head-script')
<style>
.logos-container {
    min-height: 500px;
    margin-bottom: 50px;
}
</style>

@endpush
<div 
    x-data="getLogos({ delta: @entangle('delta').defer, meta: @entangle('meta').defer, html: @entangle('html').defer})" 
>
    <div class="max-w-screen-md mx-auto mt-3 h-5">
        <span class=" ml-2 text-xs text-gray-400" x-text="transactionStatus"></span>
    </div>
    <div  @quill-input="handleInput"  class="flex flex-col mx-auto logos-container mt-1 max-w-screen-md">
        <input type="text" wire:model.defer="title" @input="simpleInput" placeholder="Título" autocomplete="off" class="ml-2 text-3xl font-bold mb-2 focus:outline-none text-gray-800">
        <x-logos :initial-delta="$delta" />
    </div>
    <h3 class=" text-xl font-semibold">Livewire</h3>
    <strong>Meta:</strong>
    <code>
        {{ json_encode($meta) }}
    </code>
    <strong>Delta:</strong>
    <code>
        {{ json_encode($delta)}}
    </code>
    

    @push('foot-script')
        <script>
            function getLogos(entangles) {
                return {
                    delta: entangles.delta,
                    meta: entangles.meta,
                    html: entangles.html,
                    transactionStatus: 'Listo',
                    handleInput: function (event) {
                        this.delta = event.detail.delta();
                        this.html = event.detail.html();
                        this.meta = {key: 'value'};
                        this.transactionStatus = 'Modificado'
                        this.save()
                    },
                    simpleInput: function () {
                        this.transactionStatus = 'Modificado'
                        this.save()
                    },
                    save: debounce(function () {
                        this.transactionStatus = "Guardando..."
                        this.$wire.save().then(() => {this.transactionStatus = 'Guardado'});
                    }, 3000, {trailing: true, maxWait: 10000})
                }
            }
        </script>
    @endpush
</div>
