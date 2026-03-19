<?php
/**
 * Helper functions for ADREMM Clock Plugin
 */

if ( ! defined('ABSPATH') ) exit;

/**
 * Return an array of Google Fonts
 */
function adremm_clock_get_google_fonts($include_theme_opt = false) {
    $fonts_file = ADREMM_CLOCK_PATH . 'assets/fonts.json';
    $fonts = array();
    if (file_exists($fonts_file)) {
        $json_fonts = json_decode(file_get_contents($fonts_file), true);
        if (is_array($json_fonts)) {
            $fonts = $json_fonts;
        }
    }
    if (empty($fonts)) {
        $fonts = array(
            'Inter', 'Poppins', 'Outfit', 'Roboto', 'Montserrat', 'Open Sans', 'Lato',
            'Nunito', 'Raleway', 'Ubuntu', 'Playfair Display', 'Oswald', 'Rubik',
            'Manrope', 'DM Sans', 'Space Grotesk', 'Lexend', 'Questrial'
        );
    }

    if ($include_theme_opt) {
        array_unshift($fonts, 'Thema');
    }

    return $fonts;
}

/**
 * Enqueue scripts and styles for the admin settings page
 */
add_action('admin_enqueue_scripts', 'adremm_clock_admin_enqueue');
function adremm_clock_admin_enqueue($hook) {
    $pages = array(
        'toplevel_page_adremm-clock-settings',
        'adremm-clock-settings_page_adremm-clock-openingstijden',
        'adremm-clock-settings_page_adremm-clock-mobile'
    );
    if (!in_array($hook, $pages)) return;

    // Enqueue Media for background image uploader
    wp_enqueue_media();

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
    $exceptional_days = !empty($settings['exceptional_days']) ? json_decode($settings['exceptional_days'], true) : array();

    if (empty($opening_hours) && empty($exceptional_days)) return array('status' => 'open', 'text' => $settings['text_open']);

    $now = current_time('timestamp');
    $today_date = date('Y-m-d', $now);
    $day = strtolower(date('D', $now));
    $current_time = date('H:i', $now);

    // Check Exceptions First (Holidays etc)
    if (!empty($exceptional_days)) {
        foreach($exceptional_days as $ex) {
            if ($ex['date'] === $today_date) {
                return array('status' => $ex['status'], 'text' => $ex['label']);
            }
        }
    }

    if (!isset($opening_hours[$day])) return array('status' => 'open', 'text' => $settings['text_open']);

    // Check if it is a "Koopavond"
    $is_koopavond = !empty($opening_hours[$day]['is_koopavond']);

    if (!empty($opening_hours[$day]['is_closed'])) {
        return array('status' => 'closed', 'text' => $settings['text_closed']);
    }

    $open = $opening_hours[$day]['open'];
    $close = $opening_hours[$day]['close'];

    // Check Break (Pauze)
    if (!empty($opening_hours[$day]['break_start']) && !empty($opening_hours[$day]['break_end'])) {
        if ($current_time >= $opening_hours[$day]['break_start'] && $current_time <= $opening_hours[$day]['break_end']) {
            return array('status' => 'closed', 'text' => $opening_hours[$day]['break_label'] ?: 'Wij zijn even pauzeren');
        }
    }

    if ($current_time >= $open && $current_time <= $close) {
        $text = $settings['text_open'];
        if ($is_koopavond) $text .= ' (Koopavond)';
        return array('status' => 'open', 'text' => $text);
    }

    return array('status' => 'closed', 'text' => $settings['text_closed']);
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
        'radioEnabled' => $settings['radio_enabled'],
        'radioChannel' => $settings['radio_channel'],
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
