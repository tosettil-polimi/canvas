<template>
    <div>
        <page-header>
            <template slot="menu">
                <div class="dropdown">
                    <a
                        id="navbarDropdown"
                        class="nav-link pr-1"
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

                    <div class="dropdown-menu dropdown-menu-right">
                        <router-link :to="{ name: 'create-post' }" class="dropdown-item">
                            {{ i18n.new_post }}
                        </router-link>
                    </div>
                </div>
            </template>
        </page-header>

        <main class="py-4" v-if="isReady">
            <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12">
                <div class="d-flex justify-content-between my-3 align-items-end">
                    <h2 class="mt-3">{{ i18n.posts_simple }}</h2>

                    <select
                        name=""
                        id=""
                        v-model="type"
                        @change="changeType"
                        class="ml-auto w-auto custom-select border-0"
                    >
                        <option value="published">{{ i18n.published }} ({{ suffixedNumber(publishedCount) }})</option>
                        <option value="draft">{{ i18n.draft }} ({{ suffixedNumber(draftCount) }})</option>
                    </select>
                </div>

                <div class="mt-5 card shadow-lg">
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
                                    <div class="pl-2 col-md-8 col-sm-10 col-10 py-1">
                                        <p class="mb-0 text-truncate">
                                            <span class="font-weight-bold lead">
                                                {{ post.title }}
                                            </span>
                                        </p>
                                        <p class="mb-1 text-truncate" v-if="post.summary">
                                            {{ post.summary }}
                                        </p>
                                        <p class="text-muted mb-0">
                                            <span v-if="isPublished(post.published_at)">
                                                {{ i18n.published }}
                                                {{ moment(post.published_at).fromNow() }}
                                            </span>

                                            <span v-if="isDraft(post.published_at)" class="text-danger">{{
                                                i18n.draft
                                            }}</span>

                                            <span class="d-none d-md-inline">
                                                ― {{ i18n.updated }}
                                                {{ moment(post.updated_at).fromNow() }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="ml-auto d-none d-md-inline pl-3">
                                        <div
                                            v-if="post.featured_image"
                                            id="featuredImage"
                                            class="mr-2 ml-3 shadow-inset"
                                            :style="{
                                                backgroundImage: 'url(' + post.featured_image + ')',
                                            }"
                                        ></div>
                                        <div v-else class="mx-3 align-middle">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="45"
                                                viewBox="0 0 24 24"
                                                class="icon-camera"
                                            >
                                                <path
                                                    class="fill-light-gray"
                                                    d="M6.59 6l2.7-2.7A1 1 0 0 1 10 3h4a1 1 0 0 1 .7.3L17.42 6H20a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8c0-1.1.9-2 2-2h2.59zM19 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-7 8a5 5 0 1 0 0-10 5 5 0 0 0 0 10z"
                                                />
                                                <path class="fill-light-gray" d="M12 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="d-inline d-lg-none pl-3 ml-auto">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="25"
                                            viewBox="0 0 24 24"
                                            class="icon-cheveron-right-circle"
                                        >
                                            <circle cx="12" cy="12" r="10" style="fill: none;"/>
                                            <path
                                                class="fill-light-gray"
                                                d="M10.3 8.7a1 1 0 0 1 1.4-1.4l4 4a1 1 0 0 1 0 1.4l-4 4a1 1 0 0 1-1.4-1.4l3.29-3.3-3.3-3.3z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </router-link>
                        </div>

                        <infinite-loading :identifier="infiniteId" @infinite="fetchData" spinner="spiral">
                            <span slot="no-more"></span>
                            <div slot="no-results" class="text-left">
                                <div class="my-5">
                                    <p class="lead text-center text-muted mt-5">
                                        <span v-if="type === 'published'">{{ i18n.you_have_no_published_posts }}</span>
                                        <span v-else>{{ i18n.you_have_no_draft_posts }}</span>
                                    </p>
                                    <p class="lead text-center text-muted mt-1">
                                        {{ i18n.write_on_the_go }}
                                    </p>
                                </div>
                            </div>
                        </infinite-loading>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
    import isEmpty from 'lodash/isEmpty';
    import NProgress from 'nprogress';
    import Hover from '../directives/Hover';
    import InfiniteLoading from 'vue-infinite-loading';
    import PageHeader from '../components/PageHeader';
    import strings from '../mixins/strings';
    import i18n from '../mixins/i18n';
    import status from '../mixins/status';

    export default {
        name: 'post-list',

        components: {
            InfiniteLoading,
            PageHeader,
        },

        mixins: [ status, strings, i18n ],

        directives: {
            Hover,
        },

        data() {
            return {
                page: 1,
                posts: [],
                publishedCount: 0,
                draftCount: 0,
                type: 'published',
                infiniteId: +new Date(),
                isReady: false,
            };
        },

        created() {
            this.fetchData();
            this.isReady = true;
            NProgress.done();
        },

        methods: {
            fetchData($state) {
                if ($state) {
                    return this.request()
                        .get('/api/posts', {
                            params: {
                                page: this.page,
                                type: this.type,
                            },
                        })
                        .then(({ data }) => {
                            if (!isEmpty(data) && !isEmpty(data.posts.data)) {
                                this.publishedCount = data.publishedCount;
                                this.draftCount = data.draftCount;

                                this.page += 1;
                                this.posts.push(...data.posts.data);

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
                }
            },

            changeType() {
                this.page = 1;
                this.posts = [];
                this.infiniteId += 1;
            },
        },
    };
</script>

<style scoped>
    #featuredImage {
        background-size: cover;
        width: 57px;
        height: 57px;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        border-radius: 50%;
    }
</style>
