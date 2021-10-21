<div x-data="creatorsHint({creators: @entangle( $attributes->wire('model') ) })"
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
        {{ $attributes->whereDoesntStartWith('wire') }}
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

    @once
        @push('head-script')
            <script>
                document.addEventListener('alpine:init', () => {
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
                })
            </script>
        @endpush
    @endonce
