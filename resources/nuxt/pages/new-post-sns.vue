<template>
  <div class="container is-padding-top">
    <b-notification
      :active.sync="isErrorActive"
      aria-close-label="Close notification"
      type="is-danger"
    >
      {{ $t('post_sns.error') }}
    </b-notification>

    <SnsAccountList :list="SnsAccountList" />

    <div v-if="SnsAccountList.length===0">
      <h3>{{ $t('post_sns.no_sns') }}</h3>
    </div>

    <div
      v-else
      class="navbar is-fixed-bottom"
    >
      <div class="container is-padding">
        <b-button
          tag="router-link"
          class="is-primary is-medium"
          :to="$store.state.prevPage"
          expanded
          rounded
        >
          {{ $t('post_sns.submit') }}
        </b-button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  layout: 'back',
  middleware: 'authed',
  data() {
    return {
      isErrorActive: false,
      SnsAccountList: [],
      previousPage: this.$store.state.prevPage
    }
  },
  asyncData({ store }) {
    // store.commit('getTitle', this.$t('post_sns.title'))
  },
  mounted() {
    // this.$store.commit('setPreviousPage', this.localePath('/new-post'))
    const storeSnsAccountList = this.$store.state.snsAccountList

    this.$axios.$get('/credential/all')
      .then(res => {
        if (storeSnsAccountList.length > 0) {
          this.SnsAccountList = res.map(e => {
            e.selected = false
            storeSnsAccountList.some((v, i) => {
              if (e.type === v.type && e.id === v.id) e.selected = true
            })
            return e
          })
        } else {
          this.SnsAccountList = res
        }
      })
      .catch(err => {
        console.log(err)
        this.isErrorActive = true
      })
  }
}
</script>
