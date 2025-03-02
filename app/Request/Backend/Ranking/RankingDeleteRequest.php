<?php

declare(strict_types=1);

namespace App\Request\Backend\Ranking;

use Hyperf\Validation\Request\FormRequest;

class RankingDeleteRequest extends FormRequest
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
            'ids' => 'required|array',
            'ids.*' => 'required|integer',
        ];
    }
}
