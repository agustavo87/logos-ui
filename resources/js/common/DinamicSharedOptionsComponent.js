/** Class for Alpine.js components that share available options */
export default class DinamicSharedOptionsComponent {

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

        setTimeout(this._optionsChanged.bind(this)); // next loop;
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

        setTimeout(this._optionsChanged.bind(this)); // next loop;
        return option;
    }

    _optionsChanged() {
        this._eventRoom.notify('available-options-change', this._getAvailableOptions());
    }

    getData(props = null) {
        let myOption = this._takeFirstOption(); // changes available options

        return {
            /**@prop {UIOption[]} */
            myOptions: [],

            /**@prop {UIOption|null} */
            ownedOption: myOption,

            selectedOption: myOption.code,

            getAvailableOptions: () => this._getAvailableOptions(),

            /**
            * @param {UIOption}    giveUpOption
            * @param {string}      takeOptionCode
            */
            exchangeOptions: (giveUpOption, takeOptionCode) => this._exchangeOption(giveUpOption, takeOptionCode),

            subscribe: (topic, cb) => this._eventRoom.subscribe(topic, cb),

            updateMyOptions: function (topic, availableOptions) {
                let options = [...availableOptions]; // modify a copy, not the shared array.
                options.unshift(this.ownedOption);
                this.myOptions = options;
            },

            dataset: {
                attribute: ''
            },

            initialize: function () {
                this.dataset = Object.assign({}, this.$el.parentElement.dataset)
                let options = this.getAvailableOptions();
                options.unshift(this.ownedOption);
                this.myOptions = options;

                this.subscribe('available-options-change', this.updateMyOptions.bind(this));

                this.$watch('selectedOption', (value) => {
                    this.ownedOption = this.exchangeOptions(this.ownedOption, value);
                });
            }
        }
    }
}
