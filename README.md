<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

## About Laravel Skeleton

各プロジェクトの共通フレームワーク

- Laravel6.2

## 主な変更点

- App\Models を作成し、モデルはapp/Modelsディレクトリに配置
- migrationにおいて、テーブル・カラムにコメントを追加
- コントローラ・モデルに基底クラスを作成（未対応）

## Gitコミットコメント

- https://qiita.com/itosho/items/9565c6ad2ffc24c09364

## Install

- clone repository

    git clone https://github.com/aidiot-inc/j-league-furoshiki.git

- install dependencies

    npm install
    
    composer install

- set up .env file

    FACEBOOK_CLIENT_ID={your facebook app ID}
    
    FACEBOOK_CLIENT_SECRET={your facebook app secret}
    
    FACEBOOK_DEFAULT_GRAPH_VERSION=2.12
    
    FACEBOOK_REDIRECT={your domain}/login/facebook/callback
    
    TWITTER_CONSUMER_KEY={your twitter app ID}
    
    TWITTER_CONSUMER_SECRET={your twitter app secret}
    
    TWITTER_ACCESS_TOKEN=
    
    TWITTER_ACCESS_TOKEN_SECRET=
    
    TWITTER_REDIRECT={your domain}/login/twitter/callback
        
    INSTAGRAM_CLIENT_ID={your instagram app ID}
    
    INSTAGRAM_CLIENT_SECRET={your instagram app secret}
    
    INSTAGRAM_REDIRECT={your domain}/login/instagram/callback
    
    TWILIO_AUTH_TOKEN={twilio app ID}
    
    TWILIO_ACCOUNT_SID={twilio app secret}
    
    TWILIO_NUMBER={twilio number}
    
- publish App key and Jwt secret key

    php artisan key:generate
    
    php artisan jwt:secret

- deploy SPA

    npm run build
    
    Packed JS files are deployed into /public/_nuxt folder.
    
- First steps for using default Admin accounts

    seed file has two admin accounts
    
    name: 運用者（バルズ） email: balz@example.com pwd: admin
    
    name: Jリーグ管理者 email: jleague@example.com pwd: admin
    
    Using Jリーグ管理者 accounts, create clubs
    
    In each clubs, one should set dropbox settings(App secret and folder) and create users
    
    
    
    
