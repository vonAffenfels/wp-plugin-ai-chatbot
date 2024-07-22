<?php

namespace WP\Plugin\AIChatbot\Templates\AdminPages;

use VAF\WP\Framework\Template\Attribute\IsTemplate;
use VAF\WP\Framework\Template\Attribute\UseAdminAjax;
use VAF\WP\Framework\Template\Attribute\UseScript;
use VAF\WP\Framework\Template\Template;
use WP\Plugin\AIChatbot\ModelEngine\ModelEngine;
use WP\Plugin\AIChatbot\Settings\ActiveEngine;
use WP\Plugin\AIChatbot\Settings\Connection;
use WP\Plugin\AIChatbot\Settings\Models;


#[IsTemplate(templateFile: '@wp-plugin-ai-chatbot/adminpages/modelEngine')]
#[UseScript(src: 'js/modelEngine.min.js', deps: ['jquery'])]
#[UseAdminAjax('install-models')]
#[UseAdminAjax('model-is-installed')]
class ModelEnginePageTemplate extends Template
{
    private Connection $connection;
    private ActiveEngine $activeEngine;
    private array $engines = [];
    private Models $models;

    public function addEngine(ModelEngine $engine): self
    {
        $this->engines[$engine->getId()] = [
            'description' => $engine->getDescription(),
            'connectionSettings' => $engine->getConnectionSettings(),
            'modelSettings' => $engine->getModelSettings()
        ];
        return $this;
    }

    public function setConnection(Connection $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    public function setModels(Models $models): self
    {
        $this->models = $models;
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
            'connection' => $this->connection,
            'models' => $this->models,
            'chatbotEnabled' => get_option('ai-chatbot-frontend-enabled', false),
            'chatbotDefaultAnswer' => get_option('ai-chatbot-default-answer', 'Ich konnte dazu leider nichts finden'),
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
