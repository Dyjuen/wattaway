import 'lazysizes';
// import a plugin
import 'lazysizes/plugins/parent-fit/ls.parent-fit';

window.lazySizesConfig = window.lazySizesConfig || {};
window.lazySizesConfig.expand = 100;
window.lazySizesConfig.expFactor = 1;


import './bootstrap';
import './animations.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
