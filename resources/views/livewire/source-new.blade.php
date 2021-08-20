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
    <ul class="overflow-y-auto overflow-hidden px-2 pb-2 " wire:loading.class.remove="overflow-y-auto" wire:loading.class="">
        @forelse ($types[$selectedType]->attributes as $attribute)
            <li>
                @switch($attribute->type)
                    @case('text')
                        <div class="flex flex-col mt-1">
                            <label for="{{$attribute->code}}" class=" flex-grow-0 text-gray-600 text-sm ml-1">
                                {{$attribute->label}}:
                            </label>
                            <input type="text" name="attribute.{{$attribute->code}}" id="input-{{$attribute->code}}"
                                   class=" flex-grow border px-2 py-1 rounded"
                            >
                        </div>
                        @break
                    @case('number')
                        <div class="flex flex-col mt-1">
                            <label for="{{$attribute->code}}" class=" flex-grow-0 text-gray-600 text-sm ml-1">
                                {{$attribute->label}}:
                            </label>
                            <input type="number" name="attribute.{{$attribute->code}}" id="input-{{$attribute->code}}"
                                    class=" flex-grow border px-2 py-1 rounded"
                            >
                        </div>
                        @break
                    @case('date')
                        <div class="flex flex-col mt-1">
                            <label for="{{$attribute->code}}" class=" flex-grow-0 text-gray-600 text-sm ml-1">
                                {{$attribute->label}}:
                            </label>
                            <input type="date" name="attribute.{{$attribute->code}}" id="input-{{$attribute->code}}"
                                    class=" flex-grow border px-2 py-1 rounded"
                            >
                        </div>
                        @break
                    @default
                        <div class="flex flex-col mt-1">
                            default: {{ $attribute->label }}
                        </div>
                @endswitch
            </li>
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
