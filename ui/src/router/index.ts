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
          path: "tracks",
          name: "tracks",
          component: () => import("@/views/DashboardViews/Tracks.vue"),
        },
        {
          path: "playlists",
          name: "playlists",
          component: () => import("@/views/DashboardViews/Playlists.vue"),
        },
        {
          path: "smartblocks",
          name: "smartblocks",
          component: () => import("@/views/DashboardViews/SmartBlocks.vue"),
        },
        {
          path: "webstreams",
          name: "webstreams",
          component: () => import("@/views/DashboardViews/Webstreams.vue"),
        },
        {
          path: "podcasts",
          name: "podcasts",
          component: () => import("@/views/DashboardViews/Podcasts.vue"),
        },
        {
          path: "calendar",
          name: "calendar",
          component: () => import("@/views/DashboardViews/Calendar.vue"),
        },
        {
          path: "widgets",
          name: "widgets",
          component: () => import("@/views/DashboardViews/Widgets.vue"),
        },
        {
          path: "settings",
          name: "settings",
          component: () => import("@/views/DashboardViews/Settings.vue"),
          children: [
            {
              path: "general",
              name: "generalsettings",
              component: () =>
                import("@/views/DashboardViews/SettingsViews/General.vue"),
            },
            {
              path: "profile",
              name: "myprofile",
              component: () =>
                import("@/views/DashboardViews/SettingsViews/MyProfile.vue"),
            },
            {
              path: "users",
              name: "users",
              component: () =>
                import("@/views/DashboardViews/SettingsViews/Users.vue"),
            },
            {
              path: "streams",
              name: "streamsettings",
              component: () =>
                import("@/views/DashboardViews/SettingsViews/Streams.vue"),
            },
            {
              path: "status",
              name: "status",
              component: () =>
                import("@/views/DashboardViews/SettingsViews/Status.vue"),
            },
          ],
        },
        {
          path: "playouthistory",
          name: "playouthistory",
          component: () => import("@/views/DashboardViews/PlayoutHistory.vue"),
        },
        {
          path: "help",
          name: "help",
          component: () => import("@/views/DashboardViews/Help.vue"),
        },
      ],
    },
    {
      path: "/embed",
      name: "embed",
      redirect: "/", // No components exist on /embed
      component: () => import("@/views/EmbedViewport.vue"),
      children: [
        {
          path: "nowplaying",
          name: "nowplaying",
          component: () => import("@/views/EmbedViews/NowPlayingWidget.vue"),
        },
        {
          path: "weeklyschedule",
          name: "weeklyschedule",
          component: () =>
            import("@/views/EmbedViews/WeeklyScheduleWidget.vue"),
        },
      ],
    },
  ],
});

export default router;
