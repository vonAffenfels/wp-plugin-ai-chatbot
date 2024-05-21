<?php

namespace WP\Plugin\AIChatbot\Templates\AdminPages;

use VAF\WP\Framework\Template\Attribute\IsTemplate;
use VAF\WP\Framework\Template\Attribute\UseAdminAjax;
use VAF\WP\Framework\Template\Attribute\UseScript;
use VAF\WP\Framework\Template\Template;
use WP\Plugin\AIChatbot\Settings\ActiveVectorDB;
use WP\Plugin\AIChatbot\Settings\Connection;
use WP\Plugin\AIChatbot\Settings\PostTypes;
use WP\Plugin\AIChatbot\VectorDB\VectorDB;


#[IsTemplate(templateFile: '@wp-plugin-ai-chatbot/adminpages/vectorDB')]
#[UseScript(src: 'js/vectorDB.min.js', deps: ['jquery'])]
#[UseAdminAjax('regenerate-embeddings')]
class VectorDBPageTemplate extends Template
{
    private ActiveVectorDB $activeEngine;
    private Connection $connection;

    private PostTypes $postTypes;
    private array $engines = [];

    public function addEngine(VectorDB $engine): self
    {
        $this->engines[$engine->getId()] = [
            'description' => $engine->getDescription(),
            'connectionSettings' => $engine->getConnectionSettings()
        ];
        return $this;
    }

    public function setConnection(Connection $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    public function setPostTypes(PostTypes $postTypes): self
    {
        $this->postTypes = $postTypes;
        return $this;
    }

    public function setActiveEngine(ActiveVectorDB $engine): self
    {
        $this->activeEngine = $engine;
        return $this;
    }

    protected function getContextData(): array
    {
        return [
            'engines' => $this->engines,
            'activeEngine' => $this->activeEngine,
            'connection' => $this->connection,
            'postTypes' => $this->postTypes
        ];
    }
}
