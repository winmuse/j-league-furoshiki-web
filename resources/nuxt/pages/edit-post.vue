<template>
  <div>
    <div class="container is-padding-top">
      <b-loading
        :is-full-page="true"
        :active.sync="isLoading"
        :can-cancel="false"
      />
      <b-field
        :type="{'is-danger':!hasSelectedSns}"
        :message="msgErrorSns"
      >
        <div class="sns-account-list is-small">
          <div class="sns-account-list-item">
            <div v-if="hasSelectedSns">
              <SnsAccountListSmall :list="SnsAccountListSmall" />
            </div>
            <div v-else>
              <span class="has-text-grey">{{ $t('edit_post.holder.sns') }}</span>
            </div>
            <div class="sns-account-list-item-control">
              <b-button
                rounded
                class="icon-plus"
                :disabled="disabled_sns_set"
                @click="toSnsSet"
              >
                <b-icon icon="icon-font-plus" />
              </b-button>
<!--              <nuxt-link-->
<!--                class="icon-plus"-->
<!--                :to="localePath('/new-post-sns')"-->
<!--              >-->
<!--                <b-icon icon="icon-font-plus" />-->
<!--              </nuxt-link>-->
            </div>
          </div>
        </div>
      </b-field>
      <div class="box is-border sns-post-new">
        <div class="sns-post-new-figure">
          <b-field class="is-borderless">
            <ul>
              <li v-for="item of SnsMediaList" :key="item">
                <div v-if="item.type == 'video'">
                  <video controls :poster="item.thumb_url">
                    <source :src="item.video_url" type="video/mp4">
                  </video>
                </div>

                <figure v-else class="image">
                  <img :src="item.thumb_url" alt="">
                </figure>
              </li>
            </ul>
          </b-field>
        </div>
        <b-field
          :type="{'is-danger':errorSnsContent}"
          :message="msgErrorContent"
        >
          <b-input
            v-model="SnsContent"
            class="is-borderless"
            type="textarea"
            :maxlength="maxContents"
            has-counter
            :placeholder="$t('edit_post.holder.content')"
          />
        </b-field>
      </div>
      <b-field
        :label="$t('edit_post.label.default_tag')"
      >
        <b-taginput
          v-model="DefaultTagList"
          :closable="false"
          :allow-new="false"
          rounded
          ellipsis
          readonly
        />
      </b-field>
      <b-field :label="$t('edit_post.label.tag')">
        <b-taginput
          v-model="SnsTagList"
          ellipsis
          :placeholder="$t('edit_post.holder.tag')"
          :before-adding="beforeAdd"
          @add="addTag"
          @remove="removeTag"
        />
      </b-field>
      <b-field
        :label="$t('edit_post.label.comment')"
      >
        <ul class="comments">
          <li v-for="item in SnsComments" :key="item.id">
            {{ item }}
          </li>
        </ul>
      </b-field>
    </div>
    <div v-if="!hasPostError" class="navbar is-fixed-bottom">
      <div class="container is-padding bottom-nav">
        <b-button
          class="is-primary is-medium btn-post"
          expanded rounded
          :disabled="disabled"
          @click="publishDraft"
        >
          {{ $t('conf_post.caption.post') }}
        </b-button>
        <b-button
          class="is-primary is-medium btn-draft"
          inverted outlined expanded rounded
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
            <p>{{ modalMessage }}</p>
          </div>
          <div class="done-button">
            <b-button
              size="is-medium"
              type="is-primary"
              rounded outlined
              tag="router-link"
              :to="localePath('/post-index')"
            >
              {{ $t('edit_post.caption.post_index') }}
            </b-button>
          </div>
        </div>
      </div>
    </b-modal>
  </div>
</template>
<style scoped>
    .comments {
        padding: 10px 15px;
        background-color: #fff;
        border-color: #d0d0d0;
        border-radius: 4px;
        color: #333;
    }
  .bottom-nav {
    text-align: center
  }
  .btn-post, .btn-draft {
    width: 40%;
    display: inline-flex;
  }
</style>

