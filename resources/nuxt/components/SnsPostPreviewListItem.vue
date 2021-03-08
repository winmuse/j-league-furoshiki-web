<template>
  <article class="box sns-post-preview" :class="'is-' + account.type">
    <div class="media">
      <figure class="media-left">
        <p class="image is-30x30">
          <img class="is-rounded" :src="account.avatar">
        </p>
      </figure>
      <div class="media-content sns-post-preview-name">
        {{ account.name }}
        <small>@{{ account.account_name }}</small>
      </div>
      <div class="media-right">
        <span class="sns-post-preview-icon">
          <img :src="'/images/icon-' + account.type + '-circle.png'" alt="">
        </span>
      </div>
    </div>
    <div class="sns-post-preview-figure">
        <ul v-if="account.type === 'instagram' || account.type === 'line'">
            <li>
                <div v-if="data.figure[0].type === 'video'">
                    <video controls :poster="data.figure[0].thumb_url">
                        <source :src="data.figure[0].video_url" type="video/mp4">
                    </video>
                </div>

                <figure v-else class="image">
                    <img :src="data.figure[0].thumb_url" alt="">
                </figure>
            </li>
        </ul>
      <ul v-else>
        <li v-for="item in data.figure" :key="item">
          <div v-if="item.type === 'video'">
            <video controls :poster="item.thumb_url">
              <source :src="item.video_url" type="video/mp4">
            </video>
          </div>

          <figure v-else class="image">
            <img :src="item.thumb_url" alt="">
          </figure>
        </li>
      </ul>
    </div>
    <div class="sns-post-preview-text">
      <p>
        <strong v-if="account.type == 'instagram'">@{{ account.account_name }}</strong>
        {{ data.text}}
      </p>
    </div>
    <ul class="sns-post-preview-hashtags">
      <li v-for="item in data.defaultTag" :key="item">
        <b-tag rounded>#{{ item }}</b-tag>
      </li>
      <li v-for="item in data.hashTag" :key="item">
        <b-tag rounded>#{{ item }}</b-tag>
      </li>
    </ul>
      <div class="sns-post-preview-comment">
          <p>
              {{ data.comment }}
          </p>
      </div>

  </article>
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
      default() {
        return {
          figure: {
            type: Array,
            required: true
          },
          text: {
            type: String,
            required: true
          },
          comment: {
            type: String,
            required: false
          },
          hashTag: {
            type: Array,
            required: false
          }
        }
      }
    },
    account: {
      type: Object,
      default() {
        return {
          type: {
            type: String,
            required: true
          },
          name: {
            type: String,
            required: true
          },
          account_name: {
            type: String,
            required: true
          },
          avatar: {
            type: String,
            required: true
          }
        }
      }
    }
  }
}
</script>
