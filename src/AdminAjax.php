<?php

namespace WP\Plugin\AIChatbot;


use VAF\WP\Framework\AdminAjax\Attributes\AsAdminAjaxContainer;
use VAF\WP\Framework\AdminAjax\Attributes\IsAdminAjaxAction;
use VAF\WP\Framework\AdminAjax\Response;
use VAF\WP\Framework\Utils\Capabilities;
use WP\Plugin\AIChatbot\ModelEngine\ModelEngine;
use WP\Plugin\AIChatbot\VectorDB\VectorDB;
use WP_Query;
use GuzzleHttp\Promise;

#[AsAdminAjaxContainer]
class AdminAjax
{
    #[IsAdminAjaxAction(action: 'regenerate-embeddings', capability: Capabilities::MANAGE_OPTIONS)]
    public function regenerateEmbeddings(
        ModelEngine $activeModelEngine,
        VectorDB $activeVectorDB,
        array $postTypes,
        int $page = 1,
        int $limit = 25
    ): Response
    {
        $query = new WP_Query([
            'post_type' => $postTypes,
            'posts_per_page' => $limit,
            'offset' => ($page - 1) * $limit,
            'ignore_sticky_posts' => true,
            'order' => 'ASC',
            'orderby' => 'date'
        ]);

        $processed = 0;

        foreach ($query->get_posts() as $post) {

            $postContent = strip_tags(apply_filters( 'the_content', get_post_field( 'post_content', $post->ID) ));
            $embedding = $activeModelEngine->generateEmbedding($postContent);
            $activeVectorDB->saveEmbedding($post->ID, $embedding);
            $processed++;
        }

        return Response::success([
            'total' => $query->found_posts,
            'processed' => $processed
        ]);
    }
}
