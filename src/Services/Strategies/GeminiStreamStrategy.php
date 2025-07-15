<?php

namespace DoanQuang\AiContent\Services\Strategies;

use DoanQuang\AiContent\Enums\Provider;
use DoanQuang\AiContent\Helpers\StreamHelper;

class GeminiStreamStrategy implements AIStreamStrategy
{
    /**
     * Stream response using Gemini
     */
    public function stream(string $message, ?string $externalId)
    {
        $provider = Provider::from('gemini');
        $connector = $provider->getConnector();
        
        // Đảm bảo externalId không null và không rỗng
        $finalExternalId = ($externalId && !empty($externalId)) ? $externalId : uniqid('gemini_', true);
        
        return StreamHelper::createEventStreamResponse(function () use ($connector, $message, $finalExternalId) {
            try {
                $model = \DoanQuang\AiContent\Helpers\AIProviderHelper::getProviderModel('gemini');
                $stream = $connector->chat($model, $message, true);
                foreach ($stream as $chunk) {
                    yield $chunk;
                }
                
                yield json_encode(['external_id' => $finalExternalId]);
            } catch (\Exception $e) {
                yield 'Error: ' . $e->getMessage();
            }
        });
    }
} 