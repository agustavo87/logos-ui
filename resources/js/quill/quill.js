import Quill from 'quill/core';

import Toolbar from 'quill/modules/toolbar';
// import Snow from 'quill/themes/snow';
import Bubble from 'quill/themes/bubble';

import Bold from 'quill/formats/bold';
import Italic from 'quill/formats/italic';
import Header from 'quill/formats/header';
import SourceBlot from 'dsm/quill/blots/source'
import Citations from 'dsm/quill/modules/Citations'
import {SourceTypes} from 'dsm/DSM/SourceTypes'

Quill.register({
  'modules/toolbar': Toolbar,
  'themes/bubble': Bubble,
  'formats/bold': Bold,
  'formats/italic': Italic,
  'formats/header': Header
});

Quill.register(SourceBlot)
Quill.register('modules/citations', Citations)
SourceTypes['CITATION_VANCOUVER'] = "citation-vancouver";




export {Quill as default, SourceTypes};