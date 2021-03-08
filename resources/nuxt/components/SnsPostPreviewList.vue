<template>
  <div>
    <slick ref="slick" :options="slickOptions">
      <div v-for="(account, key) of list.accountList" :key="key">
        <SnsPostPreviewListItem :account="account" :data="list.commonData" />
      </div>
    </slick>
  </div>
</template>

<script>
export default {
  props: {
    list: {
      type: Object,
      default() {
        return {
          commonData: {
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
          accountList: {
            type: Object,
            default() {
              return {
                type: {
                  type: String,
                  required: true
                },
                avatar: {
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
                }
              }
            }
          }
        }
      }
    }
  },
  data() {
    return {
      slickOptions: {
        arrows: false,
        centerPadding: '20px',
        centerMode: true
      }
    }
  }
}
</script>
