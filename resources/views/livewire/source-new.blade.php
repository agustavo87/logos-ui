<div x-data="alpNewSource()" x-ref="root"
     x-on:lw:message-change.window="loading = $event.detail.loading"
     class="h-full mx-5 grid border rounded relative"
>
{{-- @if ($errors->any())
    <div class="border border-red-500 bg-red-50 text-red-900 p-2">
        Erorres:
        <ul>
            @foreach ($errors->all() as $error)
            <li> {{ $error }} </li>
            @endforeach
        </ul>
    </div>
@endif --}}
{{-- Source Type Select and Key Section --}}

    <div>
        <select name="sourceType" id="sourceType" wire:model="selectedType"
                x-bind:disabled="loading"
                class="border-b border-blue-400 focus:outline-none font-medium mb-2 ml-2 mt-1 py-1 text-gray-800 text-sm"
        >
            @foreach ($sourceTypes as $type)
                <option value="{{ $type['code'] }}">{{ $type['label'] }}</option>
            @endforeach
        </select>
        <div class="flex flex-row gap-2 items-baseline ml-1 pb-1 px-2">
            <label for="source-key" class="flex-grow-0 text-gray-600 text-sm">{{ __('sources.key') }}</label>
            <div class="flex flex-col flex-grow">
                <input type="text" id="source-key" name="source-key"
                       x-bind:disabled="loading"
                       class="border flex-grow focus:outline-none px-2 py-1 rounded text-sm focus:border-blue-400"
                       value="{{$sourceKey}}"
                       autocomplete="off"
                       wire:change="computeKey($event.target.value)"
                >
                @error("sourceKey") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

