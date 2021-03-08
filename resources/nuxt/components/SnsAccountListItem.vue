<template>
  <div
    v-if="isActive"
    class="sns-account-list-item"
    :class="{ 'is-disabled': !item.active }"
  >
    <div class="username">
      <div class="username-icon">
        <template v-if="item.icon">
          <div class="image is-30x30">
            <img class="is-rounded" :src="item.icon" alt>
            <div class="image is-20x20">
              <img :src="snsIcon(item.type, item.active)" alt>
            </div>
          </div>
        </template>
        <template v-else>
          <div class="image is-30x30">
            <img :src="snsIcon(item.type, item.active)" alt>
          </div>
        </template>
      </div>
      <div class="username-data">
        @{{ item.account_name }}
      </div>
    </div>
    <div class="sns-account-list-item-control">
      <b-checkbox
        v-if="item.checkbox"
        v-model="item.selected"
        class="is-small"
        :native-value="item.type"
        :disabled="!item.active"
        @change.native="snsListSelectAccountCheck()"
      />
      <span
        v-if="item.delete"
        class="delete is-outline"
        @click="isModalActive = true"
      />
    </div>
    <b-modal :active.sync="isModalActive" :can-cancel="false" scroll="keep">
      <div class="modal-dialog">
        <div class="done">
          <div class="done-text">
            <p>{{ $t('acc_list_item.modal1.text') }}</p>
            <div class="username">
              <div class="username-icon">
                <template v-if="item.icon">
                  <div class="image is-30x30">
                    <img class="is-rounded" :src="item.icon" alt>
                    <div class="image is-20x20">
                      <img :src="snsIcon(item.type, item.active)" alt>
                    </div>
                  </div>
                </template>
                <template v-else>
                  <div class="image is-30x30">
                    <img :src="snsIcon(item.type, item.active)" alt>
                  </div>
                </template>
              </div>
              <div class="username-data">
                @{{ item.account_name }}
              </div>
            </div>
          </div>
          <div class="done-button">
            <b-button
              size="is-medium"
              type="is-primary"
              rounded
              @click="
                isModalActive = false
                isModalActive02 = true
              "
            >
                {{ $t('acc_list_item.modal1.submit') }}
            </b-button>
            <b-button
              size="is-medium"
              type="is-primary"
              rounded
              outlined
              @click="isModalActive = false"
            >
                {{ $t('acc_list_item.modal1.cancel') }}
            </b-button>
          </div>
        </div>
      </div>
    </b-modal>
    <b-modal :active.sync="isModalActive02" :can-cancel="false" scroll="keep">
      <div class="modal-dialog">
        <div class="done">
          <div class="done-text">
            <p>{{ $t('acc_list_item.modal2.text') }}</p>
            <div class="username">
              <div class="username-icon">
                <template v-if="item.icon">
                  <div class="image is-30x30">
                    <img class="is-rounded" :src="item.icon" alt>
                    <div class="image is-20x20">
                      <img :src="snsIcon(item.type, item.active)" alt>
                    </div>
                  </div>
                </template>
                <template v-else>
                  <div class="image is-30x30">
                    <img :src="snsIcon(item.type, item.active)" alt>
                  </div>
                </template>
              </div>
              <div class="username-data">
                @{{ item.account_name }}
              </div>
            </div>
          </div>
          <div class="done-button">
            <b-button
              size="is-medium"
              type="is-primary"
              rounded
              @click="
                isActive = false
                isModalActive02 = false
              "
            >
                {{ $t('acc_list_item.modal2.submit') }}
            </b-button>
            <b-button
              size="is-medium"
              type="is-primary"
              rounded
              outlined
              tag="router-link"
              :to="localePath('/material-index')"
              @click="isActive = false"
            >
                {{ $t('acc_list_item.modal2.cancel') }}
            </b-button>
          </div>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
export default {
  props: {
    item: {
      type: Object,
      default() {
        return {
          type: {
            type: String,
            required: true
          },
          icon: {
            type: String,
            required: false
          },
          account_name: {
            type: String,
            required: false
          },
          active: {
            type: Boolean,
            required: false,
            default: true
          },
          checkbox: {
            type: Boolean,
            required: false,
            default: false
          },
          selected: {
            type: Boolean,
            required: false,
            default: false
          },
          delete: {
            type: Boolean,
            required: false,
            default: false
          }
        }
      }
    }
  },
  data() {
    return {
      isActive: true,
      isModalActive: false,
      isModalActive02: false,
    }
  },
  methods: {
    snsIcon(type, active) {
      let src = ''
      if (active) {
        src = '/images/icon-' + type + '-circle.png'
      } else {
        src = '/images/icon-' + type + '-circle-off.png'
      }
      return src
    },
    snsListSelectAccountCheck() {
      if (this.item.selected) {
        this.$store.commit('snsAccountListAdd', this.item)
      } else {
        this.$store.commit('snsAccountListRemove', this.item)
      }
    }
  }
}
</script>
