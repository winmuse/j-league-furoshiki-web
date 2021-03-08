<template>
  <div>
    <div class="container block is-padding-top">
      <div class="block">
        <SnsAccountList :list="SnsAccountList" />
      </div>
      <div class="block">
        <SnsAccountListSmall :list="SnsAccountListSmall" />
      </div>
    </div>

    <SnsPostPreviewList :list="SnsPostPreviewList" />

    <div class="container block is-padding-top">
      <div class="buttons">
        <b-button
          icon-left="icon-font-twitter"
          class="is-icon-left-fix is-twitter"
          expanded
        >
          twitterと連携する
        </b-button>
        <b-button
          icon-left="icon-font-facebook"
          class="is-icon-left-fix is-facebook"
          expanded
        >
          facebookと連携する
        </b-button>
        <b-button
          icon-left="icon-font-instagram"
          class="is-icon-left-fix is-instagram"
          expanded
        >
          instagramと連携する
        </b-button>
        <b-button
          icon-left="icon-font-line"
          class="is-icon-left-fix is-line"
          expanded
        >
          LINEと連携する
        </b-button>
      </div>

      <div class="columns is-mobile">
        <div class="column">
          <b-button size="is-medium" type="is-primary" rounded expanded>
            Medium
          </b-button>
        </div>
        <div class="column">
          <b-button
            size="is-medium"
            type="is-primary"
            rounded
            outlined
            expanded
          >
            Medium
          </b-button>
        </div>
      </div>

      <b-field>
        <b-input placeholder="ログインID" />
      </b-field>
      <b-field>
        <b-input placeholder="パスワード" />
      </b-field>
      <b-field>
        <b-input
          placeholder="Search..."
          type="search"
          icon="icon-font-search"
          icon-clickable
        />
      </b-field>
      <b-field>
        <b-input
          value="2017-06-01T08:30"
          icon="icon-font-calender"
          type="datetime-local"
        />
      </b-field>
      <b-field>
        <b-select placeholder="Select a name" expanded>
          <option>
            sss
          </option>
        </b-select>
      </b-field>
    </div>

    <MediaList :list="mediaList" />
  </div>
</template>

<script>
export default {
  layout: 'hasmenu',
  data() {
    return {
      mediaListArray: [],
      mediaList: [
        {
          value: 'mediaList01',
          type: 'image'
        },
        {
          value: 'mediaList02',
          type: 'video'
        },
        {
          value: 'mediaList03',
          type: 'image'
        },
        {
          value: 'mediaList04',
          type: 'video'
        },
        {
          value: 'mediaList05',
          type: 'image'
        },
        {
          value: 'mediaList06',
          type: 'video'
        },
        {
          value: 'mediaList07',
          type: 'image'
        },
        {
          value: 'mediaList08',
          type: 'video'
        },
        {
          value: 'mediaList09',
          type: 'image'
        },
        {
          value: 'mediaList10',
          type: 'video'
        },
        {
          value: 'mediaList11',
          type: 'image'
        },
        {
          value: 'mediaList12',
          type: 'video'
        }
      ],
      SnsAccountList: [
        {
          type: 'twitter',
          account_name: 'account_name',
          active: true,
          checkbox: true
        },
        {
          type: 'facebook',
          account_name: 'account_name',
          active: false,
          checkbox: true
        },
        {
          type: 'instagram',
          account_name: 'account_name',
          active: false,
          checkbox: true
        },
        {
          type: 'line',
          account_name: 'account_name',
          active: true,
          checkbox: true
        }
      ],
      SnsAccountListSmall: [
        {
          type: 'twitter',
          account_name: 'account_name'
        },
        {
          type: 'facebook',
          account_name: 'account_name'
        },
        {
          type: 'instagram',
          account_name: 'account_name'
        },
        {
          type: 'line',
          account_name: 'account_name'
        }
      ],
      SnsPostPreviewList: {
        commonData: {
          figure: [
            'http://placehold.jp/ccc/bbb/1200x1200.png',
            'http://placehold.jp/bbb/ccc/1200x1200.png',
            'http://placehold.jp/ddd/aaa/1200x1200.png'
          ],
          text:
            '投稿内容テキスト、投稿内容テキスト、投稿内容テキスト、投稿内容テキスト、投稿内容テキスト。',
          hashTag: ['Jリーグ ', 'イニエスタ ', 'ヴィッセル神戸']
        },
        accountList: [
          {
            type: 'twitter',
            name: 'name',
            account_name: 'account_name'
          },
          {
            type: 'facebook',
            name: false,
            account_name: 'account_name'
          },
          {
            type: 'instagram',
            name: 'name',
            account_name: 'account_name'
          },
          {
            type: 'line',
            name: false,
            account_name: 'account_name'
          }
        ]
      }
    }
  }
}
</script>
