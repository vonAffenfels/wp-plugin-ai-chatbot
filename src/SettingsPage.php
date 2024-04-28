<?php

namespace WP\Plugin\AIChatbot;

class SettingsPage
{
    public function initSettingsPage()
    {
        add_action('admin_menu', [$this,'ai_chatbot_settings_menu']);
        add_action('admin_init', [$this,'ai_chatbot_settings_init'] );
    }

    public function ai_chatbot_settings_menu() {

        add_menu_page(
            __( 'AI Chatbot Settings', 'ai-chatbot' ),
            __( 'AI Chatbot Settings', 'ai-chatbot' ),
            'manage_options',
            'ai-chatbot-settings-page',
            [$this,'ai_chatbot_settings_template_callback'],
            '',
            null
        );

    }

    public function ai_chatbot_settings_template_callback() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <form action="options.php" method="post">
                <?php
                // security field
                settings_fields( 'ai-chatbot-settings-page' );

                // output settings section here
                do_settings_sections('ai-chatbot-settings-page');

                // save settings button
                submit_button( 'Save Settings' );
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Settings Template
     */
    public function ai_chatbot_settings_init() {

        // Setup settings section
        add_settings_section(
            'ai_chatbot_settings_section',
            'AI-chatbot Settings Page',
            '',
            'ai-chatbot-settings-page'
        );


        // Register token input field
        register_setting(
            'ai-chatbot-settings-page',
            'ai_chatbot_settings_input_field_ollama_url',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => ''
            )
        );

        // Add token fields
        add_settings_field(
            'ai_chatbot_settings_input_field_ollama_url',
            __( 'Ollama URL', 'ai-chatbot' ),
            [$this,'ai_chatbot_settings_input_field_ollama_url_callback'],
            'ai-chatbot-settings-page',
            'ai_chatbot_settings_section'
        );

    }

    public function ai_chatbot_settings_input_field_ollama_url_callback() {
        $version_listinput_field = get_option('ai_chatbot_settings_input_field_ollama_url');
        ?>
        <input type="text" name="ai_chatbot_settings_input_field_ollama_url" class="regular-text" value="<?php echo isset($version_listinput_field) ? esc_attr( $version_listinput_field ) : ''; ?>" />
        <?php
    }

}
