<template>
<div class="home">
  <img src="@/assets/logo.png" class="ui centered medium image">
  <div v-show="currentUser" style="margin-top: 30px">
    <div class="ui huge header">
      {{'Welcome, ' + currentUserName + '!'}}
      <div class="sub header" style="margin-top: 20px">Click on <b>My posts</b> to make a new post or see if anyone has commented on your previous posts, or just go to <b>Dashboard</b> to see what others have to say.</div>
    </div>
    <div @click="logout()" class="huge ui blue button" style="margin-top: 50px; margin-right: 50px">
      Logout
    </div>
    <div @click="showDeleteAccountModal()" class="huge ui blue button" style="margin-top: 50px">
      Delete account
    </div>
    <div class="ui mini modal" ref="deleteAccountModal">
      <div class="header">Are you sure?</div>
      <div class="content">
        You won't be able to revert this.
      </div>
      <div class="actions">
        <div class="ui green approve button">Yes</div>
        <div class="ui red cancel button">No</div>
      </div>
    </div>
  </div>
  <div v-show="!currentUser" style="margin-top: 30px">
    <div class="ui huge header">
      Welcome to the FINKI blog site!
      <div class="sub header" style="margin-top: 20px">To proceed please <b>Login</b> if you already have an account or click <b>Register</b> to join the community!</div>
    </div>
    <div @click="showLoginForm()" class="huge ui blue button" style="margin-top: 50px; margin-right: 50px">
      Login
    </div>
    <div class="ui modal" ref="loginModal">
      <i class="close icon"></i>
      <div class="header">
        Login
      </div>
      <div class="content">
        <div class="ui login form">
          <div class="ui error message"></div>
          <input type="hidden" name="correctUsername" v-model="correctUsername" />
          <input type="hidden" name="correctPasswordHash" v-model="correctPasswordHash" />
          <div class="required field">
            <label>Username</label>
            <input type="text" name="username" v-model="username" placeholder="Enter username here..." />
          </div>
          <div class="required field">
            <label>Password</label>
            <input type="password" name="password" v-model="password" placeholder="Enter password here..." />
          </div>
          <div @click="login()" class="ui blue submit button field">
            Login
          </div>
          <div @click="clearLoginFields()" class="ui clear button field" style="margin-left: 15px">
            Clear
          </div>
        </div>
      </div>
    </div>
    <div @click="showRegisterForm()" class="huge ui blue button" style="margin-top: 50px">
      Register
    </div>
    <div class="ui modal" ref="registerModal">
      <i class="close icon"></i>
      <div class="header">
        Register
      </div>
      <div class="content">
        <form class="ui register form" enctype="multipart/form-data">
          <div class="ui error message"></div>
          <div class="required field">
            <label>Username</label>
            <input type="text" name="usernameRegistration" v-model="usernameRegistration" placeholder="Enter a username here..." />
          </div>
          <div class="required field">
            <label>Password</label>
            <input type="password" name="passwordRegistration" v-model="passwordRegistration" placeholder="Enter a password here..." />
          </div>
          <div class="two fields">
            <div class="field">
              <label>First name</label>
              <input type="text" name="firstName" v-model="firstName" placeholder="Enter your first name here..." />
            </div>
            <div class="field">
              <label>Last name</label>
              <input type="text" name="lastName" v-model="lastName" placeholder="Enter your last name here..." />
            </div>
          </div>
          <div class="field">
            <label>Email</label>
            <input type="email" name="email" v-model="email" placeholder="Enter your email address here..." />
          </div>
          <div class="field">
            <label>Profile picture</label>
            <input type="hidden" name="pictureName" v-model="pictureName" />
            <input type="file" name="pictureUpload" @change="filesChange($event.target.files)" accept="image/*" />
          </div>
          <div @click="register()" class="ui blue submit button field">
            Register
          </div>
          <div @click="clearRegisterFields()" class="ui button field" style="margin-left: 15px">
            Clear
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="ui footer segments">
    <div class="ui vertical segment">
      <div>
        Icons made by <a href="https://www.flaticon.com/authors/monkik" title="monkik">monkik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/"
          title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a>
      </div>
      <div>
        Icons made by <a href="https://www.flaticon.com/authors/smashicons" title="Smashicons">Smashicons</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/"
          title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a>
      </div>
    </div>
    <div class="ui vertical segment">
      <div>
        <span class="text">Faculty of Computer Science and Engineering - Skopje 2018</span>
      </div>
    </div>
  </div>
</div>
</template>

<script lang="ts">
import Vue from 'vue'
import $ from 'jquery'
import toastr from 'toastr'
import 'toastr/build/toastr.css'
import sjcl from 'sjcl'

