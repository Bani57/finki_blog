<template>
<div class="dashboard">
  Dashboard
</div>
</template>

<script lang="ts">
import Vue from 'vue';
import toastr from 'toastr';
import 'toastr/build/toastr.css'

export default Vue.extend({
  name: 'Dashboard',
  props: {},
  components: {},
  data() {
    return {}
  },
  computed: {},
  filters: {},
  methods: {},
});
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.dashboard {
  padding: 0 15px;
}
</style>
