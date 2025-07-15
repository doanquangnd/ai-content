<?php

namespace DoanQuang\AiContent\Contracts;

interface Connector
{
    /**
     * The name of the connector/provider.
     */
    public const NAME = 'base';

    /**
     * Send a chat message to the given model.
     *
     * @return \Generator - The streaming response from the provider
     */
    public function chat(string $model, array|string $messages, bool $stream = false): \Generator;
}
