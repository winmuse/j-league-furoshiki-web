export default function ({store, redirect}) {
  const jwtDecode = require('jwt-decode')
  const token = store.state.token
  let not_authed = !token
  if(!not_authed) {
    const decoded = jwtDecode(token)
    const now = new Date().getTime()
    not_authed = decoded.exp * 1000 < now
  }

  try {
    if (!not_authed) {
        return redirect('/material-index')
    }
  } catch (e) {
    console.log(e)
  }
}
