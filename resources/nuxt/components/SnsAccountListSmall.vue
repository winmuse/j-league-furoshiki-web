<template>
  <div class="sns-account-list-small">
    <span
      v-for="(item, key) of list"
      :key="key"
      class="sns-account-list-small-item"
    >
      <SnsAccountListSmallItem :item="item" />
    </span>
  </div>
</template>

<script>
export default {
  props: {
    list: {
      type: Array,
      default() {
        return {
          id: {
            type: Number,
            required: true
          },
          type: {
            type: String,
            required: true
          },
          account_name: {
            type: String,
            required: false
          }
        }
      }
    }
  }
}
</script>
