
<div x-data="alpNewSource()" x-ref="root"
     x-on:lw:message-change.window="loading = $event.detail.loading"
     x-on:add-creator="handleAddCreator($event, $dispatch)"
     class="h-full mx-5 grid border rounded relative"
>
    {{-- Creators Selection --}}

     <div class=" absolute bottom-0 right-0"
        x-data="creatorsHint"
        x-on:hint-updated.window = "newHints($event)"
        x-ref="root"
        x-show="visible"
        x-on:creator-blur.window="visible = false"
        x.on:source-select:reset.window="deactivate"
        x-transition:enter.duration.20ms
        x-transition:leave.duration.100ms
        x-transition.opacity

     >
        <input type="hidden" wire:model.debounce.500ms="creatorSuggestionParams.hint"
            class="px-2 py-1 rounded border w-full"

        >
        <ul class="h-36 rounded border border-blue-200 bg-gray-50 text-xs p-1 overflow-y-auto overflow-x-hidden" >
            @foreach ($creatorSuggestions as $suggestion)
                <li>
                    <button class="hover:bg-blue-100 hover:text-blue-800 rounded p-1 cursor-pointer w-full text-left">
                        {{" {$suggestion['lastName']}, {$suggestion['name']} "}}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- / CreatorsSelection --}}

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
    <div class="flex flex-col items-stretch">
        <div class="bg-gray-100 flex items-center justify-between px-2 py-1 text-gray-500">
            <div class="flex gap-2 px-1">
                <h3 class="font-semibold text-sm ">Creadores</h3>
                <button class="text-blue-600 text-xs hover:text-blue-500"
                        x-on:click="$dispatch('add-creator')"
                >
                    Agregar
                </button>
            </div>
            <button class="bg-gray-50 border p-1 rounded border-gray-300 hover:bg-blue-500 hover:text-white hover:border-blue-500 focus:outline-none "
                    x-on:click="openCreators = !openCreators" x-cloak
            >
                <x-icons.chevron-down class="w-4 h-4 fill-current transition-transform ease-in-out duration-500"
                                      x-bind:class="{'transform rotate-180': openCreators}"
                />
            </button>
        </div>
        <div class="text-sm border-b overflow-hidden transition-all ease-in-out duration-500 "
             x-ref="creators"
             x-bind:style="{'max-height': openCreators ?  $el.scrollHeight + 'px' : '0px'}" {{-- Se puede agregar una longitud máxima por si el scrollHeight llega a ser muy grande --}}
        >
            <ul x-data="creatorsList({creators: logosCreators})" class="py-1 px-3" wire:ignore
                x-on:remove-creator="handleRemoveCreator($event, $dispatch)"
                x-on:add-creator.window="handleAddCreator($event, $dispatch)"
            >
                <template x-for="creator in creators" x-bind:key="creator.id">
                    <li>
                        <div class="flex py-1 items-center"
                             x-data="personInput({creator:creator})"
                            {{-- setTimeout 500 porque hay que esperar que termine la transición --}}
                             x-on:creator-added.window = "$event.detail.creator.id == myperson.id ? editNew: null"
                             x-ref="root"
                        >
                            <div  class="flex flex-row gap-1 w-full justify-between" x-show="isEditing" x-on:keyup.enter="isEditing = false"  >
                                <input type="text" class="px-2 border-b border-gray-100  focus:outline-none focus:border-blue-500"
                                       x-model="myperson.attributes.lastName"
                                       x-ref="lastName"
                                       x-on:blur="$dispatch('creator-blur')"
                                >
                                <input type="text" class="px-2 border-b border-gray-100 focus:outline-none focus:border-blue-500"
                                    x-model="myperson.attributes.name"
                                    x-on:blur="$dispatch('creator-blur')"
                                >
                                <button x-on:click="isEditing = false" class="h-6 w-6 leading-none border text-blue-900 border-blue-500 rounded hover:bg-blue-500 hover:text-white flex items-center justify-center cursor-pointer">
                                    ok
                                </button>
                                {{-- <button x-on:click="$dispatch('remove-creator', {creator: creator})" class="h-6 w-6 leading-none border text-red-900 border-red-500 rounded hover:bg-red-500 hover:text-white flex items-center justify-center cursor-pointer"> --}}
                                <button x-on:click="$dispatch('remove-creator', {creator: creator})" class="h-6 w-6 leading-none border text-red-900 border-red-500 rounded hover:bg-red-500 hover:text-white flex items-center justify-center cursor-pointer">
                                    x
                                </button>
                            </div>
                            <div x-show="!isEditing" x-on:click="isEditing=true" class="cursor-pointer hover:bg-blue-50 px-1 rounded-md italic">
                                <span x-text="myperson.attributes.lastName"></span>, <span x-text="myperson.attributes.name"></span>
                            </div>
                        </div>
                    </li>
                </template>
            </ul>
        </div>
    </div>
    {{-- / Creators Sections --}}



    {{-- Attributes Section --}}
    <ul class="overflow-y-auto overflow-hidden px-2 pb-2 "
        {{-- wire:loading.class.remove="overflow-y-auto" --}}
    >
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
                                          wire:model.defer="attributes.{{ $attribute['code'] }}"
                                          x-bind:disabled="loading"
                                          class=" flex-grow border px-2 py-1 rounded text-sm resize-none focus:outline-none focus:border-blue-400"
                                ></textarea>
                                @error("attributes.{$attribute['code']}") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            @else
                                <input type="text" name="attribute.{{$attribute['code']}}" id="input-{{$attribute['code']}}"
                                       wire:model.defer="attributes.{{ $attribute['code'] }}"
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
                                   wire:model.defer="attributes.{{ $attribute['code'] }}"
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
                                   wire:model.defer="attributes.{{ $attribute['code'] }}"
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
        {{-- Loading Overlay --}/}
        <div class="absolute inset-0 bg-gray-200 opacity-40"
            x-data="{loading:false}"
            x-show="loading"
            x-on:lw:message-change.window="console.log('loading: ', $event.detail);loading = $event.detail.loading"
        ></div>
        {{-- / Loading Overlay --}}
    </ul>
