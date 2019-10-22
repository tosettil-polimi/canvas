import Vue from 'vue';
import _ from "lodash";
import Vuex from 'vuex';
import moment from 'moment';

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        activePost: {},
    },

    mutations: {
        setActivePost(state, data) {
            let payload = {};

            payload.id = _.get(data, "id", "create");
            payload.title = _.get(data, "title", "");
            payload.slug = _.get(data, "slug", "");
            payload.summary = _.get(data, "summary", "");
            payload.body = _.get(data, "body", "");
            payload.published_at = _.get(data, "published_at", "");
            payload.featured_image = _.get(data, "featured_image", "");
            payload.featured_image_caption = _.get(data, "featured_image_caption", "");

            payload.meta = {};
            payload.meta.meta_description = _.get(data, "meta.meta_description", "");
            payload.meta.og_title = _.get(data, "meta.og_title", "");
            payload.meta.og_description = _.get(data, "meta.og_description", "");
            payload.meta.twitter_title = _.get(data, "meta.twitter_title", "");
            payload.meta.twitter_description = _.get(data, "meta.twitter_description", "");
            payload.meta.canonical_link = _.get(data, "meta.canonical_link", "");

            payload.topic = _.get(data, "topic.0", []);
            payload.tags = _.get(data, "tags", []);
            payload.errors = [];
            payload.isSaving = false;
            payload.hasSuccess = false;

            state.activePost = payload;
        },

        updatePostBody(state, data) {
            state.activePost.body = data;
        },

        saveActivePost(state, data) {
            this.$app.request()
                .post("/api/posts/" + state.activePost.id, data)
                .then(response => {
                    if (data.id === "create") {
                        this.$app.$router.push({
                            name: "posts-edit",
                            params: {id: response.data.id}
                        });
                    }

                    state.activePost.isSaving = false;
                    state.activePost.hasSuccess = true;
                    state.activePost.id = response.data.id;
                    state.activePost.post = response.data;
                })
                .catch(error => {
                    state.activePost.isSaving = false;
                    state.activePost.errors = error.response.data.errors;
                });
        },

        setPostTags(state, tags) {
            state.activePost.tags = tags;
        },

        setPostTopic(state, topic) {
            state.activePost.topic = topic;
        },

        deletePost(state, postId) {
            this.$app.request()
                .delete("/api/posts/" + postId)
                .then(response => {
                    state.activePost = {};

                    this.$app.$router.push({name: "posts"});
                })
                .catch(error => {
                    console.log(error)
                });
        },
    },
    actions: {
        setActivePost(context, payload) {
            context.commit('setActivePost', payload)
        },

        updatePostBody(context, body) {
            context.commit('updatePostBody', body)
        },

        saveActivePost(context, payload) {
            context.commit('saveActivePost', payload);
        },

        setPostTags(context, payload) {
            context.commit('setPostTags', payload);
        },

        setPostTopic(context, payload) {
            context.commit('setPostTopic', payload);
        },

        deletePost(context, payload) {
            context.commit('deletePost', payload);
        }
    },

    getters: {
        activePost(state) {
            return state.activePost;
        },

        isDraft(state) {
            const date = state.activePost.published_at;

            return date === null || date === "" || date > moment(new Date()).tz(Canvas.timezone).format().slice(0, 19).replace("T", " ");
        }
    }
});
