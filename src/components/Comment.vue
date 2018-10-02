<template>
<div class="ui segments">
  <div class="ui left aligned red segment">
    <div class="ui header">
      <img class="ui circular image" :src="setUserImage()" style="width: 3em; height: 3em;" />
      <div class="content">
        {{this.author}}
        <div class="sub header" @mouseover="setDateDisplay(false)" @mouseleave="setDateDisplay(true)">
          {{dateDisplay}}
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
        {{this.likes}}
      </div>
      <div class="ui icon button" :class="this.liked ? 'blue' : ''" @click="likeComment()">
        <i class="thumbs up icon"></i>
      </div>
    </div>
  </div>
  <div class="ui left aligned grey segment" v-if="this.author==currentUser.username">
    <button class="ui button" @click="deleteComment()">Delete comment</button>
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
      dateDisplay: moment(this.date).fromNow(),
    }
  },
  components: {},
  computed: {
    currentUser() {
      return this.$store.state.currentUser;
    },
  },
  methods: {
    setUserImage() {
      var images = require.context('@/assets/', true)
      if (this.authorAccount && this.authorAccount.picture)
        return images('./users/' + this.authorAccount.picture)
      else return images('./user.png')
    },
    setDateDisplay(relative) {
      if (relative)
        this.dateDisplay = moment(this.date).fromNow();
      else
        this.dateDisplay = moment(this.date).format("DD MMMM YYYY HH:mm:ss");
    },
    updateCommentLikes(amount) {
      fetch(`http://${process.env.VUE_APP_HOST}:8080${process.env.BASE_URL}${process.env.VUE_APP_API}/comments/updateCommentLikes.php?user=${this.author}&post=${this.post}&date=${this.date}&amount=${amount}`, {
        method: 'PATCH',
        //credentials: 'include',
      }).then((response) => {
        if (response.ok) {
          return response.json();
        } else {
          return Promise.reject(new Error('Failed updating likes.'));
        }
      }, (reason) => {
        toastr.options.preventDuplicates = true;
        toastr.error('Failed updating likes.', 'ERROR');
        return Promise.reject(reason);
      }).then((data) => {
        if (data) {
          toastr.options.preventDuplicates = true;
          toastr.success('Likes updated.', 'SUCCESS');
          this.$emit('commentDeleted');
        }
        return data;
      });
    },
    likeComment() {
      if (!this.liked) {
        this.liked = true
        this.updateCommentLikes(this.likes + 1)
      } else {
        this.liked = false
        this.updateCommentLikes(this.likes - 1)
      }
    },
    deleteComment() {
      let vm = this
      fetch(`http://${process.env.VUE_APP_HOST}:8080${process.env.BASE_URL}${process.env.VUE_APP_API}/comments/deleteComment.php?user=${this.author}&post=${this.post}&date=${this.date}`, {
        method: 'DELETE',
        //credentials: 'include',
      }).then((response) => {
        if (response.ok) {
          return response.json();
        } else {
          return Promise.reject(new Error('Failed deleting comment.'));
        }
      }, (reason) => {
        toastr.options.preventDuplicates = true;
        toastr.error('Failed deleting comment.', 'ERROR');
        return Promise.reject(reason);
      }).then((data) => {
        if (data) {
          toastr.options.preventDuplicates = true;
          toastr.success('Comment deleted.', 'SUCCESS');
          vm.$emit('commentDeleted');
        }
        return data;
      });
    },
  },
  mounted() {
    $(this.$refs.commentsAccordion).accordion('refresh');
    let vm = this
    if (this.$store.state.allUsers[this.author])
      vm.authorAccount = this.$store.state.allUsers[this.author]
    else {
      fetch(`http://${process.env.VUE_APP_HOST}:8080${process.env.BASE_URL}${process.env.VUE_APP_API}/users/getUserByUsername.php?username=${this.author}`, {
        //credentials: 'include'
      }).then(response => {
        if (response.ok) {
          return response.json()
        } else {
          return Promise.reject(new Error('Failed getting user.'))
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
}
</script>

<style scoped>
</style>
