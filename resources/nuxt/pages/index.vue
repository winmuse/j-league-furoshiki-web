<template>
  <div class="container is-padding">
    <div class="login-sitelogo">
      <div style="align-self: center">
        <img src="/images/furoshiki-logo.png" alt="">
      </div>
    </div>
  </div>
</template>

<style scope>
  .login-sitelogo {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }
</style>

<script>
export default {
  layout: 'hasmenu',
  middleware: 'authed',
  data() {
    return {
    }
  },
  head() {
    return {
      title: this.title
    }
  },
  mounted() {
    this.$router.push('/auth/login')
  }
}
</script>
