module.exports = {
    env: {
        browser: true,
        es2021: true,
    },
    extends: [
        "eslint:recommended",
        "plugin:vue/vue3-essential",
        "plugin:@typescript-eslint/recommended",
    ],
    ignorePatterns: [],
    overrides: [],
    parser: "@typescript-eslint/parser",
    parserOptions: {
        ecmaVersion: "latest",
        sourceType: "module",
    },
    plugins: ["vue", "@typescript-eslint"],
    // rules: {
    //     quotes: ["error", "single"],
    //     "array-bracket-spacing": ["error", "never"],
    //     "array-bracket-newline": ["error", "never"],
    //     "no-mixed-spaces-and-tabs": "error",
    //     "no-trailing-spaces": "error",
    //     indent: ["error", 2],
    // },
}
