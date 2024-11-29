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
        Schema::create('message_users', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('主键');
            $table->bigInteger('message_id')->comment('消息ID');
            $table->bigInteger('user_id')->comment('用户ID');
            $table->tinyInteger('status')->default(0)->comment('0未读 1已读');
            $table->datetimes();
            $table->index(['user_id', 'message_id']);
            $table->comment('消息用户关联表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_users');
    }
};
