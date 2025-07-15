<?php

namespace DoanQuang\AiContent\Helpers;

class StreamHelper
{
    /**
     * Create a streaming response with proper headers for EventSource
     */
    public static function createEventStreamResponse(callable $callback, string $endStreamWith = '[DONE]')
    {
        return response()->stream(function () use ($callback, $endStreamWith) {
            // Set headers for EventSource
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no'); // Disable nginx buffering
            
            // Call the callback function
            $generator = $callback();
            
            if ($generator instanceof \Generator) {
                foreach ($generator as $chunk) {
                    echo "data: " . $chunk . "\n\n";
                    ob_flush();
                    flush();
                }
            }
            
            // Send end marker
            echo "data: " . $endStreamWith . "\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
} 