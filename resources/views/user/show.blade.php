<x-layout.default
  title="Información de usuario"
>
  <x-container>
    <x-main-header>
      Información de usuario
    </x-main-header>
    <ul>
      <li><strong>Nombre:</strong> {{ $user->name }}</li>
      <li><strong>E-Mail:</strong> {{ $user->email }}</li>
      <li><strong>País:</strong> {{ $user->country }}</li>
    </ul>
  </x-container>
</x-layout.default>