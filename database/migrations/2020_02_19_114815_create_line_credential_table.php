<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineCredentialTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('line_credential', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id');
      $table->string('channel_secret')->nullable();
      $table->string('access_token')->nullable();
      $table->string('url')->nullable();
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
    Schema::dropIfExists('line_credential');
  }
}
