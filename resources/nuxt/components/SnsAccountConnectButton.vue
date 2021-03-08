<template>
  <div>
    <b-button
      :icon-left="'icon-font-' + account.type"
      class="is-icon-left-fix"
      :class="'is-' + account.type"
      expanded
      @click="redirectLogin"
    >
      {{ account.text }}
    </b-button>
  </div>
</template>

<script>
export default {
  props: {
    account: {
      type: Object,
      default () {
        return {
          type: {
            type: String,
            required: true
          },
          icon: {
            type: String,
            required: true
          },
          account_name: {
            type: String,
            required: true
          },
          text: {
            type: String,
            required: true
          }
        }
      }
    }
  },
  data() {
    return {
      isModalActive: false
    }
  },
  methods: {
    redirectLogin() {
      const vcode = this.$store.state.verifycode

      window.location=`/sns/login/${this.account.type}?vcode=${vcode}`
    }
  }
}
</script>
