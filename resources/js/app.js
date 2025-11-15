import 'lazysizes';
// import a plugin
import 'lazysizes/plugins/parent-fit/ls.parent-fit';

window.lazySizesConfig = window.lazySizesConfig || {};
window.lazySizesConfig.expand = 300;
window.lazySizesConfig.expFactor = 1.5;


import './bootstrap';
import './animations.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
