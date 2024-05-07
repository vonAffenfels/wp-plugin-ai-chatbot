import $ from 'jquery';
import 'css/modelEngine.scss';
import { Ollama } from 'ollama';
import {QdrantClient} from '@qdrant/js-client-rest';


$(function () {
    window.wp_plugin_ai_chatbot_modelEngine = Object.assign({
        engines: [],
        activeEngine: 'null'
    }, window.wp_plugin_ai_chatbot_modelEngine);

    function showConnectionFields() {
        $('[data-connection]').hide();

        wp_plugin_ai_chatbot_modelEngine.engines[wp_plugin_ai_chatbot_modelEngine.activeEngine].connectionSettings.forEach(function (connectionSetting) {
            $('[data-connection="' + connectionSetting + '"').show();
        });
    }

    async function testChatbot(question) {
        console.log('start');
        const ollama = new Ollama({ host: 'ollama.prokita-portal.localhost:11434' });
        const client = new QdrantClient({url: 'http://qdrant.prokita-portal.localhost:6333'});

        const questionEmbedding = await ollama.embeddings({model: 'nomic-embed-text', prompt: question});
        let searchResult = await client.search('test_collection', {
            vector: questionEmbedding.embedding,
            limit: 10
        });

        console.log(searchResult);


        const post_content = await fetch(
            "http://prokita-portal.localhost/wp-json/wp/v2/fachbeitraege/5268", {
                method: "GET"
            });

        const post_content_json = await post_content.json();


        const message = {
            role: 'user',
            content: '<s>[INST] Use the following pieces of context to answer the question at the end. Present a well-formatted answer, using Markdown if possible. Don\'t go over three paragraphs when answering. Answer only in German. --- '
                //+ post_content_json.content.rendered
                + '--- Question:'
                + question
                + ' [/INST]'
        };
        const response = await ollama.chat({model: 'llama2', messages: [message], stream: true});
        for await (const part of response) {
            console.log(part.message.content);
            $('#text_chatbot_response').textContent += part.message.content;
        }
    }

    function initListeners() {
        $('#model-engine').on('change', function () {
            wp_plugin_ai_chatbot_modelEngine.activeEngine = $(this).val();
            showConnectionFields();
        });

        $('#test_chatbot_submit').on('click', function () {
            testChatbot($('#test_chatbot')[0].value);
        });
    }

    initListeners();
    showConnectionFields();
})
