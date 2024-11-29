<?php

declare(strict_types=1);

use App\Constants\Enum\ActivityStatus;
use App\Constants\Enum\ActivityType;
use App\Model\Activity;
use Hyperf\Database\Seeders\Seeder;

class ActivitiesSeeder extends Seeder
{
    /**
     * 活动
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        foreach (ActivityType::cases() as $case) {
            Activity::firstOrCreate(['type' => $case->value], [
                'name' => $case->getMessage(),
                'type' => $case->value,
                'status' => ActivityStatus::Disable->value,
            ]);
        }
    }
}
