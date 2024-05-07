<?php

namespace WP\Plugin\AIChatbot;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use WP\Plugin\AIChatbot\VectorDB\VectorDB;

#[AsAlias(id: "wp_plugin_ai_chatbot.vectorDB-handler", public: true)]
class VectorDBHandler
{
    public function __construct(
        private readonly VectorDB $engine,
    ) {
    }

    public function saveEmbedding(array $embedding) {
        return $this->engine->saveEmbedding($embedding);
    }
}
