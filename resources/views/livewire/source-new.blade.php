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

    {{-- Source Key Input Section --}}
        <div class="flex flex-row gap-2 items-baseline ml-1 pb-1 px-2">
            <label for="source-key" class="flex-grow-0 text-gray-600 text-sm">{{ __('sources.key') }}</label>
            <div class="flex flex-col flex-grow">
                <input wire:change="computeKey($event.target.value)"
                    x-bind:disabled="keyDisabled"
                    x-bind:class="keyDisabled  ? 'bg-white text-gray-600' : 'border'"
                    value="{{$key}}"
                    type="text" id="source-key" name="source-key"
                    autocomplete="off"
                    class="flex-grow focus:outline-none px-2 py-1 rounded text-sm focus:border-blue-400"
                >
                @error("key") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>
    {{-- / Source Key Input Section --}}
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
                        {{-- Person Input --}}
                            <div x-data="personInput({participation:participation})"
                                x-effect="roles = $store.sourceTypes.roles($store.sourceTypes.selected)"
                                x-on:participation-added.window = "$event.detail.participation.i == participation.i ? editNew: null"
                                x-on:suggestion-acepted.window="handleSuggestion($event)"
                                x-ref="root"
                                class="flex py-1 items-center"
                            >
                                <div x-show="isEditing"
                                    x-on:keyup.enter="commit"
                                    x-on:keydown.escape.stop="cancel"
                                    class="flex flex-row gap-1 w-full justify-between"
                                >
                                    <div class="flex flex-row gap-1">
                                        <input x-on:input="creatorInput('lastName', $event.target.value)"
                                            x-ref="lastName"
                                            x-on:blur="$dispatch('creator-blur')"
                                            x-on:focus="$dispatch('creator-focus')"
                                            x-bind:data-i="participation.i"
                                            type="text" class="px-2 border-b border-gray-100 w-2/5 focus:outline-none focus:border-blue-500"
                                        >
                                        <input x-on:input="creatorInput('name', $event.target.value)"
                                            x-ref="name"
                                            x-on:blur="$dispatch('creator-blur')"
                                            x-on:focus="$dispatch('creator-focus')"
                                            x-bind:data-i="participation.i"
                                            type="text" class="px-2 w-2/5 border-b border-gray-100 focus:outline-none focus:border-blue-500"
                                        >
                                        <select x-show="showRoles" x-model="participation.role" class="border ml-1 rounded text-xs focus:outline-none" >
                                            <template x-for="role in roles">
                                                <option x-bind:value="role.code" x-text="role.label" x-bind:selected="participation.role ? (role.code == participation.role) : false"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="flex flex-row gap-1 ml-2">
                                        <button
                                        x-on:click="commit"
                                        x-bind:class="dirtyInput ? 'visible' : 'invisible' "
                                        title="Commit"
                                        class="h-5 w-5 mr-1 text-xs font-bold leading-none border text-blue-900 border-blue-500 rounded-full hover:bg-blue-500 hover:text-white flex items-center justify-center cursor-pointer focus:outline-none"
                                        >
                                            &#10003;
                                        </button>
                                        <button x-on:click="cancel" title="Discard Changes" class="transform rotate-180 h-5 w-5 mr-1 text-xs text-gray-600 leading-none border hover:bg-gray-500 hover:text-white border-transparent hover:border-gray-500 rounded-full flex items-center justify-center cursor-pointer focus:outline-none">
                                            &#10551;
                                        </button>
                                    </div>
                                </div>
                                <div x-show="!isEditing"
                                    x-on:click="edit"
                                    x-on:mouseover="showControls = true"
                                    x-on:mouseout="showControls = false"
                                    class="align-middle cursor-pointer flex hover:bg-blue-50 italic justify-between rounded-full w-full"
                                >
                                    <div x-bind:class="participation.creator.id && participation.creator.dirty ? 'text-blue-900' : ''"
                                        x-bind:title="participation.creator.id && participation.creator.dirty ? 'Modified' : null"
                                        class="flex items-center ml-1"
                                    >
                                        <div>
                                            <span x-text="participation.creator.attributes.lastName"></span>, <span x-text="participation.creator.attributes.name"></span>
                                        </div>
                                        <span x-show="showRoles" x-text="participation.role ? (roles[participation.role] ? roles[participation.role].label : '') : ''" class="bg-gray-400 flex h-5 leading-4 ml-2 px-2 rounded-full text-white text-xs"></span>
                                    </div>
                                    <div class="flex" x-bind:class="showControls ? 'visible': 'invisible'">
                                        <button x-on:click.stop="restore"
                                            x-bind:class="participation.creator.dirty && participation.creator.id ? '' : 'invisible' "
                                            title="Discard Changes"
                                            class="w-5 h-5 transform rotate-180 flex bg-gray-400 text-white hover:bg-white hover:text-gray-500 justify-center m-1 rounded-full focus:outline-none"
                                        >
                                            &#10551;
                                        </button>
                                        <button x-on:click.stop="moveUp(participation.i)"
                                            x-bind:class="index > 0 ? '' : 'invisible'"
                                            title="Move Up"
                                            class="text-white rounded-full m-1 bg-blue-400 hover:bg-white hover:text-blue-500 h-5 w-5 flex align-middle justify-center focus:outline-none"
                                        >
                                            &uarr;
                                        </button>
                                        <button x-on:click.stop="$dispatch('remove-particpation', {participation: participation})"
                                            title="Delete"
                                            class="rounded-full m-1 text-red-900 hover:bg-red-500 hover:text-white border-red-500  h-5 w-5 flex align-middle justify-center focus:outline-none"
                                        >
                                            &#10005;
                                        </button>
                                    </div>
                                </div>
                            </div>
                        {{-- / Person Input --}}
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

