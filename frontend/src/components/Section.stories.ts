import Section from "./Section.vue";

import type { Meta, StoryObj } from "@storybook/vue3";

const meta: Meta<typeof Section> = {
  component: Section,
  title: "Section",
  tags: ["autodocs"],
};

export default meta;

type Story = StoryObj<typeof Section>;
export const Default: Story = {};
