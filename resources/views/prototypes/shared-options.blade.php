<x-layout.default title="Scope experiments">
@push('head-script')
@verbatim
<script>

class EventRoom {
    constructor() {
        this.subscriptions = {};
    }

    /**
     * Callback that listen events.
     * @callback listenerCallback
     * @param {string} topic
     * @param {*} message
     */

    /**
     * @param {string} topic
     * @param {listenerCallback} cb
     */
     subscribe(topic, cb) {
        if (!this.topicExists(topic)) {
            this.subscriptions[topic] = [];
        }
        this.subscriptions[topic].push(cb);
    }

    /**
     * @param {string}  topic
     * @param {*}       message
     */
     notify(topic, message) {
        if (!this.topicExists(topic)) {
            throw new ReferenceError('The topic \'' + topic + '\' is not registered.');
        }
        this.subscriptions[topic].forEach(cb => cb(topic, message));
    }

    /**
     * @param {string} topic
     * @returns {boolean}
     */
     topicExists(topic) {
        return this.subscriptions.hasOwnProperty(topic);
    }
}

/** Class for Alpine.js components that share available options */
class xSharedOptions {

    /**
     * An option with label.
     * @typedef {{code:string, label:string, order:number}} UIOption
     */

    /**
     * An UIOption, with state info.
     * @typedef {{code:string, label:string, order:number, taken:boolean}} StateUIOption
     */

    /**
     * Create component instance.
     * @param {UIOption[]} options - The (initial) available options.
     * @param {EventRoom} eventRoom
     */
    constructor(options, eventRoom) {
        this._eventRoom = eventRoom;
        this._availableOptions = this._mapToStateUIOption(options);
        this._orderOptions();
    }

    /**
     * @param {UIOption[]} UIOptions
     * @returns {StateUIOption[]}
     */
    _mapToStateUIOption(UIOptions) {
        return UIOptions.map(opt => this._toStateUIOption(opt));
    }

    /**
     * @param {StateUIOption[]} UIOptions
     * @returns {UIOption[]}
     */
     _mapToUIOption(UIOptions) {
        return UIOptions.map(opt => this._toUIOption(opt));
    }

    /**
     * @param {UIOption} UIOption
     * @return {StateUIOption}
     */
    _toStateUIOption(UIOption) {
        let copy = Object.assign({}, UIOption);
        copy['taken'] = false;
        return copy;
    }

    /**
     * @param {StateUIOption} StateUIOption
     * @return {UIOption}
     */
    _toUIOption(StateUIOption) {
        let copy = Object.assign({}, StateUIOption);
        delete copy.taken;
        return copy;
    }

    _orderOptions() {
        this._availableOptions.sort((a,b) => a.order - b.order);
    }

    /**
     * @param {string} reqOptCode - Requested option code.
     * @returns {UIOption}
     */
    _take(reqOptCode) {
        let i = this._availableOptions.findIndex(opt => opt.code == reqOptCode);
        this._availableOptions[i].taken = true;
        return this._toUIOption(this._availableOptions[i]);
    }

    /** @param {UIOption} option */
    _return(option) {
        let stateOption = this._availableOptions.find(opt => opt.code === option.code);
        stateOption.taken = false;
    }

    /** @returns {UIOption[]} */
    _getAvailableOptions() {
        return this._mapToUIOption(this._availableOptions.filter(suiOpt => suiOpt.taken === false));
    }

    /** @returns {UIOption} */
    _takeFirstOption() {
        let stateOption = this._availableOptions.find((opt) => opt.taken == false);
        stateOption.taken = true;
        let option = this._toUIOption(stateOption);
        return option;
    }

    /**
     * @param {UIOption}    giveUpOption
     * @param {string}      takeOptionCode
     * @returns {UIOption}
     */
    _exchangeOption(giveUpOption, takeOptionCode) {
        this._return(giveUpOption);
        let option = this._take(takeOptionCode);
        setTimeout(this._updateOptions.bind(this));
        return option;
    }

    _updateOptions() {
        this._eventRoom.notify('source-option-change', null)
    }

    getData() {
        let myOption = this._takeFirstOption();
        let myOptions = this._getAvailableOptions();    // the options that rest after take the first.
        myOptions.unshift(myOption);                    // my options include the one i take + the availables.
        return {
            /**@prop {UIOption[]} */
            myOptions: myOptions,

            /**@prop {UIOption|null} */
            ownedOption: myOption,

            selectedOption: myOption.code,

            getAvailableOptions: () => this._getAvailableOptions(),

            /**
            * @param {UIOption}    giveUpOption
            * @param {string}      takeOptionCode
            */
            exchangeOptions: (giveUpOption, takeOptionCode) => this._exchangeOption(giveUpOption, takeOptionCode),

            subscribe: (cb) => this._eventRoom.subscribe('source-option-change', cb),

            initialized: (x = null) => this._eventRoom.notify('source-option-change', x),

            updateMyOptions: function (topic, message) {
                // console.log(this.$el.id + ': actualizando mis opciones topico: ' + topic + ', message:', message);
                let options = this.getAvailableOptions();
                options.unshift(this.ownedOption);
                this.myOptions = options;
            },

            initialize: function () {
                this.subscribe(this.updateMyOptions.bind(this));

                this.$watch('selectedOption', (value) => {
                    this.ownedOption = this.exchangeOptions(this.ownedOption, value);
                });
                this.initialized();
            }
        }
    }
}

const testOptions = [
    {
        code: "journalArticle",
        label: "Journal Article",
        order: 0
    },
    {
        code: "book",
        label: "Book",
        order: 1
    },
    {
        code: "bookSection",
        label: "Book Section",
        order: 2
    },
    {
        code: "blogPost",
        label: "Blog Post",
        order: 3
    }
];
const myEventRoom = new EventRoom();
const mySharedOptions = new xSharedOptions(testOptions, myEventRoom);

</script>
@endverbatim
@endpush
<x-container class=" mb-5">
    <x-main-heading>
        Experimentos de opciones compartidas
    </x-main-heading>
{{--
    Experimento 1: Opciones compartidas
    Varios componentes comparten opciones disponibles. Cuando en un componente
    se selecciona una opción, esta opción ya no se encuentra disponible en los
    demás.
    El estado de las opciones se actualiza automáticamente al cambiar cada
    compoentene particular.
--}}

<div class=" max-w-screen-md mx-auto border border-gray-400 p-4 m-4 rounded-md">
    <form class="flex flex-col">
        <div x-data="mySharedOptions.getData()" x-init="initialize" id="source-type-select-1" class=" w-52 flex" >
            <select name="sourceAttributes" id="sourceAttributes"
                    x-model="selectedOption" class=" focus:outline-none flex-grow py-2 px-4 m-2 rounded-sm border"
            >
                <template x-for="option in myOptions" x-bind:key="option.code">
                    <option x-bind:value="option.code" x-text="option.label"></option>
                </template>
            </select>
        </div>
        <div x-data="mySharedOptions.getData()" x-init="initialize" id="source-type-select-2"  class=" w-52 flex">
            <select name="sourceAttributes-2" id="sourceAttributes-2"
                    x-model="selectedOption" class=" focus:outline-none flex-grow py-2 px-4 m-2 rounded-sm border"
            >
                <template x-for="option in myOptions" x-bind:key="option.code">
                    <option x-bind:value="option.code" x-text="option.label"></option>
                </template>
            </select>
        </div>
    </form>
</div>


</x-container>
</x-layout.default>
