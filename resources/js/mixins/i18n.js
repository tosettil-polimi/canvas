import store from '../store';

export default {
    computed: {
        i18n() {
            return store.state.config.i18n;
        },
    },

    methods: {
        getDisplayName(locale) {
            let language = require('../data/languages.json')[locale];

            return language.nativeName;
        }
    }
};
