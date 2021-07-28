// import Quill from 'quill'
import Quill, {SourceTypes} from './quill/quill'

const debounce = require('lodash/debounce');

window.Quill = Quill;
window.SourceTypes = SourceTypes;
window.debounce = debounce;

class Logos {
    constructor(options) {
        this.ui = {};
        this.ui.sideControls = document.querySelector(options.sideControls)
        this.ui.quillContainer = document.querySelector(options.quillContainer)
        this.ui.btnShowSideControls = document.querySelector(options.btnShowSideControls)
        this.ui.toolbar = document.querySelector(options.toolbar)

        this.initialDelta = options.initialDelta;
        this.meta = [];

        this.imports = {
            'blots/block': Quill.import('blots/block')
        };
        this.initQuill()
        this.bindUIHandlers()
        this.Citations = this.quill.getModule('citations');

        if (this.initialDelta) {
            this.quill.setContents(this.initialDelta, 'api');
        }
    }

    initQuill () {
        this.quill = new Quill(this.ui.quillContainer, {
            modules: {
                toolbar: this.ui.toolbar,
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
            placeholder: "Escribe algo épico..."
        });

        this.quill.on('editor-change', (eventType, ...args) => {
            if (eventType === 'text-change') {
                const delta = args[0];
                const source = args[2];
                if (source == 'user') {
                    this.checkBottomBoundTrasspassing(delta);
                    this.notifyQuillInput();
                }

            } else if (eventType === 'selection-change') {
                const range = args[0];
                this.checkSideBarDisplay(range);
            }
        })
    }

    checkSideBarDisplay(range) {
        if (range == null) return;
        if (range.length === 0) { // there's nothing selected
            // console.log('cambio de seleccion, viendo si es una linea vacía')
            const [block, offset] = this.quill.scroll.descendant(this.imports['blots/block'], range.index);
            // check if the only element in the line is a line break, so show side tools.
            if (block != null && block.domNode.firstChild instanceof HTMLBRElement) {
                // console.log('es una linea vacía')
                let lineBounds = this.quill.getBounds(range);
                this.ui.sideControls.style.display = 'block'
                this.ui.sideControls.style.left = lineBounds.left - 48 + "px"
                this.ui.sideControls.style.top = lineBounds.top - 10 + "px" /** @todo calcular el centro */
            } else {
                this.ui.sideControls.style.display = 'none';
                this.ui.sideControls.classList.remove('active')
            }
        } else {
            this.ui.sideControls.style.display = 'none';
            this.ui.sideControls.classList.remove('active')
        }
    }

    checkBottomBoundTrasspassing(delta) {
        delta.forEach((op) => {
            if (op.insert == "\n") {
                // Evaluates in next 'tick', after quill updates.
                window.setTimeout(this.fixOffset.bind(this))
            }
        });
    }

    notifyQuillInput() {
        this.ui.quillContainer.dispatchEvent(new CustomEvent('quill-input', {
            bubbles: true,
            detail: {
                delta: () => this.quill.getContents(),
                html: () => this.quill.scroll.domNode.innerHTML,
                meta: () => this.meta
            }
        }))
    }

    bindUIHandlers() {
        this.quill.addContainer(this.ui.sideControls);
        this.ui.btnShowSideControls.addEventListener('click', () => {
            this.ui.sideControls.classList.toggle('active')
            this.quill.focus()
        })
    }

    fixOffset () {
        let bounds = this.quill.getBounds(this.quill.getSelection().index);
        let margin = bounds.height * 5;
        let screenBottom = window.pageYOffset + window.innerHeight;
        let cursorBottom = bounds.bottom + this.ui.quillContainer.offsetTop

        if (cursorBottom + margin > screenBottom) {
            window.scrollTo(0, cursorBottom + margin * 3 - window.innerHeight)
        }
    }
};

window.Logos = Logos;
