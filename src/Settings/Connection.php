<?php

namespace WP\Plugin\AIChatbot\Settings;

use VAF\WP\Framework\Setting\Attribute\AsSettingContainer;
use VAF\WP\Framework\Setting\EnvAwareSetting;
use WP\Plugin\AIChatbot\OpenSearchAWSService;

#[AsSettingContainer('connection', [
    self::FIELD_ENDPOINT => '',
    self::FIELD_CLIENT_ID => '',
    self::FIELD_CLIENT_SECRET => '',
    self::FIELD_TOKEN => '',
    self::FIELD_HOSTS => [],
    self::FIELD_HOST => '',
    self::FIELD_USER => '',
    self::FIELD_PASS => '',
    self::FIELD_INDEX => '',
    self::FIELD_DISABLE_SSL_VERIFY => false,
    self::FIELD_OS_AWS_REGION => '',
    self::FIELD_OS_AWS_SERVICE => OpenSearchAWSService::OPENSEARCH,
    self::FIELD_OS_AWS_KEY => '',
    self::FIELD_OS_AWS_SECRET => ''
])]
class Connection extends EnvAwareSetting
{
    public const FIELD_ENDPOINT = 'endpoint';
    public const FIELD_CLIENT_ID = 'clientId';
    public const FIELD_CLIENT_SECRET = 'clientSecret';
    public const FIELD_TOKEN = 'token';
    public const FIELD_HOSTS = 'hosts';
    public const FIELD_USER = 'user';
    public const FIELD_PASS = 'pass';
    public const FIELD_DISABLE_SSL_VERIFY = 'disableSslVerify';
    public const FIELD_OS_AWS_REGION = 'osAWSRegion';
    public const FIELD_OS_AWS_SERVICE = 'osAWSService';
    public const FIELD_OS_AWS_KEY = 'osAWSKey';
    public const FIELD_OS_AWS_SECRET = 'osAWSSecret';
    public const FIELD_HOST = 'host';
    public const FIELD_INDEX = 'embeddingIndex';

    public function getEndpoint(): string
    {
        return $this->get(self::FIELD_ENDPOINT);
    }

    public function setEndpoint(string $value): self
    {
        $this->set($value, self::FIELD_ENDPOINT, false);
        return $this;
    }

    public function getClientId(): string
    {
        return $this->get(self::FIELD_CLIENT_ID);
    }

    public function setClientId(string $value): self
    {
        $this->set($value, self::FIELD_CLIENT_ID, false);
        return $this;
    }

    public function getClientSecret(): string
    {
        return $this->get(self::FIELD_CLIENT_SECRET);
    }

    public function setClientSecret(string $value): self
    {
        $this->set($value, self::FIELD_CLIENT_SECRET, false);
        return $this;
    }

    public function getToken(): string
    {
        return $this->get(self::FIELD_TOKEN);
    }

    public function setToken(string $value): self
    {
        $this->set($value, self::FIELD_TOKEN, false);
        return $this;
    }

    public function getIndex(): string
    {
        return $this->get(self::FIELD_INDEX);
    }

    public function setIndex(string $value): self
    {
        $this->set($value, self::FIELD_INDEX);
        return $this;
    }

    public function getHosts(): array
    {
        return $this->get(self::FIELD_HOSTS);
    }

    public function setHosts(array $hosts): self
    {
        $this->set($hosts, self::FIELD_HOSTS);
        return $this;
    }

    public function getUser(): string
    {
        return $this->get(self::FIELD_USER);
    }

    public function setUser(string $user): self
    {
        $this->set($user, self::FIELD_USER);
        return $this;
    }

    public function getPass(): string
    {
        return $this->get(self::FIELD_PASS);
    }

    public function setPass(string $pass): self
    {
        $this->set($pass, self::FIELD_PASS);
        return $this;
    }

    public function getDisableSslVerify(): bool
    {
        return $this->get(self::FIELD_DISABLE_SSL_VERIFY);
    }

    public function setDisableSslVerify(bool $value): self
    {
        $this->set($value, self::FIELD_DISABLE_SSL_VERIFY);
        return $this;
    }

    public function getOpenSearchAWSRegion(): string
    {
        return $this->get(self::FIELD_OS_AWS_REGION);
    }

    public function setOpenSearchAWSRegion(string $region): self
    {
        $this->set($region, self::FIELD_OS_AWS_REGION);
        return $this;
    }

    public function getOpenSearchAWSService(): OpenSearchAWSService
    {
        $val = $this->get(self::FIELD_OS_AWS_SERVICE);
        return $val instanceof OpenSearchAWSService ? $val : OpenSearchAWSService::from($val);
    }

