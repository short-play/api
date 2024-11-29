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
        Schema::create('user_coin_detail', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->bigInteger('user_id')->comment('用户id')->index();
            $table->bigInteger('activity_id')->comment('活动id')->index();
            $table->bigInteger('activity_type')->comment('活动类型');
            $table->bigInteger('coin')->comment('金币');
            $table->datetimes();
            $table->comment('用户金币详细表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_coin_detail');
    }
};
