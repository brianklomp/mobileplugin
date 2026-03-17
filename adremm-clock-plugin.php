<?php
/**
 * Plugin Name: ADREMM Klok Plugin
 * Description: Een meertalige klokplugin met live previews en schaalbare weergave.
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
    update_option('adremm_clock_settings', $default_settings);
}

// Add the clock to the footer
add_action('wp_footer', 'adremm_clock_render_frontend');
function adremm_clock_render_frontend() {
    if (is_admin()) return;
    include ADREMM_CLOCK_PATH . 'clock-template.php';
}
