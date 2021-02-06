@push('head-script')
<style>
.logos-container {
    min-height: 500px;
    margin-bottom: 50px;
}
</style>

@endpush
<div>
    <button wire:click="save" class=" text-blue-500 underline focus:outline-none"> Guardar </button>
    <div wire:loading>
        <div   class=" flex items-center content-center mt-1" >
            <span class="ring-loader-xs"></span>
            <span class=" ml-2 text-xs text-gray-600">{{ ucfirst(__('ui.processing')) }}...</span>
        </div>
    </div>
    <div x-data="getLogos()" @quill-input="handleInput"  class="flex flex-col mx-auto logos-container mt-3 max-w-screen-md">
        <input type="text" wire:model.defer="article.title" placeholder="Título" autocomplete="off" class="ml-2 text-3xl font-bold mb-2 focus:outline-none text-gray-800">
        <x-logos :initial-delta="$article->delta" />
        <input type="hidden" x-ref="delta" wire:model.defer="delta">
        <input type="hidden" x-ref="html" wire:model.defer="html">
    </div>
    @push('foot-script')
        <script>
            function getLogos() {
                return {
                    handleInput: function (event) {
                        this.$refs.delta.dispatchEvent(new CustomEvent('input', {
                            detail: event.detail.delta()
                        }))
                        this.$refs.html.dispatchEvent(new CustomEvent('input', {
                            detail: event.detail.html()
                        }))
                    }
                }
            }
        </script>
    @endpush
    


</div>
