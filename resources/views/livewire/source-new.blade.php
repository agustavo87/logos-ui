<div x-data="alpNewSource()" x-ref="root"
     class="h-full mx-5 grid border rounded relative"
>
{{-- Source Type Select and Key Section --}}
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
    </div>

{{-- Creators Sections --}}
    <div
        x-data="{open:false}"
        class="flex flex-col items-stretch"
    >
        <div class="px-2 py-1 bg-gray-100 text-gray-800 flex justify-between items-center"
            >
            <div class="flex gap-2 px-1">
                <h3 class="font-semibold text-sm ">Creadores</h3>
                <button class="text-blue-500 text-xs hover:text-blue-600">Agregar</small>
            </div>
            <button class="bg-gray-50 border p-1 rounded border-blue-200 hover:bg-blue-500 hover:text-white hover:border-blue-500 focus:outline-none "
                x-on:click="open = !open"  x-cloak
            >
                <x-icons.chevron-down class="w-4 h-4 fill-current transition-transform ease-in-out duration-500"
                    x-bind:class="{'transform rotate-180': open}"
                />
            </button>
        </div>
        <div class="text-sm border-b overflow-hidden transition-all ease-in-out duration-500 "
            x-bind:style="{'max-height': open ?  $el.scrollHeight + 'px' : '0px'}"
        >
            <ul class="py-1 px-3"  >
                <li x-model="creator">
                    <div x-data="personEditor" class="flex py-1 items-center">
                        <div class="flex flex-row gap-1" x-show="isEditing" x-on:keyup.enter="handleChange($dispatch)"  >
                            <input type="text" x-model="attributes.lastName" x-on:input.stop class="px-2 border-b border-gray-100  focus:outline-none focus:border-blue-500">
                            <input type="text" x-model="attributes.name" x-on:input.stop class="px-2 border-b border-gray-100 focus:outline-none focus:border-blue-500">
                            <div x-on:click="handleChange($dispatch)" class="h-6 w-6 leading-none border text-blue-900 border-blue-500 rounded hover:bg-blue-500 hover:text-white flex items-center justify-center cursor-pointer">ok</div>
                        </div>
                        <div x-show="!isEditing" x-on:click="isEditing=true" class="cursor-pointer hover:bg-blue-50 px-1 rounded-md italic">
                            <span x-text="attributes.lastName"></span>, <span x-text="attributes.name"></span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    {{-- Attributes Section --}}
    <ul class="overflow-y-auto overflow-hidden px-2 pb-2 " wire:loading.class.remove="overflow-y-auto">
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
                creator: {
                    name: "Pepito",
                    lastName: "Murundanga"
                },
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

        Alpine.data('personEditor', () => {
            return {
                isEditing: false,
                attributes: {
                    name:'',
                    lastName: ''
                },
                handleChange: function($dispatch) {
                    this.isEditing = false;
                    $dispatch('input', Object.assign({}, this.attributes))
                }
            }
        })
    })
</script>

@endpush
@endonce
