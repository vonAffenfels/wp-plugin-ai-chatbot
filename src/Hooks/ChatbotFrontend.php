<?php

namespace WP\Plugin\AIChatbot\Hooks;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use VAF\WP\Framework\Hook\Attribute\AsHookContainer;
use VAF\WP\Framework\Hook\Attribute\Hook;
use WP\Plugin\AIChatbot\Templates\Frontend\ChatbotFrontendTemplate;

#[AsHookContainer]
#[AsAlias(id: "wp_plugin_ai_chatbot.chatbot-frontend", public: true)]
class ChatbotFrontend
{
    public function __construct(
        private ChatbotFrontendTemplate $template
    )
    {
    }

    #[Hook(hook: 'wp_footer')]
    #[Hook(hook: 'chatbot-test-hook')]
    public function registerChatbotFrontend(): void
    {
        if (get_option('ai-chatbot-frontend-enabled', false))
        {
            $this->template->output();
        }
    }
}
