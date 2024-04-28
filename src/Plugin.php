<?php

namespace WP\Plugin\AIChatbot;

class Plugin
{
    public function init(): void
    {
        $settingsPage = new SettingsPage();
        $settingsPage->initSettingsPage();

        $this->ollamaConnector = new OllamaConnector();
        $this->ollamaConnector->installRequiredModalsIfNeeded();
        $this->addHooks();
    }


    private function addHooks(): void
    {
        add_action('save_post', function ($post_id){

            $postContent = strip_tags(apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) ));
            $embedding = $this->ollamaConnector->generateEmbedding($postContent);


        });
    }
}
