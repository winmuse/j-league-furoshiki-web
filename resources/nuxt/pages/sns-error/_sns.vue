<template>
  <div class="sns-error">
    <div class="container is-padding-top">
      <div class="block is-large sns-error_block_msg">
        <p> {{ message }} </p>
      </div>

      <div class="block sns-error_block_btn">
        <b-button
          tag="router-link"
          :to="localePath('/conf-sns-set')"
        >
          {{ caption }}
        </b-button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  layout: 'back',
  middleware: 'authed',
  data() {
    this.$store.commit('setPreviousPage', this.localePath('/conf-sns-set'))
    return {
      sns: this.$route.params.sns,
      code: this.$route.query.code,
      messages: {
        twitter: {
          token: {
            en: 'Can not connect Twitter.',
            ja: 'Twitterに接続できません。'
          },
          verifycode: {
            en: 'Can not find verify code.',
            ja: '確認コードが見つかりません。'
          },
          credential: {
            en: 'Can not get user information.',
            ja: 'ユーザー情報を取得できません。'
          },
          cancel: {
            en: 'You canceled to connect this app.',
            ja: 'このアプリの接続をキャンセルしました。'
          },
          session: {
            en: 'Can not get valid session.',
            ja: '有効なセッションを取得できません。'
          }
        },
        facebook: {
          token: {
            en: 'Can not connect Facebook.',
            ja: 'Facebookに接続できません。'
          },
          verifycode: {
            en: 'Can not find verify code.',
            ja: '確認コードが見つかりません。'
          },
          long_token: {
            en: 'Can not get long lived token.',
            ja: '長期有効なトークンを取得できません。'
          },
          profile: {
            en: 'Can not get user information.',
            ja: 'ユーザー情報を取得できません。'
          },
          request: {
            en: 'Bad request.',
            ja: '要求の形式が正しくありません.'
          },
          cancel: {
            en: 'You canceled to connect this app.',
            ja: 'このアプリの接続をキャンセルしました。'
          }
        },
        instagram: {
          page: {
            en: 'Can not find Facebook page connected to Instagram.',
            ja: 'Instagramに接続されたFacebookページが見つかりません。'
          },
          verifycode: {
            en: 'Can not find verify code.',
            ja: '確認コードが見つかりません。'
          },
          profile: {
            en: 'Can not get user information.',
            ja: 'ユーザー情報を取得できません。'
          },
        }
      }
    }
  },
  computed: {
    message() {
      console.log(this.$i18n.locale)
      console.log(this.messages[this.sns][this.code][this.$i18n.locale])
      return this.messages[this.sns][this.code][this.$i18n.locale]
    },
    caption() {
      if (this.$i18n.locale === 'en') {
        return 'To SNS account page'
      }
      return 'SNSアカウント管理へ'
    }
  }
}
</script>
