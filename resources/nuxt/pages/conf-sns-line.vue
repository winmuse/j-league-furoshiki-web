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
      <div class="has-text-centered">
        <p>{{ $t('set_line.p1')}}</p>
        <p>{{ $t('set_line.p2')}}</p>
      </div>
      <ul>
        <li>1. {{ $t('set_line.l1')}}
            <br>https://manager.line.biz/
        </li>
        <li>2. {{ $t('set_line.l2')}}</li>
        <li>3. {{ $t('set_line.l3')}}</li>
        <li>4. {{ $t('set_line.l4')}}</li>
        <li>5. {{ $t('set_line.l5')}}</li>
        <li>6. {{ $t('set_line.l6')}}</li>
        <li>7. {{ $t('set_line.l7')}}</li>
      </ul>

      <b-field
        :type="{'is-danger': errors.has('url')}"
        :message="$t('set_line.msg.require')"
      >
        <b-input
          placeholder="URL"
          v-model="url"
          name="url"
          v-validate="{url: {require_protocol: true }}"
        />
      </b-field>
    </div>
    <div class="navbar is-fixed-bottom">
      <div class="container is-padding">
        <b-button
          class="is-line is-medium"
          @click="saveLineUrl"
          expanded rounded
          :disabled="disabled"
        >
          {{ $t('set_line.submit') }}
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
      url: '',
      activeNotify: false,
      notifyTypes: {
        success: 'is-success',
        error: 'is-danger'
      },
      messages: {
        success: this.$t('set_line.msg.success'),
        error: this.$t('set_line.msg.error')
      },
      notifyType: '',
      notification: ''
    }
  },
  mounted() {
    this.$axios.$get('/line_credential')
      .then(res => {
          const line = res.line_credential
          console.log(res)
          if(line) {
              this.url = line.auth_url
              this.showAlert(this.$t('set_line.msg.alert'), 'danger')
          }
      })
      .catch(err => {
        this.showAlert(this.$t('set_line.msg.alert'), 'danger')
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
    saveLineUrl() {
      this.disabled = true
      this.activeNotify = false
      const result = this.$validator.validateAll()
      if(result) {
        this.$axios.post(
          '/save-line-url',
          { url : this.url }
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
