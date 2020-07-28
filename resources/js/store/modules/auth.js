import request from "../../mixins/request";
import isEmpty from "lodash/isEmpty";
import url from "../../mixins/url";

const initialState = {
    id: window.Canvas.user.id,
    name: window.Canvas.user.name,
    email: window.Canvas.user.email,
    avatar: window.Canvas.user.avatar,
    username: window.Canvas.user.username,
    summary: window.Canvas.user.summary,
    digest: window.Canvas.user.digest,
    darkMode: window.Canvas.user.darkMode,
    locale: window.Canvas.user.locale,
    admin: window.Canvas.user.admin,
};

const state = { ...initialState };

const actions = {
    setAvatar(context, payload) {
        let url = isEmpty(payload) ? url.methods.gravatar(state.email) : payload;

        context.commit('SET_AVATAR', url);
    },

    updateDigest(context, payload) {
        request.methods
            .request()
            .post('/api/users/' + state.id, payload)
            .then(({ data }) => {
                context.commit('UPDATE_DIGEST', data.meta);
            })
            .catch((errors) => {
                console.log(errors);
            });
    },

    updateLocale(context, payload) {
        request.methods
            .request()
            .post('/api/users/' + state.id, payload)
            .then(({ data }) => {
                context.commit('UPDATE_LOCALE', data.meta);
            })
            .catch((errors) => {
                console.log(errors);
            });
    },

    updateDarkMode(context, payload) {
        request.methods
            .request()
            .post('/api/users/' + state.id, payload)
            .then(({ data }) => {
                context.commit('UPDATE_DARK_MODE', data.meta);
            })
            .catch((errors) => {
                console.log(errors);
            });
    },
};

const mutations = {
    SET_AVATAR(state, url) {
        state.avatar = url;
    },

    UPDATE_DIGEST(state, meta) {
        state.digest = meta.digest;
    },

    UPDATE_LOCALE(state, meta) {
        state.locale = meta.locale;
    },

    UPDATE_DARK_MODE(state, meta) {
        state.darkMode = meta.dark_mode;
    },
};

const getters = {};

export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
};
