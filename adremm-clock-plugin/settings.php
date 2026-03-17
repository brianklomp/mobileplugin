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
        'display_type' => 'both', // analog, digital, both
        'bg_color' => '#ffffff',
        'text_color' => '#111111',
        'font_main' => 'Inter',

        // Status
        'show_status' => 'yes',
        'text_open' => 'Wij zijn geopend',
        'text_closed' => 'Wij zijn gesloten',
        'color_open' => '#28a745',
        'color_closed' => '#dc3545',
        'font_status' => 'Inter',

        // Date
        'show_date' => 'yes',
        'color_date' => '#a690d5',
        'font_date' => 'Inter',

        // Extra
        'extra_message' => 'Alleen deze week! Extra korting aan de kassa aan de hand van de temperatuur. Kom je je voordeel ook halen in de winkel? Tot snel!',
        'extra_color' => '#6f42c1',

        // Menu
        'menu_id' => 'none',

        // Collapsible
        'is_collapsible' => 'yes',
        'tab_text' => 'KLOK',
    );
}

function adremm_clock_settings_validate($input) {
    $output = array();
    $defaults = adremm_clock_get_default_settings();

    $simple_fields = array('position', 'theme', 'display_type', 'font_main', 'show_status', 'text_open', 'text_closed', 'font_status', 'show_date', 'font_date', 'extra_message', 'menu_id', 'is_collapsible', 'tab_text');
    foreach($simple_fields as $field) {
        $output[$field] = isset($input[$field]) ? sanitize_text_field($input[$field]) : ($defaults[$field] ?? '');
    }

    // Custom RGBA/Hex sanitization
    $sanitize_color = function($color, $fallback) {
        $color = trim((string)$color);
        if (preg_match('/^rgba\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*(0(\.\d+)?|1(\.0+)?)\s*\)$/', $color)) return $color;
        if (preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $color)) return $color;
        return $fallback;
    };

    $color_fields = array('bg_color', 'text_color', 'color_open', 'color_closed', 'color_date', 'extra_color');
    foreach($color_fields as $field) {
        $output[$field] = isset($input[$field]) ? $sanitize_color($input[$field], $defaults[$field]) : $defaults[$field];
    }

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
