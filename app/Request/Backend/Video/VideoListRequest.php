<?php

declare(strict_types=1);

namespace App\Request\Backend\Video;

use App\Constants\Enum\VideoType;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class VideoListRequest extends FormRequest
{
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
            'title' => 'string|max:32',
            'type' => ['integer', Rule::in(VideoType::values())]
        ];
    }
}
