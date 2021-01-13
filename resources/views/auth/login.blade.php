<x-layout.default
  title="{{ __('messages.users.session') }}"
>
  <x-container>
    <x-main-heading>
      {{ __('messages.users.session') }}
    </x-main-heading>

    @if ($errors->any())
    <div class="bg-red-400 p-8 my-5">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <x-form.sm action="{{ route('auth.login') }}" method="POST">
      <x-form.field name="email" label="E-mail" value='' type="email" placeholder="juanp@example.com" required />
      <x-form.field name="password" label="{{ __('messages.users.pass') }}" value='' type="password" required />
      <x-form.checkbox name="remember" label="{{ __('messages.users.remember') }}" />
      <div class="flex justify-end">  
        <x-form.button type="submit" class="m-2">{{ __('messages.users.send') }}</x-form.button>
        <x-form.button type="reset" class="m-2">{{ __('messages.users.clear') }}</x-form.button>
      </div>
    </x-form.sm>
  </x-container>
</x-layout.default>