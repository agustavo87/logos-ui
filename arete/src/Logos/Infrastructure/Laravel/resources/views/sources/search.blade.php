<x-layout.default title="Search source">
    @push('head-script')
        <script>

class compAttributtor {
    constructor(initialAttributes) {
        this.initialAttributes = initialAttributes;
    }
    getData() {
        return {
            initialAttributes: this.initialAttributes,
            msg: '',
            render: function () {
                this.msg= "mis atributos son: ";
                this.msg += this.initialAttributes.reduce((list, attr) => {return list + ', ' + attr});
                this.msg += '. Pero si lo requiere, tengo otros.'
            }
        }
    }
}

const initialAttributes = @json($attributes, JSON_PRETTY_PRINT);
const myAttributtor = new compAttributtor( initialAttributes );

class compShareAttributeUpdates {
    constructor(initialAttributes) {
        this.sharedAttributes = initialAttributes;
    }
    getData() {
        return {
            getSharedAttributes: () => {return this.sharedAttributes.slice(0)},
            setSharedAttributes: (v) => {this.sharedAttributes.push(v)},
            myAttributes: this.sharedAttributes.slice(0), // passed by value, just initialization.
            currentAttribute: '',
            addAttribute: function () {
                this.setSharedAttributes(this.currentAttribute);
                this.currentAttribute = '';
            },
            updateMyAttributes: function () {
                this.myAttributes = this.getSharedAttributes();
            }
        }
    }
}

const myCompShareAttributeUpdates = new compShareAttributeUpdates(['attributo uno', 'attributo dos']);


class compNotifySharedAttriubteUpdate {
    constructor(initialAttributes) {
        this.sharedAttributes = initialAttributes;
        this.subscriptions = [];
    }

    update() {
        this.subscriptions.forEach((cb) => {
            cb();
        });
    }

    getData() {
        const xData = {
            myAttributes: this.sharedAttributes.slice(0),
            currentAttribute: '',
            pushSharedAttribute: (attr) => {this.sharedAttributes.push(attr)},
            getSharedAttributes: () => this.sharedAttributes.slice(0),
            update: function () {      // Updates the component.
                this.myAttributes = this.getSharedAttributes();
            },
            subscribe: (cb) => this.subscriptions.push(cb),
            notify: () => this.update(), // Updates the insance not the component.
            initialize: function () {
                this.subscribe(this.update.bind(this));
            },
            shareMyAttribute: function () {
                this.pushSharedAttribute(this.currentAttribute);
                this.currentAttribute = '';
                this.notify();
            }
        }
        return xData;
    }
}

const myCompNotifySharedAttriubteUpdate = new compNotifySharedAttriubteUpdate(['Attributo uno', 'Attributo dos']);

        </script>
    @endpush
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

            {{-- Source Type Select --}}

            <div x-data='compSelectSourceType( @json($sourceTypes, JSON_PRETTY_PRINT) )' class="flex flex-col justify-start">
                <label for="sourceType" class="text-gray-700 m-0  px-1 pt-4">
                    {{__('logos::sources.type')}}:
                </label>
                <select name="type" id="type" aria-label="{{__('logos::sources.type')}}"
                        class="p-2 leading-tight border border-gray-400 rounded-sm focus:outline-none"
                        x-model="selected"
                        x-on:change="$dispatch('logos:source-type-set', {value: selected})"
                >
                    <template x-for="scType in sourceTypes">
                        <option :value="scType.code" x-text="scType.label"></option>
                    </template>
                </select>
            </div>

            {{-- / Source Type Select --}}

            {{-- Owner ID Component --}}

            <div x-data="{ownerID: @json($userID)}">
                <x-form.field name="ownerID" label="ID de usuario" type="number" size="3" placeholder="0"
                              class="self-start w-16"
                              x-model:value="ownerID"
                />
                <span x-text="ownerID == 0 ? 'cualquier usuario' : '0 = cualquiera'"
                      class=" text-xs text-gray-600 italic m-0"
                ></span>
            </div>

            {{-- Owner ID Component --}}

            {{-- Attributes Section --}}

            <div class="pt-5">
                <h3 class="font-semibold text-gray-900 opacity-80">Atributos</h3>
                <div x-data="compSelectAttribute()" class="flex flex-row gap-1"
                     x-init="initialize"
                >
                    <x-form.field label="Atributo" type="text" placeholder="title" name="attribute-show"
                                  container-style="flex: 1 50px" label-padding="px-1 pt-2"
                                  list="attributes" x-model="selected"
                    />
                    <input type="hidden" x-bind:value="selectedCode"
                           name="attribute[1][name]"
                    >
                    <x-form.field name="attribute[1][value]" label="Valor" type="text" placeholder="palabra"
                                  container-style="flex: 2 50px" label-padding="px-1 pt-2" input-name="attribute.1.value"
                    />
                </div>
            </div>

            {{-- / Attributes Section --}}

            <div class="flex justify-end">
                <x-form.button type="submit" class="m-2">{{ __('messages.users.send') }}</x-form.button>
                <x-form.button type="reset" class="m-2">{{ __('messages.users.clear') }}</x-form.button>
            </div>
        </form>
        <hr class="border border-gray-300 my-5" />


        {{-- Attribute List --}}

        <datalist x-data="compAttributeList()" id="attributes"
                  x-init="setAttributes()"
                  x-on:logos:source-type-set.window="handleSourceTypeSet($event, $dispatch)"
        >
            <template x-for="attr in attributes" x-bind:key="attr.code">
                <option x-bind:value="attr.label"></option>
            </template>
        </datalist>

        {{-- / Attribute List --}}

