<?php

namespace WP\Plugin\AIChatbot\AdminPages;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use VAF\WP\Framework\Request;
use WP\Plugin\AIChatbot\Plugin;
use WP\Plugin\AIChatbot\Settings\ActiveVectorDB;
use WP\Plugin\AIChatbot\Settings\Connection;
use WP\Plugin\AIChatbot\Settings\PostTypes;
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
        private readonly ActiveVectorDB $activeEngine,
        private readonly Connection $connection,
        private readonly PostTypes $postTypes,
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
        check_admin_referer('wp-ai-chatbot-vectordb');

        $action = $this->request->getParam('action', Request::TYPE_POST, '');

        if ($action !== 'update') {
            return;
        }

        $activeEngine = $this->request->getParam('vectorDB', Request::TYPE_POST);
        if (!is_null($activeEngine) && $this->hasEngine($activeEngine) && $activeEngine !== ($this->activeEngine)()) {
            ($this->activeEngine)($activeEngine);
        }

        $hosts = $this->request->getParam(Connection::FIELD_HOSTS, Request::TYPE_POST);
        if (!is_null($hosts)) {
            $hosts = array_map('trim', explode("\n", $hosts));

            if ($this->connection->getHosts() != $hosts) {
                $this->connection->setHosts($hosts);
            }
        }

        $user = $this->request->getParam(Connection::FIELD_USER, Request::TYPE_POST);
        if (!is_null($user) && $this->connection->getUser() !== $user) {
            $this->connection->setUser($user);
        }

        $pass = $this->request->getParam(Connection::FIELD_PASS, Request::TYPE_POST);
        if (!is_null($pass) && $this->connection->getPass() !== $pass) {
            $this->connection->setPass($pass);
        }

        $index = $this->request->getParam(Connection::FIELD_INDEX, Request::TYPE_POST);
        if (!is_null($index) && $this->connection->getIndex() !== $index) {
            $this->connection->setIndex($index);
        }

        $postTypes = $this->request->getParam(PostTypes::FIELD_POST_TYPES, Request::TYPE_POST);
        if (!is_null($postTypes)) {
            if ($this->postTypes->getPostTypes() != $postTypes) {
                $this->postTypes->setPostTypes($postTypes);
            }
        }

        $disableSslVerify = $this->request->getParam(Connection::FIELD_DISABLE_SSL_VERIFY, Request::TYPE_POST);
        if (!is_null($disableSslVerify)) {
            $disableSslVerify = $disableSslVerify === '1';
            if ($this->connection->getDisableSslVerify() !== $disableSslVerify) {
                $this->connection->setDisableSslVerify($disableSslVerify);
            }
        }

        update_option('ai-chatbot-search-tolerance', $this->request->getParam('searchTolerance'));


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
        $this->pageTemplate->setPostTypes($this->postTypes);

        $this->pageTemplate->output();
    }
}
