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
        Schema::create('video_items', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->bigInteger('video_id')->comment('视频合集ID');
            $table->integer('sort')->comment('排序(集数)');
            $table->string('url', 255)->comment('视频链接');
            $table->integer('duration')->comment('时长(秒)');
            $table->integer('short_count')->default(0)->comment('点赞数量');
            $table->integer('comment_count')->default(0)->comment('评论数量');
            $table->tinyInteger('is_view')->comment('是否会员可看 0-否 1-是');
            $table->datetimes();
            $table->index(['video_id', 'sort']);
            $table->comment("视频集");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_items');
    }
};
