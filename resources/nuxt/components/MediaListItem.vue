<template>
  <div>
    <div
      class="media-list-item"
      :data-id="item.id"
      :class="{ 'is-select': isSelect }"
    >
      <b-checkbox
        v-model="mediaList"
        :native-value="item.id"
        @change.native="mediaListItemSelect"
      />
      <div
        class="media-list-item-figure"
        :style="background"
        @click="showPreview"
      >
        <div
          v-if="isVideo"
          class="media-list-item-figure-play"
        >
          <b-tag
            v-for="(tag, index) in item.tags"
            :key="index"
            rounded
          >
            {{ tag }}
          </b-tag>
        </div>
        <span v-if="isVideo" class="icon-play">
          <b-icon icon="icon-font-play" />
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import {EventBus} from '@/utils/eventBus.js'

export default {
  props: {
    item: {
      type: Object,
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
      id: {
        type: Number,
        required: false
      },
      value: {
        type: String,
        required: false
      },
      thumb_url: {
        type: String,
        required: false
      },
      source_url: {
        type: String,
        required: false
      },
      video_url: {
        type: String,
        required: false
      },
    }
  },
  data() {
    const selected = this.$store.state.snsMediaList.filter(e => e.id === this.item.id).length > 0
    return {
      isModalActive: false,
      mediaList: selected ? [this.item.id] : [],
      isSelect: selected
    }
  },
  computed: {
    background() {
      return `backgroundImage: url("${this.item.thumb_url}")`
    },
    isVideo() {
      return this.item.type === 'video'
    }
  },
  beforeMount() {
    EventBus.$on('onVideoCheck', this.onVideoCheck)
  },
  beforeDestroy() {
    EventBus.$off('onVideoCheck', this.onVideoCheck)
  },
  methods: {
    showPreview() {
      EventBus.$emit('mediaPreview', this.item.id)
    },
    onVideoCheck() {
      this.mediaList = this.$store.state.snsMediaList
        .filter(e => e.id === this.item.id).length > 0 ? [this.item.id] : []
    },
    async mediaListItemSelect() {
      if (this.isVideo) {
        const data = await this.$axios.$get(`/check_video_posted/${this.item.id}`)
        if (data.count > 0) {
          EventBus.$emit('onVideoPosted')
          this.mediaList = []
          return
        }
        if (this.mediaList.length > 0) {
          const mlist = this.$store.state.snsMediaList
          const oldVideo = mlist.filter(e => e.type === 'video')
          oldVideo.forEach(o => {
            this.$store.commit('snsMediaListRemove', o.id)
          })
          this.$store.commit('snsMediaListAdd', this.item)
          EventBus.$emit('onVideoCheck')
        } else {
          this.$store.commit('snsMediaListRemove', this.item.id)
        }
      } else if (this.mediaList.length > 0) {
        this.$store.commit('snsMediaListAdd', this.item)
      } else {
        this.$store.commit('snsMediaListRemove', this.item.id)
      }
      console.log(this.$store.state.snsMediaList)
      this.isSelect = !this.isSelect
    }
  }
}
</script>
