<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('message_counts', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('主键');
            $table->bigInteger('user_id')->comment('用户ID');
            $table->integer('reply_unread_count')->default(0)->comment('回复未读数');
            $table->integer('like_unread_count')->default(0)->comment('点赞未读数');
            $table->integer('notice_unread_count')->default(0)->comment('通知未读数');
            $table->datetimes();
            $table->index(['user_id']);
            $table->comment('消息计数表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_counts');
    }
};
