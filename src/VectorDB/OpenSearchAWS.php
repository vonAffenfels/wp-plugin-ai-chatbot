<?php

namespace WP\Plugin\AIChatbot\VectorDB;

use OpenSearch\Client;
use OpenSearch\ClientBuilder;
use WP\Plugin\AIChatbot\Attributes\IsVectorDB;
use WP\Plugin\AIChatbot\ModelHandler;
use WP\Plugin\AIChatbot\Settings\Connection;
use WP\Plugin\AIChatbot\Settings\PostTypes;

#[IsVectorDB(
    id: 'opensearchaws',
    description: 'OpenSearch (AWS)',
    shownConnectionSettings: [
        Connection::FIELD_HOSTS,
        Connection::FIELD_OS_AWS_REGION,
        Connection::FIELD_OS_AWS_SERVICE,
        Connection::FIELD_OS_AWS_KEY,
        Connection::FIELD_OS_AWS_SECRET
    ]
)]
class OpenSearchAWS extends OpenSearch
{
    protected function getClient(): Client
    {
        if (is_null($this->client)) {
            $clientBuilder = (new ClientBuilder())
                ->setHosts($this->connection->getHosts())
                ->setSigV4Region($this->connection->getOpenSearchAWSRegion())
                ->setSigV4Service($this->connection->getOpenSearchAWSService()->value);

            $key = $this->connection->getOpenSearchAWSKey();
            $secret = $this->connection->getOpenSearchAWSSecret();

            if (!empty($key) && !empty($secret)) {
                $clientBuilder->setSigV4CredentialProvider([
                    'key' => $key,
                    'secret' => $secret
                ]);
            } else {
                $clientBuilder->setSigV4CredentialProvider(true);
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
}
