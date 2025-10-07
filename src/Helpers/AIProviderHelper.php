<?php

namespace DoanQuang\AiContent\Helpers;

class AIProviderHelper
{
    /**
     * Get API key for a specific provider
     */
    public static function getApiKey(string $provider): string
    {
        return match ($provider) {
            'openai' => (string) (setting('ai_content_openai_api_key', '') ?? ''),
            'gemini' => (string) (setting('ai_content_gemini_api_key', '') ?? ''),
            'claude' => (string) (setting('ai_content_claude_api_key', '') ?? ''),
            default => '',
        };
    }

    /**
     * Check if a provider is available (has API key)
     */
    public static function isProviderAvailable(string $provider): bool
    {
        return !empty(self::getApiKey($provider));
    }

    /**
     * Get all available providers
     */
    public static function getAvailableProviders(): array
    {
        $providers = [];

        if (self::isProviderAvailable('gemini')) {
            $providers[] = 'gemini';
        }
        
        if (self::isProviderAvailable('openai')) {
            $providers[] = 'openai';
        }

        if (self::isProviderAvailable('claude')) {
            $providers[] = 'claude';
        }

        return $providers;
    }

    /**
     * Get default provider (first available provider)
     */
    public static function getDefaultProvider(): string
    {
        $availableProviders = self::getAvailableProviders();
        return $availableProviders[0] ?? 'gemini';
    }

    /**
     * Get provider configuration from config
     */
    public static function getProviderConfig(string $provider): array
    {
        return config("ai-content.providers.{$provider}", []);
    }

    /**
     * Get retry configuration for a provider
     */
    public static function getProviderRetryConfig(string $provider): array
    {
        $config = self::getProviderConfig($provider);
        $retry = $config['retry'] ?? [];

        return [
            'enabled' => (bool) ($retry['enabled'] ?? true),
            'max_attempts' => (int) ($retry['max_attempts'] ?? 3),
            'backoff_initial_ms' => (int) ($retry['backoff_initial_ms'] ?? 500),
            'backoff_factor' => (float) ($retry['backoff_factor'] ?? 2.0),
            'jitter_ms' => (int) ($retry['jitter_ms'] ?? 200),
        ];
    }

    /**
     * Get provider setting by key
     */
    public static function getProviderSetting(string $provider, string $key, $default = null)
    {
        $config = self::getProviderConfig($provider);
        return $config[$key] ?? $default;
    }

    /**
     * Get provider model from database settings
     */
    public static function getProviderModel(string $provider): string
    {
        $model = match ($provider) {
            'openai' => setting('ai_content_openai_api_model'),
            'gemini' => setting('ai_content_gemini_api_model'),
            'claude' => setting('ai_content_claude_api_model'),
            default => null,
        };
        
        // Only fallback to default if database returns null
        if ($model === null) {
            return match ($provider) {
                'openai' => 'gpt-4o-mini',
                'gemini' => 'gemini-2.0-flash',
                'claude' => 'claude-3-5-sonnet-20241022',
                default => 'gpt-4o-mini',
            };
        }
        
        return $model;
    }

    /**
     * Get provider max tokens
     */
    public static function getProviderMaxTokens(string $provider): int
    {
        return self::getProviderSetting($provider, 'max_tokens', 1000);
    }

    /**
     * Get provider temperature
     */
    public static function getProviderTemperature(string $provider): float
    {
        return self::getProviderSetting($provider, 'temperature', 0.7);
    }

    /**
     * Get provider base URL
     */
    public static function getProviderBaseUrl(string $provider): string
    {
        return (string) (self::getProviderSetting($provider, 'base_url', '') ?? '');
    }

    /**
     * Get provider name for display
     */
    public static function getProviderName(string $provider): string
    {
        return match ($provider) {
            'openai' => 'OpenAI',
            'gemini' => 'Google Gemini',
            'claude' => 'Anthropic Claude',
            default => ucfirst($provider),
        };
    }

    /**
     * Get OpenAI organization from database settings
     */
    public static function getOpenAIOrganization(): string
    {
        return (string) (setting('ai_content_openai_organization', '') ?? '');
    }

    /**
     * Check if any provider is available
     */
    public static function hasAnyProvider(): bool
    {
        return !empty(self::getAvailableProviders());
    }
}
