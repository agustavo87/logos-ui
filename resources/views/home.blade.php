<x-layout.default
  title="Bienvenido a tu lugar de mejora"
>
  <x-container>
    <x-main-header>
      Bienvenido
      @auth
      {{auth()->user()->name}}!
      @endauth
      @guest
      Invitado!
      @endguest
      
      {{-- Poner nombre de usuario cuando se realice la autenticacíon --}}
    </x-main-header>
    <p>
      Esto es una prueba
    </p>
  </x-container>
</x-layout.default>