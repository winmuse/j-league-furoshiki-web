<template>
  <div>
    <b-loading
      :is-full-page="true"
      :active.sync="isLoading"
      :can-cancel="false"
    >
    </b-loading>

    <MediaList :list="list"/>

    <scroll-loader :loader-method="loadMore" :loader-disable="loaderDisable"/>

    <MediaPreview
      :is-modal-active="previewActive"
      :item="previewItem"
      @close-modal="onCloseModal"
    />

    <div v-if="!listEmpty" class="navbar is-fixed-bottom">
      <div class="container is-padding">
        <b-button tag="router-link" class="is-primary is-medium" :to="localePath('/new-post')" expanded rounded>
          {{ $t('material_index.caption') }}
        </b-button>
      </div>
    </div>
  </div>
</template>
<style lang="css" scoped>
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(10%) scale(0.9);
    }

    to {
      opacity: 1;
      transform: translateY(0%) scale(1.0);
    }
  }
</style>
<script>
import {EventBus} from '@/utils/eventBus.js'

export default {
  layout: 'hasmenu',
  middleware: 'authed',
  data() {
    return {
      loading: false,
      loaderDisable: false,
      page: 0,
      pageSize: 30,
      isLoading: false,
      list: [],
      masks: [],
      modalTarget: false,
      previewItem: null,
      previewActive: false
    }
  },
  computed: {
    listEmpty() {
      return this.$store.state.snsMediaList.length === 0
    },
  },
  beforeMount() {
    EventBus.$on('onFilterMedia', this.onFilterMedia)
    EventBus.$on('onVideoDouble', this.onVideoDouble)
    EventBus.$on('onVideoPosted', this.onVideoPosted)
    EventBus.$on('mediaPreview', this.onPreview)
  },
  beforeDestroy() {
    EventBus.$off('onFilterMedia', this.onFilterMedia)
    EventBus.$off('onVideoDouble', this.onVideoDouble)
    EventBus.$off('onVideoPosted', this.onVideoPosted)
    EventBus.$off('mediaPreview', this.onPreview)
  },
  methods: {
    onCloseModal() {
      this.previewActive = false
    },
    // 素材の取得
    getMediaList (data) {
      if (this.loading) {
        return
      }
      this.loading = true

      this.$axios.$get('search_media', {params: data})
        .then(res => {
          // if (res.length > 0) {
            this.loadMedia(res)
          // }
          if (res.length < this.pageSize) {
            this.loaderDisable = true
          }
        })
        .catch(err => {
          this.$toast.error(this.$t('material_index.error.not_authenticated'))

          localStorage.removeItem('$furoshikiToken')
          localStorage.removeItem('$furoshikiAuth')
          localStorage.removeItem('$furoshikiVcode')

          this.$router.push('/auth/login')
        })
        .finally(() => {
          this.headerSearchActive = false
          this.loading = false
        })
    },
    // スクロール位置が、scroll-loaderに指定した位置を交差した段階で実行される関数
    // ※ 位置は下部から0pxを指定
    loadMore() {
      const filters = this.getSearchParam()
      this.getMediaList(filters)
      this.page++
    },
    onPreview(id) {
      console.log(this.previewActive)
      if (this.list.length === 0) return
      this.previewItem = this.list.filter(l => l.id === id)[0]
      this.previewActive = true
      console.log(this.previewActive)
    },
    onVideoDouble() {
      this.$toast.error(this.$t('material_index.error.video_double'))
    },
    onVideoPosted() {
      this.$toast.error(this.$t('material_index.error.video_posted'))
    },
    getSearchParam() {
      return {
        opt_keyword: this.$store.state.optKeyword,
        opt_date: this.$store.state.optDate,
        opt_player: this.$store.state.optPlayer,
        opt_page: this.page,
        opt_size: this.pageSize
      }
    },
    onFilterMedia() {
      this.list = []
      this.page = 0

      const data = this.getSearchParam()
      data.opt_page = 0
      this.getMediaList(data)
    },
    loadMedia(medias) {
      const newMedias = medias.map(e => {
        const tags = []
        e.thumb_url.includes('Shot ') && tags.push('shot')
        e.thumb_url.includes('Goal ') && tags.push('goal')
        e.thumb_url.includes('Save ') && tags.push('save')
        const isImage = e.extension === 'jpg'
        return {
          id: e.id,
          type: isImage ? 'image' : 'video',
          thumb_url: e.thumb_url,
          source_url: isImage ? e.source_url : '',
          video_url: isImage ? '' : e.video_url,
          tags
        }
      })
      return newMedias && (this.list = [...this.list, ...newMedias])
    },
  },
}
</script>
