/**
 * router/index.ts
 *
 * Automatic routes for `./src/pages/*.vue`
 */

// Composables
import { createRouter, createWebHistory } from "vue-router/auto";
import { routes } from "vue-router/auto-routes";
import {
  getLocale,
  setI18nLanguage,
  loadLocaleMessages,
} from "@/plugins/vue-i18n";

// Types
import type { I18n } from "vue-i18n";
import type { Router } from "vue-router";

export function setupRouter(i18n: I18n): Router {
  const locale = getLocale(i18n);

  const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
  });

  // Workaround for https://github.com/vitejs/vite/issues/11804
  router.onError((err, to) => {
    if (
      err?.message?.includes?.("Failed to fetch dynamically imported module")
    ) {
      if (!localStorage.getItem("vuetify:dynamic-reload")) {
        console.log("Reloading page to fix dynamic import error");
        localStorage.setItem("vuetify:dynamic-reload", "true");
        location.assign(to.fullPath);
      } else {
        console.error(
          "Dynamic import error, reloading page did not fix it",
          err,
        );
      }
    } else {
      console.error(err);
    }
  });

  router.isReady().then(() => {
    localStorage.removeItem("vuetify:dynamic-reload");
  });

  router.beforeEach(async () => {
    // load locale messages
    if (!i18n.global.availableLocales.includes(locale)) {
      await loadLocaleMessages(i18n, locale);
    }

    // set i18n language
    setI18nLanguage(i18n, locale);
  });

  return router;
}
