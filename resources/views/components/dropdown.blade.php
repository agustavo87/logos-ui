@props([
    'caption' => 'Dropdown',
    'captionStyle'=> 'relative focus:outline-none text-white font-medium py-1 px-2 rounded mt-1 sm:mt-0 w-full sm:ml-2 cursor-pointer opacity-75 hover:bg-gray-800 hover:opacity-100 active:opacity-100 active:bg-gray-700'
])
<div class="relative" x-data="{ open: false }">
    
    {{-- Button --}}
    <button @@click="open = true" class="{{ $captionStyle }}" >
        {{ $caption }}
    </button>

    {{-- Dropdown --}}
    <div @@click.away="open = false" x-show="open" class="relative mt-2 py-2 bg-gray-800 flex-col rounded-lg sm:absolute sm:bg-white sm:shadow-xl">
        {{ $slot }}
    </div>

</div>