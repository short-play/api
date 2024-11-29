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
        Schema::create('video_likes', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->bigInteger('video_id')->comment('视频合集id');
            $table->bigInteger('item_id')->comment('视频id');
            $table->bigInteger('unique_id')->comment('设备或用户唯一标识');
            $table->index(['unique_id', 'video_id', 'item_id']);
            $table->comment("点赞表");
            $table->datetimes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_shorts');
    }
};
