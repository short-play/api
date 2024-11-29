<?php

declare(strict_types=1);

use App\Model\Actor;
use Hyperf\Database\Seeders\Seeder;
use function Hyperf\Support\now;

class ActorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        Actor::getQuery()->delete();
        $actors = [
            "张三丰", "李四娘", "王五郎", "赵六妹", "孙七爷",
            "周八姐", "吴九叔", "郑十妹", "陈一一", "刘二二",
            "黄三三", "张四四", "李五五", "王六六", "赵七七",
            "孙八八", "周九九", "钱十十", "关一一", "林二二",
            "秦三三", "何四四", "高五五", "郭六六", "曹七七",
            "谢八八", "许九九", "韩十十", "冯一一", "陈二二",
            "褚三三", "卫四四", "蒋五五", "沈六六", "杨七七",
            "朱八八", "秦九九", "尤十十", "许一一", "何二二",
            "吕三三", "施四四", "张五五", "孔六六", "曹七七",
            "严八八", "华九九", "金十十"
        ];
        $inserts = [];
        foreach ($actors as $value) {
            $inserts[] = [
                'value' => $value,
                'id' => snowflakeId(),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        Actor::insert($inserts);
    }
}
