<?php

declare(strict_types=1);

namespace App\Request\Backend\Ranking;

use Hyperf\Validation\Request\FormRequest;

class RankingRequest extends FormRequest
{
    public array $scenes = [
        'create' => ['items', 'items.*.id', 'items.*.sort'],
        'update' => ['sort'],
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
            'items.*.id' => 'required|integer|distinct',
            'items.*.sort' => 'required|integer',
            'sort' => 'required|integer|min:0'
        ];
    }
}
