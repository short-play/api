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
        Schema::create('tags', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->string('value', 10)->comment('标签值');
            $table->integer('sort')->default(0)->comment('排序');
            $table->integer('search_count')->default(0)->comment('搜索次数');
            $table->datetimes();
            $table->comment('标签表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
