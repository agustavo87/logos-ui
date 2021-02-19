<div x-data="EditCreatorComponent()" class="creator-component pt-1">
    <div x-show="!isEditing" class="creator-c-label flex flex-row justify-between">
        <span class="text-sm">
            {{ $name }}, {{ $lastName }} <small class="text-gray-600 italic ml-1"> {{ $type }} </small>
        </span>
        <div class="creator-c-control">
            <button
                class="px-1 ml-1 text-sm font-bold text-gray-500 hover:text-blue-600 focus:outline-none "
                title="Editar"
                @click="edit"
            >
            <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
        </button>
            <button type="button" class="px-1 text-sm text-gray-500 hover:text-red-600 focus:outline-nonet" 
                title="Delete" 
                x-on:click="isEditing = false"
            >
            <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
        </button>
        </div>
    </div>
    <div x-show="isEditing">
        <form x-on:submit.preven="save" class="flex flex-row justify-between">
            <div>
                <input type="text" name="creator-{{$creatorId}}-name" id="creator-{{$creatorId}}-name"
                    wire:model="name" placeholder="Name"
                    class="border-b border-gray-400 text-sm px-2 py-1 focus:outline-none">
                <input type="text" name="creator-{{$creatorId}}-last_name" id="creator-{{$creatorId}}-last_name"
                    wire:model="lastName" placeholder="Last Name"
                    class="border-b border-gray-400 text-sm px-2 py-1 focus:outline-none">
                <select name="creator-{{ $creatorId}}-type" id="creator-{{ $creatorId}}-type"
                    wire:model="type" placeholder="Type"
                    class="border rounded border-gray-400 text-sm px-2 py-1 focus:outline-none">
                    <option value="author">Author</option>
                </select>
            </div>
            <div>
                <button
                    type="submit"
                    class="px-1 text-xl font-bold text-gray-600  hover:text-green-600 focus:outline-none"
                    title="Save"
                    @click="isEditing = false">
                    <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <button type="button" class="px-1 text-xl text-gray-600 hover:text-red-500 focus:outline-none" 
                    title="Cancel" 
                    x-on:click="isEditing = false">
                    <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
    @once
    @push('head-script')
        <script>
            function EditCreatorComponent() {
                return {
                    isEditing: false,
                    save: function() {
                        console.log('guardando');
                    },
                    edit: function () {
                        this.isEditing = true;
                    }
                }
            }
        </script>
        <style>
            .creator-component .creator-c-label .creator-c-control {
                visibility:hidden;
            }
            .creator-component .creator-c-label:hover .creator-c-control {
                visibility:visible;
            }
        </style>
    @endpush
    @endonce
</div>
