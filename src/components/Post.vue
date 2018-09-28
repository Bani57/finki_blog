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
          <textarea name="commentContent" v-model="commentContent" :rows="setContentRows()" placeholder="What do you think?" style="font-family: 'Lato', 'Helvetica Neue', Arial, Helvetica, sans-serif;"/>
          </div>
          <div @click="addComment()" class="ui blue submit button field">
            Comment
          </div>
          <div @click="clearCommentFields()" class="ui clear button field" style="margin-left: 15px">
            Clear
          </div>
          </div>
            <Comment author="Rashantal" :post='1' content="Nice post" :date="moment().subtract(5,'minutes').fromNow()" :likes='2'></Comment>
            <Comment author="Bani57" :post='1' content="Thanks <3" :date="moment().subtract(3,'minutes').fromNow()" :likes='1'></Comment>
            <Comment author="MakedonciNaInternet" :post='1' content="BOJKOT" :date="moment().subtract(1,'minutes').fromNow()" :likes='0'></Comment>
    </div>
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
    }
  },
  methods: {
    setUserImage() {
      var images = require.context('@/assets/', false)
      if (this.authorAccount)
        return images('./users/' + this.authorAccount.picture)
      else return images('./user.png')
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
          user: this.author,
          post: this.id,
          content: this.commentContent
        }
        fetch(`https://${process.env.VUE_APP_HOST}${process.env.BASE_URL}${process.env.VUE_APP_API}/comments/addComment.php`, {
          method: 'POST',
          body: JSON.stringify(comment),
          credentials: 'include',
        }).then((response) => {
          if (response.ok) {
            return response.json();
          } else {
            return Promise.reject(new Error('Failed adding new comment on post'));
          }
        }, (reason) => {
          toastr.options.preventDuplicates = true;
          toastr.error('Failed adding new comment on post.', 'ERROR');
          return Promise.reject(reason);
        }).then((data) => {
          if (data) {
            toastr.options.preventDuplicates = true;
            toastr.success('Comment added.', 'SUCCESS')
          }
          return data;
        });
        this.clearCommentFields()
      }
    },
    clearCommentFields() {
      this.commentContent = null
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
    fetch(`https://${process.env.VUE_APP_HOST}${process.env.BASE_URL}${process.env.VUE_APP_API}/comments/getCommentsFromPost.php?id=${this.id}`, {
      credentials: 'include'
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
  }
}
</script>

<style scoped>
</style>
