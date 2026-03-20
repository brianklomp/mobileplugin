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
    wp_enqueue_style('adremm-clock-pixel-font', "https://fonts.googleapis.com/css2?family=Silkscreen&display=swap", false);
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

    $status_data = array(
        'status' => 'closed',
        'text'   => isset($settings['text_closed']) ? $settings['text_closed'] : '',
        'pos'    => 'inherit'
    );

    // Check Exceptions First
    if (is_array($exceptional_days)) {
        foreach($exceptional_days as $ex) {
            if (is_array($ex) && isset($ex['date']) && $ex['date'] === $today_date) {
                $status_data['pos'] = isset($ex['pos']) ? $ex['pos'] : 'inherit';
                $status_label = !empty($ex['label']) ? $ex['label'] : (isset($settings['text_open']) ? $settings['text_open'] : '');
                $closed_label = !empty($ex['label']) ? $ex['label'] : (isset($settings['text_closed']) ? $settings['text_closed'] : '');

                if (isset($ex['status']) && $ex['status'] === 'closed') {
                    $status_data['status'] = 'closed';
                    $status_data['text'] = $closed_label;
                } else {
                    $open = isset($ex['open']) ? $ex['open'] : '00:00';
                    $close = isset($ex['close']) ? $ex['close'] : '23:59';
                    if ($current_time >= $open && $current_time <= $close) {
                        $status_data['status'] = 'open';
                        $status_data['text'] = $status_label;
                    } else {
                        $status_data['status'] = 'closed';
                        $status_data['text'] = isset($settings['text_closed']) ? $settings['text_closed'] : '';
                    }
                }
                return $status_data;
            }
        }
    }

    if (!is_array($opening_hours) || !isset($opening_hours[$day])) {
        return array('status' => 'open', 'text' => isset($settings['text_open']) ? $settings['text_open'] : '', 'pos' => 'inherit');
    }

    $day_data = $opening_hours[$day];
    $is_koopdag = is_array($day_data) && isset($day_data['is_koopdag']) && $day_data['is_koopdag'];
    $is_closed = is_array($day_data) && isset($day_data['is_closed']) && $day_data['is_closed'];
    $koop_suffix = $is_koopdag ? ' (Koopdag)' : '';

    if ($is_closed) {
        return array('status' => 'closed', 'text' => (isset($settings['text_closed']) ? $settings['text_closed'] : '') . $koop_suffix, 'pos' => 'inherit');
    }

    $slots = array();
    if (is_array($day_data)) {
        if (isset($day_data['slots']) && is_array($day_data['slots'])) {
            $slots = $day_data['slots'];
        } else {
            // Legacy format check: if it's a list of slots
            $is_legacy = true;
            foreach($day_data as $key => $val) {
                if ($key === 'is_closed' || $key === 'is_koopdag') continue;
                if (!is_numeric($key)) { $is_legacy = false; break; }
            }
            if ($is_legacy) $slots = $day_data;
        }
    }

    if (is_array($slots)) {
        foreach ($slots as $slot) {
            if (is_array($slot) && isset($slot['open']) && isset($slot['close'])) {
                if ($current_time >= $slot['open'] && $current_time <= $slot['close']) {
                    return array('status' => 'open', 'text' => (isset($settings['text_open']) ? $settings['text_open'] : '') . $koop_suffix, 'pos' => 'inherit');
                }
            }
        }
    }

    return array('status' => 'closed', 'text' => (isset($settings['text_closed']) ? $settings['text_closed'] : '') . $koop_suffix, 'pos' => 'inherit');
}

/**
 * Enqueue scripts and styles for the frontend
 */
add_action('wp_enqueue_scripts', 'adremm_clock_frontend_enqueue');
function adremm_clock_frontend_enqueue() {
    $settings = wp_parse_args(get_option('adremm_clock_settings', array()), adremm_clock_get_default_settings());

    wp_enqueue_style('adremm-clock-pixel-font', "https://fonts.googleapis.com/css2?family=Silkscreen&display=swap", false);
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
