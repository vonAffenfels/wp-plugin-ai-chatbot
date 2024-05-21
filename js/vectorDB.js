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
            ajaxRequest(
                'wp-plugin-ai-chatbot_regenerate-embeddings',
                {
                    page: currentPage,
                    postTypes: postTypes
                },
                function (data) {
                    processed += data.processed
                    addLogLine('Indexed posts ' + processed + '/' + data.total);
                    currentPage++;

                    if (processed >= data.total) {
                        // No more posts
                        addLogLine('Finished');
                        setLoader(false);
                        return;
                    }

                    runReindex();
                },

                function (msg) {
                    setLoader(false);
                    addLogLine('Could not reindex: ' + msg)
                }
            );
        }

       runReindex();
    });
})
