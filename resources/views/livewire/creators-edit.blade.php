<div class="flex flex-col pb-1 mb-1">

    <div class="py-1">
        @foreach ($creators as $index => $creator)
        <livewire:creator-edit :creator="$creator" :key="$creator->id">
        @endforeach
    </div>

    <form x-data="CreatorCreateComponent()" class="flex flex-row w-full h-7" x-on:submit.prevent="save">
        <div class="flex flex-row flex-grow">
            <div class="flex-grow flex flex-row" x-show="isEditing">
                <input type="text" wire:model.defer="name" placeholder="Name"
                    class="border-b border-gray-200 text-sm px-2 py-1 focus:outline-none flex-grow mr-1">
                @error('name')
                <span>{{$message}}</span>
                @enderror
                <input type="text" wire:model.defer="last_name" placeholder="Last Name"
                    class="border-b border-gray-200 text-sm px-2 py-1 focus:outline-none mr-1">
                @error('last_name')
                <span>{{$message}}</span>
                @enderror
                <select placeholder="Type" wire:model.defer="type"
                    class="border-b border-gray-200 text-xs text-gray-500 px-2 py-1 focus:outline-none">
                    <option value="author">Author</option>
                    <option value="editor">Editor</option>
                </select>
                @error('type')
                <span>{{$message}}</span>
                @enderror
            </div>
        </div>

        <div class="flex items-center w-16 justify-end">
            <button type="button" class="px-1 text-xl text-gray-400 hover:text-green-500 focus:outline-none" title="Add"
                x-on:click="isEditing = !isEditing">
                <svg x-show="!isEditing" class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <button type="submit" class="px-1 text-xl font-bold text-gray-600  hover:text-green-600 focus:outline-none"
                title="Save" x-show="isEditing">
                <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <button type="button" class="px-1 text-xl text-gray-600 hover:text-red-500 focus:outline-none"
                title="Cancel" x-show="isEditing" x-on:click="isEditing = false">
                <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>

    </form>
</div>

<script>
    function CreatorCreateComponent() {
            return {
                isEditing: false,
                save: function () {
                    this.$wire.save().then(r => this.isEditing = false);
                }
            }
        }
</script>