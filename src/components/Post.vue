<template>
<div class="ui segments" style="width: 50%; margin: auto;">
  <div class="ui left aligned blue segment">
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
    <div class="ui header">
      {{this.title}}
    </div>
    <pre style="font-family: 'Lato', 'Helvetica Neue', Arial, Helvetica, sans-serif;">{{this.content}}</pre>
  </div>
  <div class="ui right aligned segment">
    <div class="ui left labeled button" tabindex="0">
      <div class="ui basic label">
        {{this.liked ? (this.likes + 1) : this.likes}}
      </div>
      <div class="ui icon button" :class="this.liked ? 'blue' : ''" @click="likePost()">
        <i class="thumbs up icon"></i>
      </div>
    </div>
  </div>
  <div class="ui accordion segment" ref="commentsAccordion" @click="refreshAccordion()">
    <div class="title">
      <i class="dropdown icon"></i>
      <span class="ui header">Comments</span>
    </div>
    <div class="content">
      <div class="ui comment form segment" style="margin:auto;">
        <div class="ui header">
          Add a comment here
        </div>
        <div class="ui error message"></div>
        <div class="required field">
          <label>Content</label>
          <textarea name="commentContent" v-model="commentContent" :rows="setContentRows()" placeholder="What do you think?" style="font-family: 'Lato', 'Helvetica Neue', Arial, Helvetica, sans-serif;" />
          </div>
          <div @click="addComment()" class="ui blue submit button field">
            Comment
          </div>
          <div @click="clearCommentFields()" class="ui clear button field" style="margin-left: 15px">
            Clear
          </div>
          </div>
          <Comment @commentDeleted="getCommentsFromPost()" v-for="comment in comments" :key="comment.date" :author="comment.user" :post="comment.post" :content="comment.content" :date="comment.date" :likes="comment.likes"></Comment>
    </div>
  </div>
  <div class="ui left aligned grey segment" v-if="this.author==currentUser.username">
    <button class="ui button" @click="deletePost()">Delete post</button>
  </div>
</div>
</template>

<script lang="ts">
import moment from 'moment';
import toastr from 'toastr';
import $ from 'jquery';
import Comment from '@/components/Comment.vue';

$(document).ready(function() {
  $('.ui.comment.form')
    .form({
      fields: {
        commentContent: {
          identifier: 'commentContent',
          rules: [{
              type: 'empty',
              prompt: 'Sorry you cannot add an empty comment.'
            },
            {
              type: 'maxLength[200]',
              prompt: 'Sorry, your comment is too long.'
            }
          ]
        },
      },
      onSuccess: function(event) {
        event.preventDefault();
      }
    });
  $('.no-sticky-animation').on('mousedown', function(event) {
    event.preventDefault();
  });
});

export default {
  name: 'Post',
  props: {
    id: Number,
    author: String,
    title: String,
    content: String,
    date: String,
    likes: Number,
  },
  data() {
    return {
      moment: moment,
      authorAccount: null,
      liked: false,
      commentContent: null,
      comments: null,
      dateDisplay: moment(this.date).fromNow(),
    }
  },
  components: {
    Comment,
  },
  computed: {
    validComment() {
      var valid = this.commentContent
      if (this.commentContent)
        valid = valid && this.commentContent.length <= 200
      return valid
    },
    currentUser() {
      return this.$store.state.currentUser;
    }
  },
  methods: {
    getCommentsFromPost() {
      let vm = this
      fetch(`http://${process.env.VUE_APP_HOST}:8080${process.env.BASE_URL}${process.env.VUE_APP_API}/comments/getCommentsFromPost.php?post=${this.id}`, {
        //credentials: 'include'
      }).then(response => {
        if (response.ok) {
          return response.json()
        } else {
          return Promise.reject(new Error('Failed getting comments.'))
        }
      }, reason => {
        toastr.options.preventDuplicates = true;
        toastr.error('Unable to fetch comments. Try reloading', 'ERROR')
        return Promise.reject(reason)
      }).then(data => {
        vm.comments = data
        return data
      })
    },
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
    likePost() {
      if (!this.liked)
        this.liked = true
      else
        this.liked = false
    },
    refreshAccordion() {
      $(this.$refs.commentsAccordion).accordion('refresh');
    },
    setContentRows() {
      var newLines = 0
      if (this.commentContent)
        newLines = this.commentContent.split('\n').length - 1
      return newLines + 1
    },
    addComment() {
      if (this.validComment) {
        var comment = {
          user: this.currentUser.username,
          post: this.id,
          content: this.commentContent
        }
        fetch(`http://${process.env.VUE_APP_HOST}:8080${process.env.BASE_URL}${process.env.VUE_APP_API}/comments/addComment.php`, {
          method: 'POST',
          body: JSON.stringify(comment),
          //credentials: 'include',
        }).then((response) => {
          if (response.ok) {
            return response.json();
          } else {
            return Promise.reject(new Error('Failed adding new comment on post.'));
          }
        }, (reason) => {
          toastr.options.preventDuplicates = true;
          toastr.error('Failed adding new comment on post.', 'ERROR');
          return Promise.reject(reason);
        }).then((data) => {
          if (data) {
            toastr.options.preventDuplicates = true;
            toastr.success('Comment added.', 'SUCCESS');
            this.clearCommentFields();
            this.getCommentsFromPost();
          }
          return data;
        });
      }
    },
    clearCommentFields() {
      this.commentContent = null
    },
    deletePost() {
      fetch(`http://${process.env.VUE_APP_HOST}:8080${process.env.BASE_URL}${process.env.VUE_APP_API}/posts/deletePost.php?id=${this.id}`, {
        method: 'DELETE',
        //credentials: 'include',
      }).then((response) => {
        if (response.ok) {
          return response.json();
        } else {
          return Promise.reject(new Error('Failed deleting post.'));
        }
      }, (reason) => {
        toastr.options.preventDuplicates = true;
        toastr.error('Failed deleting post.', 'ERROR');
        return Promise.reject(reason);
      }).then((data) => {
        if (data) {
          toastr.options.preventDuplicates = true;
          toastr.success('Post deleted.', 'SUCCESS');
          this.$emit('postDeleted');
        }
        return data;
      });
    },
  },
  mounted() {
    $(this.$refs.commentsAccordion).accordion('refresh');
    let vm = this
    if (this.$store.state.allUsers[this.author])
      this.authorAccount = this.$store.state.allUsers[this.author]
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
    this.getCommentsFromPost()
  }
}
</script>

<style scoped>
</style>
