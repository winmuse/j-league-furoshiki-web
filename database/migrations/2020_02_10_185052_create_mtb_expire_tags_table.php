<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMtbExpireTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mtb_expire_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');         // 不定期ハッシュタグ
            $table->enum('type', ['j-league', 'club'])->default('j-league');
            $table->timestamp('expire_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mtb_expire_tags');
    }
}
