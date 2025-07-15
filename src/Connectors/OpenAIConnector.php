<?php

namespace DoanQuang\AiContent\Connectors;

use DoanQuang\AiContent\Contracts\Connector;
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
        } catch (\Exception $e) {
            if ($this->isQuotaExceeded($e)) {
                yield 'Xin lỗi, tôi đã đạt giới hạn quota. Vui lòng thử lại sau.';
            } else {
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
