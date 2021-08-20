<div x-data="alpNewSource()" class="h-full mx-5 grid border rounded relative">
    <div>
        <select name="sourceType" id="sourceType" wire:model="selectedType"
                class=" mt-1 py-1 focus:outline-none"
        >
            @foreach ($types as $type)
                <option value="{{ $type->code }}">{{ $type->label }}</option>
            @endforeach
        </select>
        <hr class="border my-1">
    </div>
    <ul class="list-disc list-inside overflow-y-auto  overflow-hidden px-2 " wire:loading.class.remove="overflow-y-auto" wire:loading.class="">
        @forelse ($types[$selectedType]->attributes as $attribute)
            <li>{{ $attribute->code }}</li>
        @empty
            <li>Sin attributos</li>
        @endforelse
    </ul>
    <div class="absolute inset-0 bg-gray-200 opacity-40" wire:loading></div>
</div>
@once
@push('head-script')

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('alpNewSource', () => {
            return {
                active: false,
                handleEvent: function (event) {
                    if (event.type == 'source-select:solve') {
                        console.log('tab change desde source-new. this:', this)
                    }
                },
                init: function () {
                    // console.log('iniciando new-source')
                    document.addEventListener(
                        'source-select:tab-change',
                        (e) => e.detail == 'new' ? this.activate(): (this.active ? this.deactivate(): null)
                    )
                },
                activate: function () {
                    this.active = true;
                    document.addEventListener(
                        'source-select:solve',
                        this,
                        false
                    )
                },
                deactivate: function () {
                    // console.log('desactivando')
                    this.active = false;
                    document.removeEventListener('source-select:solve', this, false)
                }
            }
        })
    })
</script>

@endpush
@endonce
