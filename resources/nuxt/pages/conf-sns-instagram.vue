<template>
  <div>
    <div class="container is-padding-top">
      <b-notification
        :active.sync="activeNotify"
        :type="notifyType"
        aria-close-label="Close notification"
        role="alert"
      >
        {{ notification }}
      </b-notification>

      <b-field
        label="Instagram ID"
        :type="{'is-danger': errors.has('email')}"
        :message="$t('set_ig.msg.require')"
      >
        <b-input
          placeholder="Instagram ID"
          v-model="email"
          name="email"
          v-validate="{required:true}"
        />
      </b-field>
      <b-field
        label="PASSWORD"
        :type="{'is-danger': errors.has('password')}"
        :message="$t('set_ig.msg.require')"
      >
        <b-input
          placeholder="パスワード"
          v-model="password"
          name="password"
          v-validate="{required: true}"
        />
      </b-field>
    </div>
    <div class="navbar is-fixed-bottom">
      <div class="container is-padding">
        <b-button
          class="is-instagam is-medium"
          @click="saveInstagramAccount"
          expanded rounded
          :disabled="disabled"
        >
          {{ $t('set_ig.submit') }}
        </b-button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  layout: 'hasmenu',
  data() {
    return {
      disabled: false,
      email: '',
      password: '',
      activeNotify: false,
      notifyTypes: {
        success: 'is-success',
        error: 'is-danger'
      },
      messages: {
        success: this.$t('set_ig.msg.success'),
        error: this.$t('set_ig.msg.error')
      },
      notifyType: '',
      notification: ''
    }
  },
  mounted() {
    this.$axios.$get('/ig_credential')
      .then(res => {
          const line = res.ig_credential
          console.log(res)
          if(line) {
              this.email = line.account_name
              this.password = line.ig_password
          }
      })
      .catch(err => {
        this.showAlert(this.$t('set_ig.msg.db_error'), 'danger')
      })
  },
  methods: {
    hideAlert() {
      this.activeNotify = false
    },
    showAlert(msg, alertType) {
      this.notifyType = 'is-' + alertType
      this.notification = msg
      this.activeNotify = true
    },
    saveInstagramAccount() {
      this.disabled = true
      this.activeNotify = false
      const result = this.$validator.validateAll()
      const emailErr = this.email === ''
      if(emailErr) {
        this.showAlert(this.$t('set_ig.msg.email_error'), 'danger')
        this.disabled = false
        return
      }

      if(this.password === '') {
        this.showAlert(this.$t('set_ig.msg.password_error'), 'danger')
        this.disabled = false
        return
      }
      if(result) {
        this.$axios.post(
          '/save-ig-account',
          { email : this.email, password: this.password }
        )
          .then(res => {
            this.showAlert(this.messages.success, 'success')
          })
          .catch(err => {
            this.showAlert(this.messages.error, 'danger')
          })
      } else {
        this.showAlert(this.messages.error, 'danger')
      }
      this.disabled = false
    }
  }
}
</script>
