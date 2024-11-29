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
        Schema::create('comment_likes', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->bigInteger('user_id')->comment('用户ID');
            $table->bigInteger('comment_id')->comment('评论ID');
            $table->bigInteger('cr_id')->comment('一级评论和回复的ID');
            $table->tinyInteger('type')->comment('状态');
            $table->datetimes();
            $table->index(['user_id', 'cr_id']);
            $table->index('comment_id');
            $table->comment('评论点赞表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_likes');
    }
};