{{--
    Scope Experiment 1

    Experimentar si es posible guardar datos de modo inicialización y que estos elemenntos
    permanezcan disponible de modo estático, a manera de "closure"
    en una función de inicialización de un componente de Alpine.Js.
--}}

<div x-data="myAttributtor.getData()" class="border border-gray-400 m-4 p-4 max-w-screen-md mx-auto">
    <p>The message is: "<span x-text="msg" class="italic text-gray-700 text-sm"></span>". </p>
    <button x-on:click="render" class="px-4 py-3 m-2 bg-blue-900 text-white">
        Mostrar
    </button>
</div>

{{-- / Scope Experiment 1 --}}

{{--
    Scope Experiment 2

    Experimentar si es posible actualizar datos compartidos entre distintos componentes.
--}}

<div class="flex gap-10">
    <div x-data="myCompShareAttributeUpdates.getData()" class=" flex-grow p-4 border border-gray-400">
        <h3 class="font-semibold my-4">Shared Attributes 1</h3>
        <ul>
            <template x-for="attr in myAttributes">
                <li><span x-text="attr"></span></li>
            </template>
        </ul>
        <label for="my-new-attribute-1">Nuevo Atributo:</label>
        <input type="text" x-model="currentAttribute" name="my-new-attribute-1" id="my-new-attribute-1" size="20" class="py-2 px-4 my-2 border border-gray-400 text-sm">
        <span class="block my-4" x-text="currentAttribute"></span>
        <div class="flex flex-row-reverse gap-2">
            <button x-on:click="addAttribute" class="py-2 px-4 bg-blue-800 text-white">Agregar Attributo</button>
            <button x-on:click="updateMyAttributes" class="py-2 px-4 bg-blue-800 text-white">Actualizar Attributos</button>
        </div>
    </div>

    <div x-data="myCompShareAttributeUpdates.getData()" class=" flex-grow p-4 border border-gray-400">
        <h3 class="font-semibold my-4">Shared Attributes 2</h3>
        <ul>
            <template x-for="attr in myAttributes">
                <li><span x-text="attr"></span></li>
            </template>
        </ul>
        <label for="my-new-attribute-2">Nuevo Atributo:</label>
        <input type="text" x-model="currentAttribute" name="my-new-attribute-2" id="my-new-attribute-2" size="20" class="py-2 px-4 my-2 border border-gray-400 text-sm">
        <span class="block my-4" x-text="currentAttribute"></span>
        <div class="flex flex-row-reverse gap-2">
            <button x-on:click="addAttribute" class="py-2 px-4 bg-blue-800 text-white">Agregar Attributo</button>
            <button x-on:click="updateMyAttributes" class="py-2 px-4 bg-blue-800 text-white">Actualizar Attributos</button>
        </div>
    </div>

</div>

