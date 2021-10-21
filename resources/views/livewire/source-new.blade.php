<div x-data="alpNewSource({sourceID: @entangle('sourceID') })"
    x-ref="root"
    x-on:lw:message-change.window="loading = $event.detail.loading"
    x-on:add-participation="handleAddParticipation($event, $dispatch)"
    x-on:mount-source="mountSource($event.detail); $store.sourceTypes.updateAttributes()"
    class="h-full mx-5 flex flex-col border rounded relative"
>

    <x-errors wire:model="sharedErrors" />

{{-- / Source Type Select and Source Key Input Section --}}
    <div>

        <x-source.type-select wire:model.defer="type"
            class="font-medium p-2 rounded text-xs focus:outline-none cursor-pointer hover:text-blue-900"
            types="$store.sourceTypes.list"
            updatecb="function (value) {this.$store.sourceTypes.updateSelected(value)}"
        />

        <x-source.key-input
            :key="$key"
            wire:change="computeKey($event.target.value)"
            x-bind:disabled="keyDisabled"
            x-bind:class="keyDisabled  ? 'bg-white text-gray-600' : 'border'"
            class="flex-grow focus:outline-none px-2 py-1 rounded text-sm focus:border-blue-400"
        >
            @error("key") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </x-source.key-input>

    </div>
{{-- / Source Type Select and Source Key Input Section --}}

{{-- Creators Section --}}
    <div class="flex flex-col items-stretch">
    {{-- Accordion Headline --}}
        <div class="bg-gray-100 flex items-center justify-between px-2 py-1 text-gray-500">
            <div class="flex gap-2 px-1">
                <h3 class="font-semibold text-sm ">Creadores</h3>
                <button x-on:click="$dispatch('add-participation')" class="text-blue-600 text-xs hover:text-blue-500">
                    Agregar
                </button>
            </div>
            <button x-on:click="openParticipations = !openParticipations" x-cloak class="bg-gray-50 border p-1 rounded border-gray-300 hover:bg-blue-500 hover:text-white hover:border-blue-500 focus:outline-none ">
                <x-icons.chevron-down x-bind:class="{'transform rotate-180': openParticipations}" class="w-4 h-4 fill-current transition-transform ease-in-out duration-500" />
            </button>
        </div>
    {{-- / Accordion Headline --}}

    {{-- Accordion Content --}}
        <div x-ref="participations"
            x-bind:style="{'max-height': openParticipations ?  $el.scrollHeight + 'px' : '0px'}" {{-- Se puede agregar una longitud máxima por si el scrollHeight llega a ser muy grande --}}
            class="text-sm border-b overflow-hidden transition-all ease-in-out duration-500 "
        >
            {{-- Creators List --}}
                <ul x-data="participationList()"
                    x-on:remove-particpation="handleRemoveParticipation($event, $dispatch)"
                    x-on:add-participation.window="handleAddParticipation($event, $dispatch)"
                    x-on:source-mounted.window="mountParticipations"
                    wire:ignore
                    class="py-1 px-3"
                >
                    <template x-for="(participation, index ) in participations" x-bind:key="participation.i">
                        <li>
                            <x-source.person-input participation="participation"
                                class="flex py-1 items-center"
                            />

                        </li>
                    </template>
                </ul>
            {{-- / Creators List --}}
        </div>
    {{-- / Accordion Content --}}
    </div>
{{-- / Creators Section --}}

{{-- Attributes Section --}}
    <ul x-data="sourceAttributes" class="overflow-y-auto overflow-hidden px-2 pb-2 ">
        <template x-for="attribute in $store.sourceTypes.attributes">
            <li>
                <div class="flex flex-col mt-2">
                    <label x-bind:for="'attribute.' + attribute.code" x-text="attribute.label" class=" flex-grow-0 text-gray-600 text-sm ml-1"></label>
                    <input x-show="attribute.code != 'abstractNote'"
                        x-bind:type="type(attribute.type)"
                        x-bind:name="'attribute.' + attribute.code"
                        x-bind:id="'input-' + attribute.code"
                        x-bind:value="$store.source.attributes[attribute.code] ? $store.source.attributes[attribute.code] : ($store.source.attributes[attribute.base] ? $store.source.attributes[attribute.base] : null)"
                        x-on:input="$store.source.attributes[attribute.code] = $event.target.value"
                        class=" flex-grow border text-sm px-1 py-1 rounded focus:outline-none focus:border-blue-400"
                    >
                    <textarea x-show="attribute.code == 'abstractNote'"
                        x-bind:name="'attribute.' + attribute.code"
                        x-bind:id="'input-' + attribute.code"
                        x-on:input="$store.source.attributes[attribute.code] = $event.target.value"
                        x-bind:value="$store.source.attributes[attribute.code] ? $store.source.attributes[attribute.code] : ($store.source.attributes[attribute.base] ? $store.source.attributes[attribute.base] : null)"
                        rows="4"
                        class=" flex-grow border px-2 py-1 rounded text-sm resize-none focus:outline-none focus:border-blue-400"
                    ></textarea>
                </div>
            </li>
        </template>
    </ul>
{{-- / Attributes Section --}}

    <x-source.creators-hint wire:model.defer="creatorSuggestions" class="absolute bottom-0 right-0 z-40" />


