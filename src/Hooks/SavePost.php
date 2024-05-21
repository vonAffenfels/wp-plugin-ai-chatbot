<?php

namespace WP\Plugin\AIChatbot\Hooks;

use VAF\WP\Framework\Hook\Attribute\AsHookContainer;
use VAF\WP\Framework\Hook\Attribute\Hook;
use WP\Plugin\AIChatbot\ModelHandler;
use WP\Plugin\AIChatbot\VectorDBHandler;
use WP_Post;

#[AsHookContainer]
class SavePost
{
    public function __construct(
        private ModelHandler $modelHandler,
        private VectorDBHandler $vectorDBHandler
    )
    {
    }

    #[Hook(hook: 'save_post')]
    public function savePost(int $postId, WP_Post $post): void
    {
        $postTypesToSave = $this->vectorDBHandler->getPostTypes();
        if (!array_key_exists($post->post_type, $postTypesToSave))
        {
            return;
        }

        $postContent = strip_tags(apply_filters( 'the_content', get_post_field( 'post_content', $postId) ));
        $embedding = $this->modelHandler->generateEmbedding($postContent);
        $this->vectorDBHandler->saveEmbedding($postId, $embedding);
    }
}
