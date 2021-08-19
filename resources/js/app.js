import Alpine from 'alpinejs'

require('./bootstrap');

window.Alpine = Alpine

Alpine.start()

const LivewireLiveMsjs = {
    count: 0,
    handleEvent: function (kind, component)  {
        if (kind == 'sent') {this.count++}
        if (kind == 'processed') {this.count--}
        this.notify(kind, component);
    },
    notify: function(kind, component) {
        window.dispatchEvent(new CustomEvent('lw:message-change', {
            detail:{
                count:this.count,
                kind: kind,
                component, component,
                loading: this.count > 0,
            }
        }))
    }
}

document.addEventListener("DOMContentLoaded", () => {
    Livewire.hook('message.sent', (message, component) => LivewireLiveMsjs.handleEvent('sent', component.fingerprint.name))
    Livewire.hook('message.failed', (message, component) => LivewireLiveMsjs.handleEvent('failed', component.fingerprint.name))
    Livewire.hook('message.received', (message, component) => LivewireLiveMsjs.handleEvent('received', component.fingerprint.name))
    Livewire.hook('message.processed', (message, component) => LivewireLiveMsjs.handleEvent('processed', component.fingerprint.name))
})
