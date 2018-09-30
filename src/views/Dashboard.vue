<template>
<div class="dashboard">
  <div style="width: 50%; margin: auto;">
    <div class="ui search selection dropdown" ref="followingDropdown" style="width: 80%;">
      <input type="hidden" name="userToFollow" />
      <i class="dropdown icon"></i>
      <div class="default text">Find a user to follow...</div>
      <div class="menu">
        <div class="item" @click="setUserToFollow(user.username)" v-for="user in allUsers" v-if="user.username!=currentUser.username">
          <span class="text">{{user.username}}</span>
        </div>
      </div>
    </div>
    <button class="ui inline blue button" v-if="userToFollow && !followedUsersNames.includes(userToFollow)" @click="followUser()" style="margin-left: 4%; width: 15%;">Follow</button>
    <button class="ui inline red button" :data-tooltip="'Following since '+moment(followedUsersDates[userToFollow]).fromNow()" data-position="right center" v-if="userToFollow && followedUsersNames.includes(userToFollow)" @click="unfollowUser()"
      style="margin-left: 4%; width: 15%;">Unfollow</button>
  </div>
  <div class="ui header">
    See recent posts from the bloggers you followed below!
  </div>
  <Post @postDeleted="getPostsFromFollowedUsers()" v-for="post in posts" :key="post.id" :id="post.id" :author="post.author" :title="post.title" :content="post.content" :date="post.date" :likes="post.likes"></Post>
</div>
</template>

<script lang="ts">
import Vue from 'vue';
import toastr from 'toastr';
import 'toastr/build/toastr.css'
import moment from 'moment';
import Post from '@/components/Post.vue';
import $ from 'jquery';

export default Vue.extend({
  name: 'Dashboard',
  props: {},
  components: {
    Post,
  },
  data() {
    return {
      moment: moment,
      followedUsers: null,
      posts: null,
      userToFollow: null,
    }
  },
  computed: {
    allUsers() {
      return this.$store.state.allUsers;
    },
    currentUser() {
      return this.$store.state.currentUser;
    },
    followedUsersNames() {
      var result = [];
      for (var user in this.followedUsers)
        result.push(this.followedUsers[user].user)
      return result
    },
    followedUsersDates() {
      var result = {};
      for (var user in this.followedUsers)
        Vue.set(result, this.followedUsers[user].user, this.followedUsers[user].sinceDate)
      return result
    },
  },
  filters: {},
  methods: {
    setUserToFollow(user) {
      this.userToFollow = user;
    },
    getFollowedUsers() {
      let vm = this
      fetch(`http://${process.env.VUE_APP_HOST}:8080${process.env.BASE_URL}${process.env.VUE_APP_API}/followings/getFollowedUsers.php?user=${this.currentUser.username}`, {
        //credentials: 'include'
      }).then(response => {
        if (response.ok) {
          return response.json()
        } else {
          return Promise.reject(new Error('Failed getting followed users.'))
        }
      }, reason => {
        toastr.options.preventDuplicates = true;
        toastr.error('Unable to fetch followed users. Try reloading', 'ERROR')
        return Promise.reject(reason)
      }).then(data => {
        vm.followedUsers = data
        return data
      })
    },
    getPostsFromFollowedUsers() {
      let vm = this
      fetch(`http://${process.env.VUE_APP_HOST}:8080${process.env.BASE_URL}${process.env.VUE_APP_API}/followings/getPostsFromFollowedUsers.php?user=${this.currentUser.username}`, {
        //credentials: 'include'
      }).then(response => {
        if (response.ok) {
          return response.json()
        } else {
          return Promise.reject(new Error('Failed getting posts.'))
        }
      }, reason => {
        toastr.options.preventDuplicates = true;
        toastr.error('Unable to fetch user posts. Try reloading', 'ERROR')
        return Promise.reject(reason)
      }).then(data => {
        vm.posts = data
        return data
      })
    },
    followUser() {
      let vm = this
      fetch(`http://${process.env.VUE_APP_HOST}:8080${process.env.BASE_URL}${process.env.VUE_APP_API}/followings/followUser.php?user1=${this.currentUser.username}&user2=${this.userToFollow}`, {
        method: 'POST',
        //credentials: 'include',
      }).then(response => {
        if (response.ok) {
          return response.json();
        } else {
          return Promise.reject(new Error('Failed following user.'));
        }
      }, reason => {
        toastr.options.preventDuplicates = true;
        toastr.error('Failed following user', 'ERROR');
        return Promise.reject(reason);
      }).then(data => {
        if (data) {
          toastr.options.preventDuplicates = true;
          toastr.success('Started following user.', 'SUCCESS');
          vm.getFollowedUsers();
          vm.getPostsFromFollowedUsers();
        }
        return data;
      });
    },
    unfollowUser() {
      let vm = this
      fetch(`http://${process.env.VUE_APP_HOST}:8080${process.env.BASE_URL}${process.env.VUE_APP_API}/followings/unfollowUser.php?user1=${this.currentUser.username}&user2=${this.userToFollow}`, {
        method: 'DELETE',
        //credentials: 'include',
      }).then(response => {
        if (response.ok) {
          return response.json();
        } else {
          return Promise.reject(new Error('Failed unfollowing user.'));
        }
      }, reason => {
        toastr.options.preventDuplicates = true;
        toastr.error('Failed unfollowing user', 'ERROR');
        return Promise.reject(reason);
      }).then(data => {
        if (data) {
          toastr.options.preventDuplicates = true;
          toastr.success('Stopped following user.', 'SUCCESS');
          vm.getFollowedUsers();
          vm.getPostsFromFollowedUsers();
        }
        return data;
      });
    },
  },
  mounted() {
    if (!this.currentUser)
      window.location.href = "http://localhost:8081/finki_blog/"
    $(this.$refs.followingDropdown).dropdown()
    this.getFollowedUsers()
    this.getPostsFromFollowedUsers()
  },
});
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.dashboard {
  padding: 0 15px;
}
</style>
