<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterArticleMediasDeletionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_media', function (Blueprint $table) {
            $table->bigInteger('media_id')->unsigned()->change();
            $table->bigInteger('article_id')->unsigned()->change();
            $table->foreign('media_id')
                ->references('id')->on('medias')
                ->onDelete('cascade');

            $table->foreign('article_id')
                ->references('id')->on('articles')
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
        //
    }
}
