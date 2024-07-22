import $ from 'jquery';
import 'css/chatbotFrontend.scss';

$(function () {
    function askChatbot(question) {
        const chatHistory = $('#ai-chatbot-chat-history');
        chatHistory.html('');

        const lineElQuestion = $('<p></p>');
        lineElQuestion.text(question);

        const userQuestionDiv = $('<div class="ai-chatbot-chat-element ai-chatbot-user_question"></div>');
        userQuestionDiv.append(lineElQuestion);

        chatHistory.append(userQuestionDiv);
        chatHistory.scrollTop(chatHistory.prop('scrollHeight'));

        const lineElAnswer = $('<p></p>');

        const aiAnswerDiv = $('<div class="ai-chatbot-chat-element"></div>');
        aiAnswerDiv.append(lineElAnswer);

        chatHistory.append(aiAnswerDiv);
        chatHistory.scrollTop(chatHistory.prop('scrollHeight'));

        const sourceEvents = new EventSource('/wp-json/ai-chatbot/v1/conversation/?question=' + question);
        sourceEvents.addEventListener('message', function (event) {
            lineElAnswer.append(event.data);

            aiAnswerDiv.html(lineElAnswer);

            chatHistory.scrollTop(chatHistory.prop('scrollHeight'));

        });
        sourceEvents.addEventListener('stop', function (event) {
            sourceEvents.close();
            $('.ai-chatbot-submit').removeAttr('disabled');
        });
    }

    function initListeners() {
        $('.ai-chatbot-submit').on('click', function () {
            $('.ai-chatbot-submit').attr('disabled', 'disabled');
            askChatbot($('.ai-chatbot-question')[0].value);
        });
    }

    initListeners();
});
