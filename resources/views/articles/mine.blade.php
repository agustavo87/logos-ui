<x-layout.default
title="Mostrar Artículos"
>
<x-container>
  <x-main-heading>
    Artículos de {{ auth()->user()->name}}
  </x-main-heading>

  <livewire:show-articles :user-id="auth()->user()->id"/>
  

</x-container>



</x-layout.default>