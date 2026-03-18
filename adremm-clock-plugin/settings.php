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
        'theme_font' => 'Inter',
        'panel_size' => 'normal', // small, normal, large
        'panel_padding' => '25',
        'bg_color' => '#ffffff',
        'text_color' => '#111111',

        // Analoge Klok
        'show_analog' => 'yes',
        'analog_bg_type' => 'color', // color, image
        'analog_bg_image' => '',
        'analog_bg_color' => '#000000',
        'analog_ring_color' => '#3399ff',
        'analog_ring_size' => '2',
        'analog_hour_not' => 'lines', // lines, dots
        'analog_hour_color' => '#ffffff',
        'analog_hour_thick' => '2',
        'analog_hour_length' => '8',
        'analog_min_not' => 'lines', // lines, dots
        'analog_min_color' => '#ffffff',
        'analog_min_thick' => '1',
        'analog_min_length' => '5',
        'analog_not_above' => 'yes',

        // Wijzers
        'hand_hour_thick' => '4',
        'hand_hour_color' => '#ffffff',
        'hand_hour_style' => 'rectangle', // rectangle, rounded, point, heart
        'hand_min_thick' => '3',
        'hand_min_color' => '#ffffff',
        'hand_min_style' => 'rectangle',
        'hand_sec_thick' => '1',
        'hand_sec_color' => '#ff3b30',
        'hand_sec_style' => 'point',
        'hand_sweep' => 'smooth', // smooth, ticking

        // Digitale Tijd
        'show_digital' => 'yes',
        'digital_font' => 'Inter',
        'digital_color' => '#111111',
        'digital_weight' => '700',
        'digital_bg' => 'transparent',
        'digital_show_sec' => 'yes',
        'digital_style' => 'custom', // alarm, wall, custom, blocks, dots, design
        'digital_glow' => 'no',
        'digital_glow_color' => '#ffffff',
        'digital_glow_spread' => '10',

        // Status
        'show_status' => 'yes',
        'status_pos' => 'below_digital', // above_digital, below_digital, above_analog, below_analog, below_date
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
        'extra_message' => 'Alleen deze week! Extra korting aan de kassa aan de hand van de temperatuur.',
        'extra_color' => '#6f42c1',
        'extra_font_size' => '11',
        'extra_marquee' => 'no',
        'extra_speed' => '5',

        // Tijdpaneel / Inklapbaar
        'is_collapsible' => 'yes',
        'show_close_x' => 'yes',
        'close_x_size' => '24',
        'color_close_x' => '#111111',
        'show_close_label' => 'yes',
        'close_label' => 'Sluiten',
        'color_close_label' => '#111111',
        'panel_border' => '1px solid rgba(0,0,0,0.1)',
        'panel_shadow' => '0 10px 40px rgba(0,0,0,0.15)',

        // Tab Styling
        'tab_font' => 'Inter',
        'tab_color' => '#111111',
        'tab_bg' => '#ffffff',
        'tab_text' => 'KLOK',
        'tab_arrow' => 'yes',
        'tab_shadow' => '0 4px 15px rgba(0,0,0,0.2)',
        'tab_shadow_pos' => 'outer',

        // Mobile / Tablet Settings
        'mobile_visibility' => 'both', // both, desktop, mobile, tablet
        'mobile_breakpoint' => '768',
        'tablet_breakpoint' => '1024',
        'mobile_position' => 'bottom-right',
        'mobile_size' => 'small',
        'mobile_theme' => 'inherit',

        // Algemeen
        'language' => 'auto',

        // Openingstijden (Placeholder for JSON)
        'opening_hours' => '',
    );
}

function adremm_clock_settings_validate($input) {
    // Start with the existing settings from the database
    $current_settings = get_option('adremm_clock_settings', array());
    $defaults = adremm_clock_get_default_settings();

    // Merge existing settings with defaults to ensure completeness
    $output = wp_parse_args($current_settings, $defaults);

    // Define specifically which keys are color fields that need color validation
    $color_keys = array(
        'bg_color', 'analog_bg_color', 'analog_ring_color', 'hand_hour_color',
        'hand_min_color', 'hand_sec_color', 'digital_color', 'digital_bg',
        'digital_glow_color', 'color_open', 'color_closed', 'color_date',
        'color_close_x', 'color_close_label', 'tab_color', 'tab_bg'
    );

    // Update the settings with the new input, validating as we go
    foreach($input as $key => $val) {
        if (!isset($defaults[$key])) continue; // Ignore unknown keys

        if (in_array($key, $color_keys)) {
             // Sanitization for colors (HEX, RGBA, or transparent)
             $color = trim((string)$val);
             if (empty($color) || $color === 'transparent' || $color === 'rgba(0,0,0,0)') {
                 $output[$key] = 'transparent';
             } elseif (preg_match('/^rgba\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*(0(\.\d+)?|1(\.0+)?)\s*\)$/', $color) ||
                 preg_match('/^#([A-Fa-f0-9]{3,8})$/', $color)) {
                $output[$key] = $color;
             }
        } elseif ($key === 'opening_hours' || $key === 'extra_message') {
            // Special handling for larger text/JSON
            $output[$key] = $val;
        } else {
            $output[$key] = sanitize_text_field($val);
        }
    }

    return $output;
}

// Add admin menu
add_action('admin_menu', 'adremm_clock_add_admin_menu');
function adremm_clock_add_admin_menu() {
    // Top level menu
    add_menu_page(
        'ADREMM Klok Instellingen',
        'ADREMM Klok',
        'manage_options',
        'adremm-clock-settings',
        'adremm_clock_render_settings_page',
        'dashicons-clock', // Default dashicon, user can provide 20x20px SVG later
        60
    );

    // Submenu: Instellingen (Points to same as parent)
    add_submenu_page(
        'adremm-clock-settings',
        'Instellingen',
        'Instellingen',
        'manage_options',
        'adremm-clock-settings',
        'adremm_clock_render_settings_page'
    );

    // Submenu: Openingstijden
    add_submenu_page(
        'adremm-clock-settings',
        'Openingstijden',
        'Openingstijden',
        'manage_options',
        'adremm-clock-openingstijden',
        'adremm_clock_render_openingstijden_page'
    );

    // Submenu: Mobiel & Tablet
    add_submenu_page(
        'adremm-clock-settings',
        'Mobiel & Tablet',
        'Mobiel & Tablet',
        'manage_options',
        'adremm-clock-mobile',
        'adremm_clock_render_mobile_settings_page'
    );
}

function adremm_clock_render_openingstijden_page() {
    include ADREMM_CLOCK_PATH . 'openingstijden-page.php';
}

function adremm_clock_render_mobile_settings_page() {
    include ADREMM_CLOCK_PATH . 'mobile-settings-page.php';
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
