@php
    $userCountry = old('country') ?? $user->country
@endphp

<x-layout.default
  title="{{ __('messages.users.edit') }}"
>
  <x-container>
    <x-main-heading>
      {{ __('messages.users.edit') }}
    </x-main-heading>


    <x-form.sm action="{{ route('users.update', ['user' => $user->id]) }}" method="POST">
      @method('PUT')
      <x-form.field name="name" label="{{ __('messages.users.name') }}" type="text" value="{{ old('name') ?? $user->name }}" placeholder="{{ __('messages.users.fake-name') }}" required />
      <x-form.select name="country" label="{{ __('messages.users.country') }}">
        @foreach ($locale['countries'] as $country)
          <option value="{{ $country['.key']}}" {{ $country['.key'] == $userCountry ? 'selected': ''}}>
            {{ $country['name']}}
          </option>
        @endforeach
      </x-form.select>
      <div class="flex justify-end">
        <x-form.button type="submit" class="m-2">{{ __('messages.users.send') }}</x-form.button>
        <x-form.button type="reset" class="m-2">{{ __('messages.users.clear') }}</x-form.button>
      </div>
    </x-form.sm>
  </x-container>
</x-layout.default>