<script>
export default {
  layout: 'back',
  middleware: 'authed',
  data() {
    return {
      disabled_sns_set: false,
      disabled: false,
      isLoading: false,
      isModalActive: false,
      status: 1,
      maxContents: 140,
      pid: this.$route.query.m,
      date: new Date(),
      SnsContent: '',
      SnsTagList: [],
      DefaultTagList: [],
      SnsMediaList: [],
      SnsPublishEstimate: this.$dayjs().format('YYYY-MM-DD HH:mm:ss'),
      errorSnsSelect: false,
      errorSnsPublishDate: false,
      modalMessages: [
        this.$t('edit_post.msg.modal.save'),
        this.$t('edit_post.msg.modal.post')
      ],
    }
  },
  computed: {
    modalMessage() {
      return this.modalMessages[this.status]
    },
    msgErrorContent() {
      return this.errorSnsContent ? this.$t('edit_post.msg.error.content') : ''
    },
    msgErrorSns() {
      return !this.hasSelectedSns ? this.$t('edit_post.msg.error.sns') : ''
    },
    SnsAccountListSmall() {
      return this.$store.state.snsAccountList
    },
    SnsComments() {
      return this.$store.state.snsComments
    },
    format() {
      return this.formatAmPm ? '12' : '24'
    },
    hasSelectedSns() {
      return this.$store.state.snsAccountList.length > 0
    },
    errorSnsContent() {
      return this.SnsContent === ''
    },
    hasPostError() {
      return !this.hasSelectedSns || this.errorSnsContent || this.errorSnsPublishDate
    },
  },
  asyncData({store}) {
    // store.commit('getTitle', this.$t('edit_post.title'))
  },
  async mounted() {
    this.$store.commit('setPreviousPage', this.localePath('/post-index'))
    let providerList = []
    try {
      const res = await this.$axios.$get('default_tags')
      console.log(res)
      this.DefaultTagList = res
      this.DefaultTagList.forEach(e => {
        if (!this.$store.state.snsDefaultTagList.includes(e)) {
          this.$store.commit('snsDefaultTagListAdd', e)
        }
      })
    } catch (err) {
      console.log(err)
    }
    try {
      const res = await this.$axios.$get(`post/${this.pid}`)

      this.$store.commit('setSnsContent', '')
      this.SnsContent = res.description

      this.$store.commit('snsTagListClear')
      this.SnsTagList = res.tags.filter(t => {
        return !this.DefaultTagList.includes(t.name)
      }).map(e => e.name)

      this.$store.commit('snsMediaListClear')
      this.SnsMediaList = res.medias.map(m => {
        let media = null
        if (m.extension === 'jpg') {
          media = {
            id: m.id,
            type: 'image',
            thumb_url: m.thumb_url,
            source_url: m.source_url,
            videoPosterSrc: false,
            video_url: false
          }
        } else {
          media = {
            id: m.id,
            type: 'video',
            thumb_url: null,
            source_url: false,
            videoPosterSrc: m.thumb_url,
            video_url: m.video_url
          }
        }
        this.$store.commit('snsMediaListAdd', media)
        return media
      })

      this.SnsPublishEstimate = this.$dayjs(res.publish_at).format('YYYY-MM-DDTHH:mm')
      this.$store.commit('setSnsPublishEstimate', res.publish_at)

      this.$store.commit('snsAccountListClear')
      providerList = res.providers.map((p, i) => {
        return { type: p.sns, id: p.credential_id }
      })

    } catch (err) {
      this.isErrorActive = true
    }
    // get SnsCredentials
    try {
      const snsAccounts = await this.$axios.$get('/credential/all')

      if (providerList.length > 0) {
        snsAccounts.forEach((e, i) => {
          e.selected = false
          providerList.some(v => {
            if (e.type === v.type && e.id === v.id) {
              e.selected = true
              this.$store.commit('snsAccountListAdd', e)
            }
          })
        })
      }
    } catch (err) {
      this.isErrorActive = true
    }
    try {
      const res = await this.$axios.$get('default_comment')
      console.log(res)
      if (this.$store.state.snsComments.length === 0) {
        res.forEach(e => {
          this.$store.commit('snsCommentsAdd', e.name)
        })
      }
    } catch (err) {
      console.log(err)
    }
  },
  methods: {
    toSnsSet() {
      this.disabled_sns_set=true
      this.$store.commit('setPreviousPage', this.localePath(`/edit-post?m=${this.pid}`))
      this.$router.push(this.localePath('/new-post-sns'))
    },
    publishDraft() {
      this.disabled=true
      this.postData(1)
    },
    saveDraft() {
      this.disabled=true
      this.postData(0)
    },
    postData(status) {
      this.status = status
      this.errorSnsPublishDate = this.SnsPublishEstimate === ''

      if (this.hasPostError) return

      this.$store.commit('setSnsContent', this.SnsContent)
      this.$store.commit('setSnsPublishEstimate', this.SnsPublishEstimate)

      const data = {
        status,
        id: this.pid,
        content: this.SnsContent,
        tags: [...this.DefaultTagList, ...this.SnsTagList],
        targets: this.$store.state.snsAccountList.map(e => { return {sns: e.type, id: e.id} }),
        medias: this.SnsMediaList.map(e => e.id),
        comments: this.SnsComments
      }

      this.isLoading = true
      this.$axios.post('/update_post', data)
        .then(res => {
          this.isModalActive = true
          this.$store.commit('snsAccountListClear')
          this.$store.commit('snsMediaListClear')
          this.$store.commit('snsTagListClear')
          this.$store.commit('setSnsContent', '')

          // this.$router.push(this.localePath('/post-index'))
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
    addTag(val) {
      this.$store.commit('snsTagListAdd', val)
    },
    removeTag(val) {
      this.$store.commit('snsTagListRemove', val)
    },
    beforeAdd(val) {
      const totalTagLength = this.SnsTagList.join().length + val.length + this.SnsTagList.length + 1
      if (totalTagLength + this.SnsContent.length > 140) return false

      this.maxContents = 140 - totalTagLength
      return true
    }
  }
}
</script>
