<?php

namespace WP\Plugin\AIChatbot\Templates\AdminPages;

use VAF\WP\Framework\Template\Attribute\IsTemplate;
use VAF\WP\Framework\Template\Attribute\UseScript;
use VAF\WP\Framework\Template\Template;
use WP\Plugin\AIChatbot\ModelEngine\ModelEngine;
use WP\Plugin\AIChatbot\Settings\ActiveEngine;
use WP\Plugin\AIChatbot\Settings\Connection;


#[IsTemplate(templateFile: '@wp-plugin-ai-chatbot/adminpages/modelEngine')]
#[UseScript(src: 'js/modelEngine.min.js', deps: ['jquery'])]
//#[UseAdminAjax('get-top-ten-queries')]
class ModelEnginePageTemplate extends Template
{
    private Connection $connection;
    private ActiveEngine $activeEngine;
    private array $engines = [];

    public function addEngine(ModelEngine $engine): self
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

    public function setActiveEngine(ActiveEngine $engine): self
    {
        $this->activeEngine = $engine;
        return $this;
    }

    protected function getContextData(): array
    {
        return [
            'engines' => $this->engines,
            'activeEngine' => $this->activeEngine,
            'connection' => $this->connection
        ];
    }

    protected function getJavascriptData(): false|array
    {
        return [
            'engines' => $this->engines,
            'activeEngine' => ($this->activeEngine)()
        ];
    }
}
