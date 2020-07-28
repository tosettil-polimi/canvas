<template>
    <div>
        <page-header></page-header>

        <main v-if="isReady" class="py-4">
            <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12 my-3">
                <div class="my-3">
                    <h2 class="mt-3">{{ user.name }}</h2>
                    <p class="mt-2 text-secondary">
                        {{ i18n.last_updated }} {{ moment(userLastUpdated).fromNow() }}
                    </p>
                </div>

                <div class="mt-5 card shadow-lg">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 order-md-last my-auto">
                                <file-pond
                                    v-if="isReadyToAcceptUploads"
                                    name="profileImagePond"
                                    ref="pond"
                                    max-files="1"
                                    :maxFileSize="config.maxUpload"
                                    :iconRemove="getRemoveIcon"
                                    :iconRetry="getRetryIcon"
                                    :label-idle="getPlaceholderLabel"
                                    className="w-75"
                                    accepted-file-types="image/*"
                                    imagePreviewHeight="170"
                                    imageCropAspectRatio="1:1"
                                    imageResizeTargetWidth="200"
                                    imageResizeTargetHeight="200"
                                    stylePanelLayout="compact circle"
                                    styleLoadIndicatorPosition="center bottom"
                                    styleProgressIndicatorPosition="center bottom"
                                    styleButtonProcessItemPosition="center bottom"
                                    styleButtonRemoveItemPosition="center bottom"
                                    :server="getServerOptions"
                                    :allow-multiple="false"
                                    :files="selectedImagesForPond"
                                    @processfile="processedFromFilePond"
                                    @removefile="removedFromFilePond"
                                />

                                <div v-if="!isReadyToAcceptUploads" class="text-center rounded p-3">
                                    <img :src="avatarPath" class="rounded-circle w-75 shadow-inset" />

                                    <p class="mt-3 mb-0">
                                        <a
                                            href=""
                                            @click.prevent="clearAvatar"
                                            class="text-decoration-none text-success"
                                            >Clear avatar</a
                                        >
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-8 order-md-first my-auto">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label class="font-weight-bold text-uppercase text-muted small">
                                            {{ i18n.username }}
                                        </label>
                                        <input
                                            name="username"
                                            :disabled="!isAuthUserProfile"
                                            type="text"
                                            class="form-control border-0"
                                            :class="{ disabled: !isAuthUserProfile }"
                                            title="Username"
                                            v-model="username"
                                            :placeholder="i18n.choose_a_username"
                                        />
                                        <div v-if="usernameValidationError" class="invalid-feedback d-block">
                                            <strong v-for="value in usernameValidationError" :key="`${value}`">{{
                                                value
                                            }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label class="font-weight-bold text-uppercase text-muted small">
                                            {{ i18n.summary }}
                                        </label>
                                        <textarea
                                            rows="4"
                                            id="summary"
                                            name="summary"
                                            :disabled="!isAuthUserProfile"
                                            style="resize: none;"
                                            class="form-control border-0"
                                            :class="{ disabled: !isAuthUserProfile }"
                                            v-model="summary"
                                            :placeholder="i18n.tell_us_about_yourself"
                                        >
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 mb-2">
                            <div class="col-md">
                                <a
                                    href="#"
                                    :disabled="!isAuthUserProfile"
                                    onclick="this.blur()"
                                    class="btn btn-success btn-block font-weight-bold mt-0"
                                    :class="{ disabled: !isAuthUserProfile }"
                                    aria-label="Save"
                                    @click.prevent="updateProfile"
                                >
                                    {{ i18n.save }}
                                </a>
                            </div>
                            <div class="col-md">
                                <router-link
                                    :to="{ name: 'stats' }"
                                    class="btn btn-link btn-block font-weight-bold text-muted text-decoration-none"
                                >
                                    {{ i18n.cancel }}
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import PageHeader from '../components/PageHeader';
import NProgress from 'nprogress';
import vueFilePond from 'vue-filepond';
import toast from '../mixins/toast';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import FilePondPluginImageValidateSize from 'filepond-plugin-image-validate-size';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import i18n from '../mixins/i18n';
import store from '../store';
import get from 'lodash/get';
import isEmpty from 'lodash/isEmpty';
import request from '../mixins/request';
import url from "../mixins/url";
import config from "../store/modules/config";

const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
    FilePondPluginImageValidateSize,
    FilePondPluginFileValidateSize,
    FilePondPluginImageExifOrientation
);

