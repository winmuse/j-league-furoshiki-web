<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('admins', function (Blueprint $table) {
      $table->bigIncrements('id')->comment('ID');
      $table->string('name')->comment('名前');
      $table->string('email')->unique()->comment('メールアドレス');
      $table->string('password')->comment('パスワード');
      $table->rememberToken();
      $table->timestamps();
    });

    DB::statement("ALTER TABLE `admins` COMMENT 'アカウント'");
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('admins');
  }
}
