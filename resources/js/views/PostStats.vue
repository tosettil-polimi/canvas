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
                        <router-link :to="{ name: 'edit-post', params: { id: id } }" class="dropdown-item">
                            {{ i18n.edit_post }}
                        </router-link>
                    </div>
                </div>
            </template>
        </page-header>

        <main class="py-4" v-if="isReady">
            <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12">
                <div class="my-3">
                    <h2 class="mt-3">{{ post.title  }}</h2>
                    <p class="mt-2 text-secondary">
                        {{ i18n.published }}
                        {{ moment(post.published_at).fromNow() }}
                    </p>
                </div>

                <div class="card-deck mt-5">
                    <div class="card shadow-lg">
                        <div class="card-body p-3">
                            <p class="lead border-bottom">
                                {{ i18n.lifetime_summary }}
                            </p>
                            <div class="d-flex">
                                <div class="mr-5">
                                    <p class="mb-0 small text-muted text-uppercase font-weight-bold">
                                        {{ i18n.total_views }}
                                    </p>
                                    <h3 class="mt-1">
                                        {{ suffixedNumber(totalViews) }}
                                    </h3>
                                </div>

                                <div>
                                    <p class="mb-0 small text-muted text-uppercase font-weight-bold">
                                        {{ i18n.average_reading_time }}
                                    </p>
                                    <h3 class="mt-1">
                                        {{ readTime }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-lg">
                        <div class="card-body p-3">
                            <p class="lead border-bottom">
                                {{ i18n.monthly_summary }}
                            </p>
                            <div class="d-flex">
                                <div class="mr-5">
                                    <p class="mb-0 small text-muted text-uppercase font-weight-bold">
                                        <a
                                            href="#"
                                            v-tooltip="{ placement: 'top' }"
                                            class="text-decoration-none"
                                            :title="i18n.views_info"
                                        >
                                            {{ i18n.views }}
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="17"
                                                viewBox="0 0 24 24"
                                                class="icon-help ml-1"
                                            >
                                                <path
                                                    class="fill-light-gray"
                                                    d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20z"
                                                />
                                                <path
                                                    class="fill-bg"
                                                    d="M12 19.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm1-5.5a1 1 0 0 1-2 0v-1.41a1 1 0 0 1 .55-.9L14 10.5C14.64 10.08 15 9.53 15 9c0-1.03-1.3-2-3-2-1.35 0-2.49.62-2.87 1.43a1 1 0 0 1-1.8-.86C8.05 6.01 9.92 5 12 5c2.7 0 5 1.72 5 4 0 1.3-.76 2.46-2.05 3.24L13 13.2V14z"
                                                />
                                            </svg>
                                        </a>
                                    </p>
                                    <h3 class="mt-1 mb-2">
                                        {{ suffixedNumber(monthlyViews) }}
                                    </h3>
                                    <p class="small text-muted">
                                        <span v-if="viewsAreTrendingUp">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                width="17"
                                                class="icon-arrow-thick-up-circle mr-1"
                                            >
                                                <circle cx="12" cy="12" r="10" class="fill-light-gray" />
                                                <path
                                                    class="fill-bg"
                                                    d="M14 12v5a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-5H8a1 1 0 0 1-.7-1.7l4-4a1 1 0 0 1 1.4 0l4 4A1 1 0 0 1 16 12h-2z"
                                                />
                                            </svg>
                                        </span>
                                        <span v-else>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                width="17"
                                                class="icon-arrow-thick-down-circle mr-1"
                                            >
                                                <circle cx="12" cy="12" r="10" class="fill-light-gray" />
                                                <path
                                                    class="fill-bg"
                                                    d="M10 12V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5h2a1 1 0 0 1 .7 1.7l-4 4a1 1 0 0 1-1.4 0l-4-4A1 1 0 0 1 8 12h2z"
                                                />
                                            </svg>
                                        </span>
                                        {{ viewMonthOverMonthPercentage }}%
                                        {{ i18n.from_last_month }}
                                    </p>
                                </div>

                                <div>
                                    <p class="mb-0 small text-muted text-uppercase font-weight-bold">
                                        <a
                                            href="#"
                                            v-tooltip="{ placement: 'top' }"
                                            class="text-decoration-none"
                                            :title="i18n.visits_info"
                                        >
                                            {{ i18n.visitors }}
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="17"
                                                viewBox="0 0 24 24"
                                                class="icon-help ml-1"
                                            >
                                                <path
                                                    class="fill-light-gray"
                                                    d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20z"
                                                />
                                                <path
                                                    class="fill-bg"
                                                    d="M12 19.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm1-5.5a1 1 0 0 1-2 0v-1.41a1 1 0 0 1 .55-.9L14 10.5C14.64 10.08 15 9.53 15 9c0-1.03-1.3-2-3-2-1.35 0-2.49.62-2.87 1.43a1 1 0 0 1-1.8-.86C8.05 6.01 9.92 5 12 5c2.7 0 5 1.72 5 4 0 1.3-.76 2.46-2.05 3.24L13 13.2V14z"
                                                />
                                            </svg>
                                        </a>
                                    </p>
                                    <h3 class="mt-1 mb-2">
                                        {{ suffixedNumber(monthlyVisits) }}
                                    </h3>
                                    <p class="small text-muted">
                                        <span v-if="visitsAreTrendingUp">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                width="17"
                                                class="icon-arrow-thick-up-circle mr-1"
                                            >
                                                <circle cx="12" cy="12" r="10" class="fill-light-gray" />
                                                <path
                                                    class="fill-bg"
                                                    d="M14 12v5a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-5H8a1 1 0 0 1-.7-1.7l4-4a1 1 0 0 1 1.4 0l4 4A1 1 0 0 1 16 12h-2z"
                                                />
                                            </svg>
                                        </span>
                                        <span v-else>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                width="17"
                                                class="icon-arrow-thick-down-circle mr-1"
                                            >
                                                <circle cx="12" cy="12" r="10" class="fill-light-gray" />
                                                <path
                                                    class="fill-bg"
                                                    d="M10 12V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5h2a1 1 0 0 1 .7 1.7l-4 4a1 1 0 0 1-1.4 0l-4-4A1 1 0 0 1 8 12h2z"
                                                />
                                            </svg>
                                        </span>
                                        {{ visitMonthOverMonthPercentage }}%
                                        {{ i18n.from_last_month }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <line-chart
                    :views="JSON.parse(viewPlotPoints)"
                    :visits="JSON.parse(visitPlotPoints)"
                    class="mt-5 mb-3"
                />

                <div class="row justify-content-between">
                    <div class="col-md-6 mt-4">
                        <h5 class="text-muted small text-uppercase font-weight-bold border-bottom pb-2">
                            {{ i18n.views_by_traffic_source }}
                        </h5>

                        <div v-if="topReferers">
                            <div v-for="(views, host) in topReferers" :key="`${host}-${views}`">
                                <div class="d-flex py-2 align-items-center">
                                    <div class="mr-auto">
                                        <div v-if="host === i18n.other">
                                            <p class="mb-0 py-1">
                                                <img
                                                    :src="`https://favicons.githubusercontent.com/${host}`"
                                                    :style="
                                                        user.darkMode === true
                                                            ? {
                                                                  filter: 'invert(100%)',
                                                              }
                                                            : ''
                                                    "
                                                    :alt="host"
                                                    class="mr-1"
                                                />
                                                <a
                                                    href="#"
                                                    v-tooltip="{
                                                        placement: 'right',
                                                    }"
                                                    class="text-decoration-none"
                                                    :title="i18n.referer_unknown"
                                                >
                                                    {{ host }}
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="20"
                                                        viewBox="0 0 24 24"
                                                        class="icon-help"
                                                    >
                                                        <path
                                                            class="fill-light-gray"
                                                            d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20z"
                                                        />
                                                        <path
                                                            class="fill-bg"
                                                            d="M12 19.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm1-5.5a1 1 0 0 1-2 0v-1.41a1 1 0 0 1 .55-.9L14 10.5C14.64 10.08 15 9.53 15 9c0-1.03-1.3-2-3-2-1.35 0-2.49.62-2.87 1.43a1 1 0 0 1-1.8-.86C8.05 6.01 9.92 5 12 5c2.7 0 5 1.72 5 4 0 1.3-.76 2.46-2.05 3.24L13 13.2V14z"
                                                        />
                                                    </svg>
                                                </a>
                                            </p>
                                        </div>
                                        <div v-else>
                                            <p class="mb-0 py-1">
                                                <img
                                                    :src="`https://favicons.githubusercontent.com/${host}`"
                                                    :alt="host"
                                                    class="mr-1"
                                                />
                                                <a
                                                    :href="'http://' + host"
                                                    class="text-decoration-none"
                                                    target="_blank"
                                                >
                                                    {{ host }}
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="text-muted"
                                            >{{ suffixedNumber(monthlyViews) }} {{ i18n.views }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p v-else class="py-2 font-italic">
                            {{ i18n.waiting_until_more_data }}
                        </p>
                    </div>

                    <div class="col-md-6 mt-4">
                        <h5 class="text-muted small text-uppercase font-weight-bold border-bottom pb-2">
                            {{ i18n.popular_reading_times }}
                        </h5>

                        <div v-if="popularReadingTimes">
                            <div v-for="(percentage, time) in popularReadingTimes" :key="`${time}-${percentage}`">
                                <div class="d-flex py-2 align-items-center">
                                    <div class="mr-auto">
                                        <p class="mb-0 py-1">
                                            {{ time }}
                                        </p>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="text-muted">{{ percentage + '%' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p v-else class="py-2 font-italic">
                            {{ i18n.waiting_until_more_data }}
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import NProgress from 'nprogress';
import Tooltip from '../directives/Tooltip';
import LineChart from '../components/LineChart';
import i18n from '../mixins/i18n';
import strings from '../mixins/strings';
import PageHeader from '../components/PageHeader';
import store from '../store';

export default {
    name: 'post-stats',

    components: {
        LineChart,
        PageHeader,
    },

    mixins: [i18n, strings],

    directives: {
        Tooltip,
    },

    data() {
        return {
            id: this.$route.params.id,
            post: null,
            monthlyViews: 0,
            monthlyVisits: 0,
            totalViews: 0,
            viewPlotPoints: {},
            visitPlotPoints: {},
            readTime: null,
            topReferers: null,
            popularReadingTimes: null,
            viewMonthOverMonthDirection: null,
            viewMonthOverMonthPercentage: null,
            visitMonthOverMonthDirection: null,
            visitMonthOverMonthPercentage: null,
            isReady: false,
        };
    },

    async created() {
        await this.fetchStats();
        this.isReady = true;
        NProgress.done();
    },

    methods: {
        fetchStats() {
            return this.request()
                .get('/api/stats/' + this.id)
                .then(({ data }) => {
                    this.post = data.post;
                    this.readTime = data.read_time;
                    this.popularReadingTimes = Array.isArray(data.popular_reading_times)
                        ? null
                        : data.popular_reading_times;
                    this.topReferers = Array.isArray(data.top_referers) ? null : data.top_referers;
                    this.monthlyViews = data.monthly_views;
                    this.monthlyVisits = data.monthly_visits;
                    this.totalViews = data.total_views;
                    this.viewPlotPoints = data.traffic.views;
                    this.visitPlotPoints = data.traffic.visits;
                    this.viewMonthOverMonthDirection = data.month_over_month_views.direction;
                    this.viewMonthOverMonthPercentage = data.month_over_month_views.percentage;
                    this.visitMonthOverMonthDirection = data.month_over_month_visits.direction;
                    this.visitMonthOverMonthPercentage = data.month_over_month_visits.percentage;
                })
                .catch(() => {
                    NProgress.done();
                });
        },
    },

    computed: {
        user() {
            return store.state.user;
        },

        viewsAreTrendingUp() {
            return this.viewMonthOverMonthDirection === 'up';
        },

        visitsAreTrendingUp() {
            return this.visitMonthOverMonthDirection === 'up';
        },
    },
};
</script>

<style scoped>
img {
    width: 15px;
    height: 15px;
}
</style>
