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
        Schema::create('video_tags', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->bigInteger('video_id')->comment('视频id');
            $table->bigInteger('tag_id')->comment('tagId');
            $table->datetimes();
            $table->index(['video_id', 'tag_id']);
            $table->comment('视频标签关联表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_tags');
    }
};
