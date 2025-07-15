<?php

namespace DoanQuang\AiContent\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class AiContent extends BaseModel
{
    protected $table = 'ai_content_prompts';

    protected $fillable = [
        'name',
        'prompt_content',
        'status',
    ];

    protected $casts = [
        'name' => SafeContent::class,
        'status' => BaseStatusEnum::class,
    ];
}
