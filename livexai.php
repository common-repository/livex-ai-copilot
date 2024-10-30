<?php

/**
 * Plugin Name: LiveX AI Copilot
 * Plugin URI: https://wordpress.org/plugins/livex-ai-copilot
 * Description: Embed your Livex AI chatbot on any Wordpress site.
 * Version: 1.0.4
 * Author: LiveX AI
 * Author URI: https://www.livex.ai/
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('admin_menu', 'livexaicp_add_plugin_settings_page');

// Add the setting page to the admin menu
function livexaicp_add_plugin_settings_page()
{
    add_menu_page('LiveX AI Plugin Settings', 'LiveX AI Copilot', 'administrator', 'copilot_script', 'livexaicp_plugin_settings_page');
    add_action('admin_init', 'livexaicp_register_plugin_settings');
}

// Register the settings
function livexaicp_register_plugin_settings()
{
    register_setting('livex_plugin_settings', 'copilot_script');
}

// Define the content of the setting page
function livexaicp_plugin_settings_page()
{
    $copilot_script = get_option('copilot_script', '');

    ?>
        <div class="plugin-container">
            <h1>Setting up the LiveX AI Plugin:</h1>
            <form method="post" action="options.php">
                <?php settings_fields('livex_plugin_settings'); ?>
                <?php do_settings_sections('livex_plugin_settings'); ?>

                <h3>1. Do you have the JavaScript code from LiveX AI?</h3>
                <ol class="bullet-list">
                    <li>Yes: Paste the JavaScript code into the box below.</li>
                    <li>No: Follow the steps below.</li>
                </ol>

                <h3>2. Obtaining and Pasting the JavaScript code:</h3>
                <ol class="bullet-list">
                    <li>Visit <a href="https://livex.ai/" target="_blank">LiveX AI</a>.</li>
                    <li>Either log in or sign up for the LiveX AI service.</li>
                    <li>Follow the on-screen instructions to publish your copilot.</li>
                    <li>After publishing, you'll receive a JavaScript code. Paste this code into the box below.</li>
                </ol>

                <h3>3. Don't forget to click “Save Changes” after pasting the code.</h3>

                <div class="code-section">
                    <label for="copilot_script"><?php esc_html_e('Paste the JavaScript Code here', 'copilot_script'); ?></label>
                    <textarea name="copilot_script" id="copilot_script" rows="4"><?php echo esc_html($copilot_script); ?></textarea>
                </div>

                <button class="save-button">Save Changes</button>

                <h3>Congratulations! You've successfully set up your LiveX AI chatbot on WordPress. Enjoy engaging with your visitors!</h3>
            </form>
        </div>
    <?php
}

// Display a success message upon saving the JavaScript code
function livexaicp_save_success_message() {
    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        echo '<div id="message" class="notice notice-success is-dismissible"><p>JavaScript Code saved successfully!</p></div>';
    }
}
add_action('admin_notices', 'livexaicp_save_success_message');


function livexaicp_plugin_enqueue_styles() {
    wp_enqueue_style('custom-plugin-styles', plugins_url('style.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'livexaicp_plugin_enqueue_styles');

function livexaicp_get_data_bot_id($copilot_script) {
    if (!empty($copilot_script)) {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true); // suppress parse errors and warnings
        $dom->loadHTML($copilot_script);
        libxml_clear_errors(); // clear any error messages
        $xpath = new DOMXPath($dom);
        $scripts = $xpath->query('//script[@data-bot-id]');
        foreach ($scripts as $script) {
            $data_bot_id = $script->getAttribute('data-bot-id');
            return $data_bot_id;
        }
        return null;
    }
}

function livexaicp_embed_chatbot()
{
    $copilot_script = get_option('copilot_script', '');
    $data_bot_id = livexaicp_get_data_bot_id($copilot_script);
    if (!empty($data_bot_id)) {
        echo '<script async defer data-bot-id="' . esc_attr($data_bot_id) . '" src="https://chatbox.copilot.livex.ai/livex.min.js"></script>';
    }
}
add_action('wp_head', 'livexaicp_embed_chatbot');
?>