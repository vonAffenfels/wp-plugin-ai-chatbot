import $ from 'jquery';
import 'css/modelEngine.scss';
import {ajaxRequest} from "VAFWpFramework/admin/ajax";


$(function () {
    window.wp_plugin_ai_chatbot_modelEngine = Object.assign({
        engines: [],
        activeEngine: 'null'
    }, window.wp_plugin_ai_chatbot_modelEngine);

    const spinner = $('.spinner');
    const log = $('#regenerate_log');

    function showConnectionFields() {
        $('[data-connection]').hide();

        wp_plugin_ai_chatbot_modelEngine.engines[wp_plugin_ai_chatbot_modelEngine.activeEngine].connectionSettings.forEach(function (connectionSetting) {
            $('[data-connection="' + connectionSetting + '"').show();
        });
    }

    function addLogLine(line) {
        log.text(log.text() + line);
    }

    function setLoader(loading) {
        spinner.css('visibility', loading ? 'visible' : 'hidden');
    }

    function testChatbot(question) {
        setLoader(true);
        log.empty();

        const sourceEvents = new EventSource('/wp-json/ai-chatbot/v1/conversation/?question=' + question);
        sourceEvents.addEventListener('message', function (event) {
            addLogLine(event.data);
        });
        sourceEvents.addEventListener('stop', function (event) {
            sourceEvents.close();
            setLoader(false);
            $('#test_chatbot_submit').removeAttr('disabled');
        });
    }

    function initListeners() {
        $('#model-engine').on('change', function () {
            wp_plugin_ai_chatbot_modelEngine.activeEngine = $(this).val();
            showConnectionFields();
        });

        $('#test_chatbot_submit').on('click', function () {
            testChatbot($('#test_chatbot')[0].value);
            $('#test_chatbot_submit').attr('disabled', 'disabled');
        });
    }

    initListeners();
    showConnectionFields();
})
