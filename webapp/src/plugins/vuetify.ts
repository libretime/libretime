// Styles
import "@mdi/font/css/materialdesignicons.css";
import "vuetify/styles";

// Vuetify
import { createVuetify } from "vuetify";
import type { ThemeDefinition } from "vuetify";

const customLibretimeTheme: ThemeDefinition = {
  dark: "true",
  colors: {
    background: "#353535",
    primary: "#ff5d1a",
    secondary: "#459b8f",
  },
};

export default createVuetify({
  theme: {
    defaultTheme: "customLibretimeTheme",
    themes: {
      customLibretimeTheme,
    },
  },
});
// https://vuetifyjs.com/en/introduction/why-vuetify/#feature-guides
