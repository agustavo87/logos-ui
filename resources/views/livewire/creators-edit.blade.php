<div>
    <div class="flex flex-col pb-4">
        @foreach ($creators as $index => $creator)
            <livewire:creator-edit :creator="$creator" :key="$creator->id">
        @endforeach
        <div 
            class="flex flex-row">
            <div class="flex flex-col pt-1">
                <input type="text" name="creator-add-name" id="creator-add-name"
                    placeholder="Name"
                    class="border rounded border-gray-400 text-sm px-2 py-1 focus:outline-none">
            </div>
            <div class="flex flex-col pt-1 ml-1">
                <input type="text" name="creator-add-last_name" id="creator-add-last_name"
                    placeholder="Last Name"
                    class="border rounded border-gray-400 text-sm px-2 py-1 focus:outline-none">
            </div>
            <div class="flex flex-col pt-1 ml-1">
                <select name="creator-add-type" id="creator-add-type"
                    placeholder="Type"
                    class="border rounded border-gray-400 text-xs px-2 py-1 focus:outline-none">
                    <option value="author">Author</option>
                </select>
            </div>
        </div>
    </div>
</div>
