<?php

namespace WP\Plugin\AIChatbot;

use ReflectionClass;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WP\Plugin\AIChatbot\Attributes\IsModelEngine;
use WP\Plugin\AIChatbot\Attributes\IsVectorDB;
use WP\Plugin\AIChatbot\Hooks\ChatbotFrontend;

class Plugin extends \VAF\WP\Framework\Plugin
{

    public function configureContainer(ContainerBuilder $builder, ContainerConfigurator $configurator): void
    {
        $builder->registerAttributeForAutoconfiguration(
            IsModelEngine::class,
            static function (
                ChildDefinition $defintion,
                IsModelEngine $attribute,
                ReflectionClass $reflector
            ) use ($builder): void {
                $defintion->setArgument('$id', $attribute->id);
                $defintion->setArgument('$description', $attribute->description);
                $defintion->setArgument('$shownConnectionSettings', $attribute->shownConnectionSettings);
                $defintion->addTag('wp_plugin_ai_chatbot.engine');
                $builder->setAlias('wp_plugin_ai_chatbot.engine.' . $attribute->id, $reflector->getName())
                    ->setPublic(true);
            }
        );

        $builder->registerAttributeForAutoconfiguration(
            IsVectorDB::class,
            static function (
                ChildDefinition $defintion,
                IsVectorDB $attribute,
                ReflectionClass $reflector
            ) use ($builder): void {
                $defintion->setArgument('$id', $attribute->id);
                $defintion->setArgument('$description', $attribute->description);
                $defintion->setArgument('$shownConnectionSettings', $attribute->shownConnectionSettings);
                $defintion->addTag('wp_plugin_ai_chatbot.vectorDB');
                $builder->setAlias('wp_plugin_ai_chatbot.vectorDB.' . $attribute->id, $reflector->getName())
                    ->setPublic(true);
            }
        );
    }

    public function getModelHandler(): ModelHandler
    {
        /** @var ModelHandler $handler */
        $handler = $this->getContainer()->get('wp_plugin_ai_chatbot.model-handler');
        return $handler;
    }

    public function getVectorDBHandler(): VectorDBHandler
    {
        /** @var VectorDBHandler $handler */
        $handler = $this->getContainer()->get('wp_plugin_ai_chatbot.vectorDB-handler');
        return $handler;
    }

    public function getChatbotFrontend(): ChatbotFrontend
    {
        /** @var ChatbotFrontend $handler */
        $handler = $this->getContainer()->get('wp_plugin_ai_chatbot.chatbot-frontend');
        return $handler;
    }
}
