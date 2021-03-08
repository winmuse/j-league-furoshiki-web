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
            <p>{{ $t('set_sns.item.modal1.text') }}</p>
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
              @click="removeSnsAccount(item.type, item.id)"
            >
                {{ $t('set_sns.item.modal1.btn_remove') }}
            </b-button>
            <b-button
              size="is-medium"
              type="is-primary"
              rounded
              outlined
              @click="isModalActive = false"
            >
                {{ $t('set_sns.item.modal1.btn_cancel') }}
            </b-button>
          </div>
        </div>
      </div>
    </b-modal>
    <b-modal :active.sync="isModalActive02" :can-cancel="false" scroll="keep">
      <div class="modal-dialog">
        <div class="done">
          <div class="done-text">
            <p v-if="isRemoveSuccess">{{ $t('set_sns.item.modal2.text1') }}</p>
            <p v-else>{{ $t('set_sns.item.modal2.text1') }}</p>
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
              @click="closeModals"
            >
              {{ $t('set_sns.item.modal2.btn_done') }}
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
          id: {
            type: Number,
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
      isRemoveSuccess: false,
    }
  },
  methods: {
    closeModals() {
      this.isActive = false
      this.isModalActive02 = false
    },
    snsIcon(type, active) {
      let src = ''
      if (active) {
        src = '/images/icon-' + type + '-circle.png'
      } else {
        src = '/images/icon-' + type + '-circle-off.png'
      }
      return src
    },
    removeSnsAccount(snsType, id) {
      this.$axios.$get(`/credential/remove/${snsType}/${id}`)
        .then(res => {
          this.isRemoveSuccess = true
        })
        .catch(err => {
          console.log(err)
          this.isRemoveSuccess = false
        })
        .then(() => {
          this.isModalActive = false
          this.isModalActive02 = true
        })
    }
  }
}
</script>
