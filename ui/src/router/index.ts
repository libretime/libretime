import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "radio-page",
      component: () => import("@/views/RadioPage.vue"),
    },
    {
      path: "/dashboard",
      name: "dashboard",
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import("@/views/Dashboard.vue"),
      children: [
        {
          path: "calendar",
          name: "calendar",
          component: () => import("@/views/DashboardViews/Calendar.vue"),
        },
        {
          path: "help",
          name: "help",
          component: () => import("@/views/DashboardViews/Help.vue"),
        },
      ],
    },
  ],
});

export default router;
