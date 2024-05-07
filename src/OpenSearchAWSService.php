<?php

namespace WP\Plugin\AIChatbot;

enum OpenSearchAWSService: string
{
    case OPENSEARCH = 'es';
    case OPENSEARCH_SERVERLESS = 'aoss';
}
