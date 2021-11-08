@props(['participation'])
<div x-data="personInput({participation:{{ $participation }}})"
x-effect="roles = $store.sourceTypes.roles($store.sourceTypes.selected)"
x-on:participation-added.window = "$event.detail.participation.i == participation.i ? editNew: null"
x-on:suggestion-acepted.window="handleSuggestion($event)"
x-ref="root"
{{ $attributes }}
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

@once
    @push('head-script')
        <script>
            document.addEventListener('alpine:init', () => {
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
                            this.$watch('participation.role', () => {
                                this.participation.dirty = true;
                            });
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
            })
        </script>
    @endpush
@endonce
