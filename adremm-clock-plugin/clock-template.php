<?php
/**
 * Advanced Clock Template for ADREMM Clock Plugin
 */

if ( ! defined('ABSPATH') ) exit;

$settings = get_option('adremm_clock_settings', adremm_clock_get_default_settings());
$pos = $settings['position'];

// Determine layout type: Bar (Header/Footer) or Panel (Floating)
$is_bar = ($pos === 'top-center' || $pos === 'bottom-center');
$layout_class = $is_bar ? 'adremm-clock-bar' : 'adremm-clock-panel';
$theme_class = 'adremm-clock-theme-' . $settings['theme'];
$position_class = 'adremm-clock-pos-' . $pos;

$style_vars = sprintf(
    '--adremm-clock-bg: %s; --adremm-clock-text: %s;',
    $settings['bg_color'],
    $settings['text_color']
);
?>

<div id="adremm-clock-wrapper" class="<?php echo esc_attr($layout_class); ?> <?php echo esc_attr($position_class); ?> <?php echo esc_attr($theme_class); ?>" style="<?php echo esc_attr($style_vars); ?>">

    <div class="adremm-clock-container">
        <?php if (!$is_bar): ?>
            <button class="adremm-clock-close" title="<?php _e('Sluiten', 'adremm-clock-plugin'); ?>">&times;</button>
        <?php endif; ?>

        <div class="adremm-clock-main">
            <!-- Analog Section (if enabled/placeholder) -->
            <div class="adremm-clock-analog">
                <div class="face">
                    <div class="h-hour"></div>
                    <div class="h-min"></div>
                    <div class="h-sec"></div>
                </div>
            </div>

            <!-- Digital & Info Section -->
            <div class="adremm-clock-info">
                <div class="time-row">
                    <span class="time-digital"></span>
                </div>

                <?php if ($settings['show_status'] === 'yes'): ?>
                    <div class="status-row" style="color: <?php echo esc_attr($settings['color_open']); ?>; font-family: '<?php echo esc_attr($settings['font_status']); ?>';">
                        <?php echo esc_html($settings['text_open']); ?>
                    </div>
                <?php endif; ?>

                <?php if ($settings['show_date'] === 'yes'): ?>
                    <div class="date-row" style="color: <?php echo esc_attr($settings['color_date']); ?>; font-family: '<?php echo esc_attr($settings['font_date']); ?>';">
                        <span class="date-text"></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($settings['extra_message'])): ?>
                    <div class="extra-row" style="color: <?php echo esc_attr($settings['extra_color']); ?>;">
                        <?php echo esc_html($settings['extra_message']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!$is_bar && $settings['is_collapsible'] === 'yes'): ?>
        <!-- Collapsed Tab -->
        <div class="adremm-clock-tab" style="display: none;">
            <span class="tab-label"><?php echo esc_html($settings['tab_text']); ?></span>
        </div>
    <?php endif; ?>
</div>
