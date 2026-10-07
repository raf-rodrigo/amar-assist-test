import _ from 'lodash';
window._ = _;

// Carrega o Axios para realizar requisições HTTP à API Laravel.

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
