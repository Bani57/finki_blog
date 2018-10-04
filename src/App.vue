<template>
<div id="app">
  <NavBar />
  <router-view />
</div>
</template>

<script lang="ts">
import Vue from 'vue'
import NavBar from '@/components/NavBar.vue' // @ is an alias to /src

export default Vue.extend({
  name: 'App',
  components: {
    NavBar
  },
  mounted () {
    this.$store.dispatch('getAllUsers')
  }
})
</script>

<style >
#app {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
</style>
