<template>
  <nuxt-link :to="item.link" class="box sns-post-reservation-list-item">
    <div class="sns-post-reservation-list-item-data">
      <ul class="sns-post-reservation-list-item-sns">
        <li v-for="(snsItem) of item.sns" :key="snsItem">
          <span class="image is-20x20">
            <img
              class="is-rounded"
              :src="'/images/icon-' + snsItem + '-circle.png'"
            >
          </span>
        </li>
      </ul>
      <div class="sns-post-reservation-list-item-text">
        <p>{{ item.text }}</p>
      </div>
      <div class="sns-post-reservation-list-item-date">
        <p>
          <template v-if="type === 'private'">
            投稿予定日時
          </template>
          <template v-else-if="type === 'public'">
            投稿日
          </template>
          ：{{ item.date }}
        </p>
      </div>
    </div>
    <div v-if="item.media" class="sns-post-reservation-list-item-figure">
      <figure>
        <span v-if="item.media.type == 'video'" class="icon-play">
          <b-icon icon="icon-font-play" />
        </span>

        <img :src="item.media.figure" alt="">
      </figure>
    </div>
  </nuxt-link>
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      required: true
    },
    item: {
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
          },
          link: {
            type: String,
            required: true
          }
        }
      }
    }
  }
}
</script>
