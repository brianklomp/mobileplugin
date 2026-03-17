<?php
/**
 * Clock Template for ADREMM Clock Plugin
 */

if ( ! defined('ABSPATH') ) exit;

$settings = get_option('adremm_clock_settings', adremm_clock_get_default_settings());
$position_class = 'adremm-clock-pos-' . $settings['position'];
$theme_class = 'adremm-clock-theme-' . $settings['theme'];
$style_vars = sprintf(
    '--adremm-clock-bg: %s; --adremm-clock-text: %s; --adremm-clock-font: "%s", sans-serif;',
    $settings['bg_color'],
    $settings['text_color'],
    $settings['font_family']
);
?>

<div id="adremm-clock-root" class="<?php echo esc_attr($position_class); ?> <?php echo esc_attr($theme_class); ?>" style="<?php echo esc_attr($style_vars); ?>">
    <!-- Floating Panel -->
    <div class="adremm-clock-panel">
        <button class="adremm-clock-close-btn" aria-label="Sluiten">&times;</button>
        <div class="adremm-clock-content">
            <div class="time"></div>
            <div class="date"></div>
        </div>
    </div>

    <!-- Closed Tab -->
    <div class="adremm-clock-tab" style="display: none;">
        <span><?php _e('KLOK', 'adremm-clock-plugin'); ?></span>
    </div>
</div>
