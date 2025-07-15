<?php

namespace DoanQuang\AiContent\Connectors;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use DoanQuang\AiContent\Contracts\Connector;
use DoanQuang\AiContent\Helpers\AIProviderHelper;

/**
 * The Connector for the Anthropic Claude provider
 */
class ClaudeConnector implements Connector
{
    /**
     * {@inheritDoc}
     */
    public const NAME = 'claude';

    /**
     * The Claude API key
     */
    private string $apiKey;

    /**
     * The Claude base URL
     */
    private string $baseUrl;

    /**
     * The default max tokens for completions
     */
    private int $defaultMaxTokens = 4000;

    /**
     * The default temperature for completions
     */
    private float $defaultTemperature = 0.7;

    /**
     * Create a new Claude connector
     */
    public function __construct(string $apiKey = '', string $baseUrl = '')
    {
        // Use AIProviderHelper to get settings if not provided
        $this->apiKey = $apiKey ?: AIProviderHelper::getApiKey('claude');
        $this->baseUrl = $baseUrl ?: AIProviderHelper::getProviderBaseUrl('claude');
        
        // Set default values from helper
        $this->defaultMaxTokens = AIProviderHelper::getProviderMaxTokens('claude');
        $this->defaultTemperature = AIProviderHelper::getProviderTemperature('claude');
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
        // Try with fallback models if the requested model fails
        $fallbackModels = [
            $model,
            'claude-3-5-sonnet-20241022',
            'claude-3-5-haiku-20241022',
            'claude-3-opus-20240229',
            'claude-3-sonnet-20240229'
        ];

        $lastException = null;

        foreach ($fallbackModels as $tryModel) {
            try {
                yield from $this->makeStreamRequest($tryModel, $messages);
                return; // Success, exit the loop
            } catch (\Exception $e) {
                $lastException = $e;
                
                // If it's not an overload error, don't try other models
                if (!str_contains($e->getMessage(), 'overloaded') && !str_contains($e->getMessage(), '503')) {
                    throw $e;
                }
                
                Log::warning("Claude streaming model {$tryModel} failed, trying next model", [
                    'error' => $e->getMessage(),
                    'model' => $tryModel
                ]);
            }
        }

        // If all models failed, throw the last exception
        throw $lastException;
    }

    /**
     * Make a streaming request to Claude API
     */
    private function makeStreamRequest(string $model, array|string $messages): \Generator
    {
        $prompt = is_array($messages) ? $this->formatMessages($messages) : $messages;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01'
        ])->post($this->baseUrl . '/v1/messages', [
            'model' => $model,
            'max_tokens' => $this->defaultMaxTokens,
            'temperature' => $this->defaultTemperature,
            'stream' => true,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ]);

        if ($response->successful()) {
            $stream = $response->toPsrResponse()->getBody();
            
            while (!$stream->eof()) {
                $line = trim($stream->read(1024));
                
                if (str_starts_with($line, 'data: ')) {
                    $data = substr($line, 6);
                    
                    if ($data === '[DONE]') {
                        break;
                    }
                    
                    $jsonData = json_decode($data, true);
                    
                    if (isset($jsonData['delta']['text'])) {
                        yield $jsonData['delta']['text'];
                    }
                }
            }
        } else {
            $error = $response->json();
            $statusCode = $response->status();
            $errorMessage = $error['error']['message'] ?? 'Unknown error';
            
            Log::error('Claude Streaming API Error', [
                'status_code' => $statusCode,
                'error' => $error,
                'model' => $model
            ]);
            
            throw new \Exception("Anthropic Claude Streaming API Error (HTTP {$statusCode}): {$errorMessage}");
        }
    }

    /**
     * Format messages array for Claude API
     */
    private function formatMessages(array $messages): string
    {
        $formatted = '';
        
        foreach ($messages as $message) {
            $role = $message['role'] ?? 'user';
            $content = $message['content'] ?? '';
            
            if ($role === 'user') {
                $formatted .= "Human: {$content}\n\n";
            } elseif ($role === 'assistant') {
                $formatted .= "Assistant: {$content}\n\n";
            }
        }
        
        return trim($formatted);
    }


} 