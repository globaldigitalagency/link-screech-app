
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap.bundle.js';

import '../scss/app.scss';

import showToasts from './utils/_toast';

window.addEventListener('load', function (event) {
    showToasts();
});
