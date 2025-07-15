<?php

namespace DoanQuang\AiContent\Providers;

require __DIR__ . '/../../vendor/autoload.php';

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Setting\PanelSections\SettingCommonPanelSection;
use Illuminate\Foundation\Application;
use OpenAI\Laravel\Facades\OpenAI;

use DoanQuang\AiContent\Connectors\ClaudeConnector;
use DoanQuang\AiContent\Connectors\GeminiConnector;
use DoanQuang\AiContent\Connectors\OpenAIConnector;
use DoanQuang\AiContent\Helpers\AIProviderHelper;
use DoanQuang\AiContent\Models\AiContent;
use DoanQuang\AiContent\Services\AIStreamService;

class AiContentServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        // Register AI Stream Service
        $this->app->singleton(AIStreamService::class, function () {
            return new AIStreamService();
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/ai-content')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['ai-content', 'permissions'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->publishAssets()
            ->loadMigrations();


        // Load components
        $this->registerCommands();
        $this->mergeOpenAIConfigurations();
        $this->configureDependencyInjection();

        $this->app->booted(function () {
            $this->app->register(\OpenAI\Laravel\ServiceProvider::class);
            $this->app->register(HookServiceProvider::class);
        });

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingCommonPanelSection::class,
                function () {
                    return PanelSectionItem::make('ai-content')
                        ->setTitle(trans('plugins/ai-content::ai-content.name'))
                        ->withIcon('ti ti-box')
                        ->withDescription(trans('plugins/ai-content::ai-content.description'))
                        ->withPriority(1001)
                        ->withRoute('ai-content.settings');
                }
            );
        });
    }
    /**
     * Register commands
     */
    private function registerCommands(): void
    {
        // Commands removed - no longer needed
    }

    /**
     * Merge configurations
     */
    private function mergeOpenAIConfigurations(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/ai-content.php', 'ai-content');

        // Override OpenAI config with our config using AIProviderHelper
        $openaiConfig = config('openai');
        $openaiConfig['api_key'] = AIProviderHelper::getApiKey('openai');
        $openaiConfig['organization'] = setting('ai_content_openai_organization', '');
        $this->app['config']->set('openai', $openaiConfig);
    }

    /**
     * Configure dependency injection
     */
    private function configureDependencyInjection(): void
    {
        /**
         * The OpenAI connector
         */
        $this->app->singleton(OpenAIConnector::class, function (Application $app) {
            return new OpenAIConnector(
                OpenAI::chat(),
                null,
                OpenAI::models()
            );
        });

        /**
         * The Gemini connector
         */
        $this->app->singleton(GeminiConnector::class, function (Application $app) {
            // GeminiConnector now uses AIProviderHelper internally
            return new GeminiConnector();
        });

        /**
         * The Claude connector
         */
        $this->app->singleton(ClaudeConnector::class, function (Application $app) {
            // ClaudeConnector now uses AIProviderHelper internally
            return new ClaudeConnector();
        });
    }
}
