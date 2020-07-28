<template>
    <div class="modal fade mh-100" tabindex="-1" role="dialog" aria-hidden="true" v-cloak>
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="input-group">
                        <div class="input-group-prepend mr-0 border-0">
                            <div
                                class="input-group-text pr-0 border-0"
                                :style="results.length > 0 ? 'border-radius: 0' : ''"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    width="20"
                                    class="icon-search"
                                >
                                    <circle cx="10" cy="10" r="7" style="fill: none;" />
                                    <path
                                        class="fill-muted"
                                        d="M16.32 14.9l1.1 1.1c.4-.02.83.13 1.14.44l3 3a1.5 1.5 0 0 1-2.12 2.12l-3-3a1.5 1.5 0 0 1-.44-1.14l-1.1-1.1a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"
                                    />
                                </svg>
                            </div>
                        </div>
                        <vue-fuse
                            :keys="['name']"
                            :list="searchIndex"
                            :default-all="false"
                            :include-score="true"
                            :style="results.length > 0 ? 'border-radius: 0' : ''"
                            class="form-control form-control-lg border-0"
                            :placeholder="i18n.search_canvas"
                            event-name="search"
                        >
                        </vue-fuse>
                    </div>

                    <div v-for="entity in results" :key="entity.item.id">
                        <router-link
                            :to="{
                                    name: entity.item.route,
                                    params: { id: entity.item.id },
                                }"
                            class="text-decoration-none"
                            @click="clearResults()"
                            data-dismiss="modal"
                        >
                            <div v-hover="{ class: `hover-bg` }" class="p-3">
                                <div class="d-flex align-items-center">
                                    <div class="mr-auto pl-2 col-md-8 col-sm-10 col-10">
                                        <p class="mb-0 py-1 text-truncate">
                                            <span class="font-weight-bold text-lg lead">
                                                {{ entity.item.name }}
                                            </span>
                                        </p>
                                    </div>

                                    <div class="ml-auto d-md-inline-block">
                                        <span class="mr-3 text-muted">{{ entity.item.type }}</span>
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
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import i18n from '../../mixins/i18n';
import VueFuse from 'vue-fuse';
import store from '../../store';
import Hover from '../../directives/Hover';

export default {
    name: 'search-modal',

    components: {
        VueFuse,
    },

    directives: {
        Hover,
    },

    mixins: [i18n],

    computed: {
        auth() {
            return store.state.auth;
        },
    },

    async created() {
        await this.fetchPosts();

        if (this.auth.admin === 1) {
            await this.fetchTags();
            await this.fetchTopics();
            await this.fetchUsers();
        }
    },

    mounted() {
        this.$on('search', (results) => {
            this.results = results;
        });
    },

    data() {
        return {
            results: [],
            searchIndex: []
        };
    },

    methods: {
        fetchPosts() {
            return this.request()
                .get('/api/search/posts')
                .then(({ data }) => {
                    this.searchIndex.push(...data);
                })
                .catch(() => {
                    // Add any error debugging...
                });
        },

        fetchTags() {
            return this.request()
                .get('/api/search/tags')
                .then(({ data }) => {
                    this.searchIndex.push(...data);
                })
                .catch(() => {
                    // Add any error debugging...
                });
        },

        fetchTopics() {
            return this.request()
                .get('/api/search/topics')
                .then(({ data }) => {
                    this.searchIndex.push(...data);
                })
                .catch(() => {
                    // Add any error debugging...
                });
        },

        fetchUsers() {
            return this.request()
                .get('/api/search/users')
                .then(({ data }) => {
                    this.searchIndex.push(...data);
                })
                .catch(() => {
                    // Add any error debugging...
                });
        },

        clearResults() {
            this.results = [];
        }
    },
};
</script>
