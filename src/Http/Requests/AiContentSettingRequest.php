<?php

namespace DoanQuang\AiContent\Http\Requests;

use Botble\Support\Http\Requests\Request;

class AiContentSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'ai_content_openai_api_key' => ['nullable', 'string', 'max:250'],
            'ai_content_openai_api_model' => ['nullable', 'string', 'max:250'],
            'ai_content_openai_organization' => ['nullable', 'string', 'max:250'],
            'ai_content_gemini_api_key' => ['nullable', 'string', 'max:250'],
            'ai_content_gemini_api_model' => ['nullable', 'string', 'max:250'],
            'ai_content_claude_api_key' => ['nullable', 'string', 'max:250'],
            'ai_content_claude_api_model' => ['nullable', 'string', 'max:250'],
        ];
    }
}
