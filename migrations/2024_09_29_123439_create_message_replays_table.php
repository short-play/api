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
        Schema::create('message_replays', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('主键');
            $table->bigInteger('message_user_id')->comment('消息接收者id');
            $table->bigInteger('replied_user_id')->comment('被回复者id');
            $table->bigInteger('reply_user_id')->comment('回复者用户id');
            $table->bigInteger('comment_id')->comment('一级评论id');
            $table->bigInteger('reply_id')->comment('回复评论id');
            $table->bigInteger('replied_id')->comment('被回复id 如果是一级评论id也是一级评论id');
            $table->string('replied_content', 255)->comment('被回复内容');
            $table->string('reply_content', 255)->comment('回复内容');
            $table->integer('status')->default(0)->comment('0未读 1已读');
            $table->datetimes();
            $table->softDeletes();
            $table->index('message_user_id');
            $table->index('comment_id');
            $table->comment('评论回复消息表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_replays');
    }
};
