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
        Schema::create('users', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('id');
            $table->string('mail', 40)->comment('邮箱')->unique();
            $table->string('name', 15)->comment('名称');
            $table->tinyInteger('sex')->nullable()->comment('性别 1男 2女');
            $table->string('password', 255)->comment('密码');
            $table->string('profile', 255)->nullable()->comment('头像');
            $table->string('personal_sign', 40)->nullable()->comment('个性签名');
            $table->tinyInteger('is_member')->default(0)->comment('是否会员 0否 1是');
            $table->dateTime('member_time')->nullable()->comment('会员开通时间,每次开通会更新该字段');
            $table->date('birthday')->nullable()->comment('生日');
            $table->tinyInteger('preference')->nullable()->comment('看剧偏好id');
            $table->datetimes();
            $table->softDeletes();
            $table->comment('用户表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
