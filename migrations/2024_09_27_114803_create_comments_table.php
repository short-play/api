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
        Schema::create('comments', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->bigInteger('video_id')->comment('视频合集ID');
            $table->bigInteger('item_id')->comment('视频id');
            $table->bigInteger('user_id')->comment('用户id');
            $table->string('content', 255)->comment('评论内容');
            $table->integer('reply_count')->default(0)->comment('二级评论数量');
            $table->integer('like_count')->default(0)->comment('喜欢量');
            $table->integer('interaction_count')->default(0)->comment('二级评论量+喜欢量');
            $table->index(['video_id', 'item_id']);
            $table->index(['user_id']);
            $table->comment('评论表');
            $table->datetimes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
