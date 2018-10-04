import Vue from 'vue'
import Vuex from 'vuex'
import toastr from 'toastr'
import 'toastr/build/toastr.css'

// Mutations
const SET_ALL_USERS = 'SET_ALL_USERS'
const SET_CURRENT_USER = 'SET_CURRENT_USER'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    allUsers: {},
    currentUser: null
  },
  mutations: {
    [SET_ALL_USERS] (state, users) {
      for (var user in users) { Vue.set(state.allUsers, users[user].username, users[user]) }
    },
    [SET_CURRENT_USER] (state, user) {
      state.currentUser = user
    }
  },
  actions: {
    getAllUsers ({ commit, dispatch, state }) {
      return fetch(`https://${process.env.VUE_APP_HOST}${process.env.BASE_URL}${process.env.VUE_APP_API}/users/getAllUsers.php`, {
        credentials: 'include'
      }).then(response => {
        if (response.ok) {
          return response.json()
        } else {
          return Promise.reject(new Error('Failed getting users'))
        }
      }, reason => {
        toastr.options.preventDuplicates = true
        toastr.error('Unable to fetch users. Try reloading', 'ERROR')
        return Promise.reject(reason)
      }).then(data => {
        if (data) { commit(SET_ALL_USERS, data) }
        return data
      })
    },
    setCurrentUser ({ commit, dispatch, state }, username) {
      if (state.allUsers[username]) { commit(SET_CURRENT_USER, state.allUsers[username]) } else {
        fetch(`https://${process.env.VUE_APP_HOST}${process.env.BASE_URL}${process.env.VUE_APP_API}/users/getUserByUsername.php?username=${username}`, {
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
            commit(SET_CURRENT_USER, data)
          }
          return data
        })
      }
    }
  }
})