{{-- Creators Sections --}}
    <div
        x-data="{open:false}"
        class="flex flex-col items-stretch"
    >
        <div class="bg-gray-100 flex items-center justify-between px-2 py-1 text-gray-500"
            >
            <div class="flex gap-2 px-1">
                <h3 class="font-semibold text-sm ">Creadores</h3>
                <button class="text-blue-500 text-xs hover:text-blue-600"
                    wire:click="addCreator"
                >
                    Agregar
                </button>
            </div>
            <button class="bg-gray-50 border p-1 rounded border-gray-300 hover:bg-blue-500 hover:text-white hover:border-blue-500 focus:outline-none "
                x-on:click="open = !open" x-cloak
            >
                <x-icons.chevron-down class="w-4 h-4 fill-current transition-transform ease-in-out duration-500"
                    x-bind:class="{'transform rotate-180': open}"
                />
            </button>
        </div>
        <div class="text-sm border-b overflow-hidden transition-all ease-in-out duration-500 "
             x-bind:style="{'max-height': open ?  $el.scrollHeight + 'px' : '0px'}" {{-- Se puede agregar una longitud máxima por si el scrollHeight llega a ser muy grande --}}
        >
        <ul class="py-1 px-3"  >
                @foreach ($creators as $i => $creator)
                    <li wire:key="{{$i}}">
                                <div
                                class="flex py-1 items-center"
                                x-data="personEditor">
                                    <form x-on:submit.prevent="commitChange" class="flex flex-row gap-1" x-show="isEditing" x-on:keyup.enter="handleChange($dispatch)"  >
                                        <input type="text" wire:model.defer="creators.{{$i}}.attributes.lastName" class="px-2 border-b border-gray-100  focus:outline-none focus:border-blue-500">
                                        <input type="text" wire:model.defer="creators.{{$i}}.attributes.name" class="px-2 border-b border-gray-100 focus:outline-none focus:border-blue-500">
                                        <button type="submit" class="h-6 w-6 leading-none border text-blue-900 border-blue-500 rounded hover:bg-blue-500 hover:text-white flex items-center justify-center cursor-pointer">
                                            ok
                                        </button type="submit">
                                        <button x-on:click="removeCreator({{$i}})" class="h-6 w-6 leading-none border text-red-900 border-red-500 rounded hover:bg-red-500 hover:text-white flex items-center justify-center cursor-pointer">
                                            x
                                        </button>
                                    </form>
                                    <div x-show="!isEditing" x-on:click="isEditing=true" class="cursor-pointer hover:bg-blue-50 px-1 rounded-md italic">
                                        <span>{{$creator['attributes']['lastName']}}</span>, <span>{{$creator['attributes']['name']}}</span>
                                    </div>
                                </div>

                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Attributes Section --}}
    <ul class="overflow-y-auto overflow-hidden px-2 pb-2 " wire:loading.class.remove="overflow-y-auto">
        @forelse ($sourceTypes[$selectedType]['attributes'] as $order => $attribute)
            <li>
                @switch($attribute['type'])
                    @case('text')
                        <div class="flex flex-col mt-2">
                            <label for="{{$attribute['code']}}" class=" flex-grow-0 text-gray-600 text-sm ml-1">
                                {{$attribute['label']}}:
                            </label>
                            @if ($attribute['code'] == "abstractNote")
                                <textarea name="attribute.{{$attribute['code']}}" id="input-{{$attribute['code']}}" rows="4"
                                          wire:model.lazy="attributes.{{ $attribute['code'] }}"
                                          x-bind:disabled="loading"
                                          class=" flex-grow border px-2 py-1 rounded text-sm resize-none focus:outline-none focus:border-blue-400"
                                ></textarea>
                                @error("attributes.{$attribute['code']}") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            @else
                                <input type="text" name="attribute.{{$attribute['code']}}" id="input-{{$attribute['code']}}"
                                       wire:model.lazy="attributes.{{ $attribute['code'] }}"
                                       class=" flex-grow border text-sm px-1 py-1 rounded focus:outline-none focus:border-blue-400"
                                       autocomplete="off"
                                       x-bind:disabled="loading"
                                >
                                @error("attributes.{$attribute['code']}") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            @endif
                        </div>
                        @break
                    @case('number')
                        <div class="flex flex-col mt-2">
                            <label for="{{$attribute['code']}}" class=" flex-grow-0 text-gray-600 text-sm ml-1">
                                {{$attribute['label']}}:
                            </label>
                            <input type="number" name="attribute.{{ $attribute['code'] }}" id="input-{{ $attribute['code'] }}"
                                   wire:model.lazy="attributes.{{ $attribute['code'] }}"
                                   x-bind:disabled="loading"
                                   class=" flex-grow border text-sm px-1 py-1 rounded focus:outline-none focus:border-blue-400"
                                   autocomplete="off"
                            >
                            @error("attributes.{$attribute['code']}") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        @break
                    @case('date')
                        <div class="flex flex-col mt-2">
                            <label for="{{$attribute['code']}}" class=" flex-grow-0 text-gray-600 text-sm ml-1">
                                {{$attribute['label']}}:
                            </label>
                            <input type="date" name="attribute.{{$attribute['code']}}" id="input-{{$attribute['code']}}"
                                   wire:model.lazy="attributes.{{ $attribute['code'] }}"
                                   x-bind:disabled="loading"
                                   class=" flex-grow border text-sm px-1 py-1 rounded focus:outline-none focus:border-blue-400"
                            >
                            @error("attributes.{$attribute['code']}") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        @break
                    @default
                        <div class="flex flex-col mt-2">
                            default: {{ $attribute['label'] }}
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
                loading:false,
                creator: {
                    type: 'person',
                    attributes: {
                        name: "Pepito",
                        lastName: "Murundanga"
                    }
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

                    this.$watch('creator', value => console.log(value));
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
                commitChange: function() {
                    this.isEditing = false;
                    this.$wire.changeCreator()
                },
                removeCreator: function (i) {
                    this.isEditing = false;
                    this.$wire.removeCreator(i);
                }
            }
        })
    })
</script>

@endpush
@endonce
