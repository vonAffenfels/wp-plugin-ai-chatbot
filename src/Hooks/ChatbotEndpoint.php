<?php

namespace WP\Plugin\AIChatbot\Hooks;

use VAF\WP\Framework\Hook\Attribute\AsHookContainer;
use VAF\WP\Framework\Hook\Attribute\Hook;
use WP\Plugin\AIChatbot\AnswerGenerator;
use WP\Plugin\AIChatbot\ModelHandler;
use WP\Plugin\AIChatbot\VectorDBHandler;

#[AsHookContainer]
class ChatbotEndpoint
{
    public function __construct(
        private AnswerGenerator $answerGenerator
    )
    {
    }

    #[Hook(hook: 'rest_api_init')]
    public function registerChatbotEndpoint(): void
    {
        register_rest_route(
            'ai-chatbot/v1', '/conversation', array(
                'methods'             => 'GET',
                'callback'            => [$this, 'askChatbotCallback'],
                'permission_callback' => '__return_true',
            )
        );
    }


    public function askChatbotCallback($request)
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header("X-Accel-Buffering: no");


        $question = $request['question'];

        if (!$question)
        {
            echo "event: message\n";
            echo "data: No Question given\n\n";
            ob_flush();
            flush();

            echo "event: stop\n";
            echo "data: stopped\n\n";
            ob_flush();
            return;
        }

        $response = $this->answerGenerator->generateAnswerStream($question);
        if ($response['minScoreReached'] !== true)
        {
            echo "event: message\n";
            echo "data: " . get_option('ai-chatbot-default-answer') . "\n";
            ob_flush();
            flush();

            foreach ($response['postIDs'] as $postID)
            {
                echo "event: message\n";
                echo "data: <a href='" . get_permalink($postID) . "'>" . get_the_title($postID) . "</a><br>\n\n";
                ob_flush();
                flush();
            }
            echo "event: stop\n";
            echo "data: stopped\n\n";
            ob_flush();
            flush();
        }

        else {
            $stream = $response['stream'];
            $unfinishedLine = '';

            while (!$stream->eof()) {
                $chunk = $stream->read(1024);

                $lineArray = preg_split("/\r\n|\n|\r/", $chunk);

                foreach ($lineArray as $line) {
                    if ($line === reset($lineArray)) {
                        $tmp = $unfinishedLine . $line;
                        echo "event: message\n";
                        echo "data: " . json_decode($tmp, true)['response'] . "\n\n";
                        ob_flush();
                        flush();
                        usleep(1000 * 100);
                        continue;
                    } elseif ($line === end($lineArray)) {
                        if (!str_ends_with($line, '"done":false}')) {
                            $unfinishedLine = $line;
                            break;
                        }
                    }

                    echo "event: message\n";
                    echo "data: " . json_decode($line, true)['response'] . "\n\n";
                    ob_flush();
                    flush();

                    usleep(1000 * 100);
                }

                if (connection_aborted()) {
                    break;
                }
            }

            echo "event: message\n";
            echo "data: Zugehörige Artikel: \n\n";
            ob_flush();
            flush();

            foreach ($response['postIDs'] as $postID)
            {
                echo "event: message\n";
                echo "data: <a href='" . get_permalink($postID) . "'>" . get_the_title($postID) . "</a><br>\n\n";
                ob_flush();
                flush();
            }

            echo "event: stop\n";
            echo "data: stopped\n\n";
            ob_flush();

            $stream->close();
        }
        wp_die();
    }
}
