<?php
/**
 * Plugin Name: ADREMM Klok
 * Description: Een uiterst gebruiksvriendelijke, meertalige klokplugin met live previews, openingstijden en schaalbare weergave.
 * Version: 1.0.2
 * Author: ADREMM
 * Author URI: https://adremm.nl
 * License: GPLv2 or later
 * Text Domain: adremm-clock-plugin
 * Domain Path: /languages
 */

if ( ! defined('ABSPATH') ) exit;

// Constants
define('ADREMM_CLOCK_VERSION', '1.0.2');
define('ADREMM_CLOCK_PATH', plugin_dir_path(__FILE__));
define('ADREMM_CLOCK_URL', plugin_dir_url(__FILE__));

// Debug log for path
// error_log('ADREMM_CLOCK_PATH: ' . ADREMM_CLOCK_PATH);

// Load Includes
require_once ADREMM_CLOCK_PATH . 'settings.php';
require_once ADREMM_CLOCK_PATH . 'functions.php';

/**
 * Safe version of str_pad
 */
if (!function_exists('adremm_str_pad')) {
    function adremm_str_pad($input, $pad_length, $pad_string = " ", $pad_type = STR_PAD_LEFT) {
        return str_pad((string)$input, $pad_length, $pad_string, $pad_type);
    }
}

// Plugin Activation
register_activation_hook(__FILE__, 'adremm_clock_activate');
function adremm_clock_activate() {
    $default_settings = adremm_clock_get_default_settings();
    if (!get_option('adremm_clock_settings')) {
        update_option('adremm_clock_settings', $default_settings);
    }
    set_transient('adremm_clock_activation_redirect', true, 30);
}

// Handle Redirect
add_action('admin_init', 'adremm_clock_handle_activation_redirect', 9999);
function adremm_clock_handle_activation_redirect() {
    if (get_transient('adremm_clock_activation_redirect')) {
        delete_transient('adremm_clock_activation_redirect');

        if (defined('DOING_AJAX') && DOING_AJAX) return;
        if (isset($_GET['activate-multi'])) return;

        // Use a slight delay or priority to ensure menu is registered
        wp_safe_redirect(admin_url('admin.php?page=adremm-clock-settings'));
        exit;
    }
}

// Add the clock to the footer
add_action('wp_footer', 'adremm_clock_render_frontend');
function adremm_clock_render_frontend() {
    if (is_admin()) return;
    $settings = wp_parse_args(get_option('adremm_clock_settings', array()), adremm_clock_get_default_settings());
    $status_data = adremm_clock_get_status();
    $status = $status_data['status'];
    $status_text = $status_data['text'];
    include ADREMM_CLOCK_PATH . 'clock-template.php';
}
