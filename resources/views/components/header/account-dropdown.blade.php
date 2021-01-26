<div 
  {{ $attributes->merge(['class' => 'relative'])}}
  x-data="{ open:false }"
>
    {{-- profile image button --}}
    <button @click="open = true" class="block h-8 w-8 rounded-full overflow-hidden border-2 border-white border-opacity-50 hover:border-opacity-100 focus:outline-none focus:border-opacity-100" @click="isOpen = !isOpen">
      <img src="{{ asset('images/panther-profile.png') }}" alt="Gustavo" class="relative z-10 h-full w-full object-cover">
    </button>

    {{-- dropdown menu --}}
    <div x-show.transition="open" @click.away="open = false" x-cloak  class="absolute z-20 right-0 mt-2 py-2 w-48 bg-white rounded-lg shadow-xl">
        @guest
        <x-header.account-dropdown-item href="{{ route('auth.show') }}"> {{ __('messages.header.account.start') }} </x-header.account-dropdown-item>
        <x-header.account-dropdown-item href="{{ route('users.create') }}"> {{ __('messages.header.account.register') }} </x-header.account-dropdown-item>
        @endguest
        @auth
        <x-header.account-dropdown-item href="{{ route('users.show', auth()->user()->id) }}"> {{ __('messages.header.account.profile') }} </x-header.account-dropdown-item>
        <x-header.account-dropdown-item href="{{ route('users.edit', auth()->user()->id) }}"> {{ __('messages.header.account.edit-profile') }} </x-header.account-dropdown-item>
        <x-header.account-dropdown-item href="{{ route('auth.logout') }}"> {{ __('messages.header.account.logout') }} </x-header.account-dropdown-item>
        @endauth
    </div>
</div>