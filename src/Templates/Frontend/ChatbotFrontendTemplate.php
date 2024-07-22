<?php

namespace WP\Plugin\AIChatbot\Templates\Frontend;


use VAF\WP\Framework\Template\Attribute\IsTemplate;
use VAF\WP\Framework\Template\Attribute\UseScript;
use VAF\WP\Framework\Template\Template;

#[IsTemplate(templateFile: '@wp-plugin-ai-chatbot/frontend/chatbotFrontend')]
#[UseScript(src: 'js/chatbotFrontend.min.js')]
class ChatbotFrontendTemplate extends Template
{
    protected function getContextData(): array
    {
        return [
        ];
    }
}
