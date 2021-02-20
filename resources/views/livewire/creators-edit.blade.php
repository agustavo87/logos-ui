<div class="flex flex-col pb-4">
    <div class="py-1">
        @foreach ($creators as $index => $creator)
            <livewire:creator-edit :creator="$creator" :key="$creator->id">
        @endforeach
    </div>
    <div class="flex flex-row w-full">
        <div class="flex flex-row flex-grow">
            <div class="flex-grow flex flex-row">
                <input type="text"
                    placeholder="Name"
                    class="border-b border-gray-400 text-sm px-2 py-1 focus:outline-none flex-grow mr-1">
                <input type="text"
                    wire:model="lastName" placeholder="Last Name"
                    class="border-b border-gray-400 text-sm px-2 py-1 focus:outline-none mr-1">
                <select placeholder="Type"
                    class="border-b border-gray-400 text-xs text-gray-500 px-2 py-1 focus:outline-none">
                    <option value="author">Author</option>
                </select>
            </div>
        </div>
        <div class="flex items-center w-16 justify-end">
            <button type="button" class="px-1 text-xl text-gray-600 hover:text-red-500 focus:outline-none" 
                    title="Cancel" 
                    x-on:click="isEditing = false">
                    <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
            </button>
        </div>
    </div>
</div>