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
        Schema::create('comment_replays', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->bigInteger('user_id')->comment('用户id');
            $table->bigInteger('comment_id')->comment('一级评论id');
            $table->bigInteger('parent_id')->comment('父评论(一级或二级评论)上一级删除则无法获取树状结构数据');
            $table->bigInteger('reply_user_id')->comment('被回复者id');
            $table->string('content', 255)->comment('回复内容');
            $table->integer('like_count')->default(0)->comment('喜欢量');
            $table->datetimes();
            $table->index(['comment_id']);
            $table->index(['user_id']);
            $table->comment('二级评论表(回复)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_replays');
    }
};
