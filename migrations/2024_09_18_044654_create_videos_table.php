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
        Schema::create('videos', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->string('title', 32)->comment('视频合集标题');
            $table->string('image_url', 255)->comment('视频合集封面');
            $table->tinyInteger('type')->comment('分类id');
            $table->tinyInteger('tag_type')->nullable()->comment('新剧、爆剧tag等类型');
            $table->bigInteger('item_id')->nullable()->comment('视频id（设置首页推荐播放第几集默认第一集）');
            $table->text('desc')->nullable()->comment('视频合集描述');
            $table->bigInteger('preference')->nullable()->comment('用户偏好id');
            $table->integer('num')->default(0)->comment('集数');
            $table->integer('play_count')->default(0)->comment('播放量');
            $table->integer('collect_count')->default(0)->comment('收藏量');
            $table->integer('search_count')->default(0)->comment('搜索量');
            $table->bigInteger('interact_count')->default(0)->comment('总互动量(计算出来的)');
            $table->integer('is_cat')->default(0)->comment('是否切片 0为否 1为是');
            $table->tinyInteger('is_finish')->default(0)->comment('是否完成 0为否 1为是');
            $table->decimal('rating', 3, 1)->nullable()->comment('评分');
            $table->datetimes();
            $table->softDeletes();
            $table->index('type');
            $table->comment('视频合集表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
