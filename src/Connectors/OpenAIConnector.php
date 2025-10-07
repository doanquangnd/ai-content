<?php

namespace DoanQuang\AiContent\Connectors;

use DoanQuang\AiContent\Contracts\Connector;
use DoanQuang\AiContent\Helpers\AIProviderHelper;
use OpenAI\Exceptions\ErrorException;

/**
 * The Connector for the OpenAI provider
 */
class OpenAIConnector implements Connector
{
    /**
     * {@inheritDoc}
     */
    public const NAME = 'openai';

    /**
     * The OpenAI chat client
     */
    private $chat;



    /**
     * The OpenAI models client
     */
    private $models;

    /**
     * The default max tokens for completions
     */
    private int $defaultMaxTokens = 1000;

    /**
     * The default temperature for completions
     */
    private float $defaultTemperature = 0.7;

    /**
     * Create a new OpenAI connector
     */
    public function __construct($chat, $models)
    {
        $this->chat = $chat;
        $this->models = $models;
    }

    /**
     * Set the default max tokens for completions
     */
    public function withDefaultMaxTokens(int $maxTokens): self
    {
        $this->defaultMaxTokens = $maxTokens;

        return $this;
    }

    /**
     * Set the default temperature for completions
     */
    public function withDefaultTemperature(float $temperature): self
    {
        $this->defaultTemperature = $temperature;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function chat(string $model, array|string $messages, bool $stream = false): \Generator
    {
        if (!$stream) {
            throw new \InvalidArgumentException('Only streaming is supported');
        }

        return $this->chatStream($model, $messages);
    }

    /**
     * Chat with streaming response
     */
    private function chatStream(string $model, array|string $messages): \Generator
    {
        $retry = AIProviderHelper::getProviderRetryConfig('openai');
        $attempt = 0;
        $backoffMs = $retry['backoff_initial_ms'] ?? 500;

        while (true) {
            try {
                $params = [
                    'model' => $model,
                    'messages' => is_array($messages) ? $messages : [
                        ['role' => 'user', 'content' => $messages],
                    ],
                    'temperature' => $this->defaultTemperature,
                ];

                $response = $this->chat->createStreamed($params);
                foreach ($response as $chunk) {
                    if (isset($chunk->choices[0]->delta->content)) {
                        yield $chunk->choices[0]->delta->content;
                    }
                }

                return; // success
            } catch (\Exception $e) {
                $attempt++;

                if ($this->isQuotaExceeded($e) && ($retry['enabled'] ?? true) && $attempt < ($retry['max_attempts'] ?? 3)) {
                    // sleep with exponential backoff + jitter
                    $jitter = mt_rand(0, (int) ($retry['jitter_ms'] ?? 200));
                    usleep(($backoffMs + $jitter) * 1000);
                    $backoffMs = (int) max(50, $backoffMs * ($retry['backoff_factor'] ?? 2.0));
                    continue;
                }

                if ($this->isQuotaExceeded($e)) {
                    yield $e->getMessage();
                    return;
                }

                throw $e;
            }
        }
    }

    /**
     * Check if the exception is a quota exceeded error
     */
    private function isQuotaExceeded(\Exception $e): bool
    {
        if (! $e instanceof ErrorException) {
            return false;
            }

            // Kiểm tra nội dung message xem có chứa thông tin về quota không
            $message = $e->getMessage();

            return (
                stripos($message, 'quota') !== false ||
                stripos($message, 'insufficient_quota') !== false ||
                stripos($message, 'rate limit') !== false
            );
    }
}
