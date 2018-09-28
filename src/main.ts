import Vue from 'vue'
import App from './App.vue'
import { sync } from 'vuex-router-sync';
import router from './router'
import store from './store'

import '../semantic/dist/semantic';
import '../semantic/dist/semantic.css';

const unsync = sync(store, router); // done. Returns an unsync callback fn

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
