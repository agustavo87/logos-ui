<div 
    x-data="sourceFiller('citation.book:0.0.1', {
        title: null,
        year: null,
        editorial: null,
        city: null
    }, function (data) {
        this.data.title = data.title ? data.title : null
        this.data.year = data.year ? data.year : null
        this.data.editorial = data.editorial ? data.editorial : null
        this.data.city = data.city ? data.city : null
    })" 
    x-on:set-schema.window="handleSetSchema" x-show="display" x-cloak>
    <div class="flex flex-col items-stretch">
        <input class="border rounded border-gray-400 text-sm px-2 py-1 mt-1 focus:outline-none"
            type="text" name="title" placeholder="title" 
            x-model="data.title"
            @input.stop="handleInput($event, $dispatch)">
        <input type="text" name="year" placeholder="year"  
            class="border rounded border-gray-400 text-sm px-2 py-1 mt-1 focus:outline-none"
            x-model="data.year"
            @input.stop="handleInput($event, $dispatch)">
        <input type="text" name="editorial" placeholder="editorial"  
            class="border rounded border-gray-400 text-sm px-2 py-1 mt-1 focus:outline-none"
            x-model="data.editorial"
            @input.stop="handleInput($event, $dispatch)">
        <input type="text" name="city" placeholder="city"  
            class="border rounded border-gray-400 text-sm px-2 py-1 mt-1 focus:outline-none"
            x-model="data.city"
            @input.stop="handleInput($event, $dispatch)">
    </div>

</div>

@once
    @push('foot-script')
        <x-logos.script-filler />
    @endpush
@endonce