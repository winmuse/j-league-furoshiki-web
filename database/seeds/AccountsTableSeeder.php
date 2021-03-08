<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountsTableSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    // DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // DB::table('users')->truncate();
    // DB::table('profiles')->truncate();

    // DB::table('users')->insert([
    //   [
    //     'id' => 1,
    //     'name' => 'ユーザー１',
    //     'email' => 'test@test.com',
    //     'password' => bcrypt('11111111'),
    //     'status' => 1
    //   ],
    // ]);

    // DB::table('profiles')->insert([
    //   [
    //     'id' => 1,
    //     'user_id' => 1,
    //     'mobile' => '0202384993'
    //   ]
    // ]);

    // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
  }
}
