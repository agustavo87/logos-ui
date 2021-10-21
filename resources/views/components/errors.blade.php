<div x-data="{errors: @entangle($attributes->wire('model')) , open:false}"
        x-show="open"
        x-effect="open = errors.length > 0"
        x-transition
        class="absolute top-0 border border-red-500 bg-red-50 text-red-900 p-2 w-full text-xs"
    >
    <div class="w-full p-1 flex flex-row-reverse">
        <button x-on:click="open = false" class="bg-red-200 border border-red-700 h-4 leading-3 text-center text-red-900 w-4">
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
