<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Command;

use App\Constants\Enum\TagType;
use App\Constants\Enum\VideoFinish;
use App\Constants\Enum\VideoType;
use App\Model\Actor;
use App\Model\MessageCount;
use App\Model\Tag;
use App\Model\User;
use App\Model\UserCoin;
use App\Model\Video;
use App\Model\VideoActor;
use App\Model\VideoTag;
use App\Service\ManageVideoService;
use FFMpeg\FFMpeg;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\Validation\Rule;
use function Hyperf\Config\config;
use function Hyperf\Support\now;

#[Command]
class InsertDefaultDataCommand extends HyperfCommand
{
    #[Inject]
    private FilesystemFactory $filesystemFactory;

    #[Inject]
    protected ManageVideoService $manageVideoService;

    /**
     * 执行的命令行.
     */
    protected ?string $name = 'init:db';


    public function handle()
    {
        $path = BASE_PATH . '/runtime/video';


        $dirs = scandir($path);

        $oss = $this->filesystemFactory->get('oss');

        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            $files = scandir($path . '/' . $dir);

            sort($files, 1);
//
//            $exists = Video::query()->where('type', 1)->where('title', $dir)->exists();
//            if ($exists) {
//                continue;
//            }

            $coverFilePath = '';

            // 获取图片文件
            foreach ($files as $file) {
                // 获取文件后缀名
                $fileInfo = pathinfo($path . '/' . $dir . '/' . $file);

                $extension = $fileInfo['extension'];
                if (in_array($extension, ['jpg', 'png', 'jpeg', 'gif'])) {
                    $coverFilePath = $path . '/' . $dir . '/' . $file;
                    break;
                }
            }

            $imageUrl = 'https://short-play-ai.oss-us-east-1.aliyuncs.com/video_cover/0.jpg';

            if (!empty($coverFilePath)) {
                $fileNamePath = 'videos/' . date('YmdHis') . rand(99999, 11111) . $file;

                $oss->write($fileNamePath, file_get_contents($filePath));

                $imageUrl = 'https://' . config('file.storage.oss.bucket') . '.' . config('file.storage.oss.endpoint') . '/' . $fileNamePath;
            }

            $tags = Tag::query()->limit(rand(2, 5))->pluck('id')->toArray();
            $actors = Actor::query()->limit(rand(2, 5))->pluck('id')->toArray();

            Db::beginTransaction();
            try {
                $create = Video::create([
                    'title' => $dir,
                    'image_url' => $imageUrl,
                    'type' => 1,
                    'desc' => $dir,
                    'num' => count($files),
                    'finish' => 1
                ]);

                VideoTag::batchInsertVideoTags($create->id, $tags);
                VideoActor::batchInsertVideoActors($create->id, $actors);

                Db::commit();
            } catch (\Throwable $throwable) {
                Db::rollBack();
                continue;
            }


            foreach ($files as $file) {
                $filePath = $path . '/' . $dir . '/' . $file;

                if ($file === '.' || $file === '..') {
                    continue;
                }

                $ffmpeg = FFMpeg::create();

                $video = $ffmpeg->open($filePath);

                $length = $video->getStreams()->videos()->first()->get('duration');

                $fileNamePath = 'videos/' . date('YmdHis') . rand(99999, 11111) . $file;

                $oss->write($fileNamePath, file_get_contents($filePath));

                $fileUrl = 'https://' . config('file.storage.oss.bucket') . '.' . config('file.storage.oss.endpoint') . '/' . $fileNamePath;

                $this->manageVideoService->videoItemCreate($create->id, [
                    [
                        'url' => $fileUrl,
                        'duration' => floor(floatval($length)),
                        'is_view' => 1,
                    ]
                ]);

                stdLog()->info($file . '-上传完成');
            }
        }


    }
}
