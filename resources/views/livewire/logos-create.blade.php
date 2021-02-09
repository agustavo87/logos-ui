@push('head-script')
<style>
.logos-container {
    min-height: 500px;
    margin-bottom: 50px;
}
</style>

@endpush
<div x-data="getLogos()" >
    <div class="flex flex-row items-center max-w-screen-md mx-auto mt-3 h-5">
        <span class=" ml-2 text-xs text-gray-400" x-text="transactionStatus"></span>
    </div>
    <div x-init="init" @quill-input="handleInput"  class="flex flex-col mx-auto logos-container mt-1 max-w-screen-md">
        <input type="text" wire:model.defer="article.title" @input="simpleInput" placeholder="Título" autocomplete="off" class="ml-2 text-3xl font-bold mb-2 focus:outline-none text-gray-800">
        <x-logos :initial-delta="$article->delta" />
        <input type="hidden" x-ref="delta" wire:model.defer="article.delta">
        <input type="hidden" x-ref="html" wire:model.defer="article.html">
        <input type="hidden" x-ref="meta" wire:model.defer="article.meta">
    </div>
    @push('foot-script')
        <script>
            function getLogos() {
                return {
                    transactionStatus: 'Listo',
                    handleInput: function (event) {
                        this.$refs.delta.dispatchEvent(new CustomEvent('input', {
                            detail: event.detail.delta(),
                            bubbles: false
                        }))
                        this.$refs.html.dispatchEvent(new CustomEvent('input', {
                            detail: event.detail.html(),
                            bubbles: false
                        }))
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
                    }, 3000, {trailing: true, maxWait: 10000}),
                    init: function () {
                        // this.dSave = debounce(() => {}), 2000, {
                        //     trailing: true, 
                        //     maxWait:5000
                        // })
                    }
                }
            }
        </script>
    @endpush
    


</div>
