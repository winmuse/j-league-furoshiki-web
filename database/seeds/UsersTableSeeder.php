<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    DB::table('admins')->truncate();

    DB::table('admins')->insert([
      [
        'id' => 1,
        'name' => '運用者（バルズ）',
        'name_short' => '',
        'name_en' => '',
        'email' => 'balz@example.com',
        'password' => bcrypt('admin'),
        'role' => 'balz'
      ],
    ]);

    DB::table('admins')->insert([
      [
        'id' => 2,
        'name' => 'Jリーグ管理者',
        'name_short' => '',
        'name_en' => '',
        'email' => 'jleague@example.com',
        'password' => bcrypt('admin'),
        'role' => 'j-league'
      ],
    ]);

    DB::table('admins')->insert([

      ["name_short" => "札幌","name" => "北海道コンサドーレ札幌","name_en" => "Hokkaido Consadole Sapporo","email"=>"club1@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "仙台","name" => "ベガルタ仙台","name_en" => "Vegalta Sendai","email"=>"club2@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "鹿島","name" => "鹿島アントラーズ","name_en" => "Kashima Antlers","email"=>"club3@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "浦和","name" => "浦和レッズ","name_en" => "Urawa Reds","email"=>"club4@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "柏","name" => "柏レイソル","name_en" => "Kashiwa Reysol","email"=>"club5@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "FC東京","name" => "ＦＣ東京","name_en" => "F.C.Tokyo","email"=>"club6@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "川崎Ｆ","name" => "川崎フロンターレ","name_en" => "Kawasaki Frontale","email"=>"club7@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "横浜FM","name" => "横浜Ｆ・マリノス","name_en" => "Yokohama F･Marinos","email"=>"club8@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "横浜FC","name" => "横浜ＦＣ","name_en" => "Yokohama FC","email"=>"club9@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "湘南","name" => "湘南ベルマーレ","name_en" => "Shonan Bellmare","email"=>"club10@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "清水","name" => "清水エスパルス","name_en" => "Shimizu S-Pulse","email"=>"club11@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "名古屋","name" => "名古屋グランパス","name_en" => "Nagoya Grampus","email"=>"club12@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "Ｇ大阪","name" => "ガンバ大阪","name_en" => "Gamba Osaka","email"=>"club13@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "Ｃ大阪","name" => "セレッソ大阪","name_en" => "Cerezo Osaka","email"=>"club14@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "神戸","name" => "ヴィッセル神戸","name_en" => "Vissel Kobe","email"=>"club15@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "広島","name" => "サンフレッチェ広島","name_en" => "Sanfrecce Hiroshima","email"=>"club58@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "鳥栖","name" => "サガン鳥栖","name_en" => "Sagan Tosu","email"=>"club16@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "大分","name" => "大分トリニータ","name_en" => "Oita Trinita","email"=>"club17@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "山形","name" => "モンテディオ山形","name_en" => "Montedio Yamagata","email"=>"club18@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "水戸","name" => "水戸ホーリーホック","name_en" => "Mito Hollyhock","email"=>"club19@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "栃木","name" => "栃木ＳＣ","name_en" => "Tochigi SC","email"=>"club20@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "群馬","name" => "ザスパクサツ群馬","name_en" => "Thespakusatsu Gunma","email"=>"club21@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "大宮","name" => "大宮アルディージャ","name_en" => "Omiya Ardija","email"=>"club22@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "千葉","name" => "ジェフユナイテッド千葉","name_en" => "JEF United Chiba","email"=>"club23@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "東京Ｖ","name" => "東京ヴェルディ","name_en" => "Tokyo Verdy","email"=>"club24@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "町田","name" => "ＦＣ町田ゼルビア","name_en" => "FC Machida Zelvia","email"=>"club25@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "甲府","name" => "ヴァンフォーレ甲府","name_en" => "Ventforet Kofu","email"=>"club26@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "松本","name" => "松本山雅ＦＣ","name_en" => "Matsumoto Yamaga F.C.","email"=>"club27@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "新潟","name" => "アルビレックス新潟","name_en" => "Albirex Niigata","email"=>"club28@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "金沢","name" => "ツエーゲン金沢","name_en" => "Zweigen Kanazawa","email"=>"club29@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "磐田","name" => "ジュビロ磐田","name_en" => "Jubilo Iwata","email"=>"club30@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "京都","name" => "京都サンガF.C", "name_en" => "Kyoto Sanga F.C.","email"=>"club31@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "岡山","name" => "ファジアーノ岡山","name_en" => "Fagiano Okayama","email"=>"club32@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "山口","name" => "レノファ山口ＦＣ","name_en" => "Renofa Yamaguchi FC","email"=>"club33@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "徳島","name" => "徳島ヴォルティス","name_en" => "Tokushima Vortis","email"=>"club34@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "愛媛","name" => "愛媛ＦＣ","name_en" => "Ehime FC","email"=>"club35@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "福岡","name" => "アビスパ福岡","name_en" => "Avispa Fukuoka","email"=>"club36@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "北九州","name" => "ギラヴァンツ北九州","name_en" => "Giravanz Kitakyushu","email"=>"club37@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "長崎","name" => "Ｖ・ファーレン長崎","name_en" => "V･Varen Nagasaki","email"=>"club38@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "琉球","name" => "ＦＣ琉球","name_en" => "FC Ryukyu","email"=>"club39@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "八戸","name" => "ヴァンラーレ八戸","name_en" => "Vanraure Hachinohe","email"=>"club40@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "岩手","name" => "いわてグルージャ盛岡","name_en" => "Iwate Grulla Morioka","email"=>"club41@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "秋田","name" => "ブラウブリッツ秋田","name_en" => "Blaublitz Akita","email"=>"club42@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "福島","name" => "福島ユナイテッドＦＣ","name_en" => "Fukushima United FC","email"=>"club43@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "YS横浜","name" => "Ｙ．Ｓ．Ｃ．Ｃ．横浜","name_en" => "Y.S.C.C. Yokohama","email"=>"club59@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "相模原","name" => "ＳＣ相模原","name_en" => "S.C. Sagamihara","email"=>"club44@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "長野","name" => "ＡＣ長野パルセイロ","name_en" => "AC Nagano Parceiro","email"=>"club45@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "富山","name" => "カターレ富山","name_en" => "Kataller Toyama","email"=>"club46@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "藤枝","name" => "藤枝ＭＹＦＣ","name_en" => "Fujieda MYFC","email"=>"club47@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "沼津","name" => "アスルクラロ沼津","name_en" => "Azul Claro Numazu","email"=>"club48@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "岐阜","name" => "ＦＣ岐阜","name_en" => "FC Gifu","email"=>"club49@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "鳥取","name" => "ガイナーレ鳥取","name_en" => "Gainare Tottori","email"=>"club50@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "讃岐","name" => "カマタマーレ讃岐","name_en" => "Kamatamare Sanuki","email"=>"club51@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "今治","name" => "ＦＣ今治","name_en" => "FC Imabari","email"=>"club52@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "熊本","name" => "ロアッソ熊本","name_en" => "Roasso Kumamoto","email"=>"club53@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "鹿児島","name" => "鹿児島ユナイテッドＦＣ","name_en" => "Kagoshima United FC","email"=>"club54@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "Ｆ東23","name" => "ＦＣ東京Ｕ－２３","name_en" => "F.C.Tokyo U-23","email"=>"club55@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "Ｇ大23","name" => "ガンバ大阪Ｕ－２３","name_en" => "Gamba Osaka U-23","email"=>"club56@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
      ["name_short" => "Ｃ大23","name" => "セレッソ大阪Ｕ－２３","name_en" => "Cerezo Osaka U-23","email"=>"club57@example.com","password"=>bcrypt("club1234"),"role"=>"club"],
    ]);


    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // $this->call(UsersTableSeeder::class);
  }
}
