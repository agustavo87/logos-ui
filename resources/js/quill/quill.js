import Quill from 'quill/core';

import Toolbar from 'quill/modules/toolbar';
// import Snow from 'quill/themes/snow';
import Bubble, {BubbleTooltip} from 'quill/themes/bubble';

import Bold from 'quill/formats/bold';
import Italic from 'quill/formats/italic';
import Link from 'quill/formats/link';
import Header from 'quill/formats/header';
import SourceBlot from 'dsm/quill/blots/source'
import Citations from 'dsm/quill/modules/Citations'
import {SourceTypes} from 'dsm/DSM/SourceTypes'

BubbleTooltip.TEMPLATE = [
  '<span class="ql-tooltip-arrow"></span>',
  '<div class="ql-tooltip-editor">',
    '<input type="text" data-formula="e=mc^2" data-link="https://arete.com" data-video="Embed URL">',
    '<a class="ql-close"></a>',
  '</div>'
].join('');

Quill.register({
  'modules/toolbar': Toolbar,
  'themes/bubble': Bubble,
  'formats/bold': Bold,
  'formats/italic': Italic,
  'formats/link': Link,
  'formats/header': Header
});

Quill.register(SourceBlot)
Quill.register('modules/citations', Citations)
SourceTypes['CITATION_VANCOUVER'] = "citation-vancouver";




export {Quill as default, SourceTypes};