<?php

namespace WP\Plugin\AIChatbot\VectorDB;

use Exception;
use WP\Plugin\AIChatbot\Plugin;
use WP\Plugin\AIChatbot\Settings\ActiveVectorDB;

final class Factory
{
    public function __construct(private readonly Plugin $plugin, private readonly ActiveVectorDB $adapter)
    {
    }

    /**
     * @throws Exception
     */
    public function getVectorDB(): VectorDB
    {
        $adapter = ($this->adapter)();
        $container = $this->plugin->getContainer();

        $service = 'wp_plugin_ai_chatbot.vectorDB.' . $adapter;

        if (!$container->has($service)) {
            throw new Exception('Cannot find adapter "' . $adapter . '"!');
        }

        /** @var VectorDB $engine */
        $engine = $this->plugin->getContainer()->get($service);

        return $engine;
    }
}
