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
        Schema::create('admins', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('管理员id');
            $table->string('mail', 40)->comment('邮箱')->unique();
            $table->string('name', 15)->comment('名称');
            $table->string('password', 255)->comment('密码');
            $table->tinyInteger('role')->comment('管理员类型');
            $table->softDeletes();
            $table->datetimes();
            $table->comment('管理员表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
