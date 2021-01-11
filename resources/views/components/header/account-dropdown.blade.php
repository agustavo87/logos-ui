<div 
  {{ $attributes->merge(['class' => 'relative'])}}
  x-data="{ open:false }"
>
    {{-- profile image button --}}
    <button @click="open = true" class="block h-8 w-8 rounded-full overflow-hidden border-2 border-white border-opacity-50 hover:border-opacity-100 focus:outline-none focus:border-opacity-100" @click="isOpen = !isOpen">
      <img src="{{ asset('images/panther-profile.png') }}" alt="Gustavo" class="relative z-10 h-full w-full object-cover">
    </button>

    {{-- dropdown menu --}}
    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 py-2 w-48 bg-white rounded-lg shadow-xl">
        @guest
        <x-header.account-dropdown-item href="{{ route('auth.login') }}">Iniciar Sesión</x-header.account-dropdown-item>
        <x-header.account-dropdown-item href="{{ route('user.create') }}">Registrarse</x-header.account-dropdown-item>
        @endguest
        @auth
        <x-header.account-dropdown-item href="{{ route('user.show', auth()->user()->id) }}">Mostrar Perfil</x-header.account-dropdown-item>
        <x-header.account-dropdown-item href="{{ route('user.edit', auth()->user()->id) }}">Editar Perfil</x-header.account-dropdown-item>
        <x-header.account-dropdown-item href="{{ route('auth.logout') }}">Salir</x-header.account-dropdown-item>
        @endauth
    </div>
</div>