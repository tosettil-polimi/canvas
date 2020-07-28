<template>
    <div>
        <page-header></page-header>

        <main class="py-4">
            <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12">
                <div class="my-3">
                    <h2 class="mt-3">{{ i18n.users }}</h2>
                    <p class="mt-2 text-secondary">
                        {{ i18n.manage_user_roles }}
                    </p>
                </div>

                <div class="mt-5 card shadow-lg" v-if="isReady">
                    <div class="card-body p-0">
                        <div v-for="(user, index) in users" :key="`${index}-${user.id}`">
                            <router-link
                                :to="{
                                    name: 'edit-user',
                                    params: { id: user.id },
                                }"
                                class="text-decoration-none"
                            >
                                <div
                                    v-hover="{ class: `hover-bg` }"
                                    class="d-flex p-3 align-items-center"
                                    :class="{
                                        'border-top': index !== 0,
                                        'rounded-top': index === 0,
                                        'rounded-bottom': index === users.length - 1,
                                    }"
                                >
                                    <div class="pl-2 col-md-8 col-sm-10 col-10 py-1">
                                        <p class="mb-0 text-truncate">
                                            <span class="font-weight-bold lead">
                                                {{ user.name }}
                                            </span>
                                        </p>
                                        <p class="mb-1 text-muted">
                                            {{ user.email }}
                                        </p>
                                    </div>
                                    <div class="ml-auto d-none d-md-inline pl-3">
                                        <img
                                            :src="gravatar(user.email)"
                                            style="width: 57px; height: 57px;"
                                            class="mr-2 ml-3 shadow-inset rounded-circle"
                                            :alt="user.name"
                                        />
                                    </div>

                                    <div class="d-inline d-lg-none pl-3 ml-auto">
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
                            <div slot="no-results"></div>
                        </infinite-loading>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import PageHeader from '../components/PageHeader';
import InfiniteLoading from 'vue-infinite-loading';
import i18n from '../mixins/i18n';
import Hover from '../directives/Hover';
import NProgress from 'nprogress';
import isEmpty from 'lodash/isEmpty';
import url from '../mixins/url';

export default {
    name: 'user-list',

    components: {
        InfiniteLoading,
        PageHeader,
    },

    mixins: [i18n, url],

    directives: {
        Hover,
    },

    data() {
        return {
            page: 1,
            users: [],
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
                    .get('/api/users', {
                        params: {
                            page: this.page,
                        },
                    })
                    .then(({ data }) => {
                        if (!isEmpty(data) && !isEmpty(data.data)) {
                            this.page += 1;
                            this.users.push(...data.data);

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
