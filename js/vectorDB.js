import $ from 'jquery';
import 'css/vectorDB.scss';
import {ajaxRequest} from "VAFWpFramework/admin/ajax";

$(function () {
    $('#regenerate').on('click', function (e) {
        e.preventDefault();

        const spinner = $('.spinner');
        const log = $('#regenerate_log');
        const postTypes = [];

        $("[data-post_types-input]:checked").each(function (i, obj) {
            postTypes[i] = obj.getAttribute('data-post_types-input')
        })

        setLoader(true);
        log.empty();

        let processed = 0;
        let currentPage = 1;

        function addLogLine(line) {
            const lineEl = $('<p></p>');
            lineEl.text(line);

            log.append(lineEl);
            log.scrollTop(log.prop('scrollHeight'));
        }

        function setLoader (loading) {
            spinner.css('visibility', loading ? 'visible' : 'hidden');
        }

        function runReindex() {
            $.ajax({
                url: window['vaf_admin_ajax']['wp-plugin-ai-chatbot_regenerate-embeddings']['ajaxurl'],
                type: 'post',
                data: Object.assign({
                    page: currentPage,
                    postTypes: postTypes
                    },
                    window['vaf_admin_ajax']['wp-plugin-ai-chatbot_regenerate-embeddings']['data']),
                success: function (response) {
                    if (response.success) {
                        processed += response.data.processed
                        addLogLine('Indexed posts ' + processed + '/' + response.data.total);
                        currentPage++;

                        if (processed >= response.data.total) {
                            // No more posts
                            addLogLine('Finished');
                            setLoader(false);
                            return;
                        }

                        runReindex();
                    } else {
                        setLoader(false);
                        addLogLine('Could not reindex: ' + response.message);
                    }
                },
                error: function (request, status, error) {
                    const json = request['responseJSON'] || {};
                    setLoader(false);
                    addLogLine('Could not reindex: ' + json.message || error);
                },
                timeout: 300000
            })
        }

       runReindex();
    });
})
