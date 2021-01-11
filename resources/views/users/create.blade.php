
<x-layout.default
  title="Registro de usuario"
>
  <x-container>
    <x-main-header>
      Registro de usuario
    </x-main-header>

    {{-- 
    @if ($errors->any())
    <div class="bg-red-400 p-8 my-5">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif 
    --}}

    <x-form.sm action="{{ route('users.register') }}" method="POST">
      <x-form.field name="name" label="Nombre" type="text" placeholder="Juan Perez" required />
      <x-form.field name="email" label="E-mail" type="email" placeholder="juanp@example.com" required />
      <x-form.field name="password" label="Password" value='' type="password" required />
      <x-form.select name="country" label="País">
        @foreach ($locale['countries'] as $country)
          <option value="{{ $country['.key']}}" {{ old('country') ? 'selected' : ''}}>
            {{ $country['name']}}
          </option>
        @endforeach
      </x-form.select>
      <div class="flex justify-end">
        <x-form.button type="submit" class="m-2">Enviar</x-form.button>
        <x-form.button type="reset" class="m-2">Borrar</x-form.button>
      </div>
    </x-form.sm>
  </x-container>
</x-layout.default>