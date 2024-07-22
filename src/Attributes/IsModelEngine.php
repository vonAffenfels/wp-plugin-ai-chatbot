<?php

namespace WP\Plugin\AIChatbot\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class IsModelEngine
{
    public function __construct(
        public readonly string $id,
        public readonly string $description = '',
        public readonly array $shownConnectionSettings = [],
        public readonly array $showModelSettings = []
    )
    {
    }
}
