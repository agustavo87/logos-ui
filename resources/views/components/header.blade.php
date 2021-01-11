@auth
    @if (auth()->user()->isAdministrator())
        <div class=" w-full h-1 bg-green-500 z-50 absolute top-0 left-0"></div>
    @endif
@endauth
<header
  class="bg-gray-900 sm:flex sm:justify-between sm:px-4 sm:py-2 sm:items-center"
  x-data="{ open: true }"
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
        <div class="text-sm px-2 pt-2 pb-4 sm:flex sm:items-center sm:p-0">
            
            {{-- Nav Items --}}
            {{ $slot }}

            {{-- Account Menu Dropdown sm+  --}}
            <x-header.account-dropdown class="hidden sm:block sm:ml-6"/>

        </div>

        {{-- Account Menu sm- --}}
        <div class="px-4 py-5 border-t border-gray-800 sm:hidden">
            
            {{-- Imagen de perfil --}}
            <div class="flex items-center">
                <img src="{{ asset('images/panther-profile.png') }}" alt="Gustavo" class="h-8 w-8 rounded-full border-2 border-gray-600 object-cover">
                <span class="ml-3 text-white font-semibold">
                    @auth
                        {{auth()->user()->name}}
                    @endauth

                    @guest
                        Invitado
                    @endguest
                </span>
            </div>

            {{-- Items --}}
            <div class="mt-4 text-sm">
                @guest
                <x-header.account-mobile-item href="{{ route('auth.show') }}" class="mt-3">
                    Iniciar Sesión
                </x-header.account-mobile-item>
                <x-header.account-mobile-item href="{{ route('users.create') }}">
                    Registrarse
                </x-header.account-mobile-item>
                @endguest
                {{-- Poner de hecho el usuario logeado cuando se establezca --}}
                @auth
                <x-header.account-mobile-item href="{{ route('users.show', auth()->user()->id) }}">
                    Mostrar Perfil
                </x-header.account-mobile-item>
                <x-header.account-mobile-item href="{{ route('users.edit', auth()->user()->id) }}">
                    Editar Perfil
                </x-header.account-mobile-item>
                <x-header.account-mobile-item href="{{ route('auth.logout') }}">
                    Salir
                </x-header.account-mobile-item>
                @endauth
            </div>


        </div>



    </nav>

</header>