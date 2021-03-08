<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFacebookCredentialsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('facebook_credentials', function (Blueprint $table) {
      $table->string('account_name'); // account name in FB
      $table->string('name'); // name in FB
      $table->string('avatar', 1024); // avatar in FB
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    //
  }
}