    public function setOpenSearchAWSService(OpenSearchAWSService $service): self
    {
        $this->set($service->value, self::FIELD_OS_AWS_SERVICE);
        return $this;
    }

    public function getOpenSearchAWSKey(): string
    {
        return $this->get(self::FIELD_OS_AWS_KEY);
    }

    public function setOpenSearchAWSKey(string $key): self
    {
        $this->set($key, self::FIELD_OS_AWS_KEY);
        return $this;
    }

    public function getOpenSearchAWSSecret(): string
    {
        return $this->get(self::FIELD_OS_AWS_SECRET);
    }

    public function setOpenSearchAWSSecret(string $secret): self
    {
        $this->set($secret, self::FIELD_OS_AWS_SECRET);
        return $this;
    }

    public function getHost(): ?string
    {
        return $this->get(self::FIELD_HOST);
    }

    public function setHost(string $host): self
    {
        $this->set($host, self::FIELD_HOST);
        return $this;
    }

    protected function parseEnv(): array
    {
        $envData = [];

        $baseUrl = getenv('CHATBOT_API_BASE_URL') ?: '';
        $endpoint = getenv('CHATBOT_API_ENDPOINT') ?: '';
        $clientCredentials = getenv('CHATBOT_CREDENTIALS') ?: '';
        $clientId = getenv('CHATBOT_CLIENT_ID') ?: '';
        $clientSecret = getenv('CHATBOT_CLIENT_SECRET') ?: '';
        $token = getenv('CHATBOT_API_TOKEN') ?: getenv('CHATBOT_API_ACCESS_TOKEN') ?: '';
        $hosts = getenv('CHATBOT_HOSTS') ?: '';
        $user = getenv('CHATBOT_USER') ?: '';
        $pass = getenv('CHATBOT_PASS') ?: '';
        $disableSslVerify = getenv('CHATBOT_DISABLE_SSL_VERIFY') ?: null;

        // OpenSearch AWS
        $osAWSRegion = getenv('CHATBOT_OS_AWS_REGION')
            ?: getenv('AWS_REGION')
                ?: getenv('AWS_DEFAULT_REGION')
                    ?: null;
        $osAWSService = getenv('CHATBOT_OS_AWS_SERVICE') ?: null;
        $osAWSKey = getenv('CHATBOT_OS_AWS_KEY') ?: null;
        $osAWSSecret = getenv('CHATBOT_OS_AWS_SECRET') ?: null;

        if (!empty($osAWSKey)) {
            $envData[self::FIELD_OS_AWS_KEY] = $osAWSKey;
        }

        if (!empty($osAWSSecret)) {
            $envData[self::FIELD_OS_AWS_SECRET] = $osAWSSecret;
        }

        if (!empty($osAWSService)) {
            $service = OpenSearchAWSService::tryFrom($osAWSService);
            if (!empty($service)) {
                $envData[self::FIELD_OS_AWS_SERVICE] = $service;
            }
        }

        if (!empty($osAWSRegion)) {
            $envData[self::FIELD_OS_AWS_REGION] = $osAWSRegion;
        }

        if (!empty($endpoint) || !empty($baseUrl)) {
            $envData[self::FIELD_ENDPOINT] = $baseUrl . $endpoint;
        }

        if (!empty($token)) {
            $envData[self::FIELD_TOKEN] = $token;
        }

        if (!empty($clientCredentials)) {
            parse_str($clientCredentials, $credentials);
            if (!empty($credentials['client_id'] ?? null)) {
                $envData[self::FIELD_CLIENT_ID] = $credentials['client_id'];
            }
            if (!empty($credentials['client_secret'] ?? null)) {
                $envData[self::FIELD_CLIENT_SECRET] = $credentials['client_secret'];
            }
        }

        if (!empty($clientId) && !isset($envData[self::FIELD_CLIENT_ID])) {
            $envData[self::FIELD_CLIENT_ID] = $clientId;
        }

        if (!empty($clientSecret) && !isset($envData[self::FIELD_CLIENT_SECRET])) {
            $envData[self::FIELD_CLIENT_SECRET] = $clientSecret;
        }

        if (!empty($hosts)) {
            $hosts = explode(',', $hosts);
            $envData[self::FIELD_HOSTS] = array_map('trim', $hosts);
        }

        if (!empty($user)) {
            $envData[self::FIELD_USER] = $user;
        }

        if (!empty($pass)) {
            $envData[self::FIELD_PASS] = $pass;
        }

        if (!is_null($disableSslVerify)) {
            $envData[self::FIELD_DISABLE_SSL_VERIFY] = !!$disableSslVerify;
        }

        return $envData;
    }
}
