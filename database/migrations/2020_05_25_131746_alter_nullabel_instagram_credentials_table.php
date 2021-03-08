<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNullabelInstagramCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('instagram_credentials', function (Blueprint $table) {
        $table->string('account_name')->nullable()->change();
        $table->string('name')->nullable()->change();
        $table->string('avatar')->nullable()->change();
        $table->string('page_id')->nullable()->change();
        $table->string('token')->nullable()->change();
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
