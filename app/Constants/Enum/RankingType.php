<?php

declare(strict_types=1);

namespace App\Constants\Enum;

use App\Constants\EnumConstantsTrait;
use App\Constants\EnumValuesTrait;
use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;

#[Constants]
enum RankingType: int
{
    use EnumConstantsTrait, EnumValuesTrait;

    // 推荐榜
    #[Message('recommended')]
    case Recommended = 1;

    // 新剧榜
    #[Message('new')]
    case New = 2;

    // 短剧热搜榜
    #[Message('short_search')]
    case ShortSearch = 3;

    // 分类热搜榜单
    #[Message('tag_search')]
    case TagSearch = 4;

    // 短剧新剧榜
    #[Message('new_short_search')]
    case NewShortSearch = 5;
}
