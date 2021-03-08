<template>
  <div class="pg-container">
    <header class="header">
      <nav
        class="header-main navbar is-fixed-top"
        role="navigation"
        aria-label="main navigation"
      >
        <div class="header-main-nav">
          <div>
            <div class="navbar-item">
              <div
                class="header-main-nav-burger navbar-burger"
                :class="{ 'is-active': headerSubActive }"
                @click="headerSubToggle()"
              >
                <span />
                <span />
                <span />
              </div>
            </div>
          </div>
          <div>
            <div class="header-main-nav-sitelogo">
              <nuxt-link :to="localePath('/material-index')">
                <img src="/images/sitelogo.png" alt="">
              </nuxt-link>
            </div>
          </div>
          <div>
            <div v-if="searchable" class="navbar-item">
              <div class="header-main-nav-search" @click="headerSearchToggle()">
                <b-icon icon="icon-font-search" />
              </div>
            </div>
          </div>
        </div>
      </nav>

      <div class="header-sub" :class="{ 'is-active': headerSubActive }">
        <div class="header-sub-close">
          <span class="close" @click="headerSubToggle()" />
        </div>
        <div class="header-sub-sitelogo">
          <img src="/images/sitelogo.png" alt="">
          <template v-if="$i18n.locale === 'ja'">
            <p>{{ user ? user.name : '' }}選手ようこそ</p>
          </template>
          <template v-else-if="$i18n.locale === 'en'">
            <p>Welcome, {{ user ? user.name_en : '' }}</p>
          </template>
        </div>
        <ul class="header-sub-menu">
          <li v-if="$i18n.locale === 'en'">
            <nuxt-link :to="switchLocalePath('ja')">
              <CountryFlag country="jp" size="normal" />
              <span>日本語に切替</span>
            </nuxt-link>
          </li>
          <li v-if="$i18n.locale === 'ja'">
            <nuxt-link :to="switchLocalePath('en')">
              <CountryFlag country="us" size="normal" />
              <span>Switch to English</span>
            </nuxt-link>
          </li>
          <li v-for="(item, key) of sideMenu" :key="key">
            <nuxt-link v-if="item.to" :to="localePath(item.to)" @click.native="headerSubToggle()">
              {{ item.title }}
            </nuxt-link>
            <a v-else-if="item.href" :href="item.href" target="_blank">
              {{ item.title }}
            </a>
            <template v-else>
              {{ item.title }}
            </template>
            <ul v-if="item.sub" class="header-sub-menu-sub">
              <li v-for="subitem of item.sub" :key="subitem.to">
                <nuxt-link :to="localePath(subitem.to)" @click.native="headerSubToggle()">
                  {{ subitem.title }}
                </nuxt-link>
              </li>
            </ul>
          </li>
          <li>
            <nuxt-link to="" @click.native="logout">
              {{ $t('menu.logout') }}
            </nuxt-link>
          </li>
        </ul>
      </div>

      <div class="header-search" :class="{ 'is-active': headerSearchActive }">
        <div class="header-search-close">
          <span class="close" @click="headerSearchToggle()" />
        </div>
        <div class="header-search-sitelogo">
          <img src="/images/sitelogo.png" alt="">
        </div>
        <div class="header-search-field">
          <b-field :label="$t('menu.search.player')" type="is-nega">
            <b-select v-model="player" expanded>
              <option v-for="p in players" :key="p.id" :value="p.id">
                {{ p.name }}
              </option>
            </b-select>
          </b-field>
          <b-field :label="$t('menu.search.keyword')" type="is-nega">
            <b-input
              v-model="optKeyword"
              :placeholder="$t('menu.search.keyword_placeholder')"
              type="search"
              icon="icon-font-search"
            />
          </b-field>
          <b-field :label="$t('menu.search.date')" type="is-nega">
            <b-datepicker
              v-model="optDate"
              icon="icon-font-calender"
              editable
            />
          </b-field>
        </div>
        <nav class="navbar is-fixed-bottom">
          <div class="container is-padding">
            <b-button
              tag="router-link"
              class="is-primary is-medium"
              :to="localePath('/material-index')"
              expanded
              rounded
              @click.native="clickHeaderSearch"
            >
              {{ $t('menu.search.submit') }}
            </b-button>
          </div>
        </nav>
      </div>
    </header>

    <section class="main-content">
      <nuxt />
    </section>
  </div>
</template>

<style scoped>
    .flag {
        position: relative;
        top: 10px;
    }
</style>

<script>
import {EventBus} from '@/utils/eventBus.js'

export default {
  data() {
    return {
      // menu
      user: null,
      players: [],
      player: null,
      optKeyword: null,
      optDate: null,
      headerSubActive: false,
      headerSearchActive: false,
      headerSearchField: [],
    }
  },
  computed: {
    searchable() {
      return this.$route.name.includes('material')
    },
    sideMenu() {
      return [
        // {
        //   title: this.$t('menu.mypage'),
        //   to: this.localePath('/')
        //   to: {name: 'mypage'}
        // },
        {
          title: this.$t('menu.material_index'),
          to: '/material-index'
        },
        {
          title: this.$t('menu.post_index'),
          to: '/post-index'
        },
        {
          title: this.$t('menu.sns_manage'),
          to: '/conf-sns-set'
        },
        {
          title: this.$t('menu.profile'),
          to: false,
          sub: [
            {
              title: this.$t('menu.password'),
              to: '/conf-prof-pass'
            },
            {
              title: this.$t('menu.phone'),
              to: '/conf-prof-tel'
            }
          ]
        },
        {
          title: this.$t('menu.privacy'),
          href: 'https://auth.jleague.jp/contents/privacypolicy.html',
        },
        {
          title: this.$t('menu.terms'),
          to: '/terms-of-service'
        },
      ]
    }
  },
  mounted() {
    this.$axios.$get('user', {})
      .then(res => {
        this.user = res.user
      })
      .catch(err => {
        console.log(err)
      })

    this.$axios.$get('player_list')
      .then(res => {
        this.players = res.map(e => {
          return e.user
        })
        this.players.unshift({
          id: 0,
          name: this.$t('menu.search.all')
        })
        this.player = 0
      })
      .catch(err => {
        console.log(err)
      })
  },
  methods: {
    logout() {
      // jwt logout
      this.$axios.get('logout')
        .then(res => {
          // clear vuex store and localstorage
          this.$store.commit('logout')
          localStorage.removeItem('$furoshikiToken')
          localStorage.removeItem('$furoshikiAuth')
          this.$router.push(this.localePath('/auth/login'))
        })
        .catch(err => {
          console.log(err)
        })
    },
    headerSubToggle() {
      this.headerSubActive = !this.headerSubActive
      document.getElementsByTagName('html')[0].classList.toggle('is-clipped')
    },
    headerSearchToggle() {
      this.headerSearchActive = !this.headerSearchActive
      document.getElementsByTagName('html')[0].classList.toggle('is-clipped')
    },
    clickHeaderSearch() {
      let d = ''
      if(this.optDate !== null) {
        d = this.$dayjs(this.optDate).format('YYYY-MM-DD')
      }

      this.$store.commit('setOptKeyword', this.optKeyword)
      this.$store.commit('setOptDate', d)
      this.$store.commit('setOptPlayer', this.player)
      EventBus.$emit('onFilterMedia')
      this.headerSearchActive = !this.headerSearchActive
      document.getElementsByTagName('html')[0].classList.toggle('is-clipped')
    }
  }
}
</script>
