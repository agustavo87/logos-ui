// import Quill from 'quill'
import Quill, {SourceTypes} from './quill/quill'

const debounce = require('lodash/debounce');

window.Quill = Quill;
window.SourceTypes = SourceTypes;
window.debounce = debounce;


const Logos = {
    quill: null,

    imports: {
        'blots/block': Quill.import('blots/block')
    },
    Citations: null,

    ui: {
        sideControls: null,
        quillContainer: null,
        btnShowSideControls: null
    },

    initialDelta: null,
    meta: [],

    init: function (options) {
        this.initialDelta = options.initialDelta;
        this.ui.sideControls = document.querySelector(options.sideControls)
        this.ui.quillContainer = document.querySelector(options.quillContainer)
        this.ui.btnShowSideControls = document.querySelector(options.btnShowSideControls)
        
        // inicalizar quill
        this.initQuill()

        this.Citations = this.quill.getModule('citations');

        this.quill.addContainer(this.ui.sideControls);
        this.ui.btnShowSideControls.addEventListener('click', () => {
            this.ui.sideControls.classList.toggle('active')
            this.quill.focus()
        })

        if (this.initialDelta) {
            this.quill.setContents(this.initialDelta, 'api');
        }
    },

    initQuill: function () {
        this.quill = new Quill(this.ui.quillContainer, {
            modules: {
                toolbar: '#toolbar',
                citations: {
                    type: SourceTypes.CITATION_VANCOUVER,
                    class: 'citation',
                    handlers: {
                        create: function (node, data, controller) {
                            node.setAttribute('title', data.key)
                        }
                    }
                }
            },
            theme: 'bubble',
            // debug:'info',
            placeholder: "Escribe algo épico..."
        });

        this.quill.on('editor-change', (eventType, ...args) => {
            if (eventType === 'text-change') {
                const [delta, oldDelta, source] = args;
                // console.log("Event '%s'\nrange:%o\noldRange:%o\nsource:%s", 
                //     eventType, delta, oldDelta, source)
                if (source == 'user') {
                    delta.forEach((op) => {
                        if (op.insert == "\n") {
                            // Evaluates in next 'tick', after quill updates.
                            new Promise((resolve, reject) => {
                                resolve();
                            }).then(this.corregirOffset.bind(this));
                        }
                    });
                    this.ui.quillContainer.dispatchEvent(new CustomEvent('quill-input', {
                        bubbles: true,
                        detail: {
                            delta: () => this.quill.getContents(),
                            html: () => this.quill.scroll.domNode.innerHTML,
                            meta: () => this.meta
                        }
                    }))
                }

            } else if (eventType === 'selection-change') {
                const [range, oldRange, source] = args;
                if (range == null) return;
                if (range.length === 0) {
                    // console.log("Event '%s'\nrange:%o\noldRange:%o\nsource:%s", 
                    //   eventType, range, oldRange, source);
                    const [block, offset] = this.quill.scroll.descendant(this.imports['blots/block'], range.index);
                    if (block != null && block.domNode.firstChild instanceof HTMLBRElement) {
                        // console.log("Descendientes:\nblock: %o\noffset: %i",block, offset);
                        let lineBounds = this.quill.getBounds(range);
                        this.ui.sideControls.style.display = 'block'
                        this.ui.sideControls.style.left = lineBounds.left - 42 + "px"
                        // console.log(lineBounds)
                        this.ui.sideControls.style.top = lineBounds.top - 7 + "px"
                    } else {
                        // console.log('escondiendo');
                        this.ui.sideControls.style.display = 'none';
                        this.ui.sideControls.classList.remove('active')
                    }
                } else {
                    this.ui.sideControls.style.display = 'none';
                    this.ui.sideControls.classList.remove('active')
                }
                // console.log("Event '%s'\nrange:%o\noldRange:%o\nsource:%s", 
                //     eventType, range, oldRange, source)

            }
        })
    },

    corregirOffset: function () {
        // console.log('corrigiendo offset');
        let bounds = this.quill.getBounds(this.quill.getSelection().index);
        let margin = bounds.height * 5; // px
        // console.log(bounds);
        let screenBottom = window.pageYOffset + window.innerHeight;
        let cursorBottom = bounds.bottom + this.ui.quillContainer.offsetTop
        // console.log('screen bottom: ' + screenBottom + ': cursorBottom: ' + cursorBottom);
        if (cursorBottom + margin > screenBottom) {
            window.scrollTo(0, cursorBottom + margin * 3 - window.innerHeight)
        }
    }
};
window.Logos = Logos;


console.log('logos.js cargado')