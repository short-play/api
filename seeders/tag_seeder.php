<?php

declare(strict_types=1);

use App\Model\Tag;
use Hyperf\Database\Seeders\Seeder;
use function Hyperf\Support\now;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        Tag::getQuery()->delete();
        $tags = [
            "悬疑推理", "古装奇案", "热血励志", "青春校园", "都市情感",
            "科幻冒险", "穿越时空", "浪漫爱情", "古装武侠", "玄幻仙侠",
            "喜剧搞笑", "家庭伦理", "警匪对决", "谍战风云", "战争史诗",
            "历史巨献", "奇幻童话", "惊悚恐怖", "灾难救援", "成长故事",
            "犯罪心理", "甜蜜恋爱", "青春偶像", "励志创业", "职场斗争",
            "文艺清新", "温情治愈", "奇幻异能", "冒险探索", "科幻未来",
            "古装言情", "黑帮情仇", "悬疑惊悚", "现实题材", "校园霸凌",
            "亲情纽带", "友情岁月", "科幻悬疑", "古装奇幻", "甜蜜日常",
            "励志成长", "职场励志", "奇幻冒险", "浪漫喜剧", "悬疑短片",
            "古装宫廷", "都市言情", "励志传奇", "科幻短片", "悬疑微剧"
        ];
        $inserts = [];
        foreach ($tags as $key => $value) {
            $inserts[] = [
                'id' => snowflakeId(),
                'sort' => $key + 1,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        Tag::insert($inserts);
    }
}
