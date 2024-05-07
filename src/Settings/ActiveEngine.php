<?php

namespace WP\Plugin\AIChatbot\Settings;

use VAF\WP\Framework\Setting\Attribute\AsSettingContainer;
use VAF\WP\Framework\Setting\EnvAwareSetting;

#[AsSettingContainer('active_engine', 'null')]
class ActiveEngine extends EnvAwareSetting
{
    protected function parseEnv(): ?string
    {
        return getenv('MODEL_ENGINE') ?: null;
    }
}
