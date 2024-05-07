<?php

namespace WP\Plugin\AIChatbot\AdminPages;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use VAF\WP\Framework\Request;
use WP\Plugin\AIChatbot\Plugin;
use WP\Plugin\AIChatbot\Settings\ActiveEngine;
use WP\Plugin\AIChatbot\Settings\Connection;
use WP\Plugin\AIChatbot\Templates\AdminPages\VectorDBPageTemplate;
use WP\Plugin\AIChatbot\VectorDB\VectorDB;
use VAF\WP\Framework\Utils\NoticeType;
use VAF\WP\Framework\Utils\Templates\Admin\Notice;


final class VectorDBPage
{
    private readonly iterable $engines;

    public function __construct(
        private readonly Plugin $plugin,
        private readonly VectorDBPageTemplate $pageTemplate,
        private readonly Request $request,
        private readonly ActiveEngine $activeEngine,
        private readonly Connection $connection,
        #[TaggedIterator('wp_plugin_ai_chatbot.vectorDB')]
        iterable $engines,
    )
    {
        $this->engines = $engines;
    }

    public function handle(): void
    {
        if ($this->request->isPost()) {
            $this->save();
        }

        $this->display();
    }

    private function save(): void
    {
        check_admin_referer('wp-ai-chatbot-model-engine');

        $action = $this->request->getParam('action', Request::TYPE_POST, '');

        if ($action !== 'update') {
            return;
        }

        // Finally we can update the settings
        $activeEngine = $this->request->getParam('model-engine', Request::TYPE_POST);
        if (!is_null($activeEngine) && $this->hasEngine($activeEngine) && $activeEngine !== ($this->activeEngine)()) {
            ($this->activeEngine)($activeEngine);
        }

        $host = $this->request->getParam(Connection::FIELD_HOST, Request::TYPE_POST);
        if (!is_null($host)) {
            if ($this->connection->getHost() != $host) {
                $this->connection->setHost($host);
            }
        }

        $this->connection->save();

        /** @var Notice $notice */
        $notice = $this->plugin->getContainer()->get('template.notice');
        $notice->setContent('Settings saved.');
        $notice->setType(NoticeType::SUCCESS);
        $notice->setIsDismissible(true);
        $notice->output();
    }


    private function hasEngine(string $id): bool
    {
        /** @var VectorDB $engine */
        foreach ($this->engines as $engine) {
            if ($engine->getId() === $id) {
                return true;
            }
        }

        return false;
    }


    private function display(): void
    {
        $engines = [...$this->engines];
        usort($engines, function (VectorDB $a, VectorDB $b): int {
            if ($a->getId() === 'null') {
                return -1;
            }
            if ($b->getId() === 'null') {
                return 1;
            }

            return strcasecmp($a->getId(), $b->getId());
        });
        $engines = apply_filters('wp_plugin_ai-chatbot/filter_vector_dbs', $engines);

        /** @var VectorDB $engine */
        foreach ($engines as $engine) {
            $this->pageTemplate->addEngine($engine);
        }

        $this->pageTemplate->setActiveEngine($this->activeEngine);
        $this->pageTemplate->setConnection($this->connection);

        $this->pageTemplate->output();
    }
}
