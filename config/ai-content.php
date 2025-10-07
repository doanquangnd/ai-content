<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Providers Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for all AI providers supported
    | by the LaravelAI package. Each provider can have its own settings.
    |
    */

    'providers' => [
        'gemini' => [
            'name' => 'Google Gemini',
            'enabled' => env('AI_CONTENT_GEMINI_ENABLED', true),
            'default_model' => env('AI_CONTENT_GEMINI_API_MODEL', 'gemini-2.0-flash'),
            'api_key' => env('AI_CONTENT_GEMINI_API_KEY', ''),
            'base_url' => env('AI_CONTENT_GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
            'max_tokens' => 4000,
            'temperature' => 0.7,
        ],
        
        'openai' => [
            'name' => 'OpenAI',
            'enabled' => env('AI_CONTENT_OPENAI_ENABLED', true),
            'default_model' => env('AI_CONTENT_OPENAI_API_MODEL', 'gpt-4o-mini'),
            'api_key' => env('AI_CONTENT_OPENAI_API_KEY', ''),
            'organization' => env('AI_CONTENT_OPENAI_ORGANIZATION', ''),
            'base_url' => env('AI_CONTENT_OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'max_tokens' => 1000,
            'temperature' => 0.7,
            'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),
            'retry' => [
                'enabled' => env('AI_CONTENT_OPENAI_RETRY_ENABLED', true),
                'max_attempts' => env('AI_CONTENT_OPENAI_RETRY_MAX_ATTEMPTS', 3),
                'backoff_initial_ms' => env('AI_CONTENT_OPENAI_RETRY_BACKOFF_INITIAL_MS', 500),
                'backoff_factor' => env('AI_CONTENT_OPENAI_RETRY_BACKOFF_FACTOR', 2.0),
                'jitter_ms' => env('AI_CONTENT_OPENAI_RETRY_JITTER_MS', 200),
            ],
        ],

        'claude' => [
            'name' => 'Anthropic Claude',
            'enabled' => env('AI_CONTENT_CLAUDE_ENABLED', true),
            'default_model' => env('AI_CONTENT_CLAUDE_API_MODEL', 'claude-3-5-sonnet-20241022'),
            'api_key' => env('AI_CONTENT_CLAUDE_API_KEY', ''),
            'base_url' => env('AI_CONTENT_CLAUDE_BASE_URL', 'https://api.anthropic.com'),
            'max_tokens' => 4000,
            'temperature' => 0.7,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    |
    | The default provider to use when none is specified.
    |
    */
    'default_provider' => env('AI_CONTENT_DEFAULT_PROVIDER', 'gemini'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Provider
    |--------------------------------------------------------------------------
    |
    | The fallback provider to use when the primary provider fails.
    |
    */
    'fallback_provider' => env('AI_CONTENT_FALLBACK_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Provider Priority
    |--------------------------------------------------------------------------
    |
    | The order of providers to try when multiple are available.
    |
    */
    'provider_priority' => [
        'gemini',
        'openai',
        'claude',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting settings for each provider.
    |
    */
    'rate_limiting' => [
        'enabled' => env('AI_CONTENT_RATE_LIMITING_ENABLED', true),
        'requests_per_minute' => env('AI_CONTENT_RATE_LIMIT_PER_MINUTE', 60),
        'requests_per_hour' => env('AI_CONTENT_RATE_LIMIT_PER_HOUR', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Caching settings for AI responses.
    |
    */
    'caching' => [
        'enabled' => env('AI_CONTENT_CACHING_ENABLED', false),
        'ttl' => env('AI_CONTENT_CACHE_TTL', 3600), // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Logging settings for AI requests and responses.
    |
    */
    'logging' => [
        'enabled' => env('AI_CONTENT_LOGGING_ENABLED', true),
        'level' => env('AI_CONTENT_LOG_LEVEL', 'info'),
        'log_requests' => env('AI_CONTENT_LOG_REQUESTS', true),
        'log_responses' => env('AI_CONTENT_LOG_RESPONSES', false),
    ],


];
