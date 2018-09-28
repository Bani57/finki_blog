<template>
<div class="ui segments">
  <div class="ui left aligned segment">
    <div class="ui header">
      <img class="ui circular image" :src="setUserImage()"/>
      <div class="content">
        {{this.author}}
        <div class="sub header">
          {{this.date}}
        </div>
      </div>
    </div>
  </div>
  <div class="ui left aligned segment">
    <pre style="margin: 0; font-family: 'Lato', 'Helvetica Neue', Arial, Helvetica, sans-serif;">{{this.content}}</pre>
  </div>
  <div class="ui right aligned segment">
    <div class="ui left labeled button" tabindex="0">
      <div class="ui basic label">
        {{this.liked ? (this.likes + 1) : this.likes}}
      </div>
      <div class="ui icon button" :class="this.liked ? 'blue' : ''" @click="likeComment()">
        <i class="thumbs up icon"></i>
      </div>
    </div>
  </div>
</div>
</template>

<script lang="ts">
import moment from 'moment';
import toastr from 'toastr';
import $ from 'jquery';

export default {
  name: 'Comment',
  props: {
    author: String,
    post: Number,
    content: String,
    date: String,
    likes: Number,
  },
  data() {
    return {
      moment: moment,
      authorAccount: null,
      liked: false,
    }
  },
  components: {},
  computed: {},
  methods: {
    setUserImage() {
      var images = require.context('@/assets/', false)
      if (this.authorAccount)
        return images('./users/' + this.authorAccount.picture)
      else return images('./user.png')
    },
    likeComment() {
      if (!this.liked)
        this.liked = true
      else
        this.liked = false
    },
  },
  mounted() {
    $(this.$refs.commentsAccordion).accordion('refresh');
    let vm = this
    fetch(`https://${process.env.VUE_APP_HOST}${process.env.BASE_URL}${process.env.VUE_APP_API}/users/getUserByUsername.php?username=${this.author}`, {
      credentials: 'include'
    }).then(response => {
      if (response.ok) {
        return response.json()
      } else {
        return Promise.reject(new Error('Failed getting user'))
      }
    }, reason => {
      toastr.options.preventDuplicates = true;
      toastr.error('Unable to fetch user account details. Try reloading', 'ERROR')
      return Promise.reject(reason)
    }).then(data => {
      vm.authorAccount = data
      return data
    })
  }
}
</script>

<style scoped>
</style>
