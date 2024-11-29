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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->bigInteger('unique_id')->comment('用户或设备唯一标识符');
            $table->string('title', 30)->comment('标题');
            $table->text('desc')->comment('描述');
            $table->json('pic_json')->nullable()->comment('图片json');
            $table->string('link', 50)->comment('联系方式');
            $table->tinyInteger('status')->comment('状态');
            $table->datetimes();
            $table->index('unique_id');
            $table->comment('反馈表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
