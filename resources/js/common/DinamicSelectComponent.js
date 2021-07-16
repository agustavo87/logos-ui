export default class DinamicSelectComponent {
    constructor (EventRoom, Options) {
        this.eventRoom = EventRoom;
        this.limitedSharedOptions = new SharedOptionsComponent(Options, EventRoom);
    }

    getRootData() {
        let options = this.limitedSharedOptions._getOptions();
        return {
            options: options,
            optionsLength: options.length,
            count: 0,
            attributes: [],
            initialize: function () {
                this.agregarAttributo();
            },
            updateOptions: () => {
                this.limitedSharedOptions._optionRemoved();
            },
            agregarAttributo: function () {
               this.attributes.push(this.options[++this.count - 1].code);
            },
            quitarAttributo: function () {
                this.attributes.pop();
                this.count--;
                this.$nextTick(() => this.updateOptions());
            }
        }
    }

    getSelectData() {
        return this.limitedSharedOptions.getData();
    }
}
