<?php

namespace WP\Plugin\AIChatbot\ModelEngine;

use Psr\Http\Message\StreamInterface;
use WP\Plugin\AIChatbot\Attributes\IsModelEngine;
use WP\Plugin\AIChatbot\Settings\Connection;
use GuzzleHttp\Client;
use WP\Plugin\AIChatbot\Settings\Models;

use function PHPUnit\Framework\stringContains;

#[IsModelEngine(
    id: 'ollama',
    description: 'Ollama Model Engine',
    shownConnectionSettings: [
        Connection::FIELD_HOST,
    ],
    showModelSettings: [
        Models::FIELD_EMBEDDING_MODEL,
        Models::FIELD_GENERATION_MODEL
    ]
)]
class Ollama extends ModelEngine
{

    private ?Client $client = null;

    public function __construct(
        protected readonly Connection $connection,
        protected readonly Models $models,
        string $id,
        string $description = '',
        array $shownConnectionSettings = [],
        array $showModelSettings = []
    ) {
        parent::__construct($id, $description, $shownConnectionSettings, $showModelSettings);
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
                    'model' => $this->models->getEmbeddingModel(),
                    'prompt' => $text
                ])
            ])->getBody()->getContents(),
            true
        )['embedding'];
    }

    public function askChatbotAsync(string $question, $relatedPostsContent): StreamInterface
    {
        $response = $this->getClient()->post('/api/generate', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'model' => $this->models->getGenerationModel(),
                "prompt" =>
                    'Beantworte mir ' .
                    $question .
                    ' auf Basis dieser Informationen ' .
                    $relatedPostsContent .
                    ' in 3 einfachen Sätzen unbedingt auf Deutsch',

                'stream' => true
            ])
        ]);

        return $response->getBody();
    }

    public function installModelIfNeeded(string $modelName)
    {
        if (!$this->modelIsInstalled($modelName)) {
            $pullResponse = $this->getClient()->post('/api/pull', [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'name' => $modelName,
                ])
            ]);
        }

        $response = $pullResponse->getBody()->getContents();

        if (stringContains($response, 'error'))
        {
            return [
                "error" => true,
                'response' => $response
            ];
        }

        return [
            'success' => true,
            'response' => $response
        ];

    }

    public function modelIsInstalled(string $modelName)
    {
        $installedModels = json_decode($this->getClient()->get('/api/tags')->getBody()->getContents(), true)['models'];
        $modelIsInstalled = false;

        foreach ($installedModels as $installedModel) {
            if (str_starts_with($installedModel['name'], $modelName)) {
                $modelIsInstalled = true;
            }
        }

        return $modelIsInstalled;
    }

    public function getEmbeddingDimensions()
    {
        return null;
    }
}
