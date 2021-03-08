<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable()->comment('user ID');
            $table->string('access_token')->nullable()->comment('access token for verify');
            $table->string('pin')->nullable()->comment('PIN code sent via SMS');
            $table->string('token', 1024)->nullable()->comment('JWT token');
            $table->dateTime('expired_at')->nullable()->comment('time when pin code expired');
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
        Schema::dropIfExists('login_tokens');
    }
}
