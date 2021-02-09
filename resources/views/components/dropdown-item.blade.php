@props([
    'base' => 'block px-4 py-2 text-gray-200 sm:text-gray-800 whitespace-nowrap w-full focus:outline-none text-left',
    'active' => 'hover:bg-gray-700 sm:hover:bg-indigo-500 hover:text-white',
    'inactive' => 'font-medium cursor-default',
    'disabled' => 0,
    'button' => 0,
    'dontDisable' => false
])
    <x-link
        :base=$base :active=$active :inactive=$inactive :disabled=$disabled :button=$button :dont-disable="$dontDisable"
        {{ $attributes }}
    >
        {{ $slot }}
</x-link>