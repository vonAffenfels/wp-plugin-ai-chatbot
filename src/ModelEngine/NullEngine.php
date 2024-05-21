<?php

namespace WP\Plugin\AIChatbot\ModelEngine;

use GuzzleHttp\Promise;
use WP\Plugin\AIChatbot\Attributes\IsModelEngine;

#[IsModelEngine(
    id: 'null',
    description: 'Null Model Engine (Chatbot disabled)'
)]
class NullEngine extends ModelEngine
{

    public function generateEmbedding(string $text): ?array
    {
        return null;
    }

    public function askChatbotAsync($question)
    {
        return null;
    }
}
