<div x-data="sourceFiller('citation.article:0.0.1', {
        title: null,
        year: null,
        journal: null,
        volume: null,
        issue: null
    }, function (data) {
        this.data.title = data.title ? data.title : null
        this.data.year = data.year ? data.year : null
        this.data.journal = data.journal ? data.journal : null
        this.data.volume = data.volume ? data.volume : null
        this.data.issue = data.issue ? data.issue : null
    })" 
    x-show="display" x-on:set-schema.window="handleSetSchema">
        <h4 class="font-medium">Citation Article</h4>
        <div class="flex flex-col items-stretch">
            <input class="border my-1 px-3 py-2"
                type="text" name="title" placeholder="title" 
                x-model="data.title"
                @input.stop="handleInput($event, $dispatch)">
            <input type="text" name="year" placeholder="year"  
                class="border my-1 px-3 py-2"
                x-model="data.year"
                @input.stop="handleInput($event, $dispatch)">
            <input type="text" name="journal" placeholder="journal"  
                class="border my-1 px-3 py-2"
                x-model="data.journal"
                @input.stop="handleInput($event, $dispatch)">
            <div class="flex justify-between">
                <input type="text" name="volume" placeholder="volume"  
                    class="border w-1/2 my-1 px-3 py-2"
                    x-model="data.volume"
                    @input.stop="handleInput($event, $dispatch)">
                <input type="text" name="issue" placeholder="issue"  
                    class="border w-1/2 my-1 px-3 py-2"
                    x-model="data.issue"
                    @input.stop="handleInput($event, $dispatch)">
            </div>
        </div>
</div>

@once
    @push('foot-script')
        <x-logos.script-filler />
    @endpush
@endonce