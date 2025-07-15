<?php

namespace DoanQuang\AiContent;

use Illuminate\Support\Facades\Schema;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('ai_content_prompts');
    }

    public static function activate(): void
    {
        // Run seeder to create default prompts
        $seeder = new \DoanQuang\AiContent\Database\Seeders\AiContentSeeder();
        $seeder->run();
    }
}