export default {
    name: 'edit-user',

    components: {
        PageHeader,
        FilePond,
    },

    mixins: [i18n, request, url, toast],

    data() {
        return {
            user: null,
            // meta: null,
            username: null,
            summary: null,
            avatar: null,
            selectedImagesForPond: [],
            isReadyToAcceptUploads: false,
            isReady: false,
        };
    },

    watch: {
        $route(to) {
            this.isReady = false;
            this.user = null;
            this.username =  null;
            this.summary = null;
            this.avatar = null;
            // this.meta = null;
            this.fetchUser(to.params.id);
            this.isReady = true;
            NProgress.done();
        },
    },

    async created() {
        await this.fetchUser(this.$route.params.id);
        this.isReady = true;
        NProgress.done();
    },

    computed: {
        userLastUpdated() {
            return get('updated_at', this.meta, this.user.updated_at);
        },

        config() {
            return store.state.config;
        },

        auth() {
            return store.state.auth;
        },

        avatarPath() {
            return this.avatar || url.methods.gravatar(this.user.email);
        },

        isAuthUserProfile() {
            return this.auth.id === this.user.id;
        },

        getServerOptions() {
            return {
                url: '/' + this.config.path + '/api/uploads',
                headers: {
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
                },
            };
        },

        usernameValidationError() {
            // let errors = Object.values(this.user.errors);
            //
            // return get(errors.flat(1), '[0].username', null);
        },

        getRetryIcon() {
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-refresh" width="26"><circle style="fill:none" cx="12" cy="12" r="10"/><path style="fill:white" d="M8.52 7.11a5.98 5.98 0 0 1 8.98 2.5 1 1 0 1 1-1.83.8 4 4 0 0 0-5.7-1.86l.74.74A1 1 0 0 1 10 11H7a1 1 0 0 1-1-1V7a1 1 0 0 1 1.7-.7l.82.81zm5.51 8.34l-.74-.74A1 1 0 0 1 14 13h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1.7.7l-.82-.81A5.98 5.98 0 0 1 6.5 14.4a1 1 0 1 1 1.83-.8 4 4 0 0 0 5.7 1.85z"/></svg>';
        },

        getRemoveIcon() {
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="26" class="icon-close-circle"><circle style="fill:none" cx="12" cy="12" r="10"/><path style="fill:white" d="M13.41 12l2.83 2.83a1 1 0 0 1-1.41 1.41L12 13.41l-2.83 2.83a1 1 0 1 1-1.41-1.41L10.59 12 7.76 9.17a1 1 0 0 1 1.41-1.41L12 10.59l2.83-2.83a1 1 0 0 1 1.41 1.41L13.41 12z"/></svg>';
        },

        getPlaceholderLabel() {
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="35" class="icon-cloud-upload"><path class="fill-dark-gray" d="M18 14.97c0-.76-.3-1.51-.88-2.1l-3-3a3 3 0 0 0-4.24 0l-3 3A3 3 0 0 0 6 15a4 4 0 0 1-.99-7.88 5.5 5.5 0 0 1 10.86-.82A4.49 4.49 0 0 1 22 10.5a4.5 4.5 0 0 1-4 4.47z"/><path class="fill-dark-gray" d="M11 14.41V21a1 1 0 0 0 2 0v-6.59l1.3 1.3a1 1 0 0 0 1.4-1.42l-3-3a1 1 0 0 0-1.4 0l-3 3a1 1 0 0 0 1.4 1.42l1.3-1.3z"/></svg><br/> Drop files or click here to upload';
        },
    },

    methods: {
        fetchUser(id) {
            return this.request()
                .get('/api/users/' + id)
                .then(({ data }) => {
                    console.log(data)
                    this.user = data.user;
                    this.summary = get(data.meta, 'summary', null);
                    this.username = get(data.meta, 'username', null);
                    this.avatar = get(data.meta, 'avatar', null);
                });
        },

        processedFromFilePond() {
            this.isReadyToAcceptUploads = true;
            this.avatar = document.getElementsByName('profileImagePond')[0].value;
        },

        removedFromFilePond() {
            this.isReadyToAcceptUploads = true;
            this.avatar = null;
            this.selectedImagesForPond = [];
        },

        updateProfile() {
            this.request()
                .post('/api/users/' + this.user.id, {...this.user, ...this.meta})
                .then(({ data }) => {
                    this.user = data.user;
                    this.summary = data.meta.summary;
                    this.username = data.meta.username;
                    this.avatar = data.meta.avatar;

                    store.dispatch('auth/setAvatar', data.meta.avatar);

                    toast.methods.toast(config.state.i18n.saved);
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },

        clearAvatar() {
            this.avatar = url.methods.gravatar(this.user.email);
            this.isReadyToAcceptUploads = true;
        },
    },
};
</script>

<style scoped lang="scss">
.filepond--wrapper {
    display: flex;
    justify-content: center;
}

.filepond--root {
    margin-bottom: 0;
}
</style>
