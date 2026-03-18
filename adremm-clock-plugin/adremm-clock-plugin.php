<?php
/**
 * Plugin Name: ADREMM Klok
 * Description: Een uiterst gebruiksvriendelijke, meertalige klokplugin met live previews, openingstijden en schaalbare weergave.
 * Version: 1.0.0
 * Author: ADREMM
 * Author URI: https://adremm.nl
 * License: GPLv2 or later
 * Text Domain: adremm-clock-plugin
 * Domain Path: /languages
 */

if ( ! defined('ABSPATH') ) exit;

// Constants
define('ADREMM_CLOCK_VERSION', '1.0.0');
define('ADREMM_CLOCK_PATH', plugin_dir_path(__FILE__));
define('ADREMM_CLOCK_URL', plugin_dir_url(__FILE__));

// Load Includes
require_once ADREMM_CLOCK_PATH . 'settings.php';
require_once ADREMM_CLOCK_PATH . 'functions.php';

// Plugin Activation
register_activation_hook(__FILE__, 'adremm_clock_activate');
function adremm_clock_activate() {
    $default_settings = adremm_clock_get_default_settings();
    if (!get_option('adremm_clock_settings')) {
        update_option('adremm_clock_settings', $default_settings);
    }
    add_option('adremm_clock_do_redirect', true);
}

// Handle Redirect
add_action('admin_init', 'adremm_clock_handle_activation_redirect');
function adremm_clock_handle_activation_redirect() {
    if (get_option('adremm_clock_do_redirect')) {
        delete_option('adremm_clock_do_redirect');
        if (!isset($_GET['activate-multi'])) {
            wp_safe_redirect(admin_url('admin.php?page=adremm-clock-settings'));
            exit;
        }
    }
}

// Add the clock to the footer
add_action('wp_footer', 'adremm_clock_render_frontend');
function adremm_clock_render_frontend() {
    if (is_admin()) return;
    $settings = wp_parse_args(get_option('adremm_clock_settings', array()), adremm_clock_get_default_settings());
    $status = adremm_clock_get_status(); // Initialize status for the template
    include ADREMM_CLOCK_PATH . 'clock-template.php';
}
