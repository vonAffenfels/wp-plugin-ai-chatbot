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


    public function askChatbotAsync(string $text, string $relatedPostsContent) {
        return $this->engine->askChatbotAsync($text, $relatedPostsContent);
    }

    public function getClient()
    {
        return $this->engine->getClient();
    }

    public function modelIsInstalled($modelName)
    {
        return $this->engine->modelIsInstalled($modelName);
    }


    public function getEmbeddingDimensions()
    {
        return $this->engine->getEmbeddingDimensions();
    }
}
