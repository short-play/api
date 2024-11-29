<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->tinyInteger('ranking_type')->comment('榜单type类型');
            $table->bigInteger('unique_id')->comment('目前是视频id和标签tagId');
            $table->integer('sort')->comment('排序');
            $table->index('ranking_type');
            $table->datetimes();
            $table->comment('榜单关联视频或标签表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};
