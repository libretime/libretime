import { createApp } from "vue";
import { createi18n } from "vue-i18n";
import App from "./App.vue";
import router from "./router";
import vuetify from "./plugins/vuetify";
import { loadFonts } from "./plugins/webfontloader";

loadFonts();

const i18n = createi18n({
  legacy: false,
});

createApp(App).use(router).use(vuetify).use(i18n).mount("#app");
