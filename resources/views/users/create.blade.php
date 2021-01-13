
<x-layout.default
  title="{{ __('messages.users.register') }}"
>
  <x-container>
    <x-main-heading>
      {{ __('messages.users.register') }}
    </x-main-heading>


    <x-form.sm action="{{ route('users.register') }}" method="POST">
      <x-form.field name="name" label="{{ __('messages.users.name') }}" type="text" placeholder="{{ __('messages.users.fake-name') }}" required />
      <x-form.field name="email" label="E-mail" type="email" placeholder="juanp@example.com" required />
      <x-form.field name="password" label="{{ __('messages.users.pass') }}" value='' type="password" required />
      <x-form.select name="country" label="{{ __('messages.users.country') }}">
        @foreach ($locale['countries'] as $country)
          <option value="{{ $country['.key']}}" {{ old('country') ? 'selected' : ''}}>
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