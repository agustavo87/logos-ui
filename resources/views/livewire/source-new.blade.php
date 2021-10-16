<div
    x-data="alpNewSource" x-ref="root"
    x-on:lw:message-change.window="loading = $event.detail.loading"
    x-on:add-participation="handleAddParticipation($event, $dispatch)"
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


    <div x-data="{errors:@entangle('sharedErrors'), open:false}"
    x-show="open"
    x-effect="open = errors.length > 0"
    x-transition
    class="absolute top-0 border border-red-500 bg-red-50 text-red-900 p-2 w-full text-xs">
    <div class="w-full p-1 flex flex-row-reverse">
        <button
        x-on:click="open = false"
        class="bg-red-200 border border-red-700 h-4 leading-3 text-center text-red-900 w-4">
            &#10005;
        </button>
    </div>
        <ul>
            <template x-for="error in errors" x-bind:key="error.key">
                <li>
                    <strong x-text="error.key"></strong>
                    <ul>
                        <template x-for="message in error.messages">
                            <li x-text="message"></li>
                        </template>
                    </ul>
                </li>
            </template>
        </ul>
    </div>


    {{-- Source Type Select and Key Section --}}
    <div>
        <select
        x-data="selectSourceType({type: @entangle('type').defer })"
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
                <input
                wire:change="computeKey($event.target.value)"
                x-bind:disabled="loading"
                value="{{$key}}"
                type="text" id="source-key" name="source-key"
                autocomplete="off"
                class="border flex-grow focus:outline-none px-2 py-1 rounded text-sm focus:border-blue-400"
                >
                @error("key") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    {{-- / Source Type Select and Key Section --}}

    {{-- Creators Section --}}
    <div class="flex flex-col items-stretch">

        {{-- Accordion Headline --}}
        <div class="bg-gray-100 flex items-center justify-between px-2 py-1 text-gray-500">
            <div class="flex gap-2 px-1">
                <h3 class="font-semibold text-sm ">Creadores</h3>
                <button
                x-on:click="$dispatch('add-participation')"
                class="text-blue-600 text-xs hover:text-blue-500"
                >
                    Agregar
                </button>
            </div>
            <button
            x-on:click="openParticipations = !openParticipations" x-cloak
            class="bg-gray-50 border p-1 rounded border-gray-300 hover:bg-blue-500 hover:text-white hover:border-blue-500 focus:outline-none "
            >
                <x-icons.chevron-down
                x-bind:class="{'transform rotate-180': openParticipations}"
                class="w-4 h-4 fill-current transition-transform ease-in-out duration-500"
                />
            </button>
        </div>
        {{-- / Accordion Headline --}}

        {{-- Accordion Content --}}
        <div
        x-ref="participations"
        x-bind:style="{'max-height': openParticipations ?  $el.scrollHeight + 'px' : '0px'}" {{-- Se puede agregar una longitud máxima por si el scrollHeight llega a ser muy grande --}}
        class="text-sm border-b overflow-hidden transition-all ease-in-out duration-500 "
        >

            {{-- Creators List --}}
            <ul
            x-data="participationList()"
            x-on:remove-particpation="handleRemoveParticipation($event, $dispatch)"
            x-on:add-participation.window="handleAddParticipation($event, $dispatch)"
            wire:ignore
            class="py-1 px-3"
            >
                <template x-for="(participation, index ) in participations" x-bind:key="participation.i">
                    <li>

                        {{-- Person Input --}}
                        <div
                        x-data="personInput({participation:participation})"
                        x-effect="roles = $store.sourceTypes.roles($store.sourceTypes.selected)"
                        x-on:participation-added.window = "$event.detail.participation.i == myperson.i ? editNew: null"
                        x-on:suggestion-acepted.window="handleSuggestion($event)"
                        x-ref="root"
                        class="flex py-1 items-center"
                        >
                            <div
                            x-show="isEditing"
                            x-on:keyup.enter="commit"
                            x-on:keydown.escape.stop="cancel"
                            class="flex flex-row gap-1 w-full justify-between"
                            >
                             <div class="flex flex-row gap-1">
                                <input
                                x-on:input="creatorInput('lastName', $event.target.value)"
                                x-ref="lastName"
                                x-on:blur="$dispatch('creator-blur')"
                                x-on:focus="$dispatch('creator-focus')"
                                x-bind:data-i="i"
                                type="text" class="px-2 border-b border-gray-100 w-2/5 focus:outline-none focus:border-blue-500"
                                >
                                <input
                                x-on:input="creatorInput('name', $event.target.value)"
                                x-ref="name"
                                x-on:blur="$dispatch('creator-blur')"
                                x-on:focus="$dispatch('creator-focus')"
                                x-bind:data-i="i"
                                type="text" class="px-2 w-2/5 border-b border-gray-100 focus:outline-none focus:border-blue-500"
                                >
                                <select class="border ml-1 rounded text-xs focus:outline-none"
                                    x-model="myRole"
                                >
                                    <template x-for="role in roles">
                                        <option x-bind:value="role.code" x-text="role.label" x-bind:selected="myRole ? (role.code == myRole) : false"></option>
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
                            <div
                            x-show="!isEditing" x-on:click="edit" x-on:mouseover="showControls = true" x-on:mouseout="showControls = false"
                            class="align-middle cursor-pointer flex hover:bg-blue-50 italic justify-between rounded-full w-full"
                            >
                                <div
                                x-bind:class="myperson.id && myperson.dirty ? 'text-blue-900' : ''"
                                x-bind:title="myperson.id && myperson.dirty ? 'Modified' : null"
                                class="flex items-center ml-1"
                                >
                                    <div>
                                        <span x-text="myperson.attributes.lastName"></span>, <span x-text="myperson.attributes.name"></span>
                                    </div>
                                    <span
                                    x-text="myRole ? (roles[myRole] ? roles[myRole].label : '') : ''"
                                    class="bg-gray-400 flex h-5 leading-4 ml-2 px-2 rounded-full text-white text-xs"
                                    ></span>
                                </div>
                                <div class="flex" x-bind:class="showControls ? 'visible': 'invisible'">
                                    <button x-on:click.stop="restore"
                                    x-bind:class="myperson.dirty && myperson.id ? '' : 'invisible' "
                                    title="Discard Changes"
                                    class="w-5 h-5 transform rotate-180 flex bg-gray-400 text-white hover:bg-white hover:text-gray-500 justify-center m-1 rounded-full focus:outline-none"
                                    >
                                        &#10551;
                                    </button>
                                    <button
                                    x-on:click.stop="moveUp(participation.i)"
                                    x-bind:class="index > 0 ? '' : 'invisible'"
                                    title="Move Up"
                                    class="text-white rounded-full m-1 bg-blue-400 hover:bg-white hover:text-blue-500 h-5 w-5 flex align-middle justify-center focus:outline-none"
                                    >
                                        &uarr;
                                    </button>
                                    <button
                                    x-on:click.stop="$dispatch('remove-particpation', {participation: participation})"
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
    </ul>
    {{-- / Attributes Section --}}

    {{-- Creators Suggestion --}}
    <div
    x-data="creatorsHint({creators: @entangle('creatorSuggestions').defer })"
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
        <input
        wire:model.debounce.500ms="creatorSuggestionParams.hint"
        type="hidden"
        class="px-2 py-1 rounded border w-full"
        >
        <ul
        x-on:mouseover="mouseover = true"
        x-on:mouseleave="mouseover = false"
        class="h-36 rounded border border-blue-200 bg-gray-50 text-xs p-1 overflow-y-auto overflow-x-hidden"
        >
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
<script>
    let logosCreators = @json($creators, JSON_PRETTY_PRINT);
