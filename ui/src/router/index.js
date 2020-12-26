import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routes = [
  {
    path: '/playouthistory',
    name: 'AnalyticsPlayoutHistory',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    // this pattern is intended to be used for pages that that do not target
    // regular day to day users.
    component: () => import(/* webpackChunkName: "analyticsplayouthistory" */ '../views/AnalyticsPlayoutHistory.vue')
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
