<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\Enum\AdminRole;
use App\Constants\Enum\Agreement;
use App\Constants\Enum\Language;
use App\Constants\Enum\RankingType;
use App\Constants\Enum\TagType;
use App\Constants\Enum\VideoFinish;
use App\Constants\Enum\VideoType;
use App\Constants\Enum\VideoView;
use Psr\Http\Message\ResponseInterface;

class EnumController extends AbstractController
{

    /**
     * 语言类型
     * @return ResponseInterface
     */
    public function language(): ResponseInterface
    {
        return $this->response->success(Language::valueMessage());
    }

    /**
     * 获取协议类型
     * @return ResponseInterface
     */
    public function agreement(): ResponseInterface
    {
        return $this->response->success(Agreement::valueMessage());
    }

    /**
     * 获取榜单类型
     * @return ResponseInterface
     */
    public function ranking(): ResponseInterface
    {
        return $this->response->success(RankingType::valueMessage());
    }

    /**
     * 获取视频相关枚举
     * @return ResponseInterface
     */
    public function video(): ResponseInterface
    {
        $enum = [
            'videoType' => VideoType::valueMessage(),
            'finish' => VideoFinish::valueMessage(),
            'tagType' => TagType::valueMessage(),
            'videoView' => VideoView::valueMessage(),
        ];
        return $this->response->success($enum);
    }

    /**
     * 管理员type类型
     * @return ResponseInterface
     */
    public function adminType(): ResponseInterface
    {
        return $this->response->success(AdminRole::valueMessage());
    }
}