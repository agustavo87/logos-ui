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

    <h4 class="font-medium">Libro</h4>

    <div class="flex flex-col items-stretch">
        <input type="hidden" x-ref="secretary">
        <input class="border my-1 px-3 py-2"
            type="text" name="title" placeholder="title" 
            x-model="data.title"
            @input.stop="handleInput($event, $dispatch)">
        <input type="text" name="year" placeholder="year"  
            class="border my-1 px-3 py-2"
            x-model="data.year"
            @input.stop="handleInput($event, $dispatch)">
        <input type="text" name="editorial" placeholder="editorial"  
            class="border my-1 px-3 py-2"
            x-model="data.editorial"
            @input.stop="handleInput($event, $dispatch)">
        <input type="text" name="city" placeholder="city"  
            class="border my-1 px-3 py-2"
            x-model="data.city"
            @input.stop="handleInput($event, $dispatch)">
    </div>

</div>

@once
    @push('foot-script')
        <x-logos.script-filler />
    @endpush
@endonce