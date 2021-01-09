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
        {{-- Solo para mostrar como es un link desactivado --}}
        <x-header.account-dropdown-item href="/">Portada</x-header.account-dropdown-item> 
        <x-header.account-dropdown-item href="#">Iniciar Sesión</x-header.account-dropdown-item>
        <x-header.account-dropdown-item href="#">Registrarse</x-header.account-dropdown-item>
        <x-header.account-dropdown-item href="#">Mostrar Perfil</x-header.account-dropdown-item>
        <x-header.account-dropdown-item href="#">Editar Perfil</x-header.account-dropdown-item>
        <x-header.account-dropdown-item href="#">Salir</x-header.account-dropdown-item>
    </div>
</div>