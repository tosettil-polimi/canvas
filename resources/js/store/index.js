import Vue from 'vue';
import Vuex from 'vuex';
import config from './modules/config';
import post from './modules/post';
import auth from './modules/auth';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        config,
        post,
    },
});
