<?php

namespace WP\Plugin\AIChatbot\Templates\Frontend;


use VAF\WP\Framework\Template\Attribute\IsTemplate;
use VAF\WP\Framework\Template\Attribute\UseScript;
use VAF\WP\Framework\Template\Template;

#[IsTemplate(templateFile: '@wp-plugin-ai-chatbot/adminpages/modelEngine')]
#[UseScript(src: 'js/modelEngine.min.js', deps: ['jquery'])]
class ChatbotFrontend extends Template
{
    protected function getContextData(): array
    {
        return [
        ];
    }
}
