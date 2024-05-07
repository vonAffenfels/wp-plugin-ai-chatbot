<?php

namespace WP\Plugin\AIChatbot\ModelEngine;

use Exception;
use WP\Plugin\AIChatbot\Plugin;
use WP\Plugin\AIChatbot\Settings\ActiveEngine;

final class Factory
{
    public function __construct(private readonly Plugin $plugin, private readonly ActiveEngine $adapter)
    {
    }

    /**
     * @throws Exception
     */
    public function getModelEngine(): ModelEngine
    {
        $adapter = ($this->adapter)();
        $container = $this->plugin->getContainer();

        $service = 'wp_plugin_ai_chatbot.engine.' . $adapter;

        if (!$container->has($service)) {
            throw new Exception('Cannot find adapter "' . $adapter . '"!');
        }

        /** @var ModelEngine $engine */
        $engine = $this->plugin->getContainer()->get($service);

        return $engine;
    }
}
