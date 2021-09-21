
<div x-data="alpNewSource" x-ref="root"
     x-on:lw:message-change.window="loading = $event.detail.loading"
     x-on:add-creator="handleAddCreator($event, $dispatch)"
     class="h-full mx-5 grid border rounded relative"
>
    {{-- Creators Selection --}}

     <div class=" absolute bottom-0 right-0"
        x-data="creatorsHint({creators: @entangle('creatorSuggestions').defer })"
        x-on:hint-updated.window = "newHints($event)"
        x-ref="root"
        x-show="visible"
        x-on:creator-blur.window="decideHidding"
        x-on:click.outside="decideHidding"
        x-transition:enter.duration.20ms
        x-transition:leave.duration.100ms
        x-transition.opacity
     >
        <input type="hidden" wire:model.debounce.500ms="creatorSuggestionParams.hint"
            class="px-2 py-1 rounded border w-full"

        >
        <ul class="h-36 rounded border border-blue-200 bg-gray-50 text-xs p-1 overflow-y-auto overflow-x-hidden"
            x-on:mouseover="mouseover = true"
            x-on:mouseleave="mouseover = false"
        >
           <template x-for="suggestion in creators" x-bind:key="suggestion.id">
                <li>
                    <button class="hover:bg-blue-100 hover:text-blue-800 rounded p-1 cursor-pointer w-full text-left"
                        x-on:click="acceptSugestion(suggestion.id, $dispatch)"
                    >
                        <span x-text="suggestion.attributes.lastName + ', ' + suggestion.attributes.name"></span>
                    </button>
                </li>
           </template>
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
        <select
        x-data="selectSourceType({type: @entangle('selectedType').defer })"
        x-model="type" class="font-medium p-2 rounded text-xs focus:outline-none cursor-pointer hover:text-blue-900">
            <template x-for="sType in $store.sourceTypes.list">
                <option
                x-bind:value="sType.code"
                x-text="sType.label"
                x-bind:selected="sType.code == type">
                </option>
            </template>
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
        <div
        x-ref="creators"
        x-bind:style="{'max-height': openCreators ?  $el.scrollHeight + 'px' : '0px'}" {{-- Se puede agregar una longitud máxima por si el scrollHeight llega a ser muy grande --}}
        class="text-sm border-b overflow-hidden transition-all ease-in-out duration-500 "
        >

        {{-- Creators List --}}
            <ul x-data="creatorsList({
                creators: logosCreators
            })"
            x-on:remove-creator="handleRemoveCreator($event, $dispatch)"
            x-on:add-creator.window="handleAddCreator($event, $dispatch)"
            x-effect="roles = $store.sourceTypes.roles($store.sourceTypes.selected)"
            wire:ignore
            class="py-1 px-3"
            >
                <template x-for="(creator, index ) in creators" x-bind:key="creator.i">
                    <li>
                        {{-- Person Input --}}
                        <div
                        x-data="personInput({creator:creator})"
                        x-on:creator-added.window = "$event.detail.creator.i == myperson.i ? editNew: null"
                        x-on:suggestion-acepted.window="handleSuggestion($event)"
                        x-ref="root"
                        class="flex py-1 items-center"
                        >
                            <div  class="flex flex-row gap-1 w-full justify-between" x-show="isEditing" x-on:keyup.enter="isEditing = false"

                             >
                             <div class="flex flex-row gap-1">
                                <input type="text" class="px-2 border-b border-gray-100 w-2/5 focus:outline-none focus:border-blue-500"
                                x-bind:value="myperson.attributes.lastName"
                                x-on:input="creatorInput('lastName', $event.target.value)"
                                x-ref="lastName"
                                x-on:blur="$dispatch('creator-blur')"
                                x-bind:data-i="myperson.i"
                                >
                                <input type="text" class="px-2 w-2/5 border-b border-gray-100 focus:outline-none focus:border-blue-500"
                                    x-bind:value="myperson.attributes.name"
                                    x-on:input="creatorInput('name', $event.target.value)"
                                    x-on:blur="$dispatch('creator-blur')"
                                    x-bind:data-i="myperson.i"
                                >
                                <select class="border ml-1 rounded text-xs focus:outline-none"
                                    x-model="myperson.role"
                                >
                                    <template x-for="role in roles">
                                        <option x-bind:value="role.code" x-text="role.label" x-bind:selected="role.code == validRole"></option>
                                    </template>
                                </select>
                             </div>

                                <div class="flex flex-row gap-1 ml-2">
                                    <button x-on:click="isEditing = false" class="h-5 w-5 text-xs font-bold leading-none border text-blue-900 border-blue-500 rounded-full hover:bg-blue-500 hover:text-white flex items-center justify-center cursor-pointer focus:outline-none">
                                        &#10003;
                                    </button>
                                    {{-- <button x-on:click="$dispatch('remove-creator', {creator: creator})" class="h-6 w-6 leading-none border text-red-900 border-red-500 rounded hover:bg-red-500 hover:text-white flex items-center justify-center cursor-pointer"> --}}
                                    <button x-on:click="$dispatch('remove-creator', {creator: creator})" class="h-5 w-5 text-xs font-bold leading-none border text-red-900 border-red-500 rounded-full hover:bg-red-500 hover:text-white flex items-center justify-center cursor-pointer focus:outline-none">
                                        &#10005;
                                    </button>
                                </div>
                            </div>
                            <div class="align-middle cursor-pointer flex hover:bg-blue-50 italic justify-between rounded-full w-full"
                                x-show="!isEditing" x-on:click="isEditing=true"

                            >
                                <div class="flex items-center ml-1">
                                    <div>
                                        <span x-text="myperson.attributes.lastName"></span>, <span x-text="myperson.attributes.name"></span>
                                    </div>
                                    <span class="bg-gray-400 flex h-5 leading-4 ml-2 px-2 rounded-full text-white text-xs"
                                        x-text="validRole(roles).label"
                                        {{-- Mejorar esto para que en caso de no estar disponible el rol primario sea utilizado provisto por validRole --}}
                                    ></span>
                                </div>
                                <div>
                                    <button class="text-white rounded-full m-1 bg-blue-400 hover:bg-white hover:text-blue-500 h-5 w-5 flex align-middle justify-center focus:outline-none"
                                        x-on:click.stop="moveUp(creator.i)"
                                        x-show="index > 0"
                                    >
                                        &uarr;
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>
                </template>
            </ul>
        </div>
    </div>
    {{-- / Creators Sections --}}



    {{-- Attributes Section --}}
    <ul
    x-data="sourceAttributes"
    class="overflow-y-auto overflow-hidden px-2 pb-2 "
    >
    <template x-for="attribute in $store.sourceTypes.attributes">
        <li>
            <div class="flex flex-col mt-2">
                <label
                x-bind:for="'attribute.' + attribute.code"
                x-text="attribute.label"
                class=" flex-grow-0 text-gray-600 text-sm ml-1"
                ></label>
                <input
                x-bind:type="type(attribute.type)"
                x-bind:name="'attribute.' + attribute.code"
                x-bind:id="'input-' + attribute.code"
                x-show="attribute.code != 'abstractNote'"
                x-bind:value="$store.source.attributes[attribute.code] ? $store.source.attributes[attribute.code] : ($store.source.attributes[attribute.base] ? $store.source.attributes[attribute.base] : null)"
                x-on:input="$store.source.attributes[attribute.code] = $event.target.value"
                class=" flex-grow border text-sm px-1 py-1 rounded focus:outline-none focus:border-blue-400"
                >
                <textarea
                x-bind:name="'attribute.' + attribute.code"
                x-bind:id="'input-' + attribute.code"
                x-on:input="$store.source.attributes[attribute.code] = $event.target.value"
                x-bind:value="$store.source.attributes[attribute.code] ? $store.source.attributes[attribute.code] : ($store.source.attributes[attribute.base] ? $store.source.attributes[attribute.base] : null)"
                x-show="attribute.code == 'abstractNote'"
                rows="4"
                class=" flex-grow border px-2 py-1 rounded text-sm resize-none focus:outline-none focus:border-blue-400"
                ></textarea>
            </div>
        </li>
    </template>
    {{--
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
        --}}

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
    let logosRoles = @json($logosRoles, JSON_PRETTY_PRINT);
    let logosSourceTypes = @json($sourceTypes, JSON_PRETTY_PRINT);
</script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('sourceTypes', {
            list: @json($sourceTypes, JSON_PRETTY_PRINT),
            selected: null,
            attributes: {},
            init: function () {
                this.updateAttributes()
            },
            updateAttributes: function () {
                this.attributes = Object.values(this.list[this.selected ? this.selected : 'journalArticle'].attributes)
            },
            updateSelected: function (sType) {
                this.selected = sType;
                this.updateAttributes();
            },
            roles: function (sourceType) {
                return this.list[sourceType].roles
            },
        })
        Alpine.store('source', {
            attributes: {}
        })

        Alpine.data('alpNewSource', () => {
            return {
                active: false,
                loading:false,
                openCreators: false,
                handleEvent: function (event) {
                    if (event.type == 'source-select:solve') {
                        this.$wire.save(this.$store.source)
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
            let i = 1;
            options.creators.forEach(creator => creator.i = i++)
            // console.log('procesed creators: ',  options.creators)
            // console.log('source types: ', options.sourceTypes)

            return {
                i:i,
                creators: options.creators,
                // selectedType: options.selectedType,
                roles: {},
                init: function () {
                    // this.updateRoles()
                    // this.$watch('selectedType', () => this.updateRoles())
                },
                updateRoles: function () {
                    // this.roles = this.$store.sourceTypes.roles(this.selectedType)
                    // console.log('actualizando roles: ', this.roles)
                },
                moveUp: function (i) {
                    let tempCreators = JSON.parse(JSON.stringify(this.creators))
                    let index = tempCreators.findIndex((person) => person.i == i)
                    let movingCreator = tempCreators.splice(index,1)[0]
                    tempCreators.splice(index - 1, 0,movingCreator)
                    this.creators = tempCreators;
                },
                handleAddCreator: function(e, dispatch) {
                    let index = this.creators.push({
                        i: i++,
                        id: null,
                        type: 'person',
                        attributes: {
                            name: '',
                            lastName: ''
                        }

                    }) - 1;
                    this.$nextTick(() => dispatch('creator-added', {creator: JSON.parse(JSON.stringify(this.creators[index]))}))
                },
                handleRemoveCreator: function (event, dispatch) {
                    let index = this.creators.findIndex((c) => c.i == event.detail.creator.i)
                    console.log('removiendo creator i' + event.detail.creator.i + ' index : ', index)
                    this.$nextTick(() => this.creators.splice(index, 1))
                }
            }
        })

        Alpine.data('personInput', (options) => {
            return {
                myperson: options.creator,
                // In case the saved role is not available on current source type roles.
                validRole: function (roles) {
                    return roles[this.myperson.role] ?? Object.values(roles).find(role => role.primary)
                },
                isEditing: false,
                creatorInput: function(attr, value) {
                    console.log('creator input', attr, value)
                    this.myperson.attributes[attr] = value
                    this.emitCreatorInput('lastName', value)
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
                                    value: value,
                                    creator: JSON.parse(JSON.stringify(this.myperson))
                                }
                            }))
                        })
                },
                handleSuggestion:function ($event) {
                    if ($event.detail.client.i == this.myperson.i) {
                        let creator = event.detail.creator;
                        console.log('el mismo origen: ', creator)
                        this.myperson.attributes.name  = creator.attributes.name
                        this.myperson.attributes.lastName  = creator.attributes.lastName
                        this.myperson.id = creator.id
                    }
                }
            }
        })

        Alpine.data('creatorsHint', function (options) {
            return {
                lastCreator: null,
                visible: false,
                mouseover: false,
                creators: options.creators,
                acceptSugestion: function(id, $dispatch) {
                    $dispatch('suggestion-acepted', {
                        creator: JSON.parse(JSON.stringify(this.creators[id])),
                        client: JSON.parse(JSON.stringify(this.lastCreator))
                    })
                },
                newHints: function (event) {
                    let margin= 8
                    this.$refs.root.style.top = event.target.offsetTop + event.target.offsetHeight + margin + 'px'
                    this.$refs.root.style.left = event.target.offsetLeft + 'px'
                    this.$refs.root.style.width = event.target.clientWidth + 'px'
                    this.visible = true;
                    this.lastCreator = event.detail.creator
                },
                decideHidding: function () {
                    if (this.visible && !this.mouseover) {
                        this.$nextTick(() => {
                            if (!(document.activeElement.dataset.i == this.lastCreator.i)) {
                                this.visible = false
                            }
                        })
                    }
                }
            }
        })

        Alpine.data('selectSourceType', function (options) {
            return {
                type: options.type,
                init: function () {
                    this.shareType()
                    this.$watch('type', () => this.shareType())
                },
                shareType: function () {
                    this.$store.sourceTypes.updateSelected(this.type)
                }
            }
        })

        Alpine.data('sourceAttributes', () => {
            return {
                attributes: {},
                type: function(typeCode) {
                    switch (typeCode) {
                        case 'text':
                            return 'text'
                        case 'number':
                            return 'number'
                        case 'date':
                            return 'date'
                        default:
                            return 'text'
                            break;
                    }
                }
            }
        })
    })

</script>

@endpush
@endonce
