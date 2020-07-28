<template>
    <div class="shadow">
        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12">
            <nav class="navbar d-flex px-0 py-0">
                <router-link to="/" class="navbar-brand pt-0 hover">
                    Canvas
                </router-link>

                <a @click="showSearchModal" href="#" class="hover-light ml-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" class="icon-search pr-1">
                        <circle cx="10" cy="10" r="7" style="fill: none;" />
                        <path
                            class="fill-light-gray"
                            d="M16.32 14.9l1.1 1.1c.4-.02.83.13 1.14.44l3 3a1.5 1.5 0 0 1-2.12 2.12l-3-3a1.5 1.5 0 0 1-.44-1.14l-1.1-1.1a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"
                        />
                    </svg>
                </a>

                <slot name="menu" />

                <div class="dropdown ml-3" v-cloak>
                    <a
                        href="#"
                        id="navbarDropdown"
                        class="nav-link px-0 text-secondary"
                        role="button"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <img
                            :src="user.avatar"
                            :alt="user.name"
                            class="rounded-circle my-0 shadow-inset hover-light"
                            style="width: 33px;"
                        />
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        <h6 class="dropdown-header">
                            <strong>{{ user.name }}</strong>
                            <br />
                            {{ user.email }}
                        </h6>

                        <div class="dropdown-divider"></div>

                        <router-link :to="{ name: 'edit-user', params: { id: user.id } }" class="dropdown-item">
                            {{ i18n.your_profile }}
                        </router-link>
                        <router-link :to="{ name: 'posts' }" class="dropdown-item">
                            <span>{{ i18n.posts_simple }}</span>
                        </router-link>
                        <router-link v-if="user.admin" :to="{ name: 'users' }" class="dropdown-item">
                            <span>{{ i18n.users }}</span>
                        </router-link>
                        <router-link v-if="user.admin" :to="{ name: 'tags' }" class="dropdown-item">
                            <span>{{ i18n.tags }}</span>
                        </router-link>
                        <router-link v-if="user.admin" :to="{ name: 'topics' }" class="dropdown-item">
                            <span>{{ i18n.topics }}</span>
                        </router-link>
                        <router-link :to="{ name: 'stats' }" class="dropdown-item">
                            <span>{{ i18n.stats }}</span>
                        </router-link>

                        <div class="dropdown-divider"></div>

                        <router-link :to="{ name: 'edit-settings' }" class="dropdown-item">
                            <span>{{ i18n.settings }}</span>
                        </router-link>
                        <a href="" class="dropdown-item" @click.prevent="logout">
                            {{ i18n.sign_out }}
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <search-modal ref="searchModal"></search-modal>
    </div>
</template>

<script>
import axios from 'axios';
import SearchModal from './modals/SearchModal';
import store from '../store';
import i18n from '../mixins/i18n';
import $ from 'jquery';

export default {
    name: 'page-header',

    mixins: [i18n],

    components: {
        SearchModal,
    },

    computed: {
        user() {
            return store.state.auth;
        },
    },

    methods: {
        logout() {
            let instance = axios.create();

            instance.defaults.baseURL = '/';

            instance.post('/logout').then(() => {
                window.location.href = '/login';
            });
        },

        showSearchModal() {
            $(this.$refs.searchModal.$el).modal('show');
        },
    },
};
</script>

<style scoped lang="scss">
@import '../../sass/utilities/variables';

.navbar-brand {
    font-size: 2.5rem;
    font-family: 'Caveat', serif;
}
</style>
