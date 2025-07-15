<?php

namespace DoanQuang\AiContent\Connectors;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use DoanQuang\AiContent\Contracts\Connector;
use DoanQuang\AiContent\Helpers\AIProviderHelper;

/**
 * The Connector for the Google Gemini provider
 */
class GeminiConnector implements Connector
{
    /**
     * {@inheritDoc}
     */
    public const NAME = 'gemini';

    /**
     * The Gemini API key
     */
    private string $apiKey;

    /**
     * The Gemini base URL
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
     * Create a new Gemini connector
     */
    public function __construct(string $apiKey = '', string $baseUrl = '')
    {
        // Use AIProviderHelper to get settings if not provided
        $this->apiKey = $apiKey ?: AIProviderHelper::getApiKey('gemini');
        $this->baseUrl = $baseUrl ?: AIProviderHelper::getProviderBaseUrl('gemini');
        
        // Set default values from helper
        $this->defaultMaxTokens = AIProviderHelper::getProviderMaxTokens('gemini');
        $this->defaultTemperature = AIProviderHelper::getProviderTemperature('gemini');
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
     * Chat with streaming response (simulated)
     */
    private function chatStream(string $model, array|string $messages): \Generator
    {
        // Try with fallback models if the requested model fails
        $fallbackModels = [
            $model,
            'gemini-1.5-flash',
            'gemini-1.5-pro',
            'gemini-1.0-pro',
            'gemini-pro'
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
                
                // Log fallback attempt
                Log::info('Gemini model fallback (stream)', [
                    'from_model' => $model,
                    'to_model' => $tryModel,
                    'error' => $e->getMessage()
                ]);
                
                continue;
            }
        }

        // If all models failed, throw the last exception
        throw $lastException;
    }

    /**
     * Make a single streaming request
     */
    private function makeStreamRequest(string $model, array|string $messages): \Generator
    {
            $prompt = is_array($messages) ? $this->formatMessages($messages) : $messages;

        $url = $this->baseUrl . "/models/{$model}:generateContent?key={$this->apiKey}";
        $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => $this->defaultMaxTokens,
                    'temperature' => $this->defaultTemperature,
                ],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $fullContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Simulate streaming by chunking the content
                $words = explode(' ', $fullContent);
                $chunkSize = 5;
                
                for ($i = 0; $i < count($words); $i += $chunkSize) {
                    $chunk = array_slice($words, $i, $chunkSize);
                    yield implode(' ', $chunk) . ' ';
                    
                    // Small delay to simulate streaming
                    usleep(100000); // 0.1 seconds
                }
            } else {
                $error = $response->json();
            $statusCode = $response->status();
            $errorMessage = $error['error']['message'] ?? 'Unknown error';
            
            // Log detailed error for debugging
            Log::error('Gemini API Stream Error', [
                'status_code' => $statusCode,
                'error' => $error,
                'api_key_length' => strlen($this->apiKey),
                'base_url' => $this->baseUrl,
                'model' => $model
            ]);
            
            throw new \Exception("Google Gemini API Error (HTTP {$statusCode}): {$errorMessage}");
        }
    }



    /**
     * Format messages array to a single prompt string
     */
    private function formatMessages(array $messages): string
    {
        $formatted = '';
        foreach ($messages as $message) {
            $role = $message['role'] ?? 'user';
            $content = $message['content'] ?? '';
            
            if ($role === 'system') {
                $formatted .= "System: {$content}\n\n";
            } elseif ($role === 'user') {
                $formatted .= "User: {$content}\n\n";
            } elseif ($role === 'assistant') {
                $formatted .= "Assistant: {$content}\n\n";
            }
        }
        
        return trim($formatted);
    }
} 