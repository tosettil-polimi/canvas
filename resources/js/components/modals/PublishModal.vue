<template>
    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="lead mb-1 text-center text-lg-left">
                        {{ trans.app.publishing }}
                    </p>
                    <p class="text-secondary text-center text-lg-left">
                        {{ trans.app.post_scheduling_format }}
                        <span class="font-weight-bold">{{ window.Canvas.timezone }}</span>
                        {{ trans.app.timezone }}. (m/d/y h:m)
                    </p>

                    <div class="row">
                        <div
                            class="col-sm-6 col-12 pb-sm-0 pb-3 pr-sm-0 d-flex justify-content-center justify-content-sm-start"
                        >
                            <div class="d-flex align-items-center">
                                <select
                                    class="w-auto custom-select custom-select-sm border-0"
                                    v-model="components.month"
                                >
                                    <option
                                        v-for="value in Array.from({ length: 12 }, (_, i) =>
                                            String(i + 1).padStart(2, '0')
                                        )"
                                        :value="value"
                                        v-bind:key="value"
                                    >
                                        {{ value }}
                                    </option>
                                </select>

                                <span class="px-1">/</span>
                                <select class="w-auto custom-select custom-select-sm border-0" v-model="components.day">
                                    <option
                                        v-for="value in Array.from({ length: 31 }, (_, i) =>
                                            String(i + 1).padStart(2, '0')
                                        )"
                                        :value="value"
                                        v-bind:key="value"
                                    >
                                        {{ value }}
                                    </option>
                                </select>

                                <span class="px-1">/</span>
                                <select
                                    class="w-auto custom-select custom-select-sm border-0"
                                    v-model="components.year"
                                >
                                    <option
                                        v-for="value in Array.from(
                                            { length: 15 },
                                            (_, i) => i + new Date().getFullYear() - 10
                                        )"
                                        :value="value"
                                        v-bind:key="value"
                                    >
                                        {{ value }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12 pl-sm-0 d-flex justify-content-center justify-content-sm-start">
                            <div class="d-flex align-items-center">
                                <select
                                    class="w-auto custom-select custom-select-sm border-0"
                                    v-model="components.hour"
                                >
                                    <option
                                        v-for="value in Array.from({ length: 24 }, (_, i) =>
                                            String(i).padStart(2, '0')
                                        )"
                                        :value="value"
                                        v-bind:key="value"
                                    >
                                        {{ value }}
                                    </option>
                                </select>

                                <span class="px-1">:</span>
                                <select
                                    class="w-auto custom-select custom-select-sm border-0"
                                    v-model="components.minute"
                                >
                                    <option
                                        v-for="value in Array.from({ length: 60 }, (_, i) =>
                                            String(i).padStart(2, '0')
                                        )"
                                        :value="value"
                                        v-bind:key="value"
                                    >
                                        {{ value }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <p class="mt-3 text-success font-italic" v-if="isScheduled(this.activePost.published_at)">
                        {{ trans.app.your_post_will_publish_at }}
                        {{ this.activePost.published_at }}
                        {{ trans.app.on }}
                        {{ this.activePost.published_at }}.
                    </p>
                </div>
                <div class="modal-footer">
                    <div class="row w-100">
                        <div class="col-lg order-lg-last px-0">
                            <a
                                href="#"
                                v-if="shouldPublish"
                                class="btn btn-success btn-block font-weight-bold mt-0"
                                @click="scheduleOrPublish"
                                data-dismiss="modal"
                            >
                                {{ trans.app.publish_now }}
                            </a>

                            <a
                                href="#"
                                v-else
                                class="btn btn-success btn-block font-weight-bold mt-0"
                                @click="scheduleOrPublish"
                            >
                                {{ trans.app.schedule_to_publish }}
                            </a>
                        </div>

                        <div class="col-lg order-lg-first px-0">
                            <button
                                v-if="isScheduled(this.activePost.published_at)"
                                @click="cancelScheduling"
                                type="button"
                                class="btn btn-link btn-block text-muted font-weight-bold text-decoration-none"
                                data-dismiss="modal"
                            >
                                {{ trans.app.cancel_scheduling }}
                            </button>

                            <button
                                v-else
                                type="button"
                                class="btn btn-link btn-block text-muted font-weight-bold text-decoration-none"
                                data-dismiss="modal"
                            >
                                {{ trans.app.cancel }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
    name: 'publish-modal',

    data() {
        return {
            components: {
                day: '',
                month: '',
                year: '',
                hour: '',
                minute: '',
            },
            result: '',
            trans: JSON.parse(window.Canvas.locale.translations),
        };
    },

    computed: {
        ...mapState(['activePost']),

        shouldPublish() {
            return true;
        },
    },

    mounted() {
        this.generateDatePicker(this.activePost.published_at || new Date());
    },

    watch: {
        value(val) {
            this.generateDatePicker(val);
        },

        components: {
            handler: function () {
                this.result =
                    this.components.year +
                    '-' +
                    this.components.month +
                    '-' +
                    this.components.day +
                    ' ' +
                    this.components.hour +
                    ':' +
                    this.components.minute +
                    ':00';
            },

            deep: true,
        },
    },

    methods: {
        generateDatePicker(val) {
            let date = new Date(val);

            console.log(date);

            // console.log(val)
            // let date = new Intl.DateTimeFormat(Canvas.locale).format(val)
            // const options = { month: "long", day: "numeric", year: "numeric" };
            // console.log(new Intl.DateTimeFormat(Canvas.locale, options).format(date))

            this.components = {
                month: date.getMonth(),
                day: date.getDay(),
                year: date.getFullYear(),
                hour: date.getHours(),
                minute: date.getMinutes(),
            };
        },

        scheduleOrPublish() {
            this.activePost.published_at = this.result;
            this.$parent.save();
        },

        cancelScheduling() {
            this.activePost.published_at = '';
            this.$parent.save();
        },
    },
};
</script>
