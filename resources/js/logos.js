// import Quill from 'quill'
import Quill, {SourceTypes} from './quill/quill'

const debounce = require('lodash/debounce');

window.Quill = Quill;
window.SourceTypes = SourceTypes;
window.debounce = debounce;

console.log('logos.js cargado')