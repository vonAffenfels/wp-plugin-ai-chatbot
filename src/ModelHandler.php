<?php

namespace WP\Plugin\AIChatbot;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use WP\Plugin\AIChatbot\ModelEngine\ModelEngine;

#[AsAlias(id: "wp_plugin_ai_chatbot.model-handler", public: true)]
class ModelHandler
{
    public function __construct(
        private readonly ModelEngine $engine,
    ) {
    }

    public function generateEmbedding(string $text) {
        return $this->engine->generateEmbedding($text);
    }


    public function askChatbotAsync(string $text) {
        return $this->engine->askChatbotAsync($text);
    }

    public function getClient()
    {
        return $this->engine->getClient();
    }
}
