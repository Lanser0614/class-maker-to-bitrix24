<?php
declare(strict_types=1);

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class ClassMackerWebhookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'test.test_name' => ['nullable', 'string'],
            'result.first' => ['nullable', 'string'],
            'result.last' => ['nullable', 'string'],
            'result.email' => ['nullable', 'email'],
            'result.percentage' => ['nullable'],
            'result.extra_info_answer' => ['nullable', 'string'],
        ];
    }
}
