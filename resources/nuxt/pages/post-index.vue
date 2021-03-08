<template>
  <div>
    <b-tabs expanded>
      <b-tab-item v-for="(item,key) of tabMenu" :key="key" :label="item.label">
        <div class="container is-padding-top">
          <snsPostReservationList :type="item.type" :list="item.list" />
        </div>
      </b-tab-item>
    </b-tabs>
  </div>
</template>

<script>
export default {
  layout: 'hasmenu',
  middlware: '',
  middleware: 'authed',
  data(){
    return {
      isActive: false,
      comments: [],
      tags: [],
      tabMenu: [
        {
          label: this.$t('post_index.private'),
          type: 'private',
          list: []
        },
        {
          label: this.$t('post_index.public'),
          type: 'public',
          list: []
        }
      ]
    }
  },
  async mounted() {
    try {
      const res = await this.$axios.$get('default_comment')
      console.log(res)
      this.comments = res.map(e => e.name)
    } catch (err) {
      console.log(err)
    }

    try {
      const res = await this.$axios.$get('/post_list')
      if (res.publics.length === 0 && res.privates.length === 0) {
        this.$toast.info(this.$t('post_index.info'))
        return
      }

      const posts = [res.privates, res.publics]
      posts.forEach((p, k) => {
        this.tabMenu[k].list = p.map(e => {
          const tags = e.tags.map(t => t.name)
          const content = e.description + ' ' + this.comments.join(' ') + ' #' + tags.join(' #')
          const mtype = e.medias[0].extension === 'jpg'? 'image' : 'video'
          return {
            id: e.id,
            date: this.$dayjs(e.publish_at).format('YYYY.MM.DD HH:mm'),
            sns: e.providers.map(p => p.sns),
            text: content,
            media: {type: mtype, figure: e.medias[0].thumb_url},
            link: k === 0 ? this.localePath(`/edit-post?m=${e.id}`) : ''
          }
        })
      })
    } catch (e) {
      console.log(e)
      this.$toast.error(this.$t('post_index.error'))
    }
  }
}
</script>
