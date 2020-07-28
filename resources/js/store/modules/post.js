import Vue from 'vue';

const initialState = {
    id: '',
    title: '',
    slug: '',
    summary: '',
    body: '',
    published_at: '',
    featured_image: '',
    featured_image_caption: '',
    meta: {
        description: '',
        title: '',
        canonical_link: '',
    },
    tags: [],
    topic: [],
};

const state = { ...initialState };

const actions = {
    async fetchPost(context, id) {
        this.request()
            .get('/api/posts/' + id)
            .then((response) => {
                context.commit('SET_POST', response.data.post);
            })
            .catch((errors) => {
                console.log(errors);
            });
    },

    updatePost({ id, payload }) {
        this.request()
            .post('/api/posts/' + id, payload)
            .then((response) => {
                console.log(response.data);
            })
            .catch((errors) => {
                console.log(errors);
            });
    },

    setTags({ context, tags }) {
        context.commit('SET_TAGS', tags);
    },

    setTopic({ context, topic }) {
        context.commit('SET_TOPIC', topic);
    },

    deletePost({ id }) {
        this.request()
            .delete('/api/posts/' + id, id)
            .then((response) => {
                console.log(response.data);
            })
            .catch((errors) => {
                console.log(errors);
            });
    },

    resetPost({ commit }) {
        commit('RESET_STATE');
    },
};

const mutations = {
    SET_POST(state, post) {
        state = post;
    },

    SET_TAGS(state, tags) {
        state.tags = tags;
    },

    SET_TOPIC(state, topic) {
        state.topic = topic;
    },

    RESET_STATE() {
        for (let f in state) {
            Vue.set(state, f, initialState[f]);
        }
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
