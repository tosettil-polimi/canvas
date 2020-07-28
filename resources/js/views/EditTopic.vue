<template>
    <div>
        <page-header>
            <template slot="menu" v-if="isReady">
                <div class="dropdown" v-if="id !== 'create'">
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
                            class="icon-dots-horizontal hover-light"
                        >
                            <path
                                class="fill-light-gray"
                                fill-rule="evenodd"
                                d="M5 14a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"
                            />
                        </svg>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        <a href="#" class="dropdown-item text-danger" @click="showDeleteModal">
                            {{ i18n.delete }}
                        </a>
                    </div>
                </div>
            </template>
        </page-header>

        <main v-if="isReady" class="py-4">
            <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12">
                <div class="my-3">
                    <h2 class="mt-3">{{ topic.name || i18n.new_topic }}</h2>
                    <p v-if="!creatingTopic" class="mt-2 text-secondary">
                        {{ i18n.last_updated }} {{ moment(topic.updated_at).fromNow() }}
                    </p>
                </div>

                <div class="mt-5 card shadow-lg">
                    <div class="card-body">
                        <div class="form-group">
                            <div class="col-12 row">
                                <label class="font-weight-bold text-uppercase text-muted small">
                                    {{ i18n.name }}
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    autofocus
                                    autocomplete="off"
                                    v-model="topic.name"
                                    title="Name"
                                    @keyup.enter="saveTopic"
                                    class="form-control border-0"
                                    :placeholder="i18n.give_your_topic_a_name"
                                />

                                <div v-if="errors.name" class="invalid-feedback d-block">
                                    <strong>{{ errors.name[0] }}</strong>
                                </div>
                            </div>

                            <div class="col-12 mt-3 row">
                                <label class="font-weight-bold text-uppercase text-muted small">
                                    {{ i18n.slug }}
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    disabled
                                    autocomplete="off"
                                    v-model="topic.slug"
                                    title="Slug"
                                    class="form-control border-0"
                                    :placeholder="i18n.give_your_topic_a_name_slug"
                                />
                                <div v-if="errors.slug" class="invalid-feedback d-block">
                                    <strong>{{ errors.slug[0] }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 mb-2">
                            <div class="col-md">
                                <a
                                    href="#"
                                    onclick="this.blur()"
                                    class="btn btn-success btn-block font-weight-bold mt-0"
                                    aria-label="Save"
                                    @click.prevent="saveTopic"
                                >
                                    {{ i18n.save }}
                                </a>
                            </div>
                            <div class="col-md">
                                <router-link
                                    :to="{ name: 'topics' }"
                                    class="btn btn-link btn-block font-weight-bold text-muted text-decoration-none"
                                >
                                    {{ i18n.cancel }}
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 card shadow-lg" v-if="posts.length > 0">
                    <div class="card-body p-0">
                        <div v-for="(post, index) in posts" :key="`${index}-${post.id}`">
                            <router-link
                                :to="{
                                            name: 'edit-post',
                                            params: { id: post.id },
                                        }"
                                class="text-decoration-none"
                            >
                                <div
                                    v-hover="{ class: `hover-bg` }"
                                    class="d-flex p-3 align-items-center"
                                    :class="{
                                                'border-top': index !== 0,
                                                'rounded-top': index === 0,
                                                'rounded-bottom': index === posts.length - 1,
                                            }"
                                >
                                    <div class="pl-2 col-md-6 col-sm-8 col-10">
                                        <p class="mb-1 mt-2 text-truncate">
                                            <span class="font-weight-bold lead">{{ post.title }}</span>
                                        </p>
                                        <p class="text-secondary mb-2">
                                            <span class="d-none d-md-inline"> {{ post.read_time }} ― </span>
                                            {{ i18n.published }}
                                            {{ moment(post.published_at).format('MMM D, YYYY') }}
                                        </p>
                                    </div>
                                    <div class="ml-auto">
                                        <div class="d-none d-md-inline">
                                                    <span class="text-muted mr-3"
                                                    >{{ suffixedNumber(post.views_count) }} {{ i18n.views }}</span
                                                    >
                                            <span class="mr-3"
                                            >{{ i18n.created }}
                                                        {{ moment(post.created_at).format('MMM D, YYYY') }}</span
                                            >
                                        </div>

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="25"
                                            viewBox="0 0 24 24"
                                            class="icon-cheveron-right-circle"
                                        >
                                            <circle cx="12" cy="12" r="10" style="fill: none;" />
                                            <path
                                                class="fill-light-gray"
                                                d="M10.3 8.7a1 1 0 0 1 1.4-1.4l4 4a1 1 0 0 1 0 1.4l-4 4a1 1 0 0 1-1.4-1.4l3.29-3.3-3.3-3.3z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </router-link>
                        </div>

                        <infinite-loading @infinite="fetchPosts" spinner="spiral">
                            <span slot="no-more"></span>
                            <div slot="no-results"></div>
                        </infinite-loading>
                    </div>
                </div>
            </div>
        </main>

        <delete-modal
            ref="deleteModal"
            @delete="deleteTopic"
            :header="i18n.delete"
            :message="i18n.deleted_topics_are_gone_forever"
        >
        </delete-modal>
    </div>
