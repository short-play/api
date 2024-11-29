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
        Schema::create('activities', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->string('name', 100)->comment('标题');
            $table->string('desc', 255)->nullable()->comment('描述');
            $table->tinyInteger('type')->comment('活动类型');
            $table->tinyInteger('status')->comment('状态');
            $table->json('config')->nullable()->comment('活动配置');
            $table->datetimes();
            $table->index('type');
            $table->comment('活动表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