$(document).ready(function() {
  $('.ui.login.form')
    .form({
      fields: {
        username: {
          identifier: 'username',
          rules: [{
            type: 'empty',
            prompt: 'Please enter your username to continue.'
          }]
        },
        password: {
          identifier: 'password',
          rules: [{
              type: 'empty',
              prompt: 'Please enter your password to continue.'
            },
            {
              type: 'match[correctPasswordHash]',
              prompt: 'Sorry, the password is not correct.'
            }
          ]
        },
        correctUsername: {
          identifier: 'correctUsername',
          rules: [{
            type: 'empty',
            prompt: 'Sorry, the username is not correct.'
          }]
        }
      },
      onSuccess: function(event) {
        event.preventDefault()
      }
    })
  $('.ui.register.form')
    .form({
      fields: {
        usernameRegistration: {
          identifier: 'usernameRegistration',
          rules: [{
              type: 'empty',
              prompt: 'Please enter a username to continue.'
            },
            {
              type: 'maxLength[30]',
              prompt: 'Sorry, that username is too long.'
            }
          ]
        },
        passwordRegistration: {
          identifier: 'passwordRegistration',
          rules: [{
              type: 'empty',
              prompt: 'Please enter a password to continue.'
            },
            {
              type: 'minLength[8]',
              prompt: 'Your password must be at least 8 characters long.'
            },
            {
              type: 'regExp',
              value: /^.*[A-Z].*$/,
              prompt: 'Your password must contain at least one uppercase letter.'
            },
            {
              type: 'regExp',
              value: /^.*[a-z].*$/,
              prompt: 'Your password must contain at least one lowercase letter.'
            },
            {
              type: 'regExp',
              value: /^.*[0-9].*$/,
              prompt: 'Your password must contain at least one digit.'
            },
            {
              type: 'regExp',
              value: /^.*\W.*$/,
              prompt: 'Your password must contain at least one special character.'
            },
          ]
        },
        firstName: {
          identifier: 'firstName',
          rules: [{
            type: 'maxLength[20]',
            prompt: 'Sorry, that name is too long.'
          }]
        },
        lastName: {
          identifier: 'lastName',
          rules: [{
            type: 'maxLength[30]',
            prompt: 'Sorry, that name is too long.'
          }]
        },
        email: {
          identifier: 'email',
          optional: true,
          rules: [{
            type: 'email',
            prompt: 'Please enter a valid email address.'
          }]
        },
        pictureName: {
          identifier: 'pictureName',
          rules: [{
            type: 'maxLength[100]',
            prompt: 'Sorry, that filename is too long.'
          }]
        }

      },
      onSuccess: function(event) {
        event.preventDefault()
      }
    })
  $('.no-sticky-animation').on('mousedown', function(event) {
    event.preventDefault()
  })
})

