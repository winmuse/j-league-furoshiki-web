<template>
  <div
    v-if="isModalActive"
    class="modal"
    :class="{ 'is-active': isModalActive }"
  >
    <div class="modal-background" />
    <div class="animation-content modal-content">
      <div class="header">
        <div
          class="header-main navbar is-fixed-top"
          role="navigation"
          aria-label="main navigation"
        >
          <div class="header-main-nav">
            <div class="navbar-item">
              <div class="header-main-nav-arrow">
                <span @click="closeModal">
                  <b-icon icon="icon-font-arrow" />
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="media-list-item">
        <div v-if="item.type === 'video'">
          <video
            ref="video"
            controls
            :poster="item.thumb_url"
          >
            <source :src="item.video_url" type="video/mp4">
          </video>
        </div>

        <figure v-else class="image">
          <img :src="item.source_url" alt="">
        </figure>
      </div>

      <div class="navbar is-fixed-bottom">
        <div class="container is-padding">
          <b-button
            class="is-primary is-medium"
            expanded rounded
            :disabled="disabled"
            @click="gotoNewPost"
          >
            {{ $t('material_index.caption') }}
          </b-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    isModalActive: Boolean,
    item: {
      type: Object,
      default() {
        return {
          id: { type: Number, required: false },
          type: { type: String, required: false },
          thumb_url: { type: String, required: false },
          source_url: { type: String, required: false },
          video_url: { type: String, required: false },
        }
      },
    }
  },
  data: () =>({
    disabled: false
  }),
  methods: {
    stopVideo() {
      if (this.$refs.video !== undefined) {
        this.$refs.video.pause()
      }
    },
    closeModal() {
      this.stopVideo()
      this.$emit('close-modal')
    },
    gotoNewPost() {
      this.disabled = true
      this.stopVideo()
      this.$store.commit('snsMediaListClear')
      this.$store.commit('snsMediaListAdd', this.item)
      this.$router.push(this.localePath('/new-post'))
      this.disabled = false
    },
  },
}
</script>
