<?php

declare(strict_types=1);

namespace App\Request\Backend\Video;

use App\Constants\Enum\VideoView;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class VideoItemRequest extends FormRequest
{
    /**
     * @var array|array[]
     */
    protected array $scenes = [
        'view' => ['is_view'],
        'create' => ['items', 'items.*.url', 'items.*.duration', 'items.*.is_view'],
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
            'items' => 'required|array',
            'items.*.url' => 'required|string|max:255',
            'items.*.duration' => 'required|integer|min:1',
            'items.*.is_view' => ['required', 'integer', Rule::in(VideoView::values())],
            'is_view' => ['required', 'integer', Rule::in(VideoView::values())]
        ];
    }
}
