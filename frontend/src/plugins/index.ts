/**
 * plugins/index.ts
 *
 * Automatically included in `./src/main.ts`
 */

// Plugins
import vuetify from "./vuetify";
import { setupRouter } from "../router";
import { setupI18n } from "./vue-i18n";

// Types
import type { App } from "vue";

export function registerPlugins(app: App) {
  const i18n = setupI18n();
  const router = setupRouter(i18n);
  app.use(vuetify).use(router).use(i18n);
}