</div>
@once
@push('head-script')

<script>
    let logosCreators = @json($creators, JSON_PRETTY_PRINT);
</script>

<script>
    document.addEventListener('alpine:init', () => {
        // Alpine.data('alpNewSource', (options) => {
        Alpine.data('alpNewSource', () => {
            return {
                active: false,
                loading:false,
                openCreators: false,
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
                    document.addEventListener(
                        'source-select:tab-change',
                        (e) => e.detail == 'new' ? this.activate(): (this.active ? this.deactivate(): null)
                    )
                },
                activate: function () {
                    this.active = true;
                    this.$refs.creators.classList.add('transition-all', 'duration-500')
                    document.addEventListener(
                        'source-select:solve',
                        this,
                        false
                    )
                },
                deactivate: function () {
                    this.$refs.creators.classList.remove('transition-all', 'duration-500')
                    this.$nextTick(() => {
                        this.openCreators = false
                        this.active = false
                        document.removeEventListener('source-select:solve', this, false)
                    })
                },
                handleAddCreator: function(e, dispatch) {
                    this.$nextTick(() => {
                            this.openCreators = true;
                            this.$refs.creators.style.maxHeight = this.$refs.creators.scrollHeight + 'px'
                        }
                    )
                }
            }
        })

        Alpine.data('creatorsList', (options) => {
            return {
                creators: options.creators,
                newCreators: 0,
                handleAddCreator: function(e, dispatch) {
                    let index = this.creators.push({
                        id: ++this.newCreators,
                        type: 'person',
                        attributes: {
                            name: '',
                            lastName: ''
                        }

                    }) - 1;
                    this.$nextTick(() => dispatch('creator-added', {creator: this.creators[index]}))
                },
                handleRemoveCreator: function (event, dispatch) {
                    let index = this.creators.findIndex((c) => c.id == event.detail.creator.id)
                    console.log('removiendo creator id' + event.detail.creator.id + ' index : ', index)
                    this.$nextTick(() => this.creators.splice(index, 1))
                }
            }
        })

        Alpine.data('personInput', (options) => {
            return {
                myperson: options.creator,
                isEditing: false,
                init: function () {
                    this.$watch('myperson.attributes.lastName', (value) => this.emitCreatorInput('lastName', value))
                    this.$watch('myperson.attributes.name', (value) => this.emitCreatorInput('name', value))
                },
                editNew: function () {
                    this.isEditing = true;
                    window.setTimeout(() => this.$refs.lastName.focus(), 500)
                },
                emitCreatorInput: function (attribute, value) {
                    this.$wire.creatorInput('person', attribute, value)
                        .then((result) => {
                            this.$refs.root.dispatchEvent(new CustomEvent('hint-updated', {
                                bubbles: true,
                                detail: {
                                    type: 'person',
                                    attribute: attribute,
                                    value: value
                                }
                            }))
                        })
                }
            }
        })

        Alpine.data('creatorsHint', function () {
            return {
                visible: false,
                newHints: function (event) {
                    let margin= 8
                    this.$refs.root.style.top = event.target.offsetTop + event.target.offsetHeight + margin + 'px'
                    this.$refs.root.style.left = event.target.offsetLeft + 'px'
                    this.$refs.root.style.width = event.target.clientWidth + 'px'
                    this.visible = true;
                }
            }
        })
    })
</script>

@endpush
@endonce
