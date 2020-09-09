import request from "../../mixins/request";
import isEmpty from "lodash/isEmpty";
import url from "../../mixins/url";

const initialState = {
    i18n: window.Canvas.translations,
    languageCodes: window.Canvas.languageCodes,
    maxUpload: window.Canvas.maxUpload,
    path: window.Canvas.path,
    timezone: window.Canvas.timezone,
    unsplash: window.Canvas.unsplash,
    user: window.Canvas.user,
    version: window.Canvas.version,
    roles: window.Canvas.roles,
};

const state = { ...initialState };

const actions = {
    updateDigest(context, payload) {
        request.methods
            .request()
            .post(`/api/users/${state.user.id}`, payload)
            .then(({ data }) => {
                context.commit('UPDATE_DIGEST', data.user);
            });
    },

    updateLocale(context, payload) {
        request.methods
            .request()
            .post(`/api/users/${state.user.id}`, payload)
            .then(({ data }) => {
                context.commit('UPDATE_LOCALE', data);
            });
    },

    updateDarkMode(context, payload) {
        request.methods
            .request()
            .post(`/api/users/${state.user.id}`, payload)
            .then(({ data }) => {
                context.commit('UPDATE_DARK_MODE', data.user);
            });
    },

    setAvatar(context, payload) {
        let path = isEmpty(payload) ? url.methods.gravatar(state.email) : payload;

        context.commit('SET_AVATAR', path);
    },
};

const mutations = {
    UPDATE_DIGEST(state, user) {
        state.user.digest = user.digest;
    },

    UPDATE_LOCALE(state, data) {
        state.i18n = data.i18n;
        state.user.locale = data.user.locale;
    },

    UPDATE_DARK_MODE(state, user) {
        state.user.darkMode = user.dark_mode;
    },

    SET_AVATAR(state, url) {
        state.user.avatar = url;
    },
};

const getters = {
    trans(state) {
        return JSON.parse(state.i18n);
    },

    isContributor(state) {
        return state.user.role === 1;
    },

    isEditor(state) {
        return state.user.role === 2;
    },

    isAdmin(state) {
        return state.user.role === 3;
    },
};

export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
};
