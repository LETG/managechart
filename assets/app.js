/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/styleGlob.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/bootstrap-theme.min.css';
import './styles/jquery.tablesorter.css';
import './styles/toastr.min.css';
import './styles/loader.css';
import './styles/widearea.min.css';
import './styles/dataTables.bootstrap.min.css';

const $ = require('jquery');
global.$ = global.jQuery = global.jquery = $;
import 'bootstrap';
const Cookies = require('./js/js.cookie.js');
global.Cookies = Cookies;
import './js/jquery-ui.min.js';
import './js/navColor.js';
const toastr = require('./js/toastr.min.js');
global.toastr = toastr;
import './js/widearea.min.js';
import './js/jquery.dataTables.min.js';
import './js/dataTables.bootstrap.min.js';
import './js/jquery.mousewheel.min.js';
const dt = require('datatables.net');
