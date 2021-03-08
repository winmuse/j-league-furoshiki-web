<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFailedJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->text('connection')->comment('接続');
            $table->text('queue')->commnet('ジョブキュー');
            $table->longText('payload')->comment('データ');
            $table->longText('exception')->comment('例外');
            $table->timestamp('failed_at')->useCurrent()->comment('失敗日時');
        });

        DB::statement("ALTER TABLE `failed_jobs` COMMENT 'ジョブ失敗ログ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('failed_jobs');
    }
}
