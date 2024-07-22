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
        const lineEl = $('<p></p>');
        lineEl.text(line);

        log.append(lineEl);
        log.scrollTop(log.prop('scrollHeight'));
    }

    function addLogLineWithoutNewline(line) {
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
            addLogLineWithoutNewline(event.data);
        });
        sourceEvents.addEventListener('stop', function (event) {
            sourceEvents.close();
            setLoader(false);
            $('#test_chatbot_submit').removeAttr('disabled');
        });
    }

    function installModels() {
        setLoader(true);
        $('.modelInput').each(function (i, modelInput) {
            addLogLine('Installing Model ' + modelInput.value);

            ajaxRequest(
                'wp-plugin-ai-chatbot_install-models',
                {
                    modelName: modelInput.value
                },
                function () {
                    addLogLine(modelInput.value + 'installed');
                },

                function (data) {
                    addLogLine('Error installing ' + modelInput.value + ' Error: ' + data);
                }
            );
        });
    }

    function engineIsReady()
    {
        let readyModels = 0;
        $('.modelInput').each(function (i, modelInput) {

            $.ajax({
                async: false,
                url: window['vaf_admin_ajax']['wp-plugin-ai-chatbot_model-is-installed']['ajaxurl'],
                type: 'post',
                data: Object.assign(
                    {modelName: modelInput.value},
                    window['vaf_admin_ajax']['wp-plugin-ai-chatbot_model-is-installed']['data']
                ),
                success: function (response) {
                    if (response.success == true)
                    {
                        readyModels += 1;
                    }
                }
            });
        });

        if (readyModels == $('.modelInput').length)
        {
            $('#chatbotEnabled').removeAttr('disabled');
        }
        else {
            $('#chatbotEnabled').attr('disabled', '');
            $('#chatbotEnabled').removeAttr('checked');
        }
    }

    function initListeners() {
        $('#model-engine').on('change', function () {
            wp_plugin_ai_chatbot_modelEngine.activeEngine = $(this).val();
            showConnectionFields();
        });

        $('#installModels').on('click', function () {
            installModels();
        });

        window.setInterval(function(){
            engineIsReady();
        }, 5000);

        $('#test_chatbot_submit').on('click', function () {
            testChatbot($('#test_chatbot')[0].value);
            $('#test_chatbot_submit').attr('disabled', 'disabled');
        });
    }

    engineIsReady();
    initListeners();
    showConnectionFields();
});
