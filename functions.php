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

    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('wp-color-picker-alpha', ADREMM_CLOCK_URL . 'assets/wp-color-picker-alpha.min.js', array('wp-color-picker'), '3.0.0', true);
    wp_enqueue_style('adremm-clock-admin-css', ADREMM_CLOCK_URL . 'assets/admin-style.css', array(), ADREMM_CLOCK_VERSION);
    wp_enqueue_script('adremm-clock-admin-js', ADREMM_CLOCK_URL . 'assets/admin-preview.js', array('jquery', 'wp-color-picker'), ADREMM_CLOCK_VERSION, true);

    $settings = wp_parse_args(get_option('adremm_clock_settings', array()), adremm_clock_get_default_settings());
    $font_keys = array('theme_font', 'digital_font', 'font_status', 'font_date');
    foreach ($font_keys as $key) {
        if (!empty($settings[$key]) && $settings[$key] !== 'inherit' && $settings[$key] !== 'Thema') {
            $font = str_replace(' ', '+', $settings[$key]);
            $handle = 'adremm-clock-font-' . sanitize_title($font);
            wp_enqueue_style($handle, "https://fonts.googleapis.com/css2?family={$font}&display=swap", false);
        }
    }
}

/**
 * Determine current store status with support for multiple time slots and position override
 */
function adremm_clock_get_status() {
    $settings = wp_parse_args(get_option('adremm_clock_settings', array()), adremm_clock_get_default_settings());
    $opening_hours = !empty($settings['opening_hours']) ? json_decode($settings['opening_hours'], true) : array();
    $exceptional_days = !empty($settings['exceptional_days']) ? json_decode($settings['exceptional_days'], true) : array();

    $now = current_time('timestamp');
    $today_date = date('Y-m-d', $now);
    $day = strtolower(date('D', $now));
    $current_time = date('H:i', $now);

    $status_data = array('status' => 'closed', 'text' => $settings['text_closed'], 'pos' => 'inherit');

    // Check Exceptions First
    if (!empty($exceptional_days)) {
        foreach($exceptional_days as $ex) {
            if ($ex['date'] === $today_date) {
                $status_data['pos'] = isset($ex['pos']) ? $ex['pos'] : 'inherit';
                if ($ex['status'] === 'closed') {
                    $status_data['status'] = 'closed';
                    $status_data['text'] = $ex['label'] ?: $settings['text_closed'];
                } else {
                    $open = isset($ex['open']) ? $ex['open'] : '00:00';
                    $close = isset($ex['close']) ? $ex['close'] : '23:59';
                    if ($current_time >= $open && $current_time <= $close) {
                        $status_data['status'] = 'open';
                        $status_data['text'] = $ex['label'] ?: $settings['text_open'];
                    } else {
                        $status_data['status'] = 'closed';
                        $status_data['text'] = $settings['text_closed'];
                    }
                }
                return $status_data;
            }
        }
    }

    if (!isset($opening_hours[$day])) {
        return array('status' => 'open', 'text' => $settings['text_open'], 'pos' => 'inherit');
    }

    $day_data = $opening_hours[$day];
    if (isset($day_data['is_closed']) && $day_data['is_closed']) {
        return array('status' => 'closed', 'text' => $settings['text_closed'], 'pos' => 'inherit');
    }

    if (is_array($day_data)) {
        foreach ($day_data as $slot) {
            if (isset($slot['open']) && isset($slot['close'])) {
                if ($current_time >= $slot['open'] && $current_time <= $slot['close']) {
                    return array('status' => 'open', 'text' => $settings['text_open'], 'pos' => 'inherit');
                }
            }
        }
    }

    return array('status' => 'closed', 'text' => $settings['text_closed'], 'pos' => 'inherit');
}

/**
 * Enqueue scripts and styles for the frontend
 */
add_action('wp_enqueue_scripts', 'adremm_clock_frontend_enqueue');
function adremm_clock_frontend_enqueue() {
    $settings = wp_parse_args(get_option('adremm_clock_settings', array()), adremm_clock_get_default_settings());

    wp_enqueue_style('adremm-clock-public-css', ADREMM_CLOCK_URL . 'assets/public-style.css', array(), ADREMM_CLOCK_VERSION);
    wp_enqueue_script('adremm-clock-public-js', ADREMM_CLOCK_URL . 'assets/public-clock.js', array('jquery'), ADREMM_CLOCK_VERSION, true);

    wp_localize_script('adremm-clock-public-js', 'adremmClockData', array(
        'locale' => str_replace('_', '-', get_locale()),
        'panelSize' => $settings['panel_size'],
        'handSweep' => $settings['hand_sweep'],
        'extraMarquee' => $settings['extra_marquee'],
        'extraSpeed' => $settings['extra_speed'],
        'radioEnabled' => $settings['radio_enabled'],
        'radioChannel' => $settings['radio_channel'],
    ));

    $font_keys = array('theme_font', 'digital_font', 'font_status', 'font_date');
    foreach ($font_keys as $key) {
        if (!empty($settings[$key]) && $settings[$key] !== 'inherit' && $settings[$key] !== 'Thema') {
            $font = str_replace(' ', '+', $settings[$key]);
            $handle = 'adremm-clock-font-' . sanitize_title($font);
            wp_enqueue_style($handle, "https://fonts.googleapis.com/css2?family={$font}:wght@100;400;700&display=swap", false);
        }
    }
}
