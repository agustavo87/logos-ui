<x-layout.default title="Scope experiments">
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
        this._sharedAttributes = initialAttributes;
        this._subscriptions = [];
    }

    _updateInstance() {
        this._subscriptions.forEach((cb) => {
            cb();
        });
    }

    _getData() {
        const xData = {
            myAttributes: this._sharedAttributes.slice(0),
            currentAttribute: '',
            subscribe: (cb) => this._subscriptions.push(cb),
            notify: () => this._updateInstance(),    // Updates the insance not the component.
            initialize: function () {
                this.subscribe(this.updateMe.bind(this));
            },
            updateMe: function () {           // Updates the component.
                this.myAttributes = this.getSharedAttributes();
            },
            pushSharedAttribute: (attr) => {this._sharedAttributes.push(attr)},
            getSharedAttributes: () => this._sharedAttributes.slice(0),
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
        Experimentos de scope
    </x-main-heading>
    <p class=" text-gray-800">
        Experimentos.
    </p>

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
    <div x-data="myCompNotifySharedAttriubteUpdate._getData()" x-init="initialize" class=" flex-grow p-4 border border-gray-400">
        <h3 class="font-semibold my-4">Shared Attributes 2</h3>
        <ul>
            <template x-for="attr in myAttributes">
                <li><span x-text="attr"></span></li>
            </template>
        </ul>
        <label for="my-new-notifier-attribute-1">Nuevo Atributo:</label>
        <input type="text" x-model="currentAttribute" name="my-new-notifier-attribute-1" id="my-new-notifier-attribute-1" size="20" class="py-2 px-4 my-2 border border-gray-400 text-sm">
        <button x-on:click="shareMyAttribute" class="py-2 px-4 bg-blue-800 text-white">Compartir Atributo</button>
        <div><p>Compartir: <span class="my-4" x-text="currentAttribute"></span></p></div>
    </div>
    <div x-data="myCompNotifySharedAttriubteUpdate._getData()" x-init="initialize" class=" flex-grow p-4 border border-gray-400">
        <h3 class="font-semibold my-4">Shared Attributes 2</h3>
        <ul>
            <template x-for="attr in myAttributes">
                <li><span x-text="attr"></span></li>
            </template>
        </ul>
        <label for="my-new-notifier-attribute-2">Nuevo Atributo:</label>
        <input type="text" x-model="currentAttribute" name="my-new-notifier-attribute-2" id="my-new-notifier-attribute-2" size="20" class="py-2 px-4 my-2 border border-gray-400 text-sm">
        <button x-on:click="shareMyAttribute" class="py-2 px-4 bg-blue-800 text-white">Compartir Atributo</button>
        <div><p>Compartir: <span class="my-4" x-text="currentAttribute"></span></p></div>
    </div>
</div>

{{-- / Scope Experiment 3 --}}

</x-container>
</x-layout.default>
