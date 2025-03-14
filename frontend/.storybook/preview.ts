import { setup } from "@storybook/vue3";
import type { Preview } from "@storybook/vue3";
import { withVuetifyTheme } from "./withVuetifyTheme.decorator";
import { registerPlugins } from "../src/plugins";

const preview: Preview = {
  parameters: {
    controls: {
      matchers: {
        color: /(background|color)$/i,
        date: /Date$/i,
      },
    },
  },
};

export const globalTypes = {
  theme: {
    name: "Theme",
    description: "Global theme for components",
    toolbar: {
      icon: "paintbrush",
      items: [
        { value: "light", left: "🌞", title: "Light theme" },
        { value: "dark", left: "🌚", title: "Dark theme" },
      ],
      dynamicTitle: true,
    },
  },
};

setup((app) => {
  // Register plugins into Storybook
  registerPlugins(app);
});

export default preview;
export const decorators = [withVuetifyTheme];
