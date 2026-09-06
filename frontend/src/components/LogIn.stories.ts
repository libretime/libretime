import LogIn from "./LogIn.vue";

import type { Meta, StoryObj } from "@storybook/vue3";

const meta: Meta<typeof LogIn> = {
  component: LogIn,
  title: "Log In",
  tags: ["autodocs"],
  args: {},
};

export default meta;

type Story = StoryObj<typeof LogIn>;
export const Default: Story = {
  args: {},
};
