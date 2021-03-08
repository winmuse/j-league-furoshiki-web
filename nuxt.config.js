export default {
  srcDir: 'resources/nuxt',
  mode: 'spa',
  /*
   ** Headers of the page
   */
  head: {
    title: process.env.npm_package_name || '',
    titleTemplate: '%s FUROSHIKI',
    htmlAttrs: {
      lang: 'ja'
    },
    meta: [
      {charset: 'utf-8'},
      {
        name: 'viewport',
        content:
          'width=device-width, initial-scale=1,minimum-scale=1.0,user-scalable=no'
      },
      {
        hid: 'description',
        name: 'description',
        content: process.env.npm_package_description || ''
      }
    ],
    link: [{rel: 'icon', type: 'image/x-icon', href: '/favicon.ico'}]
  },
  /*
   ** Customize the progress-bar color
   */
  loading: {color: '#3B8070'},
  /*
   ** Global CSS
   */
  css: [
    '~/assets/css/all.scss',
    '~/assets/css/vue.scss'
  ],
  /*
   ** Plugins to load before mounting the App
   */
  plugins: [
    '~plugins/common-compornents.js',
    {src: '~/plugins/vue-slick.js', ssr: false},
    '@/plugins/axios',
    '@/plugins/day'
  ],
  /*
   ** Nuxt.js dev-modules
   */
  buildModules: [
    // Doc: https://github.com/nuxt-community/eslint-module
    '@nuxtjs/eslint-module',
    // Doc: https://github.com/nuxt-community/stylelint-module
    '@nuxtjs/stylelint-module'
  ],
  /*
   ** Nuxt.js modules
   */
  modules: [
    // Doc: https://buefy.github.io/#/documentation
    [
      'nuxt-buefy',
      {css: false, materialDesignIcons: true, defaultIconPack: 'icon-font'}
    ],
    '@nuxtjs/toast',
    // Doc: https://axios.nuxtjs.org/usage
    '@nuxtjs/axios',
    'nuxt-laravel',
    'nuxt-client-init-module',
    [
      'nuxt-validate',
      {
        lang: 'ja',
        nuxti18n: {
          locale: {
            'ja': 'ja'
          }
        }
      }
    ],
    'nuxt-i18n',
  ],
  toast: {
    position: 'top-center',
    duration: 4000,
  },
  i18n: {
    locales: ['en', 'ja'],
    defaultLocale: 'ja',
    vueI18n: {
      fallbackLocale: 'ja',
      messages: {
        ja: {
          menu: {
            logout: 'ログアウト',
            mypage: 'マイページトップ',
            material_index: '素材一覧・検索',
            post_index: '投稿一覧',
            sns_manage: 'SNSアカウント管理',
            profile: 'プロフィール管理',
            password: 'パスワード再設定',
            phone: '電話番号再設定',
            privacy: '個人情報保護方針',
            terms: '利用規約',
            search: {
              player: '選手で検索',
              keyword: 'キーワード検索',
              keyword_placeholder: 'キーワードで検索',
              date: '年月で検索',
              submit: '検索',
              all: 'すべて'
            },
          },
          auth: {
            login: {
              ID: {
                placeholder: 'メールアドレス',
                error: 'メールアドレスが無効です'
              },
              password: {
                placeholder: 'パスワード',
                error: 'パスワードが無効です'
              },
              submit_caption: 'ログイン',
              alert: {
                jwt: 'サーバーエラーが発生しました。',
                twilio: '携帯電話に確認コードを送信できません。',
                credential: 'メールアドレスまたはパスワードが無効です'
              }
            },
            verify: {
              placeholder: '確認コード 例：1234',
              error_wrong: '確認コードが正しくありません。',
              error_invalid: '確認コードが正しくありません。',
              error_expired: 'コードの有効期限が切れました。',
              submit: '確認'
            },
          },
          prof_pwd: {
            label: 'パスワード再設定',
            new_pwd: {
              holder: '新しいパスワード',
              error: ''
            },
            confirm: {
              holder: '新しいパスワード（確認用）',
              error1: 'パスワードの確認フィールドは必須です',
              error2: '確認パスワードが無効です'
            },
            submit: 'パスワードを再設定する',
            modal: {
              text: 'パスワードの再設定が完了しました！',
              submit: 'トップへ戻る'
            }
          },
          prof_tel: {
            new: {
              label: '電話番号再設定',
              holder: '新しい電話番号',
            },
            confirm: {
              label: '電話番号再設定',
              holder: '新しい電話番号（確認用）',
            },
            submit: '電話番号を再設定する',
            modal: {
              text: '電話番号の再設定が完了しました！',
              submit: 'トップへ戻る'
            }
          },
          set_line: {
            p1: 'LINE連携',
            p2: 'こちらの手順で連携をお願いします',
            l1: 'LINE公式アカウント 管理画面 にログイン',
            l2: 'お使いのアカウントを選択',
            l3: '「設定」を選択',
            l4: '「権限管理」を選択',
            l5: '「メンバー追加」を選択',
            l6: '「権限の種類」で「管理者」を選択肢、「URLを発行」を選択',
            l7: 'URLが表示されるので、下記へURLをご登録ください',
            msg: {
              require: 'URLは必須です',
              success: 'urlは正常に保存されました',
              error: 'urlを保存できませんでした。 後で試してください。',
              alert: 'まだ認証されていません。'
            },
            submit: '登録する',
          },
          set_ig: {
            msg: {
              db_error: 'データベースエラー',
              require: '必須フィールド',
              success: 'アカウントが保存されました。',
              error: 'アカウントは保存されていません。 もう一度お試しください。 ',
              alert: 'まだ認証されていません。',
              email: '正しいメールアドレスを入力してください。',
              email_error: 'InstagramユーザーIDを入力してください。',
              password_error: 'パスワードを入力してください。'
            },
            submit: 'INSTAGRAMと連携する',
            caption: 'INSTAGRAMと連携する',
            item: {
              modal1: {
                text: '連携を削除してもよろしいですか？',
                btn_remove: '連携解除',
                btn_cancel: 'やめる'
              },
              modal2: {
                text1: '連携を解除いたしました。',
                text2: '連携を解除できませんでした。',
                btn_done: 'やめる'
              }
            }
          },
          set_sns: {
            msg: {
              db_error: 'データベースエラー'
            },
            submit: 'LINEと連携する',
            caption: {
              twitter: 'twitterと連携する',
              facebook: 'FACEBOOKと連携する',
              instagram: 'instagramと連携する'
            },
            item: {
              modal1: {
                text: '連携を削除してもよろしいですか？',
                btn_remove: '連携解除',
                btn_cancel: 'やめる'
              },
              modal2: {
                text1: '連携を解除いたしました。',
                text2: '連携を解除できませんでした。',
                btn_done: 'やめる'
              }
            }
          },
          edit_post: {
            title: '投稿内容を入力',
            msg: {
              alert: 'データベースエラー',
              error: {
                content: '投稿内容を入力してください。',
                sns: 'SNSを選択してください。',
              },
              modal: {
                save: '下書きが保存されました。',
                post: '投稿が完了しました！'
              }
            },
            holder: {
              sns: 'SNSを選択',
              content: '今なにしてる？（Placeholder）',
              tag: '#ハッシュタグ',
              comment: '定型テキスト'
            },
            label: {
              tag: 'ハッシュタグ',
              comment: '定型テキスト',
              default_tag: 'デフォルトハッシュタグ'
            },
            submit: '保存する',
            caption: {
              post_index: '投稿一覧へ'
            }
          },
          new_post: {
            title: '投稿内容を入力',
            msg: {
              alert: 'データベースエラー',
              error: {
                content: '投稿内容を入力してください。',
                sns: 'SNSを選択してください。',
                multi_videos: '動画は同試合で１つしか投稿できない。',
                tw_file_select: 'メディア付きTweetには、1枚のgifまたはビデオ、または4枚までの写真が必要です。',
                ig_ln_multi_media: 'InstagramとLineでは、投稿に含めることができるビデオまたは画像は1つだけです。',
                no_sns: 'メニュー画面の「SNSアカウント管理」からSNS情報を登録してください。'
              }
            },
            holder: {
              sns: 'SNSを選択',
              content: '今なにしてる？（Placeholder）',
              tag: '#ハッシュタグ',
              comment: '定型テキスト'
            },
            label: {
              tag: 'ハッシュタグ',
              comment: '定型テキスト',
              default_tag: 'デフォルトハッシュタグ'
            },
            submit: '投稿内容を確認する'
          },
          conf_post: {
            title: '投稿内容を確認',
            message: {
              error: {
                fb_connect: 'Facebook接続エラー',
                fb_pg_register: 'Facebookページは登録されていません。',
                fb_upload: 'Facebookメディアアップロードエラー',
                fb_graph: 'FacebookグラフAPIエラー',
                fb_sdk: 'Facebook SDK エラー',
                tw_upload: 'Twitterメディアアップロードエラー',
                tw_media_format: 'アップロードしたメディアはTwitterで許可されていません。',
                tw_video_size: 'ビデオサイズは15MB以上です。',
                tw_photo_size: '写真のサイズは5MB以上です。',
                tw_file_select: '1つのビデオまたは最大4つの写真を投稿できます。',
                database: 'データベースエラー'
              },
              modal: {
                save: '下書きが保存されました。',
                post: '投稿が完了しました！'
              }
            },
            caption: {
              continue: '投稿を続ける',
              to_top: 'トップへ戻る',
              post: '投稿する',
              save: '下書きを保存'
            }
          },
          post_sns: {
            error: 'データベースエラー',
            submit: '決定する',
            title: 'SNSを選択',
            no_sns: 'メニュー画面の「SNSアカウント管理」からSNS情報を登録してください'
          },
          post_index: {
            info: '投稿された記事がありません。',
            private: '未公開',
            public: '公開',
            error: 'サーバーから投稿リストを取得できません。'
          },
          material_index: {
            error: {
              not_authenticated: 'ユーザーは認証されていません。',
              video_double: '動画は1試合で1本しか投稿できません。',
              video_posted: '動画は1試合で1本しか投稿できません。'
            },
            caption: '投稿内容の入力'
          },
          acc_list_item: {
            modal1: {
              text: '連携を削除してもよろしいですか？',
              submit: '連携解除',
              cancel: 'やめる'
            },
            modal2: {
              text: '連携を解除いたしました。',
              submit: 'SNS連携を続ける',
              cancel: 'トップへ戻る'
            }
          }
        },
        en: {
          menu: {
            logout: 'Log Out',
            mypage: 'My Page',
            material_index: 'Media List Search',
            post_index: 'Post List',
            sns_manage: 'SNS account manage',
            profile: 'Profile Manage',
            password: 'Password Reset',
            phone: 'Phone number Reset',
            privacy: 'Privacy Policy',
            terms: 'Terms of Usage',
            search: {
              player: 'Player Search',
              keyword: 'Keyword Search',
              keyword_placeholder: 'Keyword Search',
              date: 'Date Search',
              submit: 'Search',
              all: 'All'
            },
          },
          auth: {
            login: {
              ID: {
                placeholder: 'e-mail',
                error: 'email is invalid'
              },
              password: {
                placeholder: 'PASSWORD',
                error: 'Password is invalid.'
              },
              submit_caption: 'LogIn',
              alert: {
                jwt: 'Server error',
                twilio: 'Can\'t send SMS',
                credential: 'email or password is incorrect'
              }
            },
            verify: {
              placeholder: 'verify code ex) 1234',
              error_wrong: 'Wrong request sent',
              error_invalid: 'Invalid request sent',
              error_expired: 'Code expired',
              submit: 'Confirm'
            },
          },
          prof_pwd: {
            label: 'Password reset',
            new_pwd: {
              holder: 'New Password',
              error: ''
            },
            confirm: {
              holder: 'New Password(confirm)',
              error1: 'This field is required.',
              error2: 'Password is different.'
            },
            submit: 'Reset Password',
            modal: {
              text: 'Password has been reset！',
              submit: 'Goto Top Page'
            }
          },
          prof_tel: {
            new: {
              label: 'Phone Number Reset',
              holder: 'New Phone Number',
            },
            confirm: {
              label: '',
              holder: 'New Phone Number(confirm)',
            },
            submit: 'Reset Phone Number',
            modal: {
              text: 'Phone Number has been reset！',
              submit: 'To Top Page'
            }
          },
          set_line: {
            p1: 'LINE Connect',
            p2: 'Please cooperate with these steps.',
            l1: 'Login to LINE official account management page',
            l2: 'Choose your account',
            l3: 'Select 「Setting」',
            l4: 'Select 「Permission Management」',
            l5: 'Select 「Add Member」',
            l6: 'In 「Permission Type」 select 「Manager」 and select 「Publish URL」',
            l7: 'The URL will be displayed, please register the URL below',
            msg: {
              require: 'URL is required',
              success: 'url has been saved.',
              error: 'url has not been saved. Please try again.',
              alert: 'Not authenticated yet.',
            },
            submit: 'Register',
          },
          set_ig: {
            msg: {
              db_error: 'Database error',
              require: 'required field',
              success: 'Account has been saved.',
              error: 'Account has not been saved. Please try again.',
              alert: 'Not authenticated yet.',
              email: 'Input correct email address.',
              email_error: 'Input Instagram user ID.',
              password_error: 'Input password.'
            },
            submit: 'Save',
            caption: 'Connect to instagram',
            item: {
              modal1: {
                text: 'Are you sure you want to delete the connection?',
                btn_remove: 'Delete Connection',
                btn_cancel: 'Cancel'
              },
              modal2: {
                text1: 'The connection has been canceled.',
                text2: 'Connection has not been canceled.',
                btn_done: 'Close'
              }
            }
          },
          set_sns: {
            msg: {
              db_error: 'Database error'
            },
            submit: 'Connect to LINE',
            caption: {
              twitter: 'Connect to twitter',
              facebook: 'Connect to FACEBOOK',
              instagram: 'Connect to instagram'
            },
            item: {
              modal1: {
                text: 'Are you sure you want to delete the connection?',
                btn_remove: 'Delete Connection',
                btn_cancel: 'Cancel'
              },
              modal2: {
                text1: 'The connection has been canceled.',
                text2: 'Connection has not been canceled.',
                btn_done: 'Close'
              }
            }
          },
          edit_post: {
            title: 'Enter post content',
            msg: {
              alert: 'Database error',
              error: {
                content: 'Please enter your post.',
                sns: 'Please select SNS.',
              },
              modal: {
                save: 'Draft has been saved.',
                post: 'Draft has been posted.'
              }
            },
            holder: {
              sns: 'Select SNS',
              content: 'What are you doing now? (Placeholder)',
              tag: '#hashtag',
              comment: 'Boilerplate'
            },
            label: {
              tag: 'Hash tag',
              comment: 'boilerplate',
              default_tag: 'Default Hashtags'
            },
            submit: 'Save',
            caption: {
              post_index: 'To Post list'
            }
          },
          new_post: {
            title: 'New Post',
            msg: {
              alert: 'Database Error',
              error: {
                content: 'Input post content.',
                sns: 'Select SNS.',
                multi_videos: 'Only one video can be posted in the match.',
                tw_file_select: 'Tweet with media must have exactly 1 gif or video or up to 4 photos.',
                ig_ln_multi_media: 'In Instagram and line, only one video or image can be included in a post.',
                no_sns: 'Please register SNS information from "SNS account management" on the menu.'
              }
            },
            holder: {
              sns: 'Select SNS',
              content: 'What are you doing now? (Placeholder)',
              tag: '#hashtag',
              comment: 'boilerplate'
            },
            label: {
              tag: 'Hash Tag',
              comment: 'Boilerplate',
              default_tag: 'Default Hash Tag'
            },
            submit: 'Confirm Post'
          },
          conf_post: {
            title: 'Confirm Post',
            message: {
              error: {
                fb_connect: 'Facebook connection error',
                fb_pg_register: 'Facebook page registration error',
                fb_upload: 'Facebook file upload error',
                fb_graph: 'Facebook graph API error',
                fb_sdk: 'Facebook SDK error',
                tw_upload: 'Twitter file upload error',
                tw_media_format: 'The media you upload is not allowed on Twitter.',
                tw_video_size: 'The video size is over 15Mb.',
                tw_photo_size: 'The photo size is over 5Mb.',
                tw_file_select: 'only a video or up to 4 photos can br posted.',
                database: 'Database error'
              },
              modal: {
                save: 'Draft has been saved.',
                post: 'Post completed!'
              }
            },
            caption: {
              continue: 'Continue post',
              to_top: 'to Top page',
              post: 'Post',
              save: 'Save'
            }
          },
          post_sns: {
            error: 'Database Error',
            submit: 'Confirm',
            title: 'SNS Select',
            no_sns: 'Register SNS information from "SNS account management" on the menu screen.'
          },
          post_index: {
            info: 'There are no posted articles.',
            private: 'Draft',
            public: 'Posted',
            error: 'Can\'t get post list from server.'
          },
          material_index: {
            error: {
              not_authenticated: 'User is not authenticated.',
              video_double: 'Only one video can be posted per game.',
              video_posted: 'Only one video can be posted per game.'
            },
            caption: 'Write post content'
          },
          acc_list_item: {
            modal1: {
              text: 'Are you sure you want to delete the connection?',
              submit: 'Delete Connection',
              cancel: 'Cancel'
            },
            modal2: {
              text: 'The connection has been canceled.',
              submit: 'Continue SNS Connect',
              cancel: 'to Top Page'
            }
          }
        }
      }
    }
  },
  router: {
    middleware: [
      // 'check-auth'
    ]
  },
  /*
   ** Axios module configuration
   ** See https://axios.nuxtjs.org/options
   */
  axios: {
    // proxyHeaders: false
    baseURL: 'https://stg.forathlete.jp/api',
    // baseURL: 'http://127.0.0.1:8000/api',
  },
  /*
   ** Build configuration
   */
  build: {
    extend: (config, ctx) => {
      // Run ESLint on save
      if (ctx.isDev && ctx.isClient) {
        config.module.rules.push({
          enforce: 'pre',
          test: /\.(js|vue)$/,
          loader: 'eslint-loader',
          exclude: /(node_modules)/
        })
      }
    }
  }
}
