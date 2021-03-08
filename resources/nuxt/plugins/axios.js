export default function ({app, $axios, store}) {
  $axios.onRequest(config => {
    try {
      config.headers.common['Content-Type'] = 'application/json'
      const token = store.state.token
      if (token) {
        config.headers.common.Authorization = 'Bearer ' + token
      }
    } catch (e) {
      console.log(e)
    }
  })
}
