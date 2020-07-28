module.exports = {
    root: true,
    env: {
        node: true,
        browser: true,
        es6: true,
    },
    extends: ['plugin:vue/essential', 'eslint:recommended', 'prettier/vue', 'plugin:prettier/recommended'],
    rules: {},
    globals: {},
    parserOptions: {
        ecmaVersion: 2018,
        parser: 'babel-eslint',
    },
    plugins: ['vue'],
};
