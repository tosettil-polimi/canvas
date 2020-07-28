<template>
    <div>
        <page-header>
            <template slot="action">
                <a
                    v-if="isDraft(post.published_at)"
                    href="#"
                    class="btn btn-sm btn-outline-success font-weight-bold my-auto"
                    @click="showPublishModal"
                >
                    <span class="d-block d-lg-none">{{ trans.app.publish }}</span>
                    <span class="d-none d-lg-block">{{ trans.app.ready_to_publish }}</span>
                </a>

                <a v-else href="#" class="btn btn-sm btn-outline-success font-weight-bold my-auto" @click="save">
                    {{ trans.app.save }}
                </a>
            </template>

            <template slot="menu">
                <div class="dropdown">
                    <a
                        id="navbarDropdown"
                        class="nav-link pr-0"
                        href="#"
                        role="button"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            width="25"
                            class="icon-dots-horizontal"
                        >
                            <path
                                class="fill-light-gray"
                                fill-rule="evenodd"
                                d="M5 14a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"
                            />
                        </svg>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right">
                        <router-link
                            v-if="!isDraft(post.published_at)"
                            :to="{ name: 'stats-show', params: { id: id } }"
                            class="dropdown-item"
                        >
                            {{ trans.app.view_stats }}
                        </router-link>
                        <div v-if="!isDraft(post.published_at)" class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item" @click="showSettingsModal">
                            {{ trans.app.general_settings }}
                        </a>
                        <a href="#" class="dropdown-item" @click="showFeaturedImageModal">
                            {{ trans.app.featured_image }}
                        </a>
                        <a href="#" class="dropdown-item" @click="showSeoModal">
                            {{ trans.app.seo_settings }}
                        </a>
                        <a
                            v-if="!isDraft(post.published_at)"
                            href="#"
                            class="dropdown-item"
                            @click.prevent="convertToDraft"
                        >
                            {{ trans.app.convert_to_draft }}
                        </a>
                        <a v-if="id !== 'create'" href="#" class="dropdown-item text-danger" @click="showDeleteModal">
                            {{ trans.app.delete }}
                        </a>
                    </div>
                </div>
            </template>
        </page-header>

        <main class="py-4" v-if="isReady">
            <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12">
                <div class="form-group my-3">
                    <textarea-autosize
                        :placeholder="trans.app.title"
                        class="form-control-lg form-control border-0 font-serif rounded shadow"
                        @input.native="update"
                        rows="1"
                        v-model="post.title"
                    />
                </div>

                <div class="form-group my-4">
                    <div class="rounded shadow">
                        <quill-editor></quill-editor>
                    </div>
                </div>
            </div>
        </main>

        <publish-modal v-if="isReady" ref="publishModal" />

        <settings-modal v-if="isReady" ref="settingsModal" :post="post" :tags="tags" :topics="topics" />

        <featured-image-modal v-if="isReady" ref="featuredImageModal" />

        <seo-modal v-if="isReady" ref="seoModal" />

        <delete-modal
            v-if="isReady"
            ref="deleteModal"
            @delete="deletePost"
            :header="trans.app.delete"
            :message="trans.app.deleted_posts_are_gone_forever"
        />
    </div>
</template>

<script>
import Vue from 'vue';
import $ from 'jquery';
import debounce from 'lodash/debounce';
import NProgress from 'nprogress';
import SeoModal from '../components/modals/SeoModal';
import PageHeader from '../components/PageHeader';
import DeleteModal from '../components/modals/DeleteModal';
import VueTextAreaAutosize from 'vue-textarea-autosize';
import PublishModal from '../components/modals/PublishModal';
import SettingsModal from '../components/modals/SettingsModal';
import QuillEditor from '../components/editor/QuillEditor';
import FeaturedImageModal from '../components/modals/FeaturedImageModal';

Vue.use(VueTextAreaAutosize);

export default {
    name: 'edit-post',

    components: {
        PublishModal,
        FeaturedImageModal,
        DeleteModal,
        QuillEditor,
        PageHeader,
        SeoModal,
        SettingsModal,
    },

    data() {
        return {
            post: {},
            tags: [],
            topics: [],
            id: this.$route.params.id || 'create',
            isReady: false,
            trans: JSON.parse(window.Canvas.locale.translations),
        };
    },

    created() {
        // todo: drop these in the header when the component loads and remove them after
        // https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.18.1/build/highlight.min.js
        // https://platform.twitter.com/widgets.js
        // dark styles
        // https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@10.1.1/build/styles/sunburst.min.css
        // light styles
        // https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@10.1.1/build/styles/github.min.css
    },

    beforeRouteEnter(to, from, next) {
        next((vm) => {
            vm.request()
                .get('/api/posts/' + vm.id)
                .then((response) => {
                    vm.$store.dispatch('setActivePost', response.data.post);

                    vm.post = vm.$store.getters.activePost;
                    vm.tags = response.data.tags;
                    vm.topics = response.data.topics;
                    vm.isReady = true;

                    NProgress.done();
                })
                .catch(() => {
                    vm.$router.push({ name: 'posts' });
                });
        });
    },

    beforeRouteLeave(to, from, next) {
        // Reset the form status to avoid it flashing on the next screen load
        this.post.isSaving = false;
        this.post.hasSuccess = false;

        next();
    },

    methods: {
        save() {
            this.post.errors = [];
            this.post.isSaving = true;
            this.post.hasSuccess = false;

            if (this.id === 'create') {
                this.id = this.post.id;
            }

            this.$store.dispatch('saveActivePost', {
                data: this.post,
                id: this.id,
            });

            setTimeout(() => {
                this.post.hasSuccess = false;
                this.post.isSaving = false;
            }, 3000);
        },

        update: debounce(function () {
            this.save();
        }, 3000),

        convertToDraft() {
            this.post.published_at = '';
            this.save();
        },

        deletePost() {
            this.$store.dispatch('deletePost', this.post.id);

            $(this.$refs.deleteModal.$el).modal('hide');
        },

        showPublishModal() {
            $(this.$refs.publishModal.$el).modal('show');
        },

        showSettingsModal() {
            $(this.$refs.settingsModal.$el).modal('show');
        },

        showFeaturedImageModal() {
            $(this.$refs.featuredImageModal.$el).modal('show');
        },

        showSeoModal() {
            $(this.$refs.seoModal.$el).modal('show');
        },

        showDeleteModal() {
            $(this.$refs.deleteModal.$el).modal('show');
        },
    },
};
</script>

<style scoped>
textarea {
    font-size: 42px;
}
</style>
