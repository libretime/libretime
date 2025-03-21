import { nextTick } from "vue";
import { createI18n } from "vue-i18n";
import en_US from "@/locales/en-US.json";

import type { I18n, I18nOptions, Locale } from "vue-i18n";

export const SUPPORTED_LOCALES = ["en-US"];

export function getLocale(i18n: I18n): string {
  return i18n.global.locale.value;
}

export function setLocale(i18n: I18n, locale: Locale): void {
  i18n.global.locale.value = locale;
}

const defaultOptions: I18nOptions = {
  locale: "en-US",
  fallbackLocale: "en-US",
  legacy: false,
  messages: {
    en_US,
  },
};
export function setupI18n(options: I18nOptions = defaultOptions): I18n {
  const i18n = createI18n(options);
  setI18nLanguage(i18n, options.locale!);
  return i18n;
}

export function setI18nLanguage(i18n: I18n, locale: Locale): void {
  setLocale(i18n, locale);
  document.querySelector("html")!.setAttribute("lang", locale);
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const getResourceMessages = (r: any) => r.default || r;

export async function loadLocaleMessages(i18n: I18n, locale: Locale) {
  const messages = await import(`@/locales/${locale}.json`).then(
    getResourceMessages,
  );
  i18n.global.setLocaleMessage(locale, messages);
  return nextTick();
}
