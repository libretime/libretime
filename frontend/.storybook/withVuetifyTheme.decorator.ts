import { h } from "vue";

import StoryWrapper from "./StoryWrapper.vue";

export const DEFAULT_THEME = "dark";

export const withVuetifyTheme = (storyFn, context) => {
  const themeName = context.globals.theme || DEFAULT_THEME;
  const story = storyFn();

  return () => {
    return h(
      StoryWrapper,
      { themeName }, // Props for StoryWrapper
      {
        // Puts your story into StoryWrapper's "story" slot with your story args
        story: () => h(story, { ...context.args }),
      },
    );
  };
};
