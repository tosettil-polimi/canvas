import moment from 'moment';
import request from './mixins/request';
import router from './router';
import Vue from 'vue';

require('bootstrap');

window.Popper = require('popper.js').default;

Vue.prototype.moment = moment;

Vue.mixin(request);

Vue.config.productionTip = false;

new Vue({
    el: '#canvas',
    router,
});
