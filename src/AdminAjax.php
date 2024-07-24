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
        int $limit = 1
    ): Response
    {
        $query = new WP_Query([
            'post_type' => $postTypes,
            'posts_per_page' => $limit,
            'post_status' => 'publish',
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


    #[IsAdminAjaxAction(action: 'install-models', capability: Capabilities::MANAGE_OPTIONS)]
    public function installModels(
        ModelEngine $activeModelEngine,
        string $modelName
    ): Response
    {
        $response = $activeModelEngine->installModelIfNeeded($modelName);

        if (array_key_exists('error', $response))
        {
            return Response::error($response['response']);
        }

        return Response::success([$response['response']]);
    }


    #[IsAdminAjaxAction(action: 'model-is-installed', capability: Capabilities::MANAGE_OPTIONS)]
    public function isModelInstalled(
        ModelEngine $activeModelEngine,
        string $modelName
    ): Response
    {
        if ($activeModelEngine->modelIsInstalled($modelName))
        {
            return Response::success([]);
        }

        return Response::error('');
    }


    #[IsAdminAjaxAction(action: 'flush-embeddings', capability: Capabilities::MANAGE_OPTIONS)]
    public function flushEmbeddings(
        VectorDB $activeVectorDB,
    ): Response
    {

        $response = $activeVectorDB->deleteIndex();

        if ($response['acknowledged'] == true)
        {
            return Response::success([]);
        }

        return Response::error('');
    }
}
