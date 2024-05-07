<?php

namespace WP\Plugin\AIChatbot;

use VAF\WP\Framework\AdminPages\Attributes\IsTabbedPage;
use VAF\WP\Framework\AdminPages\Attributes\PageTab;
use VAF\WP\Framework\AdminPages\TabbedPage;
use WP\Plugin\AIChatbot\AdminPages\ModelEnginePage;
use WP\Plugin\AIChatbot\AdminPages\VectorDBPage;

#[IsTabbedPage(pageTitle: 'Wordpress AI Chatbot')]
class AdminPage extends TabbedPage
{
    #[PageTab(slug: MenuPage::MODEL_ENGINE_SETTINGS, title: 'Model Engine')]
    public function handleModelEngineSettings(ModelEnginePage $page): void
    {
        $page->handle();
    }

    #[PageTab(slug: MenuPage::VECTOR_DB_SETTINGS, title: 'Vector DB')]
    public function handleVectorDBSettings(VectorDBPage $page): void
    {
        $page->handle();
    }
}
