export default class EventRoom {
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
            throw new ReferenceError('Event Call Error: The topic \'' + topic + '\' is not registered.');
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
