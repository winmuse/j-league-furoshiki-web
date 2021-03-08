<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOthersMediaAwsMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medias_aws_meta', function (Blueprint $table) {
            $table->string('others')->nullable()->after('group_name')->comment('note for search');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medias_aws_meta', function (Blueprint $table) {
            $table->dropColumn('others');
        });
    }
}
