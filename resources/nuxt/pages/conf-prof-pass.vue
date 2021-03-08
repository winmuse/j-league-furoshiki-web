<template>
  <div>
    <form>
      <div class="container is-padding-top">
        <b-field
          :label="$t('prof_pwd.label')"
          :type="{'is-danger': errors.has('password')}"
          :message="errors.first('password')"
        >
          <b-input
            type="password"
            :placeholder="$t('prof_pwd.new_pwd.holder')"
            v-model="password"
            name="password"
            v-validate="'required|alpha_num|min:8'"
          />
        </b-field>
        <b-field
          :type="{'is-danger': errors.has('password_confirm')}"
          :message="[{
                    'パスワードの確認フィールドは必須です' : errors.firstByRule('password_confirm', 'required'),
                    '確認パスワードが無効です' : errors.firstByRule('password_confirm', 'is')
                }]"
        >
          <b-input
            type="password"
            :placeholder="$t('prof_pwd.confirm.holder')"
            v-model="password_confirm"
            name="password_confirm"
            v-validate="{ required: true, is: password }"
          />
        </b-field>
      </div>
      <div class="navbar is-fixed-bottom">
        <div class="container is-padding">
          <b-button
            class="is-primary is-medium"
            expanded
            rounded
            :disabled="disabled"
            @click="validateBeforeSubmit"
          >
            {{ $t('prof_pwd.submit') }}
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
            <p>{{ $t('prof_pwd.modal.text') }}</p>
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
              {{ $t('prof_pwd.modal.submit')}}
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
      password: null,
      password_confirm: null
    }
  },
  methods: {
    async validateBeforeSubmit() {
      this.disabled = true
      const result = await this.$validator.validateAll()
      if (result) {
        try {
          const { data } = await this.$axios.post(
            '/change_password',
            {
              // email: this.email,
              password: this.password
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
