import Vue from 'vue';
import VueToasted from 'vue-toasted';

let Options = {
    position: 'bottom-right',
    theme: 'bubble',
    className: 'bg-success',
};

export default {
    methods: {
        toast(message, duration = 2500) {
            Vue.use(VueToasted, Options);

            return Vue.toasted.success(message).goAway(duration);
        },
    },
};
