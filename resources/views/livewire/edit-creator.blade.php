<div x-data="EditCreatorComponent()" class="creator-component">
    <div x-show="!isEditing" class="creator-c-label flex flex-row justify-between">
        <span class="text-sm">
            {{ $name }}, {{ $lastName }}
        </span>
        <div class="creator-c-control">
            <button
                class="px-1 ml-1 text-sm font-bold text-green-600 focus:outline-none border border-transparent hover:border-gray-300"
                title="Editar"
                @click="edit"
            >&#x270E;</button>
            <button type="button" class="px-1 ml-2 text-sm text-red-600 focus:outline-none border border-transparent hover:border-gray-300" 
                title="Delete" 
                x-on:click="isEditing = false"
            >&cross;</button>
        </div>
    </div>
    <div x-show="isEditing">
        <form x-on:submit.preven="save">
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
            <button
                type="submit"
                class="px-1 ml-1 text-xl font-bold text-green-600 focus:outline-none border border-transparent hover:border-gray-300"
                title="Save"
                @click="isEditing = false"
            >✓</button>
            <button type="button" class="px-1 ml-2 text-xl text-red-600 focus:outline-none border border-transparent hover:border-gray-300" 
                title="Cancel" 
                x-on:click="isEditing = false"
            >&cross;</button>
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
