<?php
/**
 * Helper functions for ADREMM Clock Plugin
 */

if ( ! defined('ABSPATH') ) exit;

/**
 * Return an array of Google Fonts
 */
function adremm_clock_get_google_fonts() {
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
    if ('settings_page_adremm-clock-settings' !== $hook) return;

    // Enqueue WP Color Picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');

    // Enqueue custom admin styles
    wp_enqueue_style('adremm-clock-admin-css', ADREMM_CLOCK_URL . 'assets/css/admin-style.css', array(), ADREMM_CLOCK_VERSION);

    // Enqueue custom admin JS
    wp_enqueue_script('adremm-clock-admin-js', ADREMM_CLOCK_URL . 'assets/js/admin-preview.js', array('jquery', 'wp-color-picker'), ADREMM_CLOCK_VERSION, true);

    // Load initial Google Font if needed
    $settings = get_option('adremm_clock_settings', adremm_clock_get_default_settings());
    if (!empty($settings['font_family'])) {
        $font = str_replace(' ', '+', $settings['font_family']);
        wp_enqueue_style('adremm-clock-admin-google-font', "https://fonts.googleapis.com/css2?family={$font}&display=swap", false);
    }
}

/**
 * Enqueue scripts and styles for the frontend
 */
add_action('wp_enqueue_scripts', 'adremm_clock_frontend_enqueue');
function adremm_clock_frontend_enqueue() {
    $settings = get_option('adremm_clock_settings', adremm_clock_get_default_settings());

    // Enqueue custom frontend styles
    wp_enqueue_style('adremm-clock-public-css', ADREMM_CLOCK_URL . 'assets/css/public-style.css', array(), ADREMM_CLOCK_VERSION);

    // Enqueue custom frontend JS
    wp_enqueue_script('adremm-clock-public-js', ADREMM_CLOCK_URL . 'assets/js/public-clock.js', array('jquery'), ADREMM_CLOCK_VERSION, true);

    // Localize script for multilingual support
    wp_localize_script('adremm-clock-public-js', 'adremmClockData', array(
        'locale' => str_replace('_', '-', get_locale()),
    ));

    // Load Google Font
    if (!empty($settings['font_family'])) {
        $font = str_replace(' ', '+', $settings['font_family']);
        wp_enqueue_style('adremm-clock-google-font', "https://fonts.googleapis.com/css2?family={$font}:wght@400;700&display=swap", false);
    }
}
