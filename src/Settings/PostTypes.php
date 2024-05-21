<?php

namespace WP\Plugin\AIChatbot\Settings;

use VAF\WP\Framework\Setting\Attribute\AsSettingContainer;
use VAF\WP\Framework\Setting\Setting;
use WP\Plugin\AIChatbot\OpenSearchAWSService;

#[AsSettingContainer('post_types', [
    self::FIELD_POST_TYPES => [],
])]
class PostTypes extends Setting
{
    public const FIELD_POST_TYPES = 'postTypes';

    public function getPostTypes(): array
    {
        return $this->get(self::FIELD_POST_TYPES);
    }

    public function setPostTypes(array $postTypes): self
    {
        $this->set($postTypes, self::FIELD_POST_TYPES);
        return $this;
    }

}
