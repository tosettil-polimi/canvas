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
                        <router-link :to="{ name: 'create-tag' }" class="dropdown-item">
                            {{ i18n.new_tag }}
                        </router-link>
                    </div>
                </div>
            </template>
        </page-header>

        <main class="py-4">
            <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12">
                <div class="my-3">
                    <h2 class="mt-3">{{ i18n.tags }}</h2>

                    <p class="mt-2 text-secondary">
                        {{ i18n.tags_are_great_for }}
                    </p>
                </div>

                <div class="mt-5 card shadow-lg" v-if="isReady">
                    <div class="card-body p-0">
                        <div v-for="(tag, index) in tags" :key="`${index}-${tag.id}`">
                            <router-link
                                :to="{
                                    name: 'edit-tag',
                                    params: { id: tag.id },
                                }"
                                class="text-decoration-none"
                            >
                                <div
                                    v-hover="{ class: `hover-bg` }"
                                    class="p-3"
                                    :class="{
                                        'border-top': index !== 0,
                                        'rounded-top': index === 0,
                                        'rounded-bottom': index === tags.length - 1,
                                    }"
                                >
                                    <div class="d-flex align-items-center">
                                        <div class="mr-auto pl-2">
                                            <p class="mb-0 py-1">
                                                <span class="font-weight-bold lead">
                                                    {{ tag.name }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="ml-auto d-none d-md-inline-block">
                                            <span class="text-muted mr-3"
                                                >{{ suffixedNumber(tag.posts_count) }} {{ i18n.posts }}</span
                                            >
                                            <span class="mr-3"
                                                >{{ i18n.created }}
                                                {{ moment(tag.created_at).format('MMM D, YYYY') }}</span
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

                        <infinite-loading @infinite="fetchData" spinner="spiral">
                            <span slot="no-more"></span>
                            <div slot="no-results" class="text-left">
                                <div class="my-5">
                                    <p class="lead text-center text-muted mt-5">
                                        {{ i18n.you_have_no_tags }}
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
import NProgress from 'nprogress';
import isEmpty from 'lodash/isEmpty';
import Hover from '../directives/Hover';
import InfiniteLoading from 'vue-infinite-loading';
import PageHeader from '../components/PageHeader';
import strings from '../mixins/strings';
import i18n from '../mixins/i18n';

export default {
    name: 'tag-list',

    components: {
        InfiniteLoading,
        PageHeader,
    },

    mixins: [strings, i18n],

    directives: {
        Hover,
    },

    data() {
        return {
            page: 1,
            tags: [],
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
                    .get('/api/tags', {
                        params: {
                            page: this.page,
                        },
                    })
                    .then(({ data }) => {
                        if (!isEmpty(data) && !isEmpty(data.data)) {
                            this.page += 1;
                            this.tags.push(...data.data);

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
    },
};
</script>
