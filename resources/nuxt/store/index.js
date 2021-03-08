const cookieparser = process.server ? require('cookieparser') : undefined
const dayjs = require('dayjs')

export const state = () => ({
  pageTitle: null,
  snsAccountList: [],
  snsTagList: [],
  snsDefaultTagList: [],
  snsContent: '',
  snsComments: [],
  snsMediaList: [],
  mediaList: [],
  snsPulishEstimate: dayjs().format('YYYY-MM-DDTHH:mm'),
  token: null,
  auth: {
    user: null,
    loggedIn: false
  },
  verifycode: null,
  prevPage: '',
  optKeyword: '',
  optDate: '',
  optPlayer: 0
})

export const mutations = {
  logout(state){
    state.token = ''
    state.auth = null
  },
  setPreviousPage(state, value) {
    state.prevPage = value
  },
  setToken(state, token) {
    state.token = token
  },
  setAuth(state, auth) {
    state.auth = auth
  },
  setVerifyCode(state, value) {
    state.verifycode = value
  },
  getTitle(state, value) {
    state.pageTitle = value
  },
  setSnsContent(state, value) {
    state.snsContent = value
  },
  setSnsComment(state, value) {
    state.snsComment = value
  },
  setSnsPublishEstimate(state, value) {
    state.snsPulishEstimate = value
  },
  snsTagListClear(state) {
    state.snsTagList = []
  },
  snsTagListAdd(state, value) {
    state.snsTagList.push(value)
  },
  snsTagListRemove(state, value) {
    state.snsTagList.some(function (v, i) {
      if (v === value) state.snsTagList.splice(i, 1)
    })
  },
  snsCommentsClear(state) {
    state.snsComments = []
  },
  snsCommentsAdd(state, value) {
    state.snsComments.push(value)
  },
  snsDefaultTagListAdd(state, value) {
    state.snsDefaultTagList.push(value)
  },
  snsAccountListClear(state) {
    state.snsAccountList = []
  },
  snsAccountListAdd(state, value) {
    state.snsAccountList.push(value)
  },
  snsAccountListRemove(state, value) {
    state.snsAccountList.some(function (v, i) {
      if (v.type === value.type && v.id === value.id) {
        state.snsAccountList.splice(i, 1)
      }
    })
  },
  snsMediaListClear(state) {
    state.snsMediaList = []
  },
  snsMediaListAdd(state, value) {
    state.snsMediaList.push(value)
  },
  snsMediaListRemove(state, value) {
    state.snsMediaList.some(function (v, i) {
      if (v.id === value) state.snsMediaList.splice(i, 1)
    })
  },
  setOptKeyword(state, value) {
    state.optKeyword = value
  },
  setOptDate(state, value) {
    state.optDate = value
  },
  setOptPlayer(state, value) {
    state.optPlayer = value
  },
}
export const actions = {
  nuxtServerInit({commit}) {
    /**
     * https://nuxtjs.org/guide/vuex-store/#the-nuxtserverinit-action
     * TODO
     */
  },
  nuxtClientInit({commit}, context) {
    /**
     * https://qiita.com/potato4d/items/cc5d8ea24949e86f8a5b
     * https://github.com/potato4d/nuxt-client-init-module
     */
    let token = null
    let auth = null
    let vcode = null
    if (localStorage.getItem('$furoshikiToken') && localStorage.getItem('$furoshikiAuth')) {
      try {
        token = localStorage.getItem('$furoshikiToken')
        vcode = localStorage.getItem('$furoshikiVcode')
        auth = JSON.parse(localStorage.getItem('$furoshikiAuth'))
      } catch (e) {
        console.log(e)
        localStorage.removeItem('$furoshikiToken')
        localStorage.removeItem('$furoshikiAuth')
        localStorage.removeItem('$furoshikiVcode')
        token = null
        auth = null
        vcode = null
      }
    } else {
      // no valid session
    }
    commit('setToken', token)
    commit('setAuth', auth)
    commit('setVerifyCode', vcode)
  }
}
