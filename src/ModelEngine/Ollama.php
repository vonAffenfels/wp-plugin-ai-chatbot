<?php

namespace WP\Plugin\AIChatbot\ModelEngine;

use Psr\Http\Message\StreamInterface;
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
    private string $embeddingModel = 'charaf/sfr-embedding';
    private string $generationModel = 'cas/german-assistant-v7';

    public function __construct(
        protected readonly Connection $connection,
        string $id,
        string $description = '',
        array $shownConnectionSettings = []
    ) {
        parent::__construct($id, $description, $shownConnectionSettings);
        $this->installModelsIfNeeded();
    }

    public function getClient(): ?Client
    {
        $this->client = new Client([
            'base_uri' => $this->connection->getHost()
        ]);

        return $this->client;
    }

    public function generateEmbedding(string $text): ?array
    {
        return json_decode(
            $this->getClient()->post('/api/embeddings', [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'model' => $this->embeddingModel,
                    'prompt' => $text
                ])
            ])->getBody()->getContents(),
            true
        )['embedding'];
    }

    public function askChatbotAsync(string $text): StreamInterface
    {
        $response = $this->getClient()->post('/api/generate', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'model' => $this->generationModel,
                "prompt" => $text,
                'stream' => true
            ])
        ]);

        return $response->getBody();
    }

    private function installModelsIfNeeded()
    {
        $installedModels = json_decode($this->getClient()->get('/api/tags')->getBody()->getContents(), true)['models'];
        $embeddingModelInstalled = false;
        $generationModelInstalled = false;

        foreach ($installedModels as $installedModel) {
            if (str_starts_with($installedModel['name'], $this->embeddingModel)) {
                $embeddingModelInstalled = true;
            } elseif (str_starts_with($installedModel['name'], $this->generationModel)) {
                $generationModelInstalled = true;
            }
        }

        if (!$embeddingModelInstalled)
        {
            $this->getClient()->post('/api/pull', [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'name' => $this->embeddingModel,
                ])
            ]);
        }

        if (!$generationModelInstalled)
        {
            $this->getClient()->post('/api/pull', [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'name' => $this->generationModel,
                ])
            ]);
        }
    }
}
