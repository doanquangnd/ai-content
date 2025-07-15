<?php

namespace DoanQuang\AiContent\Enums;

use DoanQuang\AiContent\Connectors\OpenAIConnector;
use DoanQuang\AiContent\Connectors\GeminiConnector;
use DoanQuang\AiContent\Connectors\ClaudeConnector;
use DoanQuang\AiContent\Contracts\Connector;

/**
 * This is an enumeration of all possible providers
 */
enum Provider: string
{
    /**
     * The OpenAI provider
     */
    case OpenAI = 'openai';
    
    /**
     * The Google Gemini provider
     */
    case Gemini = 'gemini';
    
    /**
     * The Anthropic Claude provider
     */
    case Claude = 'claude';
    
    // case Notion = 'notion';

    /**
     * This method returns the connector for the provider
     *
     * @return Connector The connector for the provider
     */
    public function getConnector(): Connector
    {
        return match ($this) {
            self::OpenAI => app(OpenAIConnector::class),
            self::Gemini => app(GeminiConnector::class),
            self::Claude => app(ClaudeConnector::class),
            // self::Notion => app(OpenAIConnector::class),
        };
    }
}
