<?php

declare(strict_types=1);

namespace App\Request\Backend\Agreement;

use App\Constants\Enum\Agreement;
use App\Constants\Enum\Language;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class AgreementRequest extends FormRequest
{

    /**
     * @var array|array[]
     */
    protected array $scenes = [
        'detail' => ['type', 'language'],
        'create' => ['type', 'language', 'value']
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
            'type' => ['required', 'string', Rule::in(array_map(fn($v) => $v->value, Agreement::cases()))],
            'language' => ['required', 'string', Rule::in(array_map(fn($v) => $v->value, Language::cases()))],
            'value' => 'required|string',
        ];
    }
}
