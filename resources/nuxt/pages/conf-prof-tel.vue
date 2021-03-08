<template>
  <div>
    <form>
      <div class="container is-padding-top">
        <b-field
          :label="$t('prof_tel.new.label')"
          :type="{'is-danger': errors.has('mobile')}"
          :message="errors.first('mobile')"
        >
          <b-input
            :placeholder="$t('prof_tel.new.holder')"
            v-model="mobile"
            name="mobile"
            v-validate="{ required: true, regex: /^(?:\d{10}|\d{3}-\d{3}-\d{4}|\d{2}-\d{4}-\d{4}|\d{3}-\d{4}-\d{4})$/ }"
          />
        </b-field>
        <b-field
          :type="{'is-danger': errors.has('mobile_confirm')}"
          :message="[{
                    '確認用電話番号は必須です' : errors.firstByRule('mobile_confirm', 'required'),
                    '確認用電話番号が無効です' : errors.firstByRule('mobile_confirm', 'is')
                }]"
        >
          <b-input
            v-model="mobile_confirm"
            name="mobile_confirm"
            v-validate="{ required: true, is: mobile }"
            :placeholder="$t('prof_tel.confirm.holder')"
          />
        </b-field>
      </div>
      <div class="navbar is-fixed-bottom">
        <div class="container is-padding">
          <b-button
            class="is-primary is-medium"
            expanded rounded
            :disabled="disabled"
            @click="validateBeforeSubmit"
          >
            {{ $t('prof_tel.submit') }}
          </b-button>
        </div>
      </div>
    </form>

    <b-modal :active.sync="isModalActive" :can-cancel="false" scroll="keep">
      <div class="modal-dialog">
        <div class="done">
          <div class="done-figure">
            <img src="/images/done.png" alt="" class="done-figure-done">
          </div>
          <div class="done-text">
            <p>{{$t('prof_tel.modal.text')}}</p>
          </div>
          <div class="done-button">
            <b-button
              size="is-medium"
              type="is-primary"
              rounded
              outlined
              tag="router-link"
              :to="localePath('/material-index')"
            >
              {{ $t('prof_tel.modal.') }}
            </b-button>
          </div>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
export default {
  layout: 'hasmenu',
  middleware: 'authed',
  data() {
    return {
      disabled: false,
      isModalActive: false,
      // email: null,
      mobile: null,
      mobile_confirm: null
    }
  },
  methods: {
    async validateBeforeSubmit() {
      this.disabled = true
      const result = await this.$validator.validateAll()
      if (result) {
        try {
          const { data } = await this.$axios.post(
            '/change_mobile',
            {
              // email: this.email,
              mobile: this.mobile
            }
          )
          this.isModalActive = true
        } catch (e) {
          // this.errors.remove('email')
          // this.errors.add({
          //   field: 'email',
          //   msg: 'メールアドレスが無効です'
          // })
        }
      }
      this.disabled = false
    }
  }
}
</script>
