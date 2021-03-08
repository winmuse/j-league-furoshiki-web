<template>
  <div>
    <div class="container is-padding-top">
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
              <span class="has-text-grey">{{ $t('new_post.holder.sns') }}</span>
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
              <li
                v-for="item of SnsMediaList"
                :key="item"
                class="media-block"
              >
                <span
                  class="delete is-outline"
                  @click="cancelMedia(item)"
                />
                <div>
                  <div v-if="item.type == 'video'">
                    <video controls :poster="item.thumb_url">
                      <source :src="item.video_url" type="video/mp4">
                    </video>
                  </div>

                  <figure v-else class="image">
                    <img :src="item.thumb_url" alt="">
                  </figure>
                </div>
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
            :placeholder="$t('new_post.holder.content')"
          />
        </b-field>
      </div>
      <b-field
        :label="$t('new_post.label.default_tag')"
      >
        <b-taginput
          v-model="DefaultTagList"
          :closable="false"
          :allow-new="false"
          rounded ellipsis readonly
        />
      </b-field>
      <b-field
        :label="$t('new_post.label.tag')"
      >
        <b-taginput
          v-model="SnsTagList"
          ellipsis
          :placeholder="$t('new_post.holder.tag')"
          :before-adding="beforeAdd"
          @add="addTag"
          @remove="removeTag"
        />
      </b-field>
      <b-field
        :label="$t('new_post.label.comment')"
      >
        <ul class="comments">
          <li v-for="item in SnsComments" :key="item.id">
            {{ item }}
          </li>
        </ul>
      </b-field>
    </div>
    <div v-if="!hasPostError" class="navbar is-fixed-bottom">
      <div class="container is-padding">
        <b-button
          class="is-primary is-medium"
          expanded rounded
          :disabled="disabled"
          @click="validate"
        >
          {{ $t('new_post.submit') }}
        </b-button>
      </div>
    </div>
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
</style>

<script>
export default {
  layout: 'back',
  middleware: 'authed',
  data() {
    return {
      disabled: false,
      disabled_sns_set: false,
      errMsg: '',
      isErrorActive: false,
      maxContents: 140,
      date: new Date(),
      SnsContent: this.$store.state.snsContent,
      SnsTagList: this.$store.state.snsTagList,
      DefaultTagList: this.$store.state.snsDefaultTagList,
      // SnsPublishEstimate: this.$store.state.snsPulishEstimate,
      SnsPublishEstimate: this.$dayjs().format('YYYY-MM-DD HH:mm'),
      errorSnsSelect: false,
      errorSnsPublishDate: false,
      defaultTags: [],
      SnsAccounts: []
    }
  },
  computed: {
    SnsMediaList() {
      return this.$store.state.snsMediaList
    },
    msgErrorContent() {
      return this.errorSnsContent? this.$t('new_post.msg.error.content') : ''
    },
    msgErrorSns() {
      return !this.hasSelectedSns? this.$t('new_post.msg.error.sns') : ''
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
  asyncData({ store }) {
    // store.commit('getTitle', this.$t('new_post.title'))
  },
  mounted() {
    this.$store.commit('setPreviousPage', this.localePath('/material-index'))
    this.$axios.$get('default_tags')
      .then(res => {
        console.log(res)
        this.defaultTags = res
        this.defaultTags.forEach(e => {
          if (!this.$store.state.snsDefaultTagList.includes(e)) {
            this.$store.commit('snsDefaultTagListAdd', e)
          }
        })
      })
      .catch(err => {
        console.log(err)
      })
    this.$axios.$get('default_comment')
      .then(res => {
        console.log(res)
        if (this.$store.state.snsComments.length === 0) {
          res.forEach(e => {
            this.$store.commit('snsCommentsAdd', e.name)
          })
        }
      })
      .catch(err => {
        console.log(err)
      })
    this.$axios.$get('/credential/all')
      .then(res => {
        this.SnsAccounts = res
      })
      .catch(err => {
        console.log(err)
        this.isErrorActive = true
      })

  },
  beforeDestroy() {
    this.$store.commit('setSnsContent', this.SnsContent)
  },
  methods: {
    toSnsSet() {
      this.disabled_sns_set = true
      // if(this.SnsAccounts.length === 0) {
      //   this.$toast.error(this.$t('new_post.msg.error.no_sns'))
      //   return
      // }
      this.$store.commit('setPreviousPage', this.localePath('/new-post'))
      this.$router.push(this.localePath('/new-post-sns'))
      this.disabled_sns_set = false
    },
    cancelMedia(item) {
      this.$store.commit('snsMediaListRemove', item.id)
    },
    validate() {
      this.disabled = true
      this.errorSnsPublishDate = this.SnsPublishEstimate === ''

      if (this.hasPostError) return

      const medias = this.$store.state.snsMediaList
      const targets = this.$store.state.snsAccountList.map(m => m.type)
      const types = medias.map(e => e.type)

      if (medias.length > 1) {
        if (targets.includes('instagram') || targets.includes('line')) {
          this.$toast.error(this.$t('new_post.msg.error.ig_ln_multi_media'))
          return
        }
        //  Tweet with media must have exactly 1 gif or video or up to 4 photos.
        if (types.includes('video') || types.length > 4) {
          this.$toast.error(this.$t('new_post.msg.error.tw_file_select'))
          return
        }
      }

      this.$store.commit('setSnsContent', this.SnsContent)
      this.$store.commit('setSnsPublishEstimate', this.SnsPublishEstimate)
      this.$store.commit('setSnsComment', this.SnsComments)

      this.disabled = true
      this.$router.push(this.localePath('/new-post-confirm'))
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
