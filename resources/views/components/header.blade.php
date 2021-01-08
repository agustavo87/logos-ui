<header
  class="bg-gray-900 sm:flex sm:justify-between sm:px-4 sm:py-3 sm:items-center"
  x-data="{ open: false }"
>
    <div
    class="flex justify-between items-center px-4 py-3 sm:p-0"
    >
        <!-- Logo -->
        <img class="h-8" src="/images/logo-cuadrado.png" alt="Mandala">
    
        <!-- xs Menú Button -->
        <div class="sm:hidden" >
            <button @click="open = !open" class="block h-6 w-6 text-white opacity-75 hover:opacity-100 focus:opacity-100 focus:outline-none">
            <svg x-show="!open" lass="h-6 w-6 fill-current" x-description="Heroicon name: menu" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg x-show="open" class="h-6 w-6 fill-current" x-description="Heroicon name: x" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            </button> 
        </div>

    </div>

    <nav class="p-1 hidden sm:block" :class="{'hidden':!open}" >
        <div class="text-sm px-2 pt-2 pb-4 sm:flex sm:p-0">
            {{ $slot }}

            {{-- Account Dropdown sm+ --}}
            <x-account-dropdown />

        </div>
    </nav>

</header>