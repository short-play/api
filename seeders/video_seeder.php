<?php

declare(strict_types=1);

use App\Constants\Enum\TagType;
use App\Constants\Enum\VideoFinish;
use App\Constants\Enum\VideoType;
use App\Constants\Enum\VideoView;
use App\Model\Actor;
use App\Model\Tag;
use App\Model\Video;
use App\Model\VideoActor;
use App\Model\VideoItem;
use App\Model\VideoTag;
use Hyperf\Database\Seeders\Seeder;
use function Hyperf\Support\now;

class VideoSeeder extends Seeder
{
    protected array $tags;

    protected array $actor;

    public function __construct()
    {
        $this->tags = Tag::get()->toArray();
        $this->actor = Actor::get()->toArray();
    }

    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        Video::getQuery()->delete();
        VideoTag::getQuery()->delete();
        VideoActor::getQuery()->delete();
        VideoItem::getQuery()->delete();
        $path = __DIR__ . '/video_json.json';
        $videos = json_decode(file_get_contents($path), true);
        // 短剧
        $this->short($videos['short']);
        // 电影
        $this->movieOrTv($videos['movie'], VideoType::Movie);
        // tv
        $this->movieOrTv($videos['tv'], VideoType::Tv);
    }

    /**
     * 电影电视剧
     * @param array $videos
     * @param VideoType $type
     * @return void
     */
    protected function movieOrTv(array $videos, VideoType $type): void
    {
        $videoTags = [];
        $videoActor = [];
        $insertItems = [];
        $insertVideos = [];
        foreach ($videos as $key => $video) {
            $id = snowflakeId();
            $num = $type == VideoType::Movie ? 1 : genRandomInt(5, 10);
            $items = $this->items($id, $num);
            $tags = $this->addVideoTag($id);
            $actor = $this->addActorTag($id);
            $insertData = [
                'id' => $id,
                'title' => $video['title'],
                'image_url' => 'https://picsum.photos/500/700?random=' . $id,
                'type' => $type->value,
                'item_id' => $items[0]['id'],
                'desc' => $video['description'],
                'tag_type' => null,
                'num' => $num,
                'rating' => $type == VideoType::Movie ? genRandomInt(5, 9) : null,
                'is_finish' => VideoFinish::Complete->value,
                'created_at' => now(),
                'updated_at' => now()
            ];
            if ($key % 3 == 0) {
                $insertData['tag_type'] = $this->getTagType();
            }
            $insertVideos[] = $insertData;
            $insertItems = array_merge($insertItems, $items);
            $videoTags = array_merge($videoTags, $tags);
            $videoActor = array_merge($videoActor, $actor);
        }
        Video::insert($insertVideos);
        VideoTag::insert($videoTags);
        VideoActor::insert($videoActor);
        VideoItem::insert($insertItems);
    }

    /**
     * 短剧
     * @param array $videos
     * @return void
     */
    protected function short(array $videos): void
    {
        $videoTags = [];
        $insertVideos = [];
        $insertItems = [];
        foreach ($videos as $key => $video) {
            $id = snowflakeId();
            $num = genRandomInt(5, 10);
            $items = $this->items($id, $num);
            $tags = $this->addVideoTag($id);
            $insertData = [
                'id' => $id,
                'title' => $video['title'],
                'image_url' => 'https://picsum.photos/500/700?random=' . $id,
                'type' => VideoType::Short->value,
                'item_id' => $items[0]['id'],
                'desc' => $video['description'],
                'tag_type' => null,
                'num' => $num,
                'is_finish' => VideoFinish::Complete->value,
                'created_at' => now(),
                'updated_at' => now()
            ];
            if ($key % 3 == 0) {
                $insertData['tag_type'] = $this->getTagType();
            }
            $insertVideos[] = $insertData;
            $insertItems = array_merge($insertItems, $items);
            $videoTags = array_merge($videoTags, $tags);
        }
        Video::insert($insertVideos);
        VideoTag::insert($videoTags);
        VideoItem::insert($insertItems);
    }

    /**
     * videoTag关联表数据
     * @param int $videoId
     * @return array
     */
    protected function addVideoTag(int $videoId): array
    {
        $random = genRandomInt(1, 4);
        $array = array_rand($this->tags, $random);
        $array = is_array($array) ? $array : [$array];
        return array_map(function ($item) use ($videoId) {
            return [
                'id' => snowflakeid(),
                'video_id' => $videoId,
                'tag_id' => $this->tags[$item]['id'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }, $array);
    }

    /**
     * videoActor关联表数据
     * @param int $videoId
     * @return array
     */
    protected function addActorTag(int $videoId): array
    {
        $random = genRandomInt(1, 3);
        $array = array_rand($this->actor, $random);
        $array = is_array($array) ? $array : [$array];
        return array_map(function ($item) use ($videoId) {
            return [
                'id' => snowflakeid(),
                'video_id' => $videoId,
                'actor_id' => $this->actor[$item]['id'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }, $array);
    }

    /**
     * 视频列表
     * @param int $videoId
     * @param int $num
     * @return array
     */
    protected function items(int $videoId, int $num): array
    {
        $items = [];
        for ($i = 0; $i < $num; $i++) {
            $items[] = [
                'id' => snowflakeId(),
                'video_id' => $videoId,
                'sort' => $i + 1,
                'url' => $this->getUrl(),
                'duration' => genRandomInt(50, 300),
                'is_view' => VideoView::Yes->value,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        if ($num % 2 == 0) {
            $items[genRandomInt(1, $num - 1)]['is_view'] = VideoView::No->value;
        }
        foreach ($items as $item) {
            ksort($item);
        }
        return array_values($items);
    }

    /**
     * url地址
     * @return string
     * @noinspection HttpUrlsUsage
     */
    public function getUrl(): string
    {
        $int = genRandomInt(1, 10);
        return "http://aikf-test.oss-cn-beijing.aliyuncs.com/videos/duanju/short/$int.mp4";
    }

    /**
     * tagType字段
     * @return int
     */
    protected function getTagType(): int
    {
        return TagType::from(genRandomInt(1, 2))->value;
    }
}
