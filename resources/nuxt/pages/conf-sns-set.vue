<template>
  <div>
    <b-notification
      :active.sync="isErrorActive"
      aria-close-label="Close notification"
      type="is-danger"
    >
      {{ $t('set_sns.msg.db_error') }}
    </b-notification>
    <div class="container is-padding-top">
      <div class="block is-large">
        <SnsSettingList :list="CredentialList" />
      </div>
      <div class="block">
        <SnsAccountConnectButton :account="SnsConnectAccountList.twitter" />
      </div>
      <div class="block">
        <SnsAccountConnectButton :account="SnsConnectAccountList.facebook" />
      </div>
      <div class="block">
        <b-button
          icon-left="icon-font-instagram"
          class="is-icon-left-fix is-instagram"
          expanded
          tag="router-link"
          :to="localePath('/conf-sns-instagram')"
        >
          {{ $t('set_ig.submit') }}
        </b-button>
      </div>
<!--      <div class="block">-->
<!--        <SnsAccountConnectButton :account="SnsConnectAccountList.instagram" />-->
<!--      </div>-->
      <!--      <div class="block">-->
      <!--        <b-button-->
      <!--          icon-left="icon-font-line"-->
      <!--          class="is-icon-left-fix is-line"-->
      <!--          expanded-->
      <!--          tag="router-link"-->
      <!--          :to="localePath('/conf-sns-line')"-->
      <!--        >-->
      <!--          {{ $t('set_sns.submit') }}-->
      <!--        </b-button>-->
      <!--      </div>-->
    </div>
  </div>
</template>

<script>
export default {
  layout: 'hasmenu',
  middleware: 'authed',
  data() {
    return {
      isErrorActive: false,
      CredentialList: [],
      SnsConnectAccountList: {
        twitter: {
          type: 'twitter',
          text: this.$t('set_sns.caption.twitter')
        },
        facebook: {
          type: 'facebook',
          text: this.$t('set_sns.caption.facebook')
        },
        instagram: {
          type: 'instagram',
          text: this.$t('set_sns.caption.instagram')
        }
      }
    }
  },
  mounted() {
    this.$axios.$get('/credential/all')
      .then(res => {
        this.CredentialList = res.filter(e => {
          e.checkbox = false
          e.delete = true
          return e.active === true
        })
      })
      .catch(err => {
        console.log(err)
        this.isErrorActive = true
      })
  }
}
</script>
