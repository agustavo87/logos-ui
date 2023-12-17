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
                component: component,
                componentName: component.fingerprint.name,
                loading: this.count > 0,
            }
        }))
    }
}

document.addEventListener("DOMContentLoaded", () => {
    Livewire.hook('message.sent', (message, component) => LivewireLiveMsjs.handleEvent('sent', component))
    Livewire.hook('message.failed', (message, component) => LivewireLiveMsjs.handleEvent('failed', component))
    Livewire.hook('message.received', (message, component) => LivewireLiveMsjs.handleEvent('received', component))
    Livewire.hook('message.processed', (message, component) => LivewireLiveMsjs.handleEvent('processed', component))
})


window.inyectReferences = function (references = {}) {
    document.querySelectorAll('.ed-source').forEach((sourceNode) => {
        const key = sourceNode.dataset.key
        const citationNode = sourceNode.querySelector('.citation')
        
        citationNode.setAttribute('title', references[key])

        const link = document.createElement('a');
        link.href = `#ref-${key}`;
        link.appendChild(sourceNode.cloneNode(true));
        sourceNode.parentNode.replaceChild(link, sourceNode);

    })
}