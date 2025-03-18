/**
 * plugins/index.ts
 *
 * Automatically included in `./src/main.ts`
 */

// Plugins
import vuetify from "./vuetify";
import router from "../router";
import i18n from "./vue-i18n";

// Types
import type { App } from "vue";

export function registerPlugins(app: App) {
  //const i18n = setupI18n();
  app.use(vuetify).use(router).use(i18n);
}
