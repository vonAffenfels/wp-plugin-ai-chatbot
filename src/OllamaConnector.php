<?php

namespace WP\Plugin\AIChatbot;

use GuzzleHttp\Client;

class OllamaConnector
{
    private Client $ollamaClient;

    public function __construct()
    {
        $this->ollamaClient = new Client([
            'base_uri' => get_option('ai_chatbot_settings_input_field_ollama_url')
        ]);
    }

    public function installRequiredModalsIfNeeded()
    {
        $neededModels = require_once __DIR__ . '/../config/neededModels.php';
        $installedModels = $this->getInstalledModels();

        foreach ($neededModels as $modelName)
        {
            if (!$this->modelIsInstalled($installedModels, $modelName))
            {
                $this->installModel($modelName);
            }
        }
    }


    public function generateEmbedding($content)
    {
        $response = $this->ollamaClient->post('/api/embeddings',[
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'model' => 'nomic-embed-text',
                'prompt' => $content
            ])
        ]);

        return json_decode($response->getBody()->getContents(), true)['embedding'];
    }

    private function getInstalledModels()
    {
        $installedTagsJson = $this->ollamaClient->get('api/tags')->getBody()->getContents();
        return json_decode($installedTagsJson, true)['models'];
    }


    private function modelIsInstalled($installedModels, $modelName)
    {
        foreach ($installedModels as $installedModel)
        {
            if (str_starts_with($installedModel['name'], $modelName))
            {
                return true;
            }
        }

        return false;
    }


    private function installModel($modelName)
    {
        $response = $this->ollamaClient->post('/api/pull',[
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'name' => $modelName
            ])
        ]);

        echo "<pre>", var_dump($response), "</pre>";
        die();
    }
}
