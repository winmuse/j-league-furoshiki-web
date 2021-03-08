<template>
  <div>
    <div class="container is-padding-top has-text-centered">
      <p>
        機能・要素確認中....
      </p>
    </div>
    <div class="navbar is-fixed-bottom">
      <div class="container is-padding">
        <b-button
          tag="router-link"
          class="is-primary is-medium"
          to="/material-index"
          expanded
          rounded
        >
          写真一覧を表示
        </b-button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  layout: 'hasmenu',
}
</script>