{{-- / Scope Experiment 2 --}}

{{--
    Scope Experiment 3

    Experimentar si es posible emitir un evento al actualizar un atributo
    compartido, de modo que cada componente actualice sus atributos locales.
--}}
<hr class="border border-gray-600 my-8">
<h3 class="font-semibold text-xl text-center my-8">Auto update shared attributes</h3>
<div class="flex gap-4">
    <div x-data="myCompNotifySharedAttriubteUpdate.getData()" x-init="initialize" class=" flex-grow p-4 border border-gray-400">
        <h3 class="font-semibold my-4">Shared Attributes 2</h3>
        <ul>
            <template x-for="attr in myAttributes">
                <li><span x-text="attr"></span></li>
            </template>
        </ul>
        <label for="my-new-notifier-attribute-1">Nuevo Atributo:</label>
        <input type="text" x-model="currentAttribute" name="my-new-notifier-attribute-1" id="my-new-notifier-attribute-1" size="20" class="py-2 px-4 my-2 border border-gray-400 text-sm">
        <span class="block my-4" x-text="currentAttribute"></span>
        <button x-on:click="shareMyAttribute" class="py-2 px-4 bg-blue-800 text-white">Compartir Atributo</button>
        <button x-on:click="notify" class="py-2 px-4 bg-blue-800 text-white">Notificar</button>
        <button x-on:click="update" class="py-2 px-4 bg-blue-800 text-white">Actualizar</button>
    </div>
    <div x-data="myCompNotifySharedAttriubteUpdate.getData()" x-init="initialize" class=" flex-grow p-4 border border-gray-400">
        <h3 class="font-semibold my-4">Shared Attributes 2</h3>
        <ul>
            <template x-for="attr in myAttributes">
                <li><span x-text="attr"></span></li>
            </template>
        </ul>
        <label for="my-new-notifier-attribute-2">Nuevo Atributo:</label>
        <input type="text" x-model="currentAttribute" name="my-new-notifier-attribute-2" id="my-new-notifier-attribute-2" size="20" class="py-2 px-4 my-2 border border-gray-400 text-sm">
        <span class="block my-4" x-text="currentAttribute"></span>
        <button x-on:click="shareMyAttribute" class="py-2 px-4 bg-blue-800 text-white">Compartir Atributo</button>
        <button x-on:click="notify" class="py-2 px-4 bg-blue-800 text-white">Notificar</button>
        <button x-on:click="update" class="py-2 px-4 bg-blue-800 text-white">Actualizar</button>
    </div>
</div>

{{-- / Scope Experiment 3 --}}



    </x-container>
@push('foot-script')
<script>
function compSelectSourceType(initST) {
    return {
        sourceTypes: initST,
        selected: null
    }
}

function compAttributeList() {
    return {
        loading:false,
        sourceType:null,
        initAttributes:  @json($attributes, JSON_PRETTY_PRINT),
        attributes: [],
        fetchAttributes: function (sourceType = null) {
            this.loading = true;
            let path = '/test/sources/attributes' + (sourceType ? ('?type=' + sourceType) : '')
            axios.get(path)
                    .then(({data}) => {
                    this.setAttributes(data);
                    this.loading = false;
                    });
        },
        setAttributes: function(data = null) {
            data = data ? data : this.initAttributes;
            this.attributes = data.map((code) => {
                return {code: code, label:code}
            });
        },
        handleSourceTypeSet: function ($event, $dispatch) {
            this.sourceType = $event.detail.value;
            this.fetchAttributes(this.sourceType);
        }
    }
}

function compSelectAttribute() {
    return {
        initAttributes: @json($attributes, JSON_PRETTY_PRINT),
        attributes: [],
        selected: null,
        selectedCode: null,
        setCode: function (selectedValue) {
            let selAttr = this.attributes.find(attr => attr.label == selectedValue );
            if( selAttr !== undefined) {
                this.selectedCode = selAttr.code;
                return;
            }
            this.selectedCode = null;
        },
        initialize: function () {
            this.attributes = this.initAttributes.map((code) => {
                return {code: code, label:code}
            });
            this.$watch('selected', (value) => {
                this.setCode(value);
            });
        }
    }
}
</script>
@endpush

</x-layout.default>