</template>

<script>
    import $ from 'jquery';
    import NProgress from 'nprogress';
    import PageHeader from '../components/PageHeader';
    import Hover from '../directives/Hover';
    import DeleteModal from '../components/modals/DeleteModal';
    import i18n from "../mixins/i18n";
    import toast from '../mixins/toast';
    import InfiniteLoading from 'vue-infinite-loading';
    import strings from "../mixins/strings";
    import isEmpty from "lodash/isEmpty";

    export default {
        name: 'edit-topic',

        components: {
            DeleteModal,
            InfiniteLoading,
            PageHeader,
        },

        directives: {
            Hover,
        },

        mixins: [ i18n, strings, toast ],

        data() {
            return {
                id: this.$route.params.id || 'create',
                topic: null,
                page: 1,
                posts: [],
                errors: [],
                isReady: false,
            };
        },

        async created() {
            await this.fetchTopic();
            await this.fetchPosts();
            this.isReady = true;
            NProgress.done();
        },

        watch: {
            'topic.name'(val) {
                if (!isEmpty(val)) {
                    this.topic.slug = this.slugify(val);
                }
            },

            $route(to) {
                this.isReady = false;
                this.id = to.params.id;
                this.topic = null;
                this.page = 1;
                this.posts = [];
                this.fetchTopic();
                this.fetchPosts();
                this.isReady = true;
                NProgress.done();
            },
        },

        computed: {
            creatingTopic() {
                return this.$route.name === 'create-topic';
            }
        },

        methods: {
            fetchTopic() {
                return this.request()
                    .get('/api/topics/' + this.id)
                    .then(({ data }) => {
                        this.topic = data;
                        NProgress.inc();
                    })
                    .catch(() => {
                        this.$router.push({ name: 'topics' });
                        NProgress.done();
                    });
            },

            fetchPosts($state) {
                return this.request()
                    .get('/api/topics/' + this.id + '/posts', {
                        params: {
                            page: this.page,
                        }
                    })
                    .then(({ data }) => {
                        if (!isEmpty(data) && !isEmpty(data.data)) {
                            this.page += 1;
                            this.posts.push(...data.data);

                            $state.loaded();
                        } else {
                            $state.complete();
                        }

                        if (isEmpty($state)) {
                            NProgress.inc();
                        }
                    })
                    .catch(() => {
                        NProgress.done();
                    });
            },

            saveTopic() {
                this.errors = [];

                this.request()
                    .post('/api/topics/' + this.id, {
                        name: this.topic.name,
                        slug: this.topic.slug
                    })
                    .then(({ data }) => {
                        this.id = data.id;
                        this.topic = data;
                        toast.methods.toast(this.i18n.saved);
                    })
                    .catch((error) => {
                        this.errors = error.response.data.errors;
                    });
            },

            deleteTopic() {
                this.request()
                    .delete('/api/topics/' + this.id)
                    .then(() => {
                        $(this.$refs.deleteModal.$el).modal('hide');
                        toast.methods.toast(this.i18n.success);
                        this.$router.push({ name: 'topics' });
                    })
                    .catch(() => {
                        // Add any error debugging...
                    });
            },

            showDeleteModal() {
                $(this.$refs.deleteModal.$el).modal('show');
            },
        },
    };
</script>
