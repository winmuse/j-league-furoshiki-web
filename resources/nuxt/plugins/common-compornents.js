// import
// ----------------------------------------------------
import Vue from 'vue'

// common
import MediaList from '~/components/MediaList'
import MediaListItem from '~/components/MediaListItem'
import MediaPreview from '~/components/MediaPreview'
import SnsPostPreviewList from '~/components/SnsPostPreviewList'
import SnsPostPreviewListItem from '~/components/SnsPostPreviewListItem'
import SnsAccountList from '~/components/SnsAccountList'
import SnsAccountListItem from '~/components/SnsAccountListItem'
import SnsSettingList from '~/components/SnsSettingList'
import SnsSettingListItem from '~/components/SnsSettingListItem'
import SnsAccountListSmall from '~/components/SnsAccountListSmall'
import SnsAccountListSmallItem from '~/components/SnsAccountListSmallItem'
import SnsAccountConnectButton from '~/components/SnsAccountConnectButton'
import SnsPostReservationList from '~/components/SnsPostReservationList'
import SnsPostReservationListItem from '~/components/SnsPostReservationListItem'
import CountryFlag from 'vue-country-flag'
import ScrollLoader from '~/utils/plugin-entry'

// set component
// ----------------------------------------------------
Vue.component('MediaList', MediaList)
Vue.component('MediaListItem', MediaListItem)
Vue.component('MediaPreview', MediaPreview)
Vue.component('SnsPostPreviewList', SnsPostPreviewList)
Vue.component('SnsPostPreviewListItem', SnsPostPreviewListItem)
Vue.component('SnsAccountList', SnsAccountList)
Vue.component('SnsAccountListItem', SnsAccountListItem)
Vue.component('SnsSettingList', SnsSettingList)
Vue.component('SnsSettingListItem', SnsSettingListItem)
Vue.component('SnsAccountListSmall', SnsAccountListSmall)
Vue.component('SnsAccountListSmallItem', SnsAccountListSmallItem)
Vue.component('SnsAccountConnectButton', SnsAccountConnectButton)
Vue.component('SnsPostReservationList', SnsPostReservationList)
Vue.component('SnsPostReservationListItem', SnsPostReservationListItem)
Vue.component('CountryFlag', CountryFlag)
Vue.use(ScrollLoader)
