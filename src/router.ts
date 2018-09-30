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
      props: route => ({ currentUser: route.params.user }),
    },
    {
      path: '/dashboard/:user',
      name: 'Dashboard',
      component: Dashboard,
      props: route => ({ currentUser: route.params.user }),
    },
  ]
})
