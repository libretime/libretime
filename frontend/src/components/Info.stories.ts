import Info from "./Info.vue";

import type { Meta, StoryObj } from "@storybook/vue3";

const meta: Meta<typeof Info> = {
  component: Info,
  title: "Info",
  tags: ["autodocs"],
  args: {},
};

export default meta;

type Story = StoryObj<typeof Info>;
export const Default: Story = {
  args: {},
};
