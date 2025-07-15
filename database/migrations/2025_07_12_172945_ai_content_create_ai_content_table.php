<?php

use Botble\Base\Enums\BaseStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('ai_content_prompts')) {
            Schema::create('ai_content_prompts', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->text('prompt_content');
                $table->string('status',60)->default(BaseStatusEnum::PUBLISHED);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_content_prompts');
    }
};
