<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediasAwsMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias_aws_meta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('media_id')->unsigned();
            $table->string('event')->nullable()->comment('イベント名');
            $table->string('game')->nullable()->comment('試合名');
            $table->date('game_date')->nullable()->comment('撮影日');
            $table->string('game_place')->nullable()->comment('撮影場所');
            $table->enum('game_time', ['昼', '夜'])->nullable()->comment('昼／夜');
            $table->string('home_team')->nullable()->comment('チーム名(ホーム)');
            $table->string('away_team')->nullable()->comment('チーム名(ホーム)');
            $table->string('players')->nullable()->comment('選手名');
            $table->string('subject1')->nullable()->comment('被写体1');
            $table->string('subject2')->nullable()->comment('被写体2');
            $table->string('subject3')->nullable()->comment('被写体3');
            $table->string('state1')->nullable()->comment('状態1');
            $table->string('state2')->nullable()->comment('状態2');
            $table->string('state3')->nullable()->comment('状態3');
            $table->string('group_name')->nullable()->comment('グループ');
            $table->timestamps();

            $table->foreign('media_id')
                ->references('id')->on('medias')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medias_aws_meta');
    }
}
