// any CSS you import will output into a single css file (chart.css in this case)
import './styles/styleGlob.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/loader.css';

const $ = require('jquery');
global.$ = global.jQuery = global.jquery = $;
import 'bootstrap';
const Cookies = require('./js/js.cookie.js');
global.Cookies = Cookies;
import 'bootstrap/dist/js/bootstrap.min.js';
import './js/navColor.js';
import './js/Chart/color.js'
const Highcharts = require('./js/Chart/highcharts.js');
global.Highcharts = Highcharts;
import './js/Chart/highcharts-more.js';
import './js/Chart/heatmapscript.js';
import './js/Chart/heatmap.js';
