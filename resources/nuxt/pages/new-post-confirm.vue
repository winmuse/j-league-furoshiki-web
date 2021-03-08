<template>
  <div>
    <div class="container is-padding-top-only">
      <SnsPostPreviewList :list="SnsPostPreviewList"/>
    </div>
    <b-loading
      :is-full-page="true"
      :active.sync="isLoading"
      :can-cancel="false"
    >
    </b-loading>
    <div class="navbar is-fixed-bottom">
      <div class="container is-padding centered">
        <b-button
          class="is-primary is-medium btn-post btn-40"
          rounded
          :disabled="disabled"
          @click="submitPost"
        >
          {{ $t('conf_post.caption.post') }}
        </b-button>
        <b-button
          class="is-primary is-medium btn-draft btn-40"
          inverted outlined rounded
          :disabled="disabled"
          @click="saveDraft"
        >
          {{ $t('conf_post.caption.save') }}
        </b-button>
      </div>
    </div>
    <b-modal :active.sync="isModalActive" :can-cancel="false" scroll="keep">
      <div class="modal-dialog">
        <div class="done">
          <div class="done-figure">
            <img src="/images/done.png" alt class="done-figure-done">
          </div>
          <div class="done-text">
            <p>{{modalMessage}}</p>
          </div>
          <div class="done-button">
<!--            <b-button-->
<!--              size="is-medium"-->
<!--              type="is-primary"-->
<!--              rounded-->
<!--              tag="router-link"-->
<!--              :to="localePath('/material-index')"-->
<!--            >-->
<!--              {{ $t('conf_post.caption.continue') }}-->
<!--            </b-button>-->
            <b-button
              size="is-medium"
              type="is-primary"
              rounded outlined
              tag="router-link"
              :to="localePath('/material-index')"
            >
              {{ $t('conf_post.caption.to_top') }}
            </b-button>
          </div>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<style scoped>
  .centered {
    text-align: center;
  }
  .btn-40 {
    width: 40%;
  }
</style>
<script>
export default {
  layout: 'back',
  middleware: 'authed',
  data() {
    const cnt = this.$store.state.snsContent + ' ' + this.$store.state.snsComments.join(' ')
    return {
      disabled_sns_set: false,
      disabled: false,
      content: cnt,
      isLoading: false,
      isModalActive: false,
      status: 1,
      modalMessages: [
        this.$t('conf_post.message.modal.save'),
        this.$t('conf_post.message.modal.post')
      ],
      SnsPostPreviewList: {
        commonData: {
          figure: this.$store.state.snsMediaList,
          text: cnt,
          hashTag: this.$store.state.snsTagList,
          defaultTag: this.$store.state.snsDefaultTagList,
        },
        accountList: this.$store.state.snsAccountList
      }
    }
  },
  computed: {
    modalMessage() {
      return this.modalMessages[this.status]
    }
  },
  asyncData({store}) {
    // store.commit('getTitle', this.$t('conf_post.title'))
  },
  mounted() {
    this.$store.commit('setPreviousPage', this.localePath('/new-post'))
  },
  methods: {
    postData(status) {
      this.status = status

      const medias = this.$store.state.snsMediaList
      const targets = this.$store.state.snsAccountList.map(e => { return {sns: e.type, id: e.id} })
      //  Tweet with media must have exactly 1 gif or video or up to 4 photos.
      if (targets.includes('twitter')) {
        if (medias.length > 1) {
          const types = medias.map(e => e.type)
          const err = types.includes('video') || types.length > 4
          if (err) {
            this.showError(this.$t('conf_post.message.error.tw_file_select'))
            return
          }
        }
      }
      const data = {
        content: this.$store.state.snsContent,
        comments: this.$store.state.snsComments,
        tags: [...this.$store.state.snsDefaultTagList, ...this.$store.state.snsTagList],
        medias: medias.map(e => e.id),
        targets,
        publish: this.$store.state.snsPulishEstimate,
        status
      }

      console.log(data)

      this.isLoading = true
      this.$axios.post('/new_post', data)
        .then(res => {
          this.isModalActive = true
        })
        .catch(err => {
          switch (err.response.data.error) {
          case 'fb connect error':
            this.$toast.error(this.$t('conf_post.message.error.fb_connect'))
            break
          case 'no registered fb pages':
            this.$toast.error(this.$t('conf_post.message.error.fb_pg_register'))
            break
          case 'fb media upload error':
            this.$toast.error(this.$t('conf_post.message.error.fb_upload'))
            break
          case 'Graph error':
            this.$toast.error(this.$t('conf_post.message.error.fb_graph'))
            break
          case 'Facebook SDK error':
            this.$toast.error(this.$t('conf_post.message.error.fb_sdk'))
            break
          case 'tw file upload error':
            this.$toast.error(this.$t('conf_post.message.error.tw_upload'))
            break
          case 'tw video size over':
            this.$toast.error(this.$t('conf_post.message.error.tw_video_size'))
            break
          case 'tw photo size over':
            this.$toast.error(this.$t('conf_post.message.error.tw_photo_size'))
            break
          case 'tw media format error':
            this.$toast.error(this.$t('conf_post.message.error.tw_media_format'))
            break
          case 'db error':
            this.$toast.error(this.$t('conf_post.message.error.database'))
            break
          }
        })
        .then(() => {
          this.isLoading = false
          this.disabled = false
        })
    },
    submitPost() {
      this.disabled = true
      this.postData(1)
    },
    saveDraft() {
      this.disabled = true
      this.postData(0)
    }
  }
}
</script>
