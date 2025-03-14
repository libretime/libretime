import { createI18n } from "vue-i18n"
// import messages from '@intlify/unplugin-vue-i18n/messages';
import en_US from "@/locale/en_US.json"

export const allLocales: string[] = [
    "cs_CZ",
    "de_AT",
    "de_DE",
    "el_GR",
    "en_CA",
    "en_GB",
    "en_US",
    "es_ES",
    "fr_FR",
    "hr_HR",
    "hu_HU",
    "it_IT",
    "ja_JP",
    "ko_KR",
    "nl_NL",
    "pl_PL",
    "pt_BR",
    "ru_RU",
    "sr_RS",
    "tr_TR",
    "uk_UA",
    "zh_CN",
]

export const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: "en_US",
    messages: { en_US },
})

export async function setLocale(locale: any) {
    if (!i18n.global.availableLocales.includes(locale)) {
        const messages = await import(`@/locale/${locale}.json`)

        if (messages === undefined) {
            return
        }

        i18n.global.setLocaleMessage(locale, messages)
    }

    i18n.global.locale.value = locale
}
