<?php

namespace WP\Plugin\AIChatbot\ModelEngine;

use WP\Plugin\AIChatbot\Attributes\IsModelEngine;
use WP\Plugin\AIChatbot\Settings\Connection;
use GuzzleHttp\Client;

#[IsModelEngine(
    id: 'ollama',
    description: 'Ollama Model Engine',
    shownConnectionSettings: [
        Connection::FIELD_HOST,
    ]
)]
class Ollama extends ModelEngine
{

    private ?Client $client = null;

    public function __construct(
        protected readonly Connection $connection,
        string $id,
        string $description = '',
        array $shownConnectionSettings = []
    ) {
        parent::__construct($id, $description, $shownConnectionSettings);
    }

    protected function getClient(): ?Client
    {
        $this->client = new Client([
            'base_uri' => $this->connection->getHost()
        ]);

        return $this->client;
    }

    public function generateEmbedding(string $text): ?array
    {
        return json_decode($this->getClient()->post('/api/embeddings',[
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'model' => 'nomic-embed-text',
                'prompt' => $text
            ])])->getBody()->getContents(), true);
    }
}
