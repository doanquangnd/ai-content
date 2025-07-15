<?php

namespace DoanQuang\AiContent\Services;

use DoanQuang\AiContent\Services\Strategies\AIStreamStrategy;
use DoanQuang\AiContent\Services\Strategies\OpenAIStreamStrategy;
use DoanQuang\AiContent\Services\Strategies\GeminiStreamStrategy;
use DoanQuang\AiContent\Services\Strategies\ClaudeStreamStrategy;

class AIStreamService
{
    private array $strategies = [];

    public function __construct()
    {
        $this->registerStrategies();
    }

    /**
     * Register all available streaming strategies
     */
    private function registerStrategies(): void
    {
        $this->strategies = [
            'openai' => new OpenAIStreamStrategy(),
            'gemini' => new GeminiStreamStrategy(),
            'claude' => new ClaudeStreamStrategy(),
        ];
    }

    /**
     * Stream response based on provider
     */
    public function stream(string $message, ?string $externalId, string $provider = 'gemini')
    {
        $strategy = $this->getStrategy($provider);
        
        if (!$strategy) {
            throw new \InvalidArgumentException("Unsupported AI provider: {$provider}");
        }

        return $strategy->stream($message, $externalId);
    }

    /**
     * Get strategy for specific provider
     */
    private function getStrategy(string $provider): ?AIStreamStrategy
    {
        return $this->strategies[$provider] ?? null;
    }

    /**
     * Get all available providers
     */
    public function getAvailableProviders(): array
    {
        return array_keys($this->strategies);
    }

    /**
     * Check if provider is supported
     */
    public function isProviderSupported(string $provider): bool
    {
        return isset($this->strategies[$provider]);
    }
} 