<div x-data="sourceFiller('citation.article:0.0.1', {
        title: null,
        year: null,
        journal: null,
        volume: null,
        issue: null,
        firstPage: null,
        lastPage: null,
    }, function (data) {
        this.data.title = data.title ? data.title : null
        this.data.year = data.year ? data.year : null
        this.data.journal = data.journal ? data.journal : null
        this.data.volume = data.volume ? data.volume : null
        this.data.issue = data.issue ? data.issue : null
        this.data.firstPage = data.firstPage ? data.firstPage : null
        this.data.lastPage = data.lastPage ? data.lastPage : null
    })" 
    x-show="display" x-on:set-schema.window="handleSetSchema">
        <div class="flex flex-col items-stretch">
            <input class="border rounded border-gray-400 text-sm px-2 py-1 mt-1 focus:outline-none"
                type="text" name="title" placeholder="title" 
                x-model="data.title"
                @input.stop="handleInput($event, $dispatch)">
            <input type="text" name="year" placeholder="year"  
                class="border rounded border-gray-400 text-sm px-2 py-1 mt-1 focus:outline-none"
                x-model="data.year"
                @input.stop="handleInput($event, $dispatch)">
            <input type="text" name="journal" placeholder="journal"  
                class="border rounded border-gray-400 text-sm px-2 py-1 mt-1 focus:outline-none"
                x-model="data.journal"
                @input.stop="handleInput($event, $dispatch)">
            <div class="flex justify-between">
                <input type="text" name="volume" placeholder="volume"  
                    class="w-1/2 border rounded border-gray-400 text-sm px-2 py-1 mt-1 focus:outline-none"
                    x-model="data.volume"
                    @input.stop="handleInput($event, $dispatch)">
                <input type="text" name="issue" placeholder="issue"  
                    class="w-1/2 border rounded border-gray-400 text-sm px-2 py-1 mt-1 focus:outline-none"
                    x-model="data.issue"
                    @input.stop="handleInput($event, $dispatch)">
            </div>
            <div class="flex justify-between">
                <input type="text" name="firstPage" placeholder="First Page"  
                    class="w-1/2 border rounded border-gray-400 text-sm px-2 py-1 mt-1 focus:outline-none"
                    x-model="data.firstPage"
                    @input.stop="handleInput($event, $dispatch)">
                <input type="text" name="lastPage" placeholder="Last Page"  
                    class="w-1/2 border rounded border-gray-400 text-sm px-2 py-1 mt-1 focus:outline-none"
                    x-model="data.lastPage"
                    @input.stop="handleInput($event, $dispatch)">
            </div>
        </div>
</div>

@once
    @push('foot-script')
        <x-logos.script-filler />
    @endpush
@endonce