<x-layout.default
  title="{{ __('messages.users.info')}}"
>
  <x-container>
    <x-main-heading>
      {{ __('messages.users.info')}}
    </x-main-heading>
    <ul>
      <li><strong>{{ __('messages.users.info')}}:</strong> {{ $user->name }}</li>
      <li><strong>E-Mail:</strong> {{ $user->email }}</li>
      <li><strong>{{ __('messages.users.country')}}:</strong> {{ $country['name'] }}</li>
      <li><strong>{{ __('messages.users.lang')}}:</strong> {{ $language }}</li>
    </ul>
  </x-container>
</x-layout.default>