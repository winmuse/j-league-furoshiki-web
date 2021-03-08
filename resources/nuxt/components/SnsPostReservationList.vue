<template>
  <div class="sns-post-reservation-list">
    <snsPostReservationListItem
      v-for="(item, key) of list"
      :key="key"
      :type="type"
      :item="item"
    />
  </div>
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      required: true
    },
    list: {
      type: Array,
      default() {
        return {
          date: {
            type: String,
            required: true
          },
          sns: {
            type: Array,
            required: true
          },
          text: {
            type: String,
            required: true
          },
          media: {
            type: Object,
            Boolean,
            required: true,
            default() {
              return {
                type: {
                  type: String,
                  required: false
                },
                figure: {
                  type: String,
                  required: false
                }
              }
            }
          }
        }
      }
    }
  }
}
</script>
