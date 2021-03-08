<template>
  <b-tag
    v-if="isActive"
    :type="type(item.type)"
    closable
    aria-close-label="Close tag"
    class="is-account"
    @close="snsListSelectAccountCheck"
  >
    <b-icon
      :icon="icon(item.type)"
      size="is-small"
    />
    @{{ item.account_name }}
  </b-tag>
</template>

<script>
export default {
  props: {
    item: {
      type: Object,
      default () {
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
          },
        }
      }
    }
  },
  data(){
    return {
      isActive: true
    }
  },
  methods: {
    type(type){
      return 'is-' + type
    },
    icon(type){
      return 'icon-font-' + type
    },
    snsListSelectAccountCheck() {
      this.$store.commit('snsAccountListRemove', this.item)
    }
  }
}
</script>
