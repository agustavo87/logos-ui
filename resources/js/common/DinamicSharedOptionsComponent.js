/** Class for Alpine.js components that share available options */
export default class DinamicSharedOptionsComponent {

    /**
     * An option with label.
     * @typedef {{code:string, label:string, order:number}} UIOption
     */

    /**
     * An UIOption, with state info.
     * @typedef {{code:string, label:string, order:number, taken:boolean, owner:HTMLElement|null}} StateUIOption
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
        return  UIOptions.map((opt) => this._toUIOption(opt));
    }

    /**
     * @param {UIOption} UIOption
     * @return {StateUIOption}
     */
    _toStateUIOption(UIOption) {
        return Object.assign({taken: false, owner:null}, UIOption);
    }

    /**
     * @param {StateUIOption} StateUIOption
     * @return {UIOption}
     */
    _toUIOption(StateUIOption) {
        let uiopt = Object.assign({}, StateUIOption);
        delete uiopt.owner;
        delete uiopt.taken;
        return uiopt;
    }

    _orderOptions() {
        this._availableOptions.sort((a,b) => a.order - b.order);
    }

    /** make available options with inexistent nodes */
    _screenOptions() {
        this._availableOptions.forEach( (option) => {
            if (option.owner && !document.body.contains(option.owner)) {
                option.taken = false;
                option.owner = null;
            }
        });
    }

    /**
     * @param {string} reqOptCode - Requested option code.
     * @param {HTMLElement} owner - the owner who take it.
     * @returns {UIOption}
     */
    _take(reqOptCode, owner) {
        let i = this._availableOptions.findIndex(opt => opt.code == reqOptCode);
        let reqOption = this._availableOptions[i];
        reqOption.taken = true;
        reqOption.owner = owner;
        return this._toUIOption(reqOption);
    }

    /** @param {string} optionCode */
    _return(optionCode) {
        let stateOption = this._availableOptions.find(opt => opt.code === optionCode);
        stateOption.taken = false;
        stateOption.owner = null;
    }

    _optionRemoved() {
        this._optionsChanged();
    }

    _getOptions() {
        return this._mapToUIOption(this._availableOptions);
    }

    /** @returns {UIOption[]} */
    _getAvailableOptions() {
        this._screenOptions();
        return this._mapToUIOption(this._availableOptions.filter(suiOpt => suiOpt.taken === false));
    }

    /**
     * @param {HTMLElement}  owner - the node that request an option.
     * @returns {UIOption}
     */
    _takeFirstOption(owner) {
        let stateOption = this._availableOptions.find((opt) => opt.taken == false);
        let option = this._take(stateOption.code, owner);

        setTimeout(this._optionsChanged.bind(this)); // next loop;
        return option;
    }


    _count() {
        return this._availableOptions.length;
    }

    /**
     * @param {UIOption}    giveUpOption
     * @param {string}      takeOptionCode
     * @param {HTMLElement} owner
     * @returns {UIOption}
     */
    _exchangeOption(giveUpOption, takeOptionCode, owner) {
        this._return(giveUpOption.code);
        let option = this._take(takeOptionCode, owner);

        setTimeout(this._optionsChanged.bind(this)); // next loop;
        return option;
    }

    _optionsChanged() {
        this._eventRoom.notify('available-options-change', this._getAvailableOptions());
    }

    getData(props = null) {

        return {
            /**@prop {UIOption[]} */
            myOptions: [],

            /**@prop {UIOption|null} */
            ownedOption: null,

            selectedOption: null,

            getAvailableOptions: () => this._getAvailableOptions(),

            /**
            * @param {UIOption}     giveUpOption
            * @param {string}       takeOptionCode
            * @param {HTMLElement}  owner
            */
            exchangeOptions: (giveUpOption, takeOptionCode, owner) => {
                return this._exchangeOption(giveUpOption, takeOptionCode, owner)
            },

            takeFirstOption: (owner) => this._takeFirstOption(owner),

            subscribe: (topic, cb) => this._eventRoom.subscribe(topic, cb),

            updateMyOptions: function (topic, availableOptions) {
                let options = [...availableOptions];
                options.unshift(this.ownedOption);
                this.myOptions = options;
            },

            dataset: {
                attribute: ''
            },

            initialize: function () {
                // update dataset
                this.dataset = Object.assign({}, this.$el.parentElement.dataset)

                // take one option
                let myOption = this.takeFirstOption(this.$el); // changes available options
                this.ownedOption = myOption;
                this.selectedOption = myOption.code;

                // get the rest of available options
                let options = this.getAvailableOptions();
                options.unshift(this.ownedOption);
                this.myOptions = options;

                this.subscribe('available-options-change', this.updateMyOptions.bind(this));

                this.$watch('selectedOption', (value) => {
                    this.ownedOption = this.exchangeOptions(this.ownedOption, value, this.$el);
                });
            }
        }
    }
}