export default Vue.extend({
  name: 'Home',
  props: {},
  components: {},
  data() {
    return {
      username: null,
      password: null,
      usernameRegistration: null,
      passwordRegistration: null,
      firstName: null,
      lastName: null,
      email: null,
      pictureName: null,
      image: null,
      correctUsername: null,
      correctPasswordHash: null,
      currentUserName: ''
    }
  },
  computed: {
    currentUser() {
      var user = this.$store.state.currentUser
      if (user)
        this.currentUserName = user.username
      return user
    },
    validLogin() {
      let vm = this
      if (vm.$store.state.allUsers[this.username]) {
        vm.correctUsername = vm.$store.state.allUsers[this.username].username
        vm.correctPasswordHash = vm.$store.state.allUsers[this.username].password
      } else vm.getUserByUsername()
      return this.username && this.password && vm.correctUsername === vm.username && vm.correctPasswordHash === vm.passwordHash(vm.password)
    },
    validRegistration() {
      var valid = this.usernameRegistration && this.passwordRegistration
      if (this.firstName) {
        valid = valid && this.firstName.length <= 20
      }
      if (this.lastName) {
        valid = valid && this.lastName.length <= 30
      }
      var re = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/
      if (this.email) {
        valid = valid && re.test(this.email)
      }
      if (this.pictureName) {
        valid = valid && this.pictureName.length <= 100
      }
      return valid
    }
  },
  filters: {},
  methods: {
    getUserByUsername() {
      let vm = this
      fetch(`https://${process.env.VUE_APP_HOST}${process.env.BASE_URL}${process.env.VUE_APP_API}/users/getUserByUsername.php?username=${this.username}`, {
        credentials: 'include'
      }).then(response => {
        if (response.ok) {
          return response.json()
        } else {
          return Promise.reject(new Error('Failed getting user'))
        }
      }, reason => {
        toastr.options.preventDuplicates = true
        toastr.error('Unable to fetch user account details. Try reloading', 'ERROR')
        return Promise.reject(reason)
      }).then(data => {
        if (data) {
          vm.correctUsername = data.username
          vm.correctPasswordHash = data.password
        }
        return data
      })
    },
    passwordHash(password) {
      var hasher = new sjcl.hash.sha256()
      hasher.update(password)
      var result = sjcl.codec.hex.fromBits(hasher.finalize())
      return result
    },
    showLoginForm() {
      $(this.$refs.loginModal).modal('hide')
      $(this.$refs.registerModal).modal('hide')
      $(this.$refs.deleteAccountModal).modal('hide')
      $(this.$refs.loginModal).modal({
        inline: true
      }).modal('show')
    },
    showRegisterForm() {
      $(this.$refs.loginModal).modal('hide')
      $(this.$refs.registerModal).modal('hide')
      $(this.$refs.deleteAccountModal).modal('hide')
      $(this.$refs.registerModal).modal({
        inline: true
      }).modal('show')
    },
    showDeleteAccountModal() {
      $(this.$refs.loginModal).modal('hide')
      $(this.$refs.registerModal).modal('hide')
      $(this.$refs.deleteAccountModal).modal('hide')
      let vm = this
      $(this.$refs.deleteAccountModal).modal({
        inline: true,
        closable: false,
        onApprove: function() {
          vm.deleteUser()
        }
      }).modal('show')
    },
    login() {
      if (this.validLogin) {
        this.$store.dispatch('setCurrentUser', this.username) // Final command before complete login.
        $(this.$refs.loginModal).modal('hide')
        this.clearLoginFields()
        toastr.options.preventDuplicates = true
        toastr.success('You are now logged in!', 'SUCCESS')
      }
    },
    logout() {
      this.$store.commit('SET_CURRENT_USER', null)
      toastr.options.preventDuplicates = true
      toastr.success('Logout successful. Bye bye!', 'SUCCESS')
    },
    deleteUser() {
      let vm = this
      fetch(`https://${process.env.VUE_APP_HOST}${process.env.BASE_URL}${process.env.VUE_APP_API}/users/deleteUser.php?username=${this.currentUser.username}`, {
        method: 'DELETE',
        credentials: 'include'
      }).then((response) => {
        if (response.ok) {
          return response.json()
        } else {
          return Promise.reject(new Error('Failed deleting user account.'))
        }
      }, (reason) => {
        toastr.options.preventDuplicates = true
        toastr.error('Failed deleting user account.', 'ERROR')
        return Promise.reject(reason)
      }).then((data) => {
        if (data) {
          toastr.options.preventDuplicates = true
          toastr.success('Goodbye ' + vm.currentUser.username + '!', 'SUCCESS')
          this.$store.commit('SET_CURRENT_USER', null)
          $(this.$refs.deleteAccountModal).modal('hide')
        }
        return data
      })
    },
    clearLoginFields() {
      this.username = null
      this.password = null
      this.correctUsername = null
      this.correctPasswordHash = null
    },
    filesChange(filesList) {
      var imageFile = filesList[0]
      this.image = imageFile
      this.pictureName = imageFile.name
    },
    register() {
      var vm = this
      if (this.validRegistration) {
        var user = {
          username: vm.usernameRegistration,
          password: vm.passwordHash(vm.passwordRegistration),
          firstName: vm.firstName,
          lastName: vm.lastName,
          email: vm.email,
          picture: vm.pictureName
        }
        fetch(`https://${process.env.VUE_APP_HOST}${process.env.BASE_URL}${process.env.VUE_APP_API}/users/addUser.php`, {
          method: 'POST',
          body: JSON.stringify(user),
          credentials: 'include'
        }).then(response => {
          if (response.ok) {
            return response.json()
          } else {
            return Promise.reject(new Error('Failed adding new user'))
          }
        }, reason => {
          toastr.options.preventDuplicates = true
          toastr.error('Failed adding new user.', 'ERROR')
          return Promise.reject(reason)
        }).then(data => {
          if (data) {
            toastr.options.preventDuplicates = true
            toastr.success('User account created.', 'SUCCESS')
          }
          return data
        })
        let formData = new FormData()
        if (this.image) {
          formData.append('fileToUpload', this.image)
          fetch(`https://${process.env.VUE_APP_HOST}${process.env.BASE_URL}${process.env.VUE_APP_API}/users/uploadPicture.php`, {
            method: 'POST',
            body: formData,
            credentials: 'include'
          }).then(response => {
            if (response.ok) {
              return response.json()
            } else {
              return Promise.reject(new Error('Failed uploading picture.'))
            }
          }, reason => {
            toastr.options.preventDuplicates = true
            toastr.error('Failed uploading picture.', 'ERROR')
            return Promise.reject(reason)
          }).then(data => {
            if (data) {
              toastr.options.preventDuplicates = true
              toastr.success('Profile picture uploaded.', 'SUCCESS')
            }
            return data
          })
        }
        $(this.$refs.registerModal).modal('hide')
        this.clearRegisterFields()
      }
    },
    clearRegisterFields() {
      this.usernameRegistration = null
      this.passwordRegistration = null
      this.firstName = null
      this.lastName = null
      this.email = null
      this.pictureName = null
    }
  },
  beforeDestroy: function() {
    $(this.$refs.loginModal).modal('hide')
    $(this.$refs.registerModal).modal('hide')
    $(this.$refs.deleteAccountModal).modal('hide')
  }
})
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.home {
  height: 100%;
}

@media (min-height: 320px) {
  .footer {
    position: relative !important;
    top: 16em;
    width: 100%;
  }
}
</style>
