@php
    $userCountry = old('country') ?? $user->country
@endphp

<x-layout.default
  title="Editar información de usuario"
>
  <x-container>
    <x-main-header>
      Editar información de usuario
    </x-main-header>


    <x-form.sm action="{{ route('user.update', ['user' => $user->id]) }}" method="POST">
      @method('PUT')
      <x-form.field name="name" label="Nombre" type="text" value="{{ old('name') ?? $user->name }}" placeholder="Juan Perez" required />
      <x-form.select name="country" label="País">
        @foreach ($locale['countries'] as $country)
          <option value="{{ $country['.key']}}" {{ $country['.key'] == $userCountry ? 'selected': ''}}>
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