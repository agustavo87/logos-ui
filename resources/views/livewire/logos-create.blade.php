@push('head-script')
<style>
.logos-container {
    min-height: 500px;
    margin-bottom: 50px;
}
</style>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('LogosWrapper', ($wire) => {
            return {
                title: $wire.entangle('article.title').defer,
                delta: $wire.entangle('article.delta').defer,
                meta: $wire.entangle('article.meta').defer,
                html: $wire.entangle('article.html').defer,
                transactionStatus: 'Ready',

                init: function () {
                    this.debounceSave = debounce(this.commitSave, 3000, {trailing: true, maxWait: 10000});
                },

                save: function (doDebounce = true) {
                    if (doDebounce) {
                        this.debounceSave();
                    } else {
                        this.commitSave()
                    }
                },

                commitSave: function () {
                    this.transactionStatus = "Saving..."
                    this.$wire.save().then(() => {this.transactionStatus = 'Saved!'});
                },

                debounceSave: null,

                handleQuillInput: function (event) {
                    this.delta = event.detail.delta();
                    this.html = event.detail.html();
                    this.meta = {key: 'value'};
                    this.transactionStatus = 'Modified'
                    this.save()
                },

                handleSimpleInput: function () {
                    this.transactionStatus = 'Modified'
                    this.save(false)
                }, 

                handleReferencesUpdate: function (envent) {
                    this.$wire.saveList(event.detail.list);
                },
            }
        })
    })
    </script>

@endpush
<div x-data="LogosWrapper($wire)">
    <div class="max-w-screen-md mx-auto mt-3 h-5">
         {{-- Status information --}}
        <span class=" ml-2 text-xs text-gray-400" x-text="transactionStatus"></span>
    </div>
    <div x-on:quill-input="handleQuillInput"
         x-on:references-updated="handleReferencesUpdate"
         class="flex flex-col mx-auto logos-container mt-1 max-w-screen-md"
    >
        {{-- Title --}}
        <input type="text" placeholder="Título" autocomplete="off"
               x-model="title" x-on:change="handleSimpleInput"
               class="ml-2 text-3xl font-bold mb-2 focus:outline-none text-gray-800"
        >

        {{-- Logos Editor --}}
        <x-logos :initial-delta="$article->delta" />

    </div>
    <div class="max-w-screen-md mx-auto mt-t mb-12">
        <h1 class="text-xl font-bold text-gray-700 mb-2">References</h1>
        <livewire:document-citations :article-id="$articleID" />
    </div>
</div>
