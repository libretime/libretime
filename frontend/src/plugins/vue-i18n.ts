import { createI18n } from "vue-i18n";
//import { nextTick } from "vue";
import en_US from "@/locales/en-US.json";

//import type { I18n } from "vue-i18n";

export const SUPPORTED_LOCALES = ["en_US"];

type MessageSchema = typeof en_US;

const defaultOptions = {
  locale: "en_US",
  fallbackLocale: "en_US",
  legacy: false,
  messages: {
    en_US: en_US,
  },
};

export default createI18n<[MessageSchema], "en_US">(defaultOptions);

// export function setupI18n(options = defaultOptions) {
//   const i18n = createI18n<[MessageSchema], string>(options);
//   setI18nLanguage(i18n, options.locale);
//   return i18n;
// }
//
// export function setI18nLanguage(i18n: I18n, locale: string) {
//   if (i18n.mode === "legacy") {
//     i18n.global.locale = locale;
//   } else {
//     i18n.global.locale.value = locale;
//   }
//   const root = document.querySelector("html")
//   if (root === null) {
//     return;
//   }
//   root.setAttribute("lang", locale);
// }
//
// export async function loadLocaleMessages(i18n: I18n, locale: string) {
//   console.log("Loading locale", locale);
//   const messages = await import(
//     /* webpackChunkName: "locale-[request]" */ `@/locales/${locale}.json`
//   );
//
//   if (messages === undefined) {
//     console.log("Error loading locale", locale);
//     return;
//   }
//
//   i18n.global.setLocaleMessage(locale, messages.default);
//   return nextTick();
// }
