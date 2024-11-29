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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->string('no', 20)->unique()->comment('订单号');
            $table->bigInteger('user_id')->index()->comment('用户id');
            $table->decimal('amount')->comment('金额');
            $table->tinyInteger('status')->comment('状态');
            $table->dateTime('pay_time')->nullable()->comment('支付时间');
            $table->datetimes();
            $table->comment('订单表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