</script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('sourceTypes', {
            list: @json($types, JSON_PRETTY_PRINT),
            selected: null,
            attributes: {},
            init: function () {
                this.updateAttributes()
                console.log('this $wire: ', this.$wire)
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
            attributes: @json($attributes, JSON_PRETTY_PRINT),
            creators: @json($creators, JSON_PRETTY_PRINT),
            participations: @json($participations, JSON_PRETTY_PRINT)
        })

        Alpine.data('alpNewSource', () => {
            return {
                active: false,
                loading:false,
                openParticipations: false,
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
                    let tempParticipations = JSON.parse(JSON.stringify(this.participations))
                    let index = tempParticipations.findIndex((person) => person.i == i)
                    let movingParticipation = tempParticipations.splice(index,1)[0]
                    tempParticipations.splice(index - 1, 0,movingParticipation)
                    this.participations = tempParticipations;
                },
                handleAddParticipation: function(e, dispatch) {
                    let index = this.participations.push({
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
                    console.log('removiendo participación i' + event.detail.participation.i + ' index : ', index)
                    this.$nextTick(() => this.participations.splice(index, 1))
                }
            }
        })

        Alpine.data('personInput', (options) => {
            return {
                i: options.participation.i,
                myperson: options.participation.creator,
                myRole: options.participation.role,
                dirty: options.participation.dirty,
                roles: {},
                dirtyInput: false,
                isEditing: false,
                showControls: false,
                cache: {
                    myperson: JSON.parse(JSON.stringify(options.participation.creator))
                },
                init: function () {
                    this.$watch('roles', this.getRole.bind(this))
                },
                getRole: function() {
                    let roles = Object.values(this.roles)
                    let primary = roles.find(role => role.primary)
                    if (!this.myRole) {
                        this.myRole  = primary.code
                    } else if(roles.find((role) => role.code == this.myRole) == undefined) {
                        console.log('no existe ', this.myRole, ' dentro de ', Object.getOwnPropertyNames(this.roles))
                        this.myRole  = primary.code
                    }
                },
                fillInputs: function() {
                    let attributeNames = Object.getOwnPropertyNames(this.myperson.attributes)
                    attributeNames.forEach(
                        attrName => this.$refs[attrName].value = this.myperson.attributes[attrName]
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
                    let attributeNames = Object.getOwnPropertyNames(this.myperson.attributes)
                    attributeNames.forEach(
                        attrName => this.myperson.attributes[attrName] = this.$refs[attrName].value
                    )
                    this.myperson.dirty = this.dirtyInput
                    this.isEditing = false;
                },
                closeHints: function (force = false) {
                    this.$refs.root.dispatchEvent(
                        new CustomEvent('close-hints', {bubbles: true, detail: {force:force}})
                    )
                },
                cancel: function () {
                    this.closeHints(true)
                    if (!this.myperson.dirty) {
                        this.dirtyInput = false
                    }
                    this.isEditing = false
                },
                restore: function () {
                    if (this.cache.myperson != undefined) {
                        this.myperson = this.cache.myperson
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
                                    participation: JSON.parse(JSON.stringify({i: this.i, creator:this.myperson}))
                                }
                            }))
                        })
                },
                handleSuggestion:function ($event, $dispatch) {
                    if ($event.detail.client.i == this.i) {
                        console.log('suggestion is to me - \n\tclient:', $event.detail.client, '\n\tme:', this.myperson)
                        let creator = event.detail.creator;
                        this.myperson.attributes.name  = creator.attributes.name
                        this.myperson.attributes.lastName  = creator.attributes.lastName
                        this.myperson.id = creator.id
                        this.myperson.dirty = false;
                        this.cache['myperson'] = JSON.parse(JSON.stringify(this.myperson));
                        this.isEditing = false;
                        this.closeHints(true)
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
                        client: JSON.parse(JSON.stringify(this.lastCreator))
                    })
                },
                newHints: function (event) {
                    this.haveNewHints = true
                    let margin= 8
                    this.$refs.root.style.top = event.target.offsetTop + event.target.offsetHeight + margin + 'px'
                    this.$refs.root.style.left = event.target.offsetLeft + 'px'
                    this.$refs.root.style.width = event.target.clientWidth + 'px'
                    this.lastCreator = event.detail.participation
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
