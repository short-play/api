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
        Schema::create('video_views', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->bigInteger('video_id')->comment('视频合集ID');
            $table->bigInteger('item_id')->comment('视频id');
            $table->bigInteger('unique_id')->comment('用户或设备唯一标识符');
            $table->integer('num')->comment('观看集数');
            $table->integer('duration')->comment('时长(秒)');
            $table->integer('play_duration')->default(0)->comment('播放时长(秒)');
            $table->index(['unique_id', 'video_id', 'item_id']);
            $table->datetimes();
            $table->comment('视频播放记录表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_views');
    }
};
