<?php
/**
 * Settings Registration for ADREMM Clock Plugin
 */

if ( ! defined('ABSPATH') ) exit;

add_action('admin_init', 'adremm_clock_register_settings');

function adremm_clock_register_settings() {
    register_setting('adremm_clock_options', 'adremm_clock_settings', 'adremm_clock_settings_validate');

    add_settings_section('adremm_clock_general', __('General Settings', 'adremm-clock-plugin'), null, 'adremm-clock-settings');

    add_settings_field('position', __('Clock Position', 'adremm-clock-plugin'), 'adremm_clock_position_callback', 'adremm-clock-settings', 'adremm_clock_general');
    add_settings_field('theme', __('Clock Theme', 'adremm-clock-plugin'), 'adremm_clock_theme_callback', 'adremm-clock-settings', 'adremm_clock_general');
    add_settings_field('google_font', __('Google Font', 'adremm-clock-plugin'), 'adremm_clock_font_callback', 'adremm-clock-settings', 'adremm_clock_general');
    add_settings_field('colors', __('Colors', 'adremm-clock-plugin'), 'adremm_clock_colors_callback', 'adremm-clock-settings', 'adremm_clock_general');
}

function adremm_clock_get_default_settings() {
    return array(
        'position' => 'bottom-right',
        'theme' => 'modern',
        'font_family' => 'Inter',
        'bg_color' => '#ffffff',
        'text_color' => '#111111',
        'menu_id' => 'none',
    );
}

function adremm_clock_settings_validate($input) {
    $output = array();

    $defaults = adremm_clock_get_default_settings();

    $output['position'] = isset($input['position']) ? sanitize_text_field($input['position']) : $defaults['position'];
    $output['theme'] = isset($input['theme']) ? sanitize_text_field($input['theme']) : $defaults['theme'];
    $output['font_family'] = isset($input['font_family']) ? sanitize_text_field($input['font_family']) : $defaults['font_family'];
    $output['menu_id'] = isset($input['menu_id']) ? sanitize_text_field($input['menu_id']) : 'none';

    // Custom RGBA/Hex sanitization
    $sanitize_color = function($color, $fallback) {
        $color = trim($color);
        if (preg_match('/^rgba\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*(0(\.\d+)?|1(\.0+)?)\s*\)$/', $color)) return $color;
        if (preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $color)) return $color;
        return $fallback;
    };

    $output['bg_color'] = isset($input['bg_color']) ? $sanitize_color($input['bg_color'], $defaults['bg_color']) : $defaults['bg_color'];
    $output['text_color'] = isset($input['text_color']) ? $sanitize_color($input['text_color'], $defaults['text_color']) : $defaults['text_color'];

    return $output;
}

// Add admin menu
add_action('admin_menu', 'adremm_clock_add_admin_menu');
function adremm_clock_add_admin_menu() {
    add_options_page(
        'ADREMM Klok Instellingen',
        'ADREMM Klok',
        'manage_options',
        'adremm-clock-settings',
        'adremm_clock_render_settings_page'
    );
}

function adremm_clock_render_settings_page() {
    include ADREMM_CLOCK_PATH . 'settings-page.php';
}

// Callbacks for settings fields (to be refined in the view step)
function adremm_clock_position_callback() {
    // Handled in the view
}
function adremm_clock_theme_callback() {
    // Handled in the view
}
function adremm_clock_font_callback() {
    // Handled in the view
}
function adremm_clock_colors_callback() {
    // Handled in the view
}
