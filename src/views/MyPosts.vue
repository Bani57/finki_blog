<template>
<div class="myposts">
  <div class="ui post form segment" style="width: 50%; margin:auto;">
    <div class="ui header">
      Write a new post
    </div>
    <div class="ui error message"></div>
    <div class="required field">
      <label>Title</label>
      <input type="text" name="title" v-model="title" placeholder="Give a name to your post!" />
    </div>
    <div class="required field">
      <label>Content</label>
      <textarea name="content" v-model="content" :rows="setContentRows()" placeholder="What's on your mind today?" style="font-family: 'Lato', 'Helvetica Neue', Arial, Helvetica, sans-serif;" />
      </div>
      <div class="required field">
        <label>Captcha</label>
        <input type="hidden" name="captcha" v-model="captchaCorrect" />
        <my-captcha :callSuccess="captchaOk" color="green" mode="text" resolve="text" class="captcha" ref="captcha"></my-captcha>
      </div>
      <div @click="makePost()" class="ui blue submit button field">
        Post
      </div>
      <div @click="clearPostFields()" class="ui clear button field" style="margin-left: 15px">
        Clear
      </div>
      </div>
      <Post @postDeleted="getPostsFromUser()" v-for="post in posts" :key="post.id" :id="post.id" :author="post.author" :title="post.title" :content="post.content" :date="post.date" :likes="post.likes"></Post>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import toastr from 'toastr';
import 'toastr/build/toastr.css'
import $ from 'jquery';
import Post from '@/components/Post.vue';
import moment from 'moment';
import myCaptcha from 'vue-captcha';

$(document).ready(function() {
  $('.ui.post.form')
    .form({
      fields: {
        title: {
          identifier: 'title',
          rules: [{
              type: 'empty',
              prompt: 'Please enter a title of your post.'
            },
            {
              type: 'maxLength[50]',
              prompt: 'Sorry, that title is too long.'
            }
          ]
        },
        content: {
          identifier: 'content',
          rules: [{
              type: 'empty',
              prompt: 'Sorry you cannot submit an empty post.'
            },
            {
              type: 'maxLength[2000]',
              prompt: 'Sorry, your post is too long.'
            }
          ]
        },
        captcha: {
          identifier: 'captcha',
          rules: [{
            type: 'empty',
            prompt: 'Please solve the captcha.'
          }]
        }
      },
      onSuccess: function(event) {
        event.preventDefault();
      }
    });
  $('.no-sticky-animation').on('mousedown', function(event) {
    event.preventDefault();
  });
});

export default Vue.extend({
  name: 'MyPosts',
  props: {},
  components: {
    Post,
    myCaptcha,
  },
  data() {
    return {
      moment: moment,
      title: null,
      content: null,
      posts: null,
      captchaCorrect: null,
    }
  },
  computed: {
    validPost() {
      var valid = this.title && this.content && this.captchaCorrect
      if (this.title)
        valid = valid && this.title.length <= 50
      if (this.content)
        valid = valid && this.content.length <= 2000
      return valid
    },
    currentUser() {
      return this.$store.state.currentUser;
    }
  },
  filters: {},
  methods: {
    getPostsFromUser() {
      let vm = this
      fetch(`http://${process.env.VUE_APP_HOST}:8080${process.env.BASE_URL}${process.env.VUE_APP_API}/posts/getPostsFromUser.php?author=${this.currentUser.username}`, {
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
    setContentRows() {
      var newLines = 0
      if (this.content)
        newLines = this.content.split('\n').length - 1
      return newLines + 1
    },
    makePost() {
      if (this.validPost) {
        var post = {
          title: this.title,
          content: this.content
        }
        fetch(`http://${process.env.VUE_APP_HOST}:8080${process.env.BASE_URL}${process.env.VUE_APP_API}/posts/addPost.php?username=${this.currentUser.username}`, {
          method: 'POST',
          body: JSON.stringify(post),
          //credentials: 'include',
        }).then((response) => {
          if (response.ok) {
            return response.json();
          } else {
            return Promise.reject(new Error('Failed adding new post.'));
          }
        }, (reason) => {
          toastr.options.preventDuplicates = true;
          toastr.error('Failed adding new post.', 'ERROR');
          return Promise.reject(reason);
        }).then((data) => {
          if (data) {
            toastr.options.preventDuplicates = true;
            toastr.success('Post created.', 'SUCCESS');
            this.clearPostFields();
            this.getPostsFromUser();
          }
          return data;
        });
      }
    },
    clearPostFields() {
      this.title = null
      this.content = null
      this.showCaptcha = false
      this.captchaCorrect = null
      this.$refs.captcha.picture()
      this.$refs.captcha.auth = false
      this.$refs.captcha.success = 0
      this.$refs.captcha.text = null
    },
    captchaOk() {
      this.captchaCorrect = 'OK'
    }
  },
  mounted() {
    if (!this.currentUser)
      window.location.href = "http://localhost:8081/finki_blog/"
    this.getPostsFromUser()
  },
});
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
.myposts {
  padding: 0 15px;
}

.captcha {
  border-radius: 0.28571429rem;
  width: 50% !important;
  margin: auto;
}

.captcha .content-captcha .vue-captcha-img {
  border-radius: 0.28571429rem;
  width: 75%;
  margin: auto;
}

.captcha .content-text {
  width: 75%;
  margin: auto;
  padding: 0 !important;
}

.captcha .content-text .input {
  width: 100%;
}

.captcha .content-ok {
  width: 75%;
  margin: auto;
}

.captcha button {
  width: 37% !important;
  border-radius: 0.28571429rem;
}
</style>
