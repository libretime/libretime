<template>
    <v-select
        v-bind:label="$t('Language')"
        :items="allLocales"
        v-model="selectedLocale"
        variant="underlined"
    ></v-select>
</template>

<script lang="ts">
import { allLocales, setLocale } from "@/plugins/vuei18n"
import { ref, watch } from "vue"

export default {
    setup() {
        const selectedLocale = ref("")

        watch(selectedLocale, async (selectedLocale) => {
            try {
                await setLocale(selectedLocale)
            } catch (err) {
                console.log(err)
            }
        })

        return {
            selectedLocale,
            allLocales,
        }
    },
}
</script>
