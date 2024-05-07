<?php

namespace WP\Plugin\AIChatbot\ModelEngine;

use WP\Plugin\AIChatbot\Attributes\IsModelEngine;
use WP\Plugin\AIChatbot\Settings\Connection;

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
}
