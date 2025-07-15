<?php

namespace DoanQuang\AiContent\Services\Strategies;

use DoanQuang\AiContent\Enums\Provider;
use DoanQuang\AiContent\Helpers\AIProviderHelper;
use DoanQuang\AiContent\Helpers\StreamHelper;

class OpenAIStreamStrategy implements AIStreamStrategy
{
    /**
     * Stream response using OpenAI
     */
    public function stream(string $message, ?string $externalId)
    {
        $provider = Provider::from('openai');
        $connector = $provider->getConnector();
        
        // Đảm bảo externalId không null và không rỗng
        $finalExternalId = ($externalId && !empty($externalId)) ? $externalId : uniqid('openai_', true);
        
        return StreamHelper::createEventStreamResponse(function () use ($connector, $message, $finalExternalId) {
            try {
                $model = AIProviderHelper::getProviderModel('openai');
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