<template>
<div class="nav-bar ui menu" ref="navBar" style="border-radius: 0px !important">
  <router-link class="header item" to="/">
    <img src="../assets/logo.png">
    <span style="margin-left: 10px">FinkiBlog</span>
  </router-link>
  <router-link to="/myposts" class="header item">My posts</router-link>
  <router-link to="/dashboard" class="header item">Dashboard</router-link>

  <div class="right menu">
    <div class="header item">{{currentDate}}</div>
  </div>
</div>
</template>

<script lang="ts">
import moment from 'moment'

export default {
  name: 'NavBar',
  data() {
    return {
      moment: moment
    }
  },
  components: {},
  computed: {
    currentDate() {
      return moment().format("DD.MM.YYYY");
    }
  },
  methods: {},
}
</script>

<style scoped>
</style>
