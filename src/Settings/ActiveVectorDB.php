<?php

namespace WP\Plugin\AIChatbot\Settings;

use VAF\WP\Framework\Setting\Attribute\AsSettingContainer;
use VAF\WP\Framework\Setting\EnvAwareSetting;

#[AsSettingContainer('active_vectorDB', 'null')]
class ActiveVectorDB extends EnvAwareSetting
{
    protected function parseEnv(): ?string
    {
        return getenv('VectorDB') ?: null;
    }
}
