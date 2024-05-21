<?php

namespace WP\Plugin\AIChatbot;

use WP\Plugin\AIChatbot\ModelHandler;
use WP\Plugin\AIChatbot\VectorDBHandler;

class AnswerGenerator
{
    public function __construct(
        private ModelHandler $modelHandler,
        private VectorDBHandler $vectorDBHandler
    )
    {
    }

    public function generateAnswerStream($question)
    {
        $this->searchRelatedPosts($question);
        return $this->modelHandler->askChatbotAsync($question);
    }


    private function searchRelatedPosts($question)
    {
        $vector = $this->modelHandler->generateEmbedding($question);
        $relatedPosts = $this->vectorDBHandler->searchVectorDB($vector);

        trigger_error($relatedPosts);

        echo "<pre>", var_dump($relatedPosts), "</pre>";
        die();
    }
}
