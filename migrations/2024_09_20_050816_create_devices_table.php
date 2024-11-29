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
        Schema::create('devices', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->string('device', 100)->unique()->comment('设备id');
            $table->tinyInteger('preference')->nullable()->comment('看剧偏好id');
            $table->datetimes();
            $table->comment('设备表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
