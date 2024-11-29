#!/bin/sh

# 安装依赖
composer install

# 执行迁移
php bin/hyperf.php migrate

# 管理员数据填充
php bin/hyperf.php db:seed --path="seeders/admin_seeder.php"

# 活动数据填充
php bin/hyperf.php db:seed --path="seeders/activities_seeder.php"

# 启动应用
composer start

# 保持容器运行
tail -f /dev/null
