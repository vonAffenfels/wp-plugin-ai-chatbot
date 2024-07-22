<?php

namespace WP\Plugin\AIChatbot\ModelEngine;

use GuzzleHttp\Promise;


abstract class ModelEngine
{
    public function __construct(
        private readonly string $id,
        private readonly string $description = '',
        private readonly array $shownConnectionSettings = [],
        private readonly array $showModelSettings = []
    ) {
    }


    final public function getId(): string
    {
        return $this->id;
    }


    final public function getDescription(): string
    {
        return $this->description ?: static::class;
    }


    final public function getConnectionSettings(): array
    {
        return $this->shownConnectionSettings;
    }

    final public function getModelSettings(): array
    {
        return $this->showModelSettings;
    }

    abstract public function generateEmbedding(string $text): ?array;

    abstract public function askChatbotAsync(string $question, string $relatedPostsContent);

    abstract public function installModelIfNeeded(string $modelName);

    abstract public function modelIsInstalled(string $modelName);

    abstract public function getEmbeddingDimensions();
}
