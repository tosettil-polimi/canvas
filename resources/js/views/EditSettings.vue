<template>
    <div>
        <page-header></page-header>

        <main class="py-4">
            <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12 my-3">
                <div class="my-3">
                    <h2 class="mt-3">{{ i18n.settings }}</h2>
                </div>

                <div class="mt-5 card shadow-lg" v-if="isReady">
                    <div class="card-body p-0">
                        <div class="d-flex rounded-top p-3 align-items-center">
                            <div class="mr-auto py-1">
                                <p class="mb-1 font-weight-bold lead">
                                    {{ i18n.weekly_digest }}
                                </p>
                                <p class="mb-1 d-none d-lg-block">
                                    {{ i18n.toggle_digest }}
                                </p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="align-middle">
                                    <div class="form-group my-auto">
                                        <span class="switch switch-sm">
                                            <input
                                                type="checkbox"
                                                class="switch"
                                                id="digest"
                                                @change="toggleDigest"
                                                :checked="auth.digest"
                                                v-model="auth.digest"
                                            />
                                            <label for="digest" class="mb-0 sr-only">
                                                {{ i18n.weekly_digest }}
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex border-top p-3 align-items-center">
                            <div class="mr-auto py-1">
                                <p class="mb-1 font-weight-bold lead">
                                    {{ i18n.dark_mode }}
                                </p>
                                <p class="mb-1 d-none d-lg-block">
                                    {{ i18n.toggle_dark_mode }}
                                </p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="align-middle">
                                    <div class="form-group my-auto">
                                        <span class="switch switch-sm">
                                            <input
                                                type="checkbox"
                                                class="switch"
                                                id="darkMode"
                                                @change="toggleDarkMode"
                                                :checked="auth.darkMode"
                                                v-model="auth.darkMode"
                                            />
                                            <label for="darkMode" class="mb-0 sr-only">
                                                {{ i18n.dark_mode }}
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex border-top p-3 align-items-center">
                            <div class="mr-auto py-1">
                                <p class="mb-1 font-weight-bold lead">
                                    {{ i18n.locale }}
                                </p>
                                <p class="mb-1 d-none d-lg-block">
                                    {{ i18n.select_your_language_or_region }}
                                </p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="align-middle">
                                    <div class="form-group row mt-3">
                                        <div class="col-12">
                                            <select
                                                class="custom-select border-0"
                                                @change="selectLocale"
                                                v-model="auth.locale"
                                                name="locale"
                                            >
                                                <option
                                                    v-for="code in config.languageCodes"
                                                    :key="code"
                                                    :value="code"
                                                    :selected="auth.locale === code"
                                                >
                                                    {{ getLocaleDisplayName(code) }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <div class="mt-3 d-flex justify-content-center">
            <p class="text-muted">{{ config.version }}</p>
        </div>
    </div>
</template>

<script>
    import NProgress from 'nprogress';
    import PageHeader from '../components/PageHeader';
    import i18n from '../mixins/i18n';
    import Hover from '../directives/Hover';
    import store from '../store';

    export default {
        name: 'edit-settings',

        components: {
            PageHeader,
        },

        mixins: [ i18n ],

        directives: {
            Hover,
        },

        data() {
            return {
                isReady: false,
            };
        },

        created() {
            this.isReady = true;
            NProgress.done();
        },

        computed: {
            auth() {
                return store.state.auth;
            },

            config() {
                return store.state.config;
            },
        },

        methods: {
            getLocaleDisplayName(locale) {
                return i18n.methods.getDisplayName(locale)
            },

            toggleDigest() {
                store.dispatch('auth/updateDigest', this.auth);
            },

            selectLocale() {
                store.dispatch('config/updateI18n', this.auth.locale);
                store.dispatch('auth/updateLocale', this.auth);
            },

            toggleDarkMode() {
                store.dispatch('auth/updateDarkMode', this.auth);

                if (this.auth.darkMode === true) {
                    document.body.setAttribute('data-theme', 'dark');
                } else {
                    document.body.removeAttribute('data-theme');
                }
            },
        },
    };
</script>
