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
        $minHitscore = get_option('ai-chatbot-search-tolerance');
        $relatedPosts = $this->searchRelatedPosts($question);

        $postIDs = [];
        $relatedPostsContent = '';
        foreach ($relatedPosts['hits']['hits'] as $hit)
        {
            $postIDs[] = $hit['_source']['postID'];
            $relatedPostsContent .= strip_tags(apply_filters( 'the_content', get_post_field( 'post_content', $hit['_id']) ));
        }


        if ($relatedPosts['hits']['max_score'] <= $minHitscore || empty($relatedPostsContent)) {
            return [
                'minScoreReached' => false,
                "postIDs" => $postIDs
            ];
        }
        return [
            'minScoreReached' => true,
            'stream' => $this->modelHandler->askChatbotAsync($question, '$relatedPostsContent'),
            'postIDs' => $postIDs];
    }


    private function searchRelatedPosts($question)
    {
        $vector = $this->modelHandler->generateEmbedding($question);
        $relatedPosts = $this->vectorDBHandler->searchVectorDB($vector);
        return $relatedPosts;
    }
}
