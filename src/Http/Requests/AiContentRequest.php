<?php

namespace DoanQuang\AiContent\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AiContentRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'prompt_content' => ['required', 'string', 'max:2500'],
            'status' => ['required', Rule::in(BaseStatusEnum::values())],
        ];
    }
}
