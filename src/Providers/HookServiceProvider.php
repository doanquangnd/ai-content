<?php

namespace DoanQuang\AiContent\Providers;

use Illuminate\Support\ServiceProvider;
use Botble\Base\Facades\Assets;

class HookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        add_filter(BASE_FILTER_FORM_EDITOR_BUTTONS, [$this, 'addViewButton'], 120, 1);
    }

    public function addViewButton($data)
    {
        Assets::addScriptsDirectly(['vendor/core/plugins/ai-content/js/ai-content.js?v1.0.5']);
        Assets::addStylesDirectly(['vendor/core/plugins/ai-content/css/ai-content.css']);

        return $data . view('plugins/ai-content::index')->render();
    }

}
