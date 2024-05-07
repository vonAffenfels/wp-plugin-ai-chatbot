<?php
/** @noinspection PhpRedundantCatchClauseInspection */

namespace WP\Plugin\AIChatbot\VectorDB;

use OpenSearch\Client;
use OpenSearch\ClientBuilder;
use WP\Plugin\AIChatbot\Attributes\IsVectorDB;
use WP\Plugin\AIChatbot\Settings\Connection;

#[IsVectorDB(
    id: 'opensearch',
    description: 'OpenSearch',
    shownConnectionSettings: [
        Connection::FIELD_HOST,
        Connection::FIELD_USER,
        Connection::FIELD_PASS,
        Connection::FIELD_DISABLE_SSL_VERIFY
    ]
)]
class OpenSearch extends VectorDB
{
    public function __construct(
        protected readonly Connection $connection,
        string $id,
        string $description = '',
        array $shownConnectionSettings = []
    ) {
        parent::__construct($id, $description, $shownConnectionSettings);
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

    public function saveEmbedding($embedding): ?array
    {
        return ['test'];
    }
}
