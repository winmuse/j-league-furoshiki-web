<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitterCredentialTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('twitter_credentials', function (Blueprint $table) {
      $table->increments('id');
      $table->bigInteger('user_id');
      $table->string('provider_id')->unique()->nullable();
      $table->string('token'); // OAuth Token
      $table->string('secret'); // OAuth Token Secret
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
    Schema::dropIfExists('twitter_credentials');
  }
}
