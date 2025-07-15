<?php

namespace DoanQuang\AiContent\Services\Strategies;

interface AIStreamStrategy
{
    /**
     * Stream response for the specific AI provider
     */
    public function stream(string $message, ?string $externalId);
} 