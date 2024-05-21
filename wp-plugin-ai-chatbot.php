<?php

/**
 * Plugin Name:       Wordpress Ai Chatbot
 * Description:       Plugin to create a Chatbot with Vector Search and LLM Support
 * Version:           0.0.1
 * Requires at least: 6.2
 * Author:            Niklas Nellinger <niklas.nellinger@vonaffenfels.de>
 * Author URI:        https://www.vonaffenfels.de
 */

use WP\Plugin\AIChatbot\Plugin;


if (!defined('ABSPATH')) {
    die('');
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

Plugin::registerPlugin(__FILE__, defined('WP_DEBUG') && WP_DEBUG);
