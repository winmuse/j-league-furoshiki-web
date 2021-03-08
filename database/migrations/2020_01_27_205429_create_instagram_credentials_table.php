<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstagramCredentialsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('instagram_credentials', function (Blueprint $table) {
      $table->increments('id');
      $table->bigInteger('user_id');
      $table->string('fb_id')->unique()->nullable();
      $table->string('ig_user_id')->unique()->nullable();
      $table->string('ig_business_id')->unique()->nullable();
      $table->string('account_name'); // account name in FB
      $table->string('name'); // name in FB
      $table->string('avatar', 1024); // avatar in FB
      $table->string('page_id'); // id of connected facebook page
      $table->string('token'); // OAuth Token
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('instagram_credentials');
  }
}
