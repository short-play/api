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
        Schema::create('agreements', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->string('type', 20)->comment('协议类型');
            $table->string('language', 20)->comment('语言');
            $table->text('value')->comment('协议内容');
            $table->datetimes();
            $table->index(['type', 'language']);
            $table->comment('协议表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};
