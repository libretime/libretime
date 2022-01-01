import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

// define all the individual routes as chunk so they get lazy loaded into the legacy interface
// we can default to non layz loading routes once our frontcontroller is replaced by the spa
// and we can get rid of the postcss prefix chainWebpack config the prefixes our vars with
// the .libretime-vue class. Yes, this is hacky but it lets us stangle all the individual
// routes by implementing their views first and refactoring the topbar and nav later.
const routes = [
  {
    path: '/',
    name: 'RadioPage',
    component: () => import(/* webpackChunkName: "RadioPage" */ '../views/Index.vue'),
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import(/* webpackChunkName: "Dashboard" */ '../views/Dashboard.vue'),
    children: [
      {
        path: 'library',
        name: 'Library',
        component: () => import(/* webpackChunkName: "library" */ '../views/Library.vue'),
      },
      {
        path: 'calendar',
        name: 'Calendar',
        component: () => import(/* webpackChunkName: "calendar" */ '../views/Calendar.vue'),
      },
      {
        path: 'widgets',
        name: 'Widgets',
        component: () => import(/* webpackChunkName: "widgets" */ '../views/Widgets.vue'),
      },
      {
        path: 'playouthistory',
        name: 'AnalyticsPlayoutHistory',
        component: () =>
          import(
            /* webpackChunkName: "analyticsplayouthistory" */ '../views/AnalyticsPlayoutHistory.vue'
          ),
      },
      {
        path: 'settings',
        name: 'Settings',
        component: () => import(/* webpackChunkName: "settings" */ '../views/Settings.vue'),
        children: [
          {
            path: 'general',
            name: 'GeneralSettings',
            component: () =>
              import(/* webpackChunkName: "GeneralSettings" */ '../views/Settings/General.vue'),
          },
          {
            path: 'users',
            name: 'Users',
            component: () => import(/* webpackChunkName: "Users" */ '../views/Settings/Users.vue'),
          },
          {
            path: 'streams',
            name: 'StreamSettings',
            component: () =>
              import(/* webpackChunkName: "StreamSettings" */ '../views/Settings/Stream.vue'),
          },
          {
            path: 'tracktypes',
            name: 'TrackTypes',
            component: () =>
              import(/* webpackChunkName: "TrackTypes" */ '../views/Settings/TrackTypes.vue'),
          },
          {
            path: 'status',
            name: 'Status',
            component: () =>
              import(/* webpackChunkName: "Status" */ '../views/Settings/Status.vue'),
          },
        ],
      },
      {
        path: 'help',
        name: 'Help',
        component: () => import(/* webpackChunkName: "help" */ '../views/Help.vue'),
      },
    ],
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes,
})

export default router
