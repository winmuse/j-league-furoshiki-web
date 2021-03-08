<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSnsToArticleProviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_provider', function (Blueprint $table) {
            $table->bigInteger('article_id')->unsigned()->change();
            $table->bigInteger('provider_id')->unsigned()->change();
            $table->string('sns')->after('article_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_provider', function (Blueprint $table) {
            $table->dropColumn('sns');
        });
    }
}
