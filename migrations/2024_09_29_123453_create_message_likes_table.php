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
        Schema::create('message_likes', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('主键');
            $table->bigInteger('message_user_id')->comment('消息接收者id');
            $table->bigInteger('like_user_id')->comment('点赞用户id');
            $table->bigInteger('comment_id')->comment('点赞一级评论id');
            $table->bigInteger('reply_id')->comment('点赞回复id 如果是点赞的一级则评论id也是一级评论id');
            $table->string('content', 255)->comment('评论内容');
            $table->tinyInteger('status')->default(0)->comment('0未读 1已读');
            $table->softDeletes();
            $table->datetimes();
            $table->index('message_user_id');
            $table->index('comment_id');
            $table->index('like_user_id');
            $table->comment('评论点赞消息表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_likes');
    }
};
