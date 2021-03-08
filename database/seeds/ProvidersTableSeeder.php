<?php

use Illuminate\Database\Seeder;

class ProvidersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::statement('SET FOREIGN_KEY_CHECKS=0;');

      DB::table('providers')->truncate();

      DB::table('providers')->insert([
        [ 'id' => 1, 'name' => 'facebook' ],
        [ 'id' => 2, 'name' => 'twitter' ],
        [ 'id' => 3, 'name' => 'instagram' ],
        [ 'id' => 4, 'name' => 'line' ],
      ]);

      DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
