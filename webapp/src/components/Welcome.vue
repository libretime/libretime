<template>
    <v-container class="fill-height">
        <v-responsive class="d-flex align-center text-center fill-height">
            <v-sheet color="grey-darken-1" rounded>
                <v-row>
                    <v-col class="v-col-2 ma-5">
                        <LocaleSelect />
                    </v-col>
                    <v-col> </v-col>
                </v-row>
                <v-img height="100" src="@/assets/icon.svg" />

                <div class="text-body-2 font-weight-light mb-n1">
                    Welcome to
                </div>

                <h1 class="text-h2 font-weight-bold">Libretime</h1>

                <div class="py-8"></div>

                <h4 class="text-h4 py-2">{{ $t("Getting Started") }}</h4>

                <div class="py-2"></div>

                <v-row class="d-flex align-center justify-center">
                    <v-col cols="auto">
                        <v-btn
                            href="https://vuetifyjs.com/components/all/"
                            min-width="164"
                            rel="noopener noreferrer"
                            target="_blank"
                            variant="text"
                        >
                            <v-icon
                                icon="mdi-view-dashboard"
                                size="large"
                                start
                            />

                            {{ $t("User Manual") }}
                        </v-btn>
                    </v-col>

                    <v-col cols="auto">
                        <v-btn
                            color="primary"
                            href="https://vuetifyjs.com/introduction/why-vuetify/#feature-guides"
                            min-width="228"
                            rel="noopener noreferrer"
                            size="x-large"
                            target="_blank"
                            variant="flat"
                        >
                            <v-icon icon="mdi-speedometer" size="large" start />

                            {{ $t("Status") }}
                        </v-btn>
                    </v-col>

                    <v-col cols="auto">
                        <v-btn
                            href="https://libretime.org/"
                            min-width="164"
                            rel="noopener noreferrer"
                            target="_blank"
                            variant="text"
                        >
                            <v-icon
                                icon="mdi-account-group"
                                size="large"
                                start
                            />

                            {{ $t("Help") }}
                        </v-btn>
                    </v-col>
                </v-row>

                <v-divider
                    :thickness="5"
                    color="white"
                    class="ma-5"
                ></v-divider>

                <v-row class="px-2">
                    <v-col>
                        <v-card
                            title="Easy to Use"
                            text="LibreTime was designed from the ground up to be easily installed and used to get your radio station on the air quickly."
                        />
                    </v-col>
                    <v-col>
                        <v-card
                            title="Powerful Library Management"
                            text="Import everything from music to sweepers to full-length programs, then have shows and podcasts scheduled automatically."
                            variant="outlined"
                        />
                    </v-col>
                    <v-col>
                        <v-card
                            title="AM/FM & Web"
                            text="Audio playout to your broadcasting console or transmitter, or start an internet radio station directly from the cloud. LibreTime can do it all."
                        />
                    </v-col>
                </v-row>
                <div class="py-1"></div>
            </v-sheet>
        </v-responsive>
    </v-container>
</template>

<script setup lang="ts">
import LocaleSelect from "@/components/LocaleSelect.vue"
</script>
