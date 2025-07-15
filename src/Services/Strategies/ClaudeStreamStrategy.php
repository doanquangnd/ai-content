<?php

namespace DoanQuang\AiContent\Services\Strategies;

use DoanQuang\AiContent\Helpers\StreamHelper;
use DoanQuang\AiContent\Enums\Provider;
use DoanQuang\AiContent\Helpers\AIProviderHelper;

class ClaudeStreamStrategy implements AIStreamStrategy
{
    /**
     * Stream response using Claude (Anthropic)
     */
    public function stream(string $message, ?string $externalId)
    {
        $provider = Provider::from('claude');
        $connector = $provider->getConnector();
        
        // Đảm bảo externalId không null và không rỗng
        $finalExternalId = ($externalId && !empty($externalId)) ? $externalId : uniqid('claude_', true);
        
        return StreamHelper::createEventStreamResponse(function () use ($connector, $message, $finalExternalId) {
            try {
                $model = AIProviderHelper::getProviderModel('claude');
                $stream = $connector->chat($model, $message, true);
                
                foreach ($stream as $chunk) {
                    yield $chunk;
                }
                
                // Send external ID at the end
                yield json_encode(['external_id' => $finalExternalId]);
                
            } catch (\Exception $e) {
                yield 'Error: ' . $e->getMessage();
            }
        });
    }
} 