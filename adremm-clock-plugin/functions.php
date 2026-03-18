<?php
/**
 * Helper functions for ADREMM Clock Plugin
 */

if ( ! defined('ABSPATH') ) exit;

/**
 * Return an array of Google Fonts
 */
function adremm_clock_get_google_fonts() {
    $fonts_file = ADREMM_CLOCK_PATH . 'assets/fonts.json';
    if (file_exists($fonts_file)) {
        $fonts = json_decode(file_get_contents($fonts_file), true);
        if (is_array($fonts)) {
            return $fonts;
        }
    }
    return array(
        'Inter', 'Poppins', 'Outfit', 'Roboto', 'Montserrat', 'Open Sans', 'Lato',
        'Nunito', 'Raleway', 'Ubuntu', 'Playfair Display', 'Oswald', 'Rubik',
        'Manrope', 'DM Sans', 'Space Grotesk', 'Lexend', 'Questrial'
    );
}

/**
 * Enqueue scripts and styles for the admin settings page
 */
add_action('admin_enqueue_scripts', 'adremm_clock_admin_enqueue');
function adremm_clock_admin_enqueue($hook) {
    if ('toplevel_page_adremm-clock-settings' !== $hook && 'adremm-clock-settings_page_adremm-clock-openingstijden' !== $hook) return;

    // Enqueue WP Color Picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('wp-color-picker-alpha', ADREMM_CLOCK_URL . 'assets/wp-color-picker-alpha.min.js', array('wp-color-picker'), '3.0.0', true);

    // Enqueue custom admin styles
    wp_enqueue_style('adremm-clock-admin-css', ADREMM_CLOCK_URL . 'assets/admin-style.css', array(), ADREMM_CLOCK_VERSION);

    // Enqueue custom admin JS
    wp_enqueue_script('adremm-clock-admin-js', ADREMM_CLOCK_URL . 'assets/admin-preview.js', array('jquery', 'wp-color-picker'), ADREMM_CLOCK_VERSION, true);

    // Load initial Google Font if needed
    $settings = wp_parse_args(get_option('adremm_clock_settings', array()), adremm_clock_get_default_settings());
    $font_keys = array('theme_font', 'digital_font', 'font_status', 'font_date');
    foreach ($font_keys as $key) {
        if (!empty($settings[$key]) && $settings[$key] !== 'inherit') {
            $font = str_replace(' ', '+', $settings[$key]);
            $handle = 'adremm-clock-font-' . sanitize_title($font);
            wp_enqueue_style($handle, "https://fonts.googleapis.com/css2?family={$font}&display=swap", false);
        }
    }
}

/**
 * Determine current store status
 */
function adremm_clock_get_status() {
    $settings = wp_parse_args(get_option('adremm_clock_settings', array()), adremm_clock_get_default_settings());
    $opening_hours = !empty($settings['opening_hours']) ? json_decode($settings['opening_hours'], true) : array();

    if (empty($opening_hours)) return 'open';

    $now = current_time('timestamp');
    $day = strtolower(date('D', $now));
    $current_time = date('H:i', $now);

    if (!isset($opening_hours[$day])) return 'open';
    if (!empty($opening_hours[$day]['is_closed'])) return 'closed';

    $open = $opening_hours[$day]['open'];
    $close = $opening_hours[$day]['close'];

    if ($current_time >= $open && $current_time <= $close) {
        return 'open';
    }

    return 'closed';
}

/**
 * Enqueue scripts and styles for the frontend
 */
add_action('wp_enqueue_scripts', 'adremm_clock_frontend_enqueue');
function adremm_clock_frontend_enqueue() {
    $settings = wp_parse_args(get_option('adremm_clock_settings', array()), adremm_clock_get_default_settings());

    // Enqueue custom frontend styles
    wp_enqueue_style('adremm-clock-public-css', ADREMM_CLOCK_URL . 'assets/public-style.css', array(), ADREMM_CLOCK_VERSION);

    // Enqueue custom frontend JS
    wp_enqueue_script('adremm-clock-public-js', ADREMM_CLOCK_URL . 'assets/public-clock.js', array('jquery'), ADREMM_CLOCK_VERSION, true);

    // Localize script for multilingual support and settings
    wp_localize_script('adremm-clock-public-js', 'adremmClockData', array(
        'locale' => str_replace('_', '-', get_locale()),
        'panelSize' => $settings['panel_size'],
        'handSweep' => $settings['hand_sweep'],
        'extraMarquee' => $settings['extra_marquee'],
        'extraSpeed' => $settings['extra_speed'],
    ));

    // Load Google Fonts
    $font_keys = array('theme_font', 'digital_font', 'font_status', 'font_date');
    foreach ($font_keys as $key) {
        if (!empty($settings[$key]) && $settings[$key] !== 'inherit') {
            $font = str_replace(' ', '+', $settings[$key]);
            $handle = 'adremm-clock-font-' . sanitize_title($font);
            wp_enqueue_style($handle, "https://fonts.googleapis.com/css2?family={$font}:wght@400;700&display=swap", false);
        }
    }
}
