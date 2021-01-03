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
    path: '/playouthistory',
    name: 'AnalyticsPlayoutHistory',
    component: () =>
      import(
        /* webpackChunkName: "analyticsplayouthistory" */ '../views/AnalyticsPlayoutHistory.vue'
      ),
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes,
})

export default router
