<?php
/** @noinspection PhpRedundantCatchClauseInspection */

namespace WP\Plugin\AIChatbot\VectorDB;

use OpenSearch\Client;
use OpenSearch\ClientBuilder;
use WP\Plugin\AIChatbot\Attributes\IsVectorDB;
use WP\Plugin\AIChatbot\ModelHandler;
use WP\Plugin\AIChatbot\Settings\Connection;
use WP\Plugin\AIChatbot\Settings\PostTypes;

#[IsVectorDB(
    id: 'opensearch',
    description: 'OpenSearch',
    shownConnectionSettings: [
        Connection::FIELD_HOST,
        Connection::FIELD_USER,
        Connection::FIELD_PASS,
        Connection::FIELD_DISABLE_SSL_VERIFY,
        Connection::FIELD_INDEX
    ]
)]
class OpenSearch extends VectorDB
{
    public function __construct(
        protected readonly Connection $connection,
        protected readonly PostTypes $postTypes,
        string $id,
        string $description = '',
        array $shownConnectionSettings = [],
    ) {
        parent::__construct($postTypes, $id, $description, $shownConnectionSettings);
    }

    protected ?Client $client = null;

    protected function getClient(): Client
    {
        if (is_null($this->client)) {
            $clientBuilder = (new ClientBuilder())
                ->setHosts($this->connection->getHosts())
                ->setSSLVerification(!$this->connection->getDisableSslVerify());

            $user = $this->connection->getUser();
            $pass = $this->connection->getPass();

            if (!empty($user) && !empty($pass)) {
                $clientBuilder->setBasicAuthentication($user, $pass);
            }

            $this->client = $clientBuilder->build();
        }

        return $this->client;
    }

    public function saveEmbedding($postID, $embedding): callable|array
    {
        $this->createIndexIfNotExisting();
        return $this->getClient()->update([
            'index' => $this->connection->getIndex(),
            'refresh' => true,
            'body' => [
                'doc' => [
                    'embedding' => $embedding,
                    'postID' => $postID
                ],
                'doc_as_upsert' => true
            ],
            'id' => $postID
        ]);
    }

    private function createIndexIfNotExisting(): void
    {
        if (!$this->indexExists()) {
            $this->getClient()->indices()->create([
                'index' => $this->connection->getIndex(),
                'body' => [
                    'settings' => [
                        "index.knn" => true
                    ],
                    "mappings" => [
                        "properties" => [
                            "embedding" => [
                                "type" => "knn_vector",
                                "dimension" => 768
                            ],
                            "postID" => [
                                "type" => "text"
                            ]
                        ]
                    ]
                ]
            ]);
        }
    }


    private function indexExists(): bool
    {
        return $this->getClient()->indices()->exists([
            'index' => $this->connection->getIndex(),
        ]);
    }

    public function vectorSearch($vector): ?array
    {
        $docs = $this->getClient()->search([
            'index' => $this->connection->getIndex(),
            'size' => 3,
            'body' => [
                'query' => [
                    "knn" => [
                         "embedding" => [
                             "vector"=> $vector,
                             "k" => 3
                         ]
                    ]
                ],
                "fields"=> [
                    "postID"
                    ]
            ]
        ]);
        return $docs;
    }


    public function deleteIndex(): array
    {
        return $this->getClient()->indices()->delete([
            'index' => $this->connection->getIndex(),
        ]);
    }
}
