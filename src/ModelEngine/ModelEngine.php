<?php

namespace WP\Plugin\AIChatbot\ModelEngine;

abstract class ModelEngine
{
    public function __construct(
        private readonly string $id,
        private readonly string $description = '',
        private readonly array $shownConnectionSettings = []
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

    abstract public function generateEmbedding(string $text): ?array;

}
