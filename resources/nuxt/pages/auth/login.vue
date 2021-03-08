<template>
  <div class="login">
    <div class="login-inner">
      <div class="login-sitelogo">
        <img src="/images/furoshiki-logo.png" alt="">
      </div>
      <form ref="form">
        <div class="block is-large">
          <b-field
            :type="errors.has('identity') ? 'is-danger': 'is-nega'"
            :message="errors.first('identity')"
          >
            <b-input
              :placeholder="$t('auth.login.ID.placeholder')"
              v-model="identity"
              name="identity"
              v-validate="'required|email'"
            />
          </b-field>
          <b-field
            :type="errors.has('password') ? 'is-danger': 'is-nega'"
            :message="errors.first('password')"
          >
            <b-input
              :placeholder="$t('auth.login.password.placeholder')"
              type="password"
              v-model="password"
              name="password"
              v-validate="'required|alpha_num|min:8'"
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
              {{ $t('auth.login.submit_caption') }}
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
      disabled: false,
      identity: null,
      password: null,
    }
  },
  methods: {
    showError(field, message) {
      this.errors.remove(field)
      this.errors.add({
        field: field,
        msg: message
      })
    },
    validateBeforeSubmit: async function () {
      this.disabled = true
      const result = await this.$validator.validateAll()
      if (result) {
        try {
          const {data} = await this.$axios.post(
            '/login',
            {
              email: this.identity,
              password: this.password
            }
          )

          if (data.skip_sms) {
            // SMS認証を行わない
            this.skipSmsVerification(data)
          } else {
            // SMS認証を行う
            const access_token = data.access_token
            // go to SMS verify page
            if(data.id === 1) {
              this.$router.push(this.localePath('/material-index'))
            }
            this.$router.push(this.localePath(`/auth/verify?token=${access_token}`))
          }
        } catch (e) {
          const response = e.response.data
          const error = response.error

          switch (response.type) {
            case 'request' : // validation error
              if (error.hasOwnProperty('email')) {
                this.showError('email', this.$t('auth.login.ID.error'))
              }
              if (error.hasOwnProperty('password')) {
                this.showError('password', this.$t('auth.login.password.error'))
              }
              break;
            case 'login' : // wrong credential
                this.$toast.error(this.$t('auth.login.alert.credential'))
              break;
            case 'skip sms' : // server error : cant skip SMS
            case 'jwt' : // server error : cant make JWT token
                this.$toast.error(this.$t('auth.login.alert.jwt'))
              break;
            case 'twilio' : // server error : cant send SMS
                this.$toast.error(this.$t('auth.login.alert.twilio'))
              break;
          }
        }
      }
      this.disabled = false
    },
    skipSmsVerification(data) {
      if (data && data.token) {
        const vcode = `${data.access_token}|${data.pincode}`
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
    }
  }
}
</script>
