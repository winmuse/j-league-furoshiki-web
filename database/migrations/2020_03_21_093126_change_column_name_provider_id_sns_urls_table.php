<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnNameProviderIdSnsUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sns_urls', function (Blueprint $table) {
            $table->renameColumn('provider_id', 'credential_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sns_urls', function (Blueprint $table) {
            $table->renameColumn('credential_id', 'provider_id');
        });
    }
}
