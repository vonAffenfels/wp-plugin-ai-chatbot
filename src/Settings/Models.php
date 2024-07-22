<?php

namespace WP\Plugin\AIChatbot\Settings;

use VAF\WP\Framework\Setting\Attribute\AsSettingContainer;
use VAF\WP\Framework\Setting\EnvAwareSetting;
use WP\Plugin\AIChatbot\OpenSearchAWSService;

#[AsSettingContainer('models', [
    self::FIELD_GENERATION_MODEL => '',
    self::FIELD_EMBEDDING_MODEL => '',
])]
class Models extends EnvAwareSetting
{
    public const FIELD_GENERATION_MODEL = 'generation';
    public const FIELD_EMBEDDING_MODEL = 'embedding';

    public function getGenerationModel(): string
    {
        return $this->get(self::FIELD_GENERATION_MODEL);
    }

    public function setGenerationModel(string $value): self
    {
        $this->set($value, self::FIELD_GENERATION_MODEL, false);
        return $this;
    }

    public function getEmbeddingModel(): string
    {
        return $this->get(self::FIELD_EMBEDDING_MODEL);
    }

    public function setEmbeddingModel(string $value): self
    {
        $this->set($value, self::FIELD_EMBEDDING_MODEL, false);
        return $this;
    }

    protected function parseEnv(): array
    {
        $envData = [];

        $generationModel = getenv('CHATBOT_GENERATION_MODEL') ?: '';
        $embeddingModel = getenv('CHATBOT_EMBEDDING_MODEL') ?: '';

        if (!empty($generationModel)) {
            $envData[self::FIELD_GENERATION_MODEL] = $generationModel;
        }

        if (!empty($embeddingModel)) {
            $envData[self::FIELD_EMBEDDING_MODEL] = $embeddingModel;
        }

        return $envData;
    }
}
