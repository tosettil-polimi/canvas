<template>
    <div>
        <page-header></page-header>

        <main class="py-4">
            <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12">
                <div class="my-3">
                    <h2 class="mt-3">{{ i18n.stats }}</h2>
                    <p class="mt-2 text-secondary">
                        {{ i18n.click_to_see_insights }}
                    </p>
                </div>

                <div v-if="isReady">
                    <div v-if="posts.length">
                        <div class="card-deck mt-5">
                            <div class="card shadow-lg">
                                <div
                                    class="card-header pb-0 bg-transparent d-flex justify-content-between align-middle border-0"
                                >
                                    <p class="font-weight-bold text-muted small text-uppercase">{{ i18n.views }}</p>
                                    <p>
                                        <span class="badge badge-pill badge-success p-2 font-weight-bold">
                                            {{ i18n.last_thirty_days }}
                                        </span>
                                    </p>
                                </div>
                                <div class="card-body pt-0 pb-2">
                                    <p class="card-text display-4">{{ suffixedNumber(totalViews) }}</p>
                                </div>
                            </div>
                            <div class="card shadow-lg">
                                <div
                                    class="card-header pb-0 bg-transparent d-flex justify-content-between align-middle border-0"
                                >
                                    <p class="font-weight-bold text-muted small text-uppercase">{{ i18n.visitors }}</p>
                                    <p>
                                        <span class="badge badge-pill badge-primary p-2 font-weight-bold">{{
                                            i18n.last_thirty_days
                                        }}</span>
                                    </p>
                                </div>
                                <div class="card-body pt-0 pb-2">
                                    <p class="card-text display-4">{{ suffixedNumber(totalVisits) }}</p>
                                </div>
                            </div>
                        </div>

                        <line-chart
                            :views="JSON.parse(viewPlotPoints)"
                            :visits="JSON.parse(visitPlotPoints)"
                            class="mt-5"
                        />

                        <div class="mt-5 card shadow-lg">
                            <div class="card-body p-0">
                                <div v-for="(post, index) in posts" :key="`${index}-${post.id}`">
                                    <router-link
                                        :to="{
                                            name: 'post-stats',
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

                    <div v-else class="card shadow mt-5">
                        <div class="card-body p-0">
                            <div class="my-5">
                                <p class="lead text-center text-muted mt-5">{{ i18n.you_have_no_published_posts }}</p>
                                <p class="lead text-center text-muted mt-1">{{ i18n.stats_are_made_available }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import NProgress from 'nprogress';
import isEmpty from 'lodash/isEmpty';
import InfiniteLoading from 'vue-infinite-loading';
import Hover from '../directives/Hover';
import LineChart from '../components/LineChart';
import strings from '../mixins/strings';
import PageHeader from '../components/PageHeader';
import i18n from '../mixins/i18n';

export default {
    name: 'stats',

    components: {
        LineChart,
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
            posts: [],
            totalViews: 0,
            totalVisits: 0,
            viewPlotPoints: {},
            visitPlotPoints: {},
            isReady: false,
        };
    },

    async created() {
        await Promise.all([
            this.fetchStats(),
            this.fetchPosts()
        ])
        this.isReady = true;
        NProgress.done();
    },

    methods: {
        fetchStats() {
            return this.request()
                .get('/api/stats')
                .then(({ data }) => {
                    this.totalViews = data.total_views;
                    this.totalVisits = data.total_visits;
                    this.viewPlotPoints = data.traffic.views;
                    this.visitPlotPoints = data.traffic.visits;

                    NProgress.inc();
                })
                .catch(() => {
                    NProgress.done();
                });
        },

        fetchPosts($state) {
            return this.request()
                .get('/api/posts', {
                    params: {
                        page: this.page,
                    },
                })
                .then(({ data }) => {
                    if (!isEmpty(data) && !isEmpty(data.posts.data)) {
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
        },
    },
};
</script>

<style scoped lang="scss">
    @import "../../sass/utilities/variables";

    .badge-success {
        background-color: $green-500;
        color: darken($green, 20%);
    }

    .badge-primary {
        background-color: $blue-500;
        color: darken($blue, 35%);
    }
</style>
