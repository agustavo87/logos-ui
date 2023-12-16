@php
    $user = auth()->check() ? auth()->user()->name : __('messages.guest')
@endphp

<x-layout.default
  title="{{ __('messages.home.title')}}"
>
  <x-container class="mt-10">
    <x-main-heading>
      {{__('messages.greet.someone', ['name' => $user])}}
    </x-main-heading>
    <p>
      {{ __('messages.test') }}.
    </p>
  </x-container>
</x-layout.default>