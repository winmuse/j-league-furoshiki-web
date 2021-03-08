<template>
  <div class="media-list">
    <MediaListItem v-for="item in list" :key="item.value" :item="item"/>
  </div>
</template>

<script>
export default {
  props: {
    list: {
      type: Array,
      default() {
        return {
          value: {
            type: String,
            required: false
          },
          video: {
            type: String,
            required: false
          }
        }
      },
      value: {
        type: String,
        required: false
      },
      thumbSrc: {
        type: String,
        required: false
      },
      imageSrc: {
        type: String,
        required: false
      },
      // videoPosterSrc: {
      //   type: String,
      //   required: false
      // },
      videoSrc: {
        type: String,
        required: false
      },
    },
  }
}
</script>
