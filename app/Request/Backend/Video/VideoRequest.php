<?php

declare(strict_types=1);

namespace App\Request\Backend\Video;

use App\Constants\Enum\TagType;
use App\Constants\Enum\VideoFinish;
use App\Constants\Enum\VideoType;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class VideoRequest extends FormRequest
{

    /**
     * @var array|array[]
     */
    protected array $scenes = [
        'finish' => [
            'finish'
        ],
        'update' => [
            'title', 'image_url', 'tags', 'tags.*',
            'actors', 'actors.*', 'tag_type', 'desc', 'num', 'rating'
        ],
        'create' => [
            'title', 'image_url', 'tags', 'tags.*',
            'actors', 'actors.*', 'tag_type', 'desc', 'num', 'rating', 'type',
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
            'title' => 'required|string|max:32',
            'image_url' => 'required|string|max:255',
            'type' => ['required', 'integer', Rule::in(VideoType::values())],
            'tags' => 'required|array|between:1,4',
            'tags.*' => 'required|integer|distinct',
            'actors' => 'required|array|between:1,2',
            'actors.*' => 'required|distinct|integer',
            'tag_type' => ['nullable', 'integer', Rule::in(TagType::values())],
            'desc' => 'required|string',
            'num' => 'required|integer|min:1',
            'rating' => 'nullable|decimal:1|between:1,10',
            'finish' => ['required', Rule::in(VideoFinish::values())]
        ];
    }
}
