@push('head-script')
<style>
.logos-container {
    min-height: 500px;
    margin-bottom: 50px;
}
</style>

@endpush
<div x-data="LogosWrapper($wire)">
    <div class="max-w-screen-md mx-auto mt-3 h-5">
         {{-- Status information --}}
        <span class=" ml-2 text-xs text-gray-400" x-text="transactionStatus"></span>
    </div>
    <div x-on:quill-input="handleQuillInput"
         class="flex flex-col mx-auto logos-container mt-1 max-w-screen-md"
    >
        {{-- Title --}}
        <input type="text" placeholder="Título" autocomplete="off"
               x-model="title" x-on:input="handleSimpleInput"
               class="ml-2 text-3xl font-bold mb-2 focus:outline-none text-gray-800"
        >
        {{-- Logos Editor --}}
        <x-logos :initial-delta="$article->delta" />

    </div>
    <h3 class=" text-xl font-semibold">Livewire</h3>
    <strong>Meta:</strong>
    <code>
        {{ json_encode($article->meta) }}
    </code>
    <strong>Delta:</strong>
    <code>
        {{ json_encode($article->delta)}}
    </code>


@push('head-script')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('LogosWrapper', ($wire) => {
        return {
            title: $wire.entangle('article.title').defer,
            delta: $wire.entangle('article.delta').defer,
            meta: $wire.entangle('article.meta').defer,
            html: $wire.entangle('article.html').defer,
            transactionStatus: 'Listo',

            handleQuillInput: function (event) {
                this.delta = event.detail.delta();
                this.html = event.detail.html();
                this.meta = {key: 'value'};
                this.transactionStatus = 'Modificado'
                this.save()
            },

            handleSimpleInput: function () {
                this.transactionStatus = 'Modificado'
                this.save()
            },

            save: debounce(function () {
                this.transactionStatus = "Guardando..."
                this.$wire.save().then(() => {this.transactionStatus = 'Guardado'});
            }, 3000, {trailing: true, maxWait: 10000})
        }
    })
})
</script>
@endpush
</div>
