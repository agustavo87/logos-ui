<x-layout.default
  title="Iniciar Sesión"
>
  <x-container>
    <x-main-header>
      Iniciar Sesión
    </x-main-header>

    @if ($errors->any())
    <div class="bg-red-400 p-8 my-5">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <x-form.sm action="{{ route('auth.identify') }}" method="POST">
      <x-form.field name="email" label="E-mail" value='' type="email" placeholder="juanp@example.com" required />
      <x-form.field name="password" label="Password" value='' type="password" required />
      <x-form.checkbox name="remember" label="Recordar" />
      <div class="flex justify-end">  
        <x-form.button type="submit" class="m-2">Enviar</x-form.button>
        <x-form.button type="reset" class="m-2">Borrar</x-form.button>
      </div>
    </x-form.sm>
  </x-container>
</x-layout.default>