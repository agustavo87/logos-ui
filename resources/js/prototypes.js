import EventRoom from './common/EventRoom';
import SharedOptionsComponent from './common/SharedOptionsComponent';
import DinamicSelectComponent from './common/DinamicSelectComponent';

window.EventRoom = EventRoom;
window.SharedOptionsComponent = SharedOptionsComponent;
window.DinamicSelectComponent = DinamicSelectComponent;

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