</div>
@once
@push('head-script')
    {{-- The sources are generated depending on locale (labels are localized) and can be cached --}}
    <script src="{{ route('sources.jsstypes')}}"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('sourceTypes', {
                list: Lg.sourceTypes, // {{-- asset returned by route sources.jsstypes --}}
                selected: @json($type),
                attributes: {},
                init: function () {
                    this.updateAttributes()
                },
                updateAttributes: function () {
                    if (!this.list) return;
                    this.attributes = Object.values(this.list[this.selected ? this.selected : 'journalArticle'].attributes)
                },
                updateSelected: function (sType) {
                    this.selected = sType;
                    this.updateAttributes();
                },
                roles: function (sourceType) {
                    if (!this.list) return {};
                    return this.list[sourceType].roles
                },
            })

            Alpine.store('source', {
                attributes: {},
                participations:[],
                mount: function (attributes, participations) {
                    this.attribute = attributes;
                    this.participations = participations;
                }
            })

            Alpine.data('alpNewSource', (options) => {
                return {
                    sourceID: options.sourceID,
                    active: false,
                    loading:false,
                    openParticipations: false,
                    get keyDisabled() {
                        return Boolean(this.loading || this.sourceID);
                    },
                    mountSource: function (target) {
                        this.$store.source.attributes = JSON.parse(JSON.stringify(this.$wire.attributes))
                        this.$store.source.participations = JSON.parse(JSON.stringify(this.$wire.participations))
                        this.$refs.root.dispatchEvent(
                            new CustomEvent('source-mounted', {bubbles: true, detail: target})
                        )
                    },
                    handleEvent: function (event) {
                        if (event.type == 'source-select:solve') {
                            let data = {
                                attributes: JSON.parse(JSON.stringify(this.$store.source.attributes)) ,
                                participations: JSON.parse(JSON.stringify(this.$store.source.participations))
                            }
                            this.$wire.save(data)
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
                        this.$refs.participations.classList.add('transition-all', 'duration-500')
                        document.addEventListener(
                            'source-select:solve',
                            this,
                            false
                        )
                    },
                    deactivate: function () {
                        this.$refs.participations.classList.remove('transition-all', 'duration-500')
                        this.$nextTick(() => {
                            this.openParticipations = false
                            this.active = false
                            document.removeEventListener('source-select:solve', this, false)
                        })
                    },
                    handleAddParticipation: function(e, dispatch) {
                        this.$nextTick(() => {
                                this.openParticipations = true;
                                this.$refs.participations.style.maxHeight = this.$refs.participations.scrollHeight + 'px'
                            }
                        )
                    }
                }
            })

            Alpine.data('participationList', (options) => {
                return {
                    i:0,
                    participations: [],
                    init: function () {
                        this.mountParticipations();
                    },
                    mountParticipations: function () {
                        let participations = this.$store.source.participations
                        let i = 1;
                        participations.forEach(participation => {
                            participation.i = i++
                            participation.dirty =  false
                            participation.creator.dirty =  false
                        })
                        this.participations = participations
                        this.i = i
                    },
                    moveUp: function (i) {
                        let index = this.participations.findIndex((person) => person.i == i)
                        let movingParticipation = this.participations.splice(index,1)[0]
                        this.participations.splice(index - 1, 0,movingParticipation)
                    },
                    handleAddParticipation: function(e, dispatch) {
                        let index = this.$store.source.participations.push({
                            i:  this.i++,
                            role: null,
                            relevance: null,
                            creator: {
                                id: null,
                                type:"person",
                                attributes: {
                                    name: '',
                                    lastName: ''
                                },
                                dirty: false
                            },
                            dirty: false

                        }) - 1;
                        this.$nextTick(() => dispatch('participation-added', {participation: JSON.parse(JSON.stringify(this.participations[index]))}))
                    },
                    handleRemoveParticipation: function (event, dispatch) {
                        let index = this.participations.findIndex((c) => c.i == event.detail.participation.i)
                        this.$nextTick(() => this.participations.splice(index, 1))
                    }
                }
            })




            Alpine.data('sourceAttributes', () => {
                return {
                    attributes: @json($attributes, JSON_PRETTY_PRINT),
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
