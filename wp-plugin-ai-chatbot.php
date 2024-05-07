<?php

/**
 * Plugin Name:       Wordpress Ai Chatbot
 * Description:       Plugin to create a Chatbot with Vector Search and LLM Support
 * Version:           0.0.1
 * Requires at least: 6.2
 * Author:            Niklas Nellinger <niklas.nellinger@vonaffenfels.de>
 * Author URI:        https://www.vonaffenfels.de
 */

use Qdrant\Config;
use Qdrant\Http\GuzzleClient;
use Qdrant\Models\PointsStruct;
use Qdrant\Models\PointStruct;
use Qdrant\Models\Request\CreateCollection;
use Qdrant\Models\Request\VectorParams;
use Qdrant\Models\VectorStruct;
use Qdrant\Qdrant;
use WP\Plugin\AIChatbot\Plugin;





if (!defined('ABSPATH')) {
    die('');
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

Plugin::registerPlugin(__FILE__, defined('WP_DEBUG') && WP_DEBUG);

add_action('save_post', function ($postID){

    /** @var WP\Plugin\AIChatbot\Plugin $plugin */
    $plugin = apply_filters('vaf-get-plugin', null, 'wp-plugin-ai-chatbot');

    $postContent = strip_tags(apply_filters( 'the_content', get_post_field( 'post_content', $postID) ));
    $embedding = $plugin->getSearchHandler()->generateEmbedding($postContent);

    $config = new Config('qdrant');
    $client = new Qdrant(new GuzzleClient($config));


    $points = new PointsStruct();
    $points->addPoint(
        new PointStruct(
            $postID,
            new VectorStruct($embedding)
        )
    );
    $client->collections('test_collection')->points()->upsert($points);
});
