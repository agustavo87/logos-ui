<x-layout.default title="Search source">
    <x-container class=" mb-5">
        <x-main-heading>
            Buscar fuente
        </x-main-heading>
        <p class=" text-gray-800">
            Introduce la fuente que deseas buscar.
        </p>
        <form action="/test/sources/search" method="POST" class="mx-auto max-w-screen-lg rounded-lg bg-gray-100 p-4 flex flex-col
                   justify-start shadow-lg mt-5">
            @csrf
            <x-form.select name="type" label="{{__('logos::sources.type')}}">
                @foreach ($sourceTypes as $type)
                <option value="{{ $type->code }}" {{ old('type') == $type->code ? 'selected' : ''}}>
                    {{ $type->label }}
                </option>
                @endforeach
            </x-form.select>
            <x-form.field name="ownerID" label="ID de usuario" type="number" size="3" placeholder="0"
                class="self-start w-16" :value=$userID />
            <div class="pt-5">
                <h3 class="font-semibold text-gray-900 opacity-80">Atributos</h3>
                <div class="flex flex-row gap-1">
                    <x-form.field name="attribute[1][name]" label="Atributo" type="text" placeholder="title"
                        container-style="flex: 1 50px" label-padding="px-1 pt-2" input-name="attribute.1.name" />
                    <x-form.field name="attribute[1][value]" label="Valor" type="text" placeholder="palabra"
                        container-style="flex: 2 50px" label-padding="px-1 pt-2" input-name="attribute.1.value" />
                </div>
                <div class="flex flex-row gap-1">
                    <x-form.field name="attribute[2][name]" label="Atributo" type="text" placeholder="title"
                        container-style="flex: 1 50px" label-padding="px-1 pt-2" input-name="attribute.2.name" />
                    <x-form.field name="attribute[2][value]" label="Valor" type="text" placeholder="palabra"
                        container-style="flex: 2 50px" label-padding="px-1 pt-2" input-name="attribute.2.value" />
                </div>
                <div class="flex flex-row gap-1">
                    <x-form.field name="attribute[3][name]" label="Atributo" type="text" placeholder="title"
                        container-style="flex: 1 50px" label-padding="px-1 pt-2" input-name="attribute.3.value" />
                    <x-form.field name="attribute[3][value]" label="Valor" type="text" placeholder="palabra"
                        container-style="flex: 2 50px" label-padding="px-1 pt-2" input-name="attribute.3.value" />
                </div>
            </div>
            <div class="flex justify-end">
                <x-form.button type="submit" class="m-2">{{ __('messages.users.send') }}</x-form.button>
                <x-form.button type="reset" class="m-2">{{ __('messages.users.clear') }}</x-form.button>
            </div>
        </form>
        <hr class="border border-gray-300 my-5" />
        <script>
            function attributes() {
                return {
                    selected: '',
                    loading: false,
                    attributes: [
                        {
                            code: 'title',
                            label: 'Título'
                        },
                        {
                            code: 'abstractNote',
                            label: 'Resumen'
                        }
                    ],
                    getCode: function () {
                        if (selAttr = this.attributes.find(attr => attr.label == this.selected)) {
                            return selAttr.code;
                        }
                        return '';
                    },
                    fetchAttributes: function () {
                        this.loading = true;
                        axios.get('/test/sources/attributes')
                             .then(({data}) => {
                                console.log(data)
                                attributes = data.map((code) => {
                                    return {code: code, label:code}
                                });
                                this.attributes = attributes;
                                this.loading = false;
                             });
                    }
                }
            }
        </script>
        <div class="border border-gray-300 mx-auto my-5 p-4"
            x-data="attributes()"
            x-init="fetchAttributes"
        >
            <span x-show="loading" class=" text-sm text-gray-700 italic" x-cloak>Cargando...</span>
            <h2 class="text-lg font-semibold my-2">Hola voy a ser un componente de AlpineJS </h2>
            <p>Código Selccionado: <span x-text="getCode()"></span></p>
            <form>
                <div class="m-2">
                    <label for="attribute-choice">Attributes</label>
                    <input type="text" size="8" id="attribute-choice" name="attribute-choice"
                           list="attributes"
                           class="border mt-2 block p-1"
                           x-model="selected"
                    >
                    <input type="hidden"
                           name="attribute-choice-code"
                           :value="getCode()" id="attribute-choice-code">
                </div>
                <datalist id="attributes">
                    <template x-for="attr in attributes" :key="attr.code">
                        <option :value="attr.label"></option>
                    </template>
                </datalist>

            </form>

        </div>
    </x-container>
</x-layout.default>