{{-- Creators Suggestion --}}
    <div x-data="creatorsHint({creators: @entangle('creatorSuggestions').defer })"
        x-on:hint-updated.window = "newHints($event)"
        x-ref="root"
        x-show="visible"
        x-on:close-hints.window="decideHidding"
        x-on:creator-blur.window="focusCount--"
        x-on:creator-focus.window="focusCount++"
        x-on:click.outside="decideHidding"
        x-transition:enter.duration.20ms
        x-transition:leave.duration.100ms
        x-transition.opacity
        class="absolute bottom-0 right-0 z-40"
    >
        <input wire:model.debounce.500ms="creatorSuggestionParams.hint"
            type="hidden"
            class="px-2 py-1 rounded border w-full"
        >
        <ul x-on:mouseover="mouseover = true" x-on:mouseleave="mouseover = false" class="h-36 rounded border border-blue-200 bg-gray-50 text-xs p-1 overflow-y-auto overflow-x-hidden">
           <template x-for="suggestion in creators" x-bind:key="suggestion.id">
                <li>
                    <button
                    x-on:click="acceptSugestion(suggestion.id, $dispatch)"
                    class="hover:bg-blue-100 hover:text-blue-800 rounded p-1 cursor-pointer w-full text-left"
                    >
                        <span x-text="suggestion.attributes.lastName + ', ' + suggestion.attributes.name"></span>
                    </button>
                </li>
           </template>
        </ul>
    </div>
{{-- / Creators Suggestion --}}

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
                attributes: @json($attributes, JSON_PRETTY_PRINT),
                participations: @json($participations, JSON_PRETTY_PRINT),
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

            Alpine.data('personInput', (options) => {
                return {
                    participation: options.participation,
                    i: options.participation.i,
                    myperson: options.participation.creator,
                    myRole: options.participation.role,
                    dirty: options.participation.dirty,
                    roles: {},
                    showRoles: false,
                    dirtyInput: false,
                    isEditing: false,
                    showControls: false,
                    cache: {
                        creator: JSON.parse(JSON.stringify(options.participation.creator))
                    },
                    init: function () {
                        this.$watch('roles', this.getRole.bind(this))
                    },
                    getRole: function() {
                        let roles = Object.values(this.roles)
                        if (!roles.length) {
                            this.showRoles = false;
                            return;
                        }
                        this.showRoles = true;

                        let primary = roles.find(role => role.primary)
                        if (!this.participation.role) {
                            this.participation.role  = primary.code
                        } else if(roles.find((role) => role.code == this.participation.role) == undefined) {
                            this.participation.role  = primary.code
                        }
                    },
                    fillInputs: function() {
                        let attributeNames = Object.getOwnPropertyNames(this.participation.creator.attributes)
                        attributeNames.forEach(
                            attrName => this.$refs[attrName].value = this.participation.creator.attributes[attrName]
                        )
                    },
                    creatorInput: function(attr, value) {
                        this.dirtyInput = true;
                        this.emitCreatorInput(attr, value)
                    },
                    edit: function () {
                        this.fillInputs()
                        this.dirtyInput = false;
                        this.isEditing = true;
                    },
                    editNew: function () {
                        this.isEditing = true;
                        window.setTimeout(() => this.$refs.lastName.focus(), 500)
                    },
                    commit: function () {
                        this.closeHints(true)
                        let attributeNames = Object.getOwnPropertyNames(this.participation.creator.attributes)
                        attributeNames.forEach(
                            attrName => this.participation.creator.attributes[attrName] = this.$refs[attrName].value
                        )
                        this.participation.creator.dirty = this.dirtyInput
                        this.isEditing = false;
                    },
                    closeHints: function (force = false) {
                        this.$refs.root.dispatchEvent(
                            new CustomEvent('close-hints', {bubbles: true, detail: {force:force}})
                        )
                    },
                    cancel: function () {
                        this.closeHints(true)
                        if (!this.participation.creator.dirty) {
                            this.dirtyInput = false
                        }
                        this.isEditing = false
                    },
                    restore: function () {
                        if (this.cache.myperson != undefined) {
                            this.participation.creator = this.cache.creator
                        }
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
                                        participation: JSON.parse(JSON.stringify(this.participation))
                                    }
                                }))
                            })
                    },
                    handleSuggestion:function ($event, $dispatch) {
                        if ($event.detail.clientParticipation.i == this.participation.i) {
                            let creator = event.detail.creator;
                            this.participation.creator.attributes.name  = creator.attributes.name
                            this.participation.creator.attributes.lastName  = creator.attributes.lastName
                            this.participation.creator.id = creator.id
                            this.participation.creator.dirty = false;
                            this.cache['creator'] = JSON.parse(JSON.stringify(this.participation.creator));
                            this.isEditing = false;
                            this.closeHints(true)
                        }
                    }
                }
            })

            Alpine.data('creatorsHint', function (options) {
                return {
                    lastParticipation: null,
                    visible: false,
                    mouseover: false,
                    creators: options.creators,
                    count: null,
                    focusCount: 0,
                    haveNewHints: false,
                    init: function () {
                        this.$watch('creators', value => this.count = this.countCreators())
                        this.$watch('count', value => this.decideHidding())
                    },
                    countCreators: function (creators) {
                        if (Array.isArray(this.creators)) {
                            return this.creators.length
                        } else if (typeof this.creators == 'object') {
                            return Object.getOwnPropertyNames(this.creators).length
                        }
                        return null
                    },
                    acceptSugestion: function(id, $dispatch) {
                        $dispatch('suggestion-acepted', {
                            creator: JSON.parse(JSON.stringify(this.creators[id])),
                            clientParticipation: JSON.parse(JSON.stringify(this.lastParticipation))
                        })
                    },
                    newHints: function (event) {
                        this.haveNewHints = true
                        let margin= 8
                        this.$refs.root.style.top = event.target.offsetTop + event.target.offsetHeight + margin + 'px'
                        this.$refs.root.style.left = event.target.offsetLeft + 'px'
                        this.$refs.root.style.width = event.target.clientWidth + 'px'
                        this.lastParticipation = event.detail.participation
                        this.decideHidding()
                    },
                    decideHidding: function (event) {
                        if (event && event.detail.force) {
                            this.visible = false
                            return
                        }
                        if (this.count <= 0) {
                            this.visible = false
                            return
                        }
                        if (this.visible && !this.mouseover && this.focusCount <= 0) {
                            this.visible = false
                            return
                        } else if (this.haveNewHints) {
                            this.visible = true
                            this.haveNewHints = false
                        }

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
