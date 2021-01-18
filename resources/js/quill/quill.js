import Quill from 'quill/core';

import Toolbar from 'quill/modules/toolbar';
// import Snow from 'quill/themes/snow';
import Bubble from 'quill/themes/bubble';

import Bold from 'quill/formats/bold';
import Italic from 'quill/formats/italic';
import Header from 'quill/formats/header';


Quill.register({
  'modules/toolbar': Toolbar,
  'themes/bubble': Bubble,
  'formats/bold': Bold,
  'formats/italic': Italic,
  'formats/header': Header
});


export default Quill;