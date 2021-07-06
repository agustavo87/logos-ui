<x-layout.default title="Search source">
    <x-container>
        <x-main-heading>
          Buscar fuente
        </x-main-heading>
        <p>
          Introduce la fuente que deseas buscar.
        </p>
        <form action="/test/sources/search" method="POST"
            class="mx-auto max-w-screen-lg rounded-lg bg-gray-100 p-4 flex flex-col
                   justify-start shadow-lg mt-5"
        >
        @csrf
            <x-form.select name="type" label="Tipo">
                @foreach (['journalArticle', 'book', 'bookSection' ] as $sourceType)
                  <option value="{{ $sourceType }}" {{ old('type') == $sourceType ? 'selected' : ''}}>
                    {{ $sourceType }}
                  </option>
                @endforeach
            </x-form.select>
            <x-form.field name="ownerID" label="ID de usuario" type="number"
            size="3" placeholder="0"  class="self-start w-16" :value=$userID  />
            <div class="pt-5">
                <h3 class="font-semibold text-gray-900 opacity-80">Atributos</h3>
                <div class="flex flex-row gap-1">
                    <x-form.field name="attribute[1][name]" label="Atributo"
                        type="text" placeholder="title"
                        container-style="flex: 1 50px"  label-padding="px-1 pt-2"
                        input-name="attribute.1.name"
                    />
                    <x-form.field name="attribute[1][value]" label="Valor"
                        type="text" placeholder="palabra"
                        container-style="flex: 2 50px" label-padding="px-1 pt-2"
                        input-name="attribute.1.value"
                    />
                </div>
                <div class="flex flex-row gap-1">
                    <x-form.field name="attribute[2][name]" label="Atributo"
                        type="text" placeholder="title"
                        container-style="flex: 1 50px"  label-padding="px-1 pt-2"
                        input-name="attribute.2.name"
                    />
                    <x-form.field name="attribute[2][value]" label="Valor"
                        type="text" placeholder="palabra"
                        container-style="flex: 2 50px" label-padding="px-1 pt-2"
                        input-name="attribute.2.value"
                    />
                </div>
                <div class="flex flex-row gap-1">
                    <x-form.field name="attribute[3][name]" label="Atributo"
                        type="text" placeholder="title"
                        container-style="flex: 1 50px"  label-padding="px-1 pt-2"
                        input-name="attribute.3.value"
                    />
                    <x-form.field name="attribute[3][value]" label="Valor"
                        type="text" placeholder="palabra"
                        container-style="flex: 2 50px" label-padding="px-1 pt-2"
                        input-name="attribute.3.value"
                    />
                </div>
            </div>
            <div class="flex justify-end">
              <x-form.button type="submit" class="m-2">{{ __('messages.users.send') }}</x-form.button>
              <x-form.button type="reset" class="m-2">{{ __('messages.users.clear') }}</x-form.button>
            </div>
          </form>
      </x-container>
</x-layout.default>
