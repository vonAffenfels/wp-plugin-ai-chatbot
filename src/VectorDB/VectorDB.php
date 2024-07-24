<?php

namespace WP\Plugin\AIChatbot\VectorDB;

use phpunit\Unit\FunctionDirectoryTest;
use WP\Plugin\AIChatbot\Settings\PostTypes;

abstract class VectorDB
{
    public function __construct(
        private readonly PostTypes $postTypes,
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

    final public function getPostTypes(): array
    {
        return $this->postTypes->getPostTypes();
    }

    final public function getConnectionSettings(): array
    {
        return $this->shownConnectionSettings;
    }

    abstract public function saveEmbedding(int $postID, array $embedding);

    abstract public function vectorSearch($vector): ?array;

    abstract public function deleteIndex(): array;

}
