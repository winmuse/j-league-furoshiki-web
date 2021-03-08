export default function ({store, redirect}) {
  const jwtDecode = require('jwt-decode')
  try {
    const token = store.state.token
    let not_authed = !token
    if(!not_authed) {
      const decoded = jwtDecode(token)
      const now = new Date().getTime()
      not_authed = decoded.exp * 1000 < now
    }

    if (not_authed) {
      store.commit('setToken', null)
      store.commit('setAuth', null)
      return redirect('/auth/login')
    }
  } catch (e) {
    console.log(e)
  }
}
