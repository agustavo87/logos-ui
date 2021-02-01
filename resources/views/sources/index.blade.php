<x-layout.default
title="Mostrar Fuentes"
>
<x-container>
  <x-main-heading>
    Mostrar Fuentes
  </x-main-heading>

  <div class="py-9">
    <livewire:show-sources :user-id="auth()->user()->id">
  </div>


</x-container>



</x-layout.default>