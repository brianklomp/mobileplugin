<?php
/**
 * Advanced Clock Template for ADREMM Clock Plugin
 */

if ( ! defined('ABSPATH') ) exit;

// $settings already parsed in adremm-clock-plugin.php
$pos = $settings['position'] ?? 'bottom-right';

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
        <?php if (!$is_bar && $settings['show_close_x'] === 'yes'): ?>
            <button class="adremm-clock-close" title="<?php _e('Sluiten', 'adremm-clock-plugin'); ?>" style="color: <?php echo esc_attr($settings['color_close_x']); ?>;">&times;</button>
        <?php endif; ?>

        <div class="adremm-clock-main">
            <!-- ABOVE ANALOG STATUS -->
            <?php if ($settings['show_status'] === 'yes' && $settings['status_pos'] === 'above_analog'): ?>
                 <div class="status-row" style="color: <?php echo ($status === 'open') ? esc_attr($settings['color_open']) : esc_attr($settings['color_closed']); ?>; font-family: '<?php echo esc_attr($settings['font_status']); ?>';"><?php echo ($status === 'open') ? esc_html($settings['text_open']) : esc_html($settings['text_closed']); ?></div>
            <?php endif; ?>

            <!-- Analog Section -->
            <?php if ($settings['show_analog'] === 'yes'): ?>
                <div class="adremm-clock-analog">
                    <div class="face" style="
                        background-color: <?php echo ($settings['analog_bg_type'] === 'color') ? esc_attr($settings['analog_bg_color']) : 'transparent'; ?>;
                        background-image: <?php echo ($settings['analog_bg_type'] === 'image' && $settings['analog_bg_image']) ? 'url('.esc_url($settings['analog_bg_image']).')' : 'none'; ?>;
                        border-color: <?php echo esc_attr($settings['analog_ring_color']); ?>;
                        border-width: <?php echo esc_attr($settings['analog_ring_size']); ?>px;
                    ">
                        <!-- Hour Notations -->
                        <div class="notations hour-notations <?php echo esc_attr($settings['analog_hour_not']); ?> <?php echo ($settings['analog_not_above'] === 'yes') ? 'above' : ''; ?>" style="color: <?php echo esc_attr($settings['analog_hour_color']); ?>;">
                            <?php for($i=1; $i<=12; $i++): ?><i style="transform: rotate(<?php echo $i*30; ?>deg)"></i><?php endfor; ?>
                        </div>
                        <div class="h-hour <?php echo esc_attr($settings['hand_hour_style']); ?>" style="background-color: <?php echo esc_attr($settings['hand_hour_color']); ?>; width: <?php echo esc_attr($settings['hand_hour_thick']); ?>px;"></div>
                        <div class="h-min <?php echo esc_attr($settings['hand_min_style']); ?>" style="background-color: <?php echo esc_attr($settings['hand_min_color']); ?>; width: <?php echo esc_attr($settings['hand_min_thick']); ?>px;"></div>
                        <div class="h-sec <?php echo esc_attr($settings['hand_sec_style']); ?>" style="background-color: <?php echo esc_attr($settings['hand_sec_color']); ?>; width: <?php echo esc_attr($settings['hand_sec_thick']); ?>px;"></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- BELOW ANALOG STATUS -->
            <?php if ($settings['show_status'] === 'yes' && $settings['status_pos'] === 'below_analog'): ?>
                 <div class="status-row" style="color: <?php echo ($status === 'open') ? esc_attr($settings['color_open']) : esc_attr($settings['color_closed']); ?>; font-family: '<?php echo esc_attr($settings['font_status']); ?>';"><?php echo ($status === 'open') ? esc_html($settings['text_open']) : esc_html($settings['text_closed']); ?></div>
            <?php endif; ?>

            <!-- Digital & Info Section -->
            <div class="adremm-clock-info">
                <!-- ABOVE DIGITAL STATUS -->
                <?php if ($settings['show_status'] === 'yes' && $settings['status_pos'] === 'above_digital'): ?>
                    <div class="status-row" style="color: <?php echo ($status === 'open') ? esc_attr($settings['color_open']) : esc_attr($settings['color_closed']); ?>; font-family: '<?php echo esc_attr($settings['font_status']); ?>';"><?php echo ($status === 'open') ? esc_html($settings['text_open']) : esc_html($settings['text_closed']); ?></div>
                <?php endif; ?>

                <?php if ($settings['show_digital'] === 'yes'): ?>
                    <div class="time-row digital-style-<?php echo esc_attr($settings['digital_style']); ?>" style="
                        font-family: '<?php echo esc_attr($settings['digital_font']); ?>';
                        color: <?php echo esc_attr($settings['digital_color']); ?>;
                        background-color: <?php echo esc_attr($settings['digital_bg']); ?>;
                    ">
                        <span class="time-digital"></span>
                    </div>
                <?php endif; ?>

                <!-- BELOW DIGITAL STATUS (DEFAULT) -->
                <?php if ($settings['show_status'] === 'yes' && ($settings['status_pos'] === 'below_digital' || empty($settings['status_pos']))): ?>
                    <div class="status-row" style="color: <?php echo ($status === 'open') ? esc_attr($settings['color_open']) : esc_attr($settings['color_closed']); ?>; font-family: '<?php echo esc_attr($settings['font_status']); ?>';"><?php echo ($status === 'open') ? esc_html($settings['text_open']) : esc_html($settings['text_closed']); ?></div>
                <?php endif; ?>

                <?php if ($settings['show_date'] === 'yes'): ?>
                    <div class="date-row" style="color: <?php echo esc_attr($settings['color_date']); ?>; font-family: '<?php echo esc_attr($settings['font_date']); ?>';">
                        <span class="date-text"></span>
                    </div>
                <?php endif; ?>

                <!-- BELOW DATE STATUS -->
                <?php if ($settings['show_status'] === 'yes' && $settings['status_pos'] === 'below_date'): ?>
                    <div class="status-row" style="color: <?php echo ($status === 'open') ? esc_attr($settings['color_open']) : esc_attr($settings['color_closed']); ?>; font-family: '<?php echo esc_attr($settings['font_status']); ?>';"><?php echo ($status === 'open') ? esc_html($settings['text_open']) : esc_html($settings['text_closed']); ?></div>
                <?php endif; ?>

                <?php if (!empty($settings['extra_message'])): ?>
                    <div class="extra-row" style="color: <?php echo esc_attr($settings['extra_color']); ?>; font-size: <?php echo esc_attr($settings['extra_font_size']); ?>px;">
                        <span><?php echo esc_html($settings['extra_message']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!$is_bar && $settings['is_collapsible'] === 'yes'): ?>
        <!-- Collapsed Tab -->
        <div class="adremm-clock-tab" style="display: none; background-color: <?php echo esc_attr($settings['tab_bg']); ?>; color: <?php echo esc_attr($settings['tab_color']); ?>; font-family: '<?php echo esc_attr($settings['tab_font']); ?>'; box-shadow: <?php echo esc_attr($settings['tab_shadow']); ?>; z-index: 2147483647;">
            <?php if ($settings['tab_arrow'] === 'yes'): ?>
                <span class="tab-arrow"></span>
            <?php endif; ?>
            <span class="tab-label"><?php echo esc_html($settings['tab_text']); ?></span>
        </div>
    <?php endif; ?>
</div>
