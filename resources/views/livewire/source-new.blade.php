<div x-data="alpNewSource()" x-ref="root"
     class="h-full mx-5 grid border rounded relative"
>
    <div>
        <select name="sourceType" id="sourceType" wire:model="selectedType"
                class=" mt-1 py-1 ml-2 text-sm focus:outline-none"
        >
            @foreach ($types as $type)
                <option value="{{ $type->code }}">{{ $type->label }}</option>
            @endforeach
        </select>
        <div class="flex flex-row gap-2 items-baseline ml-1 pb-1 px-2">
            <label for="source-key" class="flex-grow-0 text-gray-600 text-sm">Clave</label>
            <input type="text" id="source-key" name="source-key"
                   class="border flex-grow focus:outline-none px-2 py-1 rounded text-sm focus:border-blue-400"
                   value="{{$sourceKey}}"
                   autocomplete="off"
                   wire:change="computeKey($event.target.value)"
            >
        </div>
        <hr class="border my-1">
    </div>
    <ul class="overflow-y-auto overflow-hidden px-2 pb-2 " wire:loading.class.remove="overflow-y-auto" wire:loading.class="">
        @forelse ($types[$selectedType]->attributes as $attribute)
            <li>
                @switch($attribute->type)
                    @case('text')
                        <div class="flex flex-col mt-2">
                            <label for="{{$attribute->code}}" class=" flex-grow-0 text-gray-600 text-sm ml-1">
                                {{$attribute->label}}:
                            </label>
                            @if ($attribute->code == "abstractNote")
                                <textarea name="attribute.{{$attribute->code}}" id="input-{{$attribute->code}}" rows="4"
                                          wire:model.lazy="attributes.{{ $attribute->code }}"
                                          class=" flex-grow border px-2 py-1 rounded text-sm resize-none focus:outline-none focus:border-blue-400"
                                ></textarea>
                            @else
                                <input type="text" name="attribute.{{$attribute->code}}" id="input-{{$attribute->code}}"
                                       wire:model.lazy="attributes.{{ $attribute->code }}"
                                       class=" flex-grow border text-sm px-1 py-1 rounded focus:outline-none focus:border-blue-400"
                                       autocomplete="off"
                                >
                            @endif
                        </div>
                        @break
                    @case('number')
                        <div class="flex flex-col mt-2">
                            <label for="{{$attribute->code}}" class=" flex-grow-0 text-gray-600 text-sm ml-1">
                                {{$attribute->label}}:
                            </label>
                            <input type="number" name="attribute.{{$attribute->code}}" id="input-{{$attribute->code}}"
                                   wire:model.lazy="attributes.{{ $attribute->code }}"
                                   class=" flex-grow border text-sm px-1 py-1 rounded focus:outline-none focus:border-blue-400"
                                   autocomplete="off"
                            >
                        </div>
                        @break
                    @case('date')
                        <div class="flex flex-col mt-2">
                            <label for="{{$attribute->code}}" class=" flex-grow-0 text-gray-600 text-sm ml-1">
                                {{$attribute->label}}:
                            </label>
                            <input type="date" name="attribute.{{$attribute->code}}" id="input-{{$attribute->code}}"
                                   wire:model.lazy="attributes.{{ $attribute->code }}"
                                   class=" flex-grow border text-sm px-1 py-1 rounded focus:outline-none focus:border-blue-400"
                            >
                        </div>
                        @break
                    @default
                        <div class="flex flex-col mt-2">
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
                        this.$wire.save()
                            .then(result => {
                                this.$refs.root.dispatchEvent(
                                    new CustomEvent(
                                        'source-new:save',
                                        {bubbles:true, detail:result}
                                    )
                                )
                            })
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
