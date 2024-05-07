<?php

namespace WP\Plugin\AIChatbot\VectorDB;

abstract class VectorDB
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

    abstract public function saveEmbedding(array $embedding): ?array;

}
