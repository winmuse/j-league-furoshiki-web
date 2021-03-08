<template>
  <div class="sns-account-list">
    <SnsSettingListItem v-for="(item,key) of list" :key="key" :item="item" />
  </div>
</template>

<script>
export default {
  props: {
    list: {
      type: Array,
      default () {
        return {
          type: {
            type: String,
            required: true
          },
          id: {
            type: Number,
            required: true
          },
          icon: {
            type: String,
            required: false
          },
          account_name: {
            type: String,
            required: false
          },
          active: {
            type: Boolean,
            required: false,
            default: true
          },
          checkbox: {
            type: Boolean,
            required: false,
            default: false
          },
          delete: {
            type: Boolean,
            required: false,
            default: true
          }
        }
      }
    }
  }
}
</script>
