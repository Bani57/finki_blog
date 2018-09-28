import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'
import MyPosts from './views/MyPosts.vue'
import Dashboard from './views/Dashboard.vue'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/myposts',
      name: 'myposts',
      component: MyPosts
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: Dashboard
    },
  ]
})
