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
      path: '/myposts/:user',
      name: 'MyPosts',
      component: MyPosts,
    },
    {
      path: '/dashboard/:user',
      name: 'Dashboard',
      component: Dashboard,
    },
  ]
})
