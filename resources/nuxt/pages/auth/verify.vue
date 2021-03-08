<template>
  <div class="login">
    <div class="login-inner">
      <div class="login-sitelogo">
        <img src="/images/furoshiki-logo.png" alt="">
      </div>
      <form
        ref="form"
      >
        <div class="block is-large">
          <b-field
            :type="errors.has('pincode') ? 'is-danger': 'is-nega'"
            :message="errors.first('pincode')"
          >
            <b-input
              v-model="pincode"
              name="pincode"
              v-validate="'required|digits:4'"
              :placeholder="$t('auth.verify.placeholder')"
            />
          </b-field>
        </div>
        <div class="container">
          <b-button
            class="is-primary is-medium"
            expanded rounded
            :disabled="disabled"
            @click="validateBeforeSubmit"
          >
            {{ $t('auth.verify.submit') }}
          </b-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  layout: 'fullscreen',
  middleware: 'notAuthed',
  data() {
    return {
      access_token: null,
      pincode: null,
      disabled: false
    }
  },
  created() {
    if (! this.$route.query.token) {
      this.$router.push('/auth/login')
    }
    this.access_token = this.$route.query.token
  },
  methods: {
    showError(field, message) {
      this.errors.remove(field)
      this.errors.add({
        field: field,
        msg: message
      })
    },
    async validateBeforeSubmit() {
      this.disabled = true
      const result = await this.$validator.validateAll()
      if (result) {
        try {
          const { data } = await this.$axios.post(
            '/verify',
            {
              access_token: this.access_token,
              code: this.pincode
            }
          )
          if (data && data.token) {
            const vcode = `${this.access_token}|${this.pincode}`
            this.$store.commit('setVerifyCode', vcode)
            this.$store.commit('setToken', data.token)
            localStorage.setItem('$furoshikiToken', data.token)
            localStorage.setItem('$furoshikiVcode', vcode)
            const auth = {
              user: data.user,
              loggedIn: true
            }
            this.$store.commit('setAuth', auth)
            localStorage.setItem('$furoshikiAuth', JSON.stringify(auth))
            this.$router.push(this.localePath('/material-index'))
          }
        } catch (e) {
          const response = e.response.data
          const error = response.error

          switch (response.type) {
            case 'wrong request' : // validation error
              this.showError('pincode',this.$t('auth.verify.error_wrong'))
              break;
            case 'invalid request' : // validation error
              this.showError('pincode',this.$t('auth.verify.error_invalid'))
              break;
            case 'pin expired' : // validation error
              this.showError('pincode',this.$t('auth.verify.error_expired'))
              break;
          }
        }
      }
      this.disabled = false
    }
  }
}
</script>
