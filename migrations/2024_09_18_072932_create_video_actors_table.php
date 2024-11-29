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
        Schema::create('video_actors', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->bigInteger('video_id')->comment("视频合集id");
            $table->bigInteger('actor_id')->comment("演员id");
            $table->datetimes();
            $table->index(['video_id', 'actor_id']);
            $table->comment("视频演员关联表");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_actors');
    }
};
