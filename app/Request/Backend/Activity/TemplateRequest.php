<?php

declare(strict_types=1);

namespace App\Request\Backend\Activity;

use App\Constants\Enum\ActivityType;
use Hyperf\Validation\Request\FormRequest;

class TemplateRequest extends FormRequest
{

    /**
     * @var array|array[]
     */
    protected array $scenes = [
        ActivityType::Sign->name => [
            'config.days', 'config.days.*'
        ],
        ActivityType::WatchDuration->name => [
            'config.watch', 'config.watch.*.duration', 'config.watch.*.coin'
        ],
        ActivityType::AppointVideo->name => [
            'config.appoint.duration', 'config.appoint.coin',
            'config.appoint.video_ids', 'config.appoint.video_ids.*',
        ],
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'config.days' => 'required|array|size:7',
            'config.days.*' => 'required|integer:strict|min:1',
            'config.watch' => 'required|array|min:1',
            'config.watch.*.duration' => 'required|integer:strict|min:1|distinct',
            'config.watch.*.coin' => 'required|integer:strict|min:1',
            'config.appoint.duration' => 'required|integer:strict|min:1',
            'config.appoint.coin' => 'required|integer:strict|min:1',
            'config.appoint.video_ids' => 'required|array|min:1',
            'config.appoint.video_ids.*' => 'required|integer|distinct',
        ];
    }
}
