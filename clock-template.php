<?php
/**
 * Advanced Clock Template for ADREMM Clock Plugin
 */

if ( ! defined('ABSPATH') ) exit;

// $settings already parsed in adremm-clock-plugin.php
$pos = isset($settings['position']) ? $settings['position'] : 'bottom-right';

// Determine layout type: Bar (Header/Footer) or Panel (Floating)
$is_bar = ($pos === 'top-center' || $pos === 'bottom-center');
$layout_class = $is_bar ? 'adremm-clock-bar' : 'adremm-clock-panel';
$theme_class = 'theme-mode-' . (isset($settings['theme_mode']) ? $settings['theme_mode'] : 'light');
$position_class = 'adremm-clock-pos-' . $pos;

// Mobile Overrides
$mobile_vis_class = (isset($settings['mobile_visibility']) ? $settings['mobile_visibility'] : 'both') === 'desktop' ? 'adremm-clock-mobile-hide' : '';
$mobile_pos_class = 'mobile-pos-' . (isset($settings['mobile_position']) ? $settings['mobile_position'] : 'bottom-right');
$mobile_size_class = 'mobile-size-' . (isset($settings['mobile_size']) ? $settings['mobile_size'] : 'small');

$style_vars = sprintf(
    '--user-bg: %s; --user-text: %s; font-family: %s;',
    $settings['bg_color'],
    $settings['text_color'],
    ($settings['theme_font'] === 'inherit' ? 'inherit' : '"' . $settings['theme_font'] . '", sans-serif')
);
?>

<div id="adremm-clock-wrapper" class="<?php echo esc_attr($layout_class); ?> <?php echo esc_attr($position_class); ?> <?php echo esc_attr($theme_class); ?> panel-size-<?php echo esc_attr($settings['panel_size']); ?> <?php echo esc_attr($mobile_vis_class); ?> <?php echo esc_attr($mobile_pos_class); ?> <?php echo esc_attr($mobile_size_class); ?>" style="<?php echo esc_attr($style_vars); ?> --panel-width: <?php echo esc_attr($settings['panel_width']); ?>px;">

    <div class="adremm-clock-container" style="box-shadow: <?php echo esc_attr($settings['panel_shadow']); ?>; border: <?php echo esc_attr($settings['panel_border']); ?>; width: var(--panel-width);">
        <?php if (!$is_bar && ($settings['show_close_x'] === 'yes' || $settings['show_close_label'] === 'yes')): ?>
            <button class="adremm-clock-close anim-<?php echo esc_attr(isset($settings['close_x_anim']) ? $settings['close_x_anim'] : 'fade'); ?>" title="<?php _e('Sluiten', 'adremm-clock-plugin'); ?>" style="color: <?php echo esc_attr(isset($settings['color_close_x']) ? $settings['color_close_x'] : (isset($settings['color_close_label']) ? $settings['color_close_label'] : '#000')); ?>;">
                <?php if ($settings['show_close_label'] === 'yes'): ?>
                    <span class="close-label" style="color: <?php echo esc_attr($settings['color_close_label']); ?>;"><?php echo esc_html($settings['close_label']); ?></span>
                <?php endif; ?>
                <?php if ($settings['show_close_x'] === 'yes'): ?>
                    <span class="close-x" style="font-size: <?php echo esc_attr($settings['close_x_size']); ?>px;">&times;</span>
                <?php endif; ?>
            </button>
        <?php endif; ?>

        <div class="adremm-clock-main">
            <!-- ABOVE ANALOG STATUS -->
            <?php if ($settings['show_status'] === 'yes' && $settings['status_pos'] === 'above_analog'): ?>
                 <div class="status-row" style="color: <?php echo ($status === 'open') ? esc_attr($settings['color_open']) : esc_attr($settings['color_closed']); ?>; font-family: <?php echo ($settings['font_status'] === 'Thema' ? 'inherit' : "'" . esc_attr($settings['font_status']) . "'"); ?>;"><?php echo esc_html($status_text); ?></div>
            <?php endif; ?>

            <!-- Analog Section -->
            <?php if ($settings['show_analog'] === 'yes'): ?>
                <div class="adremm-clock-analog">
                    <div class="face" style="
                        background-color: <?php echo esc_attr(isset($settings['analog_bg_color']) ? $settings['analog_bg_color'] : '#000'); ?>;
                        background-image: <?php echo (!empty($settings['analog_bg_image'])) ? 'url('.esc_url($settings['analog_bg_image']).')' : 'none'; ?>;
                        background-size: cover;
                        background-position: center;
                        border-color: <?php echo esc_attr($settings['analog_ring_color']); ?>;
                        border-width: <?php echo esc_attr($settings['analog_ring_size']); ?>px;
                    ">
                        <!-- Notations -->
                        <div class="notations hour-notations <?php echo ($settings['analog_not_above'] === 'yes') ? 'above' : ''; ?>" style="color: <?php echo esc_attr($settings['analog_hour_color']); ?>; --not-thick: <?php echo esc_attr($settings['analog_hour_thick']); ?>px; --not-len: <?php echo esc_attr($settings['analog_hour_length']); ?>px; transform: scale(<?php echo esc_attr(isset($settings['analog_not_scale']) ? $settings['analog_not_scale'] : '1.0'); ?>);">
                            <?php for($i=1; $i<=12; $i++): ?><i style="transform: rotate(<?php echo $i*30; ?>deg)"></i><?php endfor; ?>
                        </div>
                        <div class="notations min-notations <?php echo ($settings['analog_not_above'] === 'yes') ? 'above' : ''; ?>" style="color: <?php echo esc_attr($settings['analog_min_color']); ?>; --not-thick: <?php echo esc_attr($settings['analog_min_thick']); ?>px; --not-len: <?php echo esc_attr($settings['analog_min_length']); ?>px; transform: scale(<?php echo esc_attr(isset($settings['analog_not_scale']) ? $settings['analog_not_scale'] : '1.0'); ?>);">
                            <?php for($i=1; $i<=60; $i++): if($i%5!==0): ?><i style="transform: rotate(<?php echo $i*6; ?>deg)"></i><?php endif; endfor; ?>
                        </div>
                        <div class="h-hour <?php echo esc_attr($settings['hand_hour_style']); ?>" style="background-color: <?php echo esc_attr($settings['hand_hour_color']); ?>; width: <?php echo esc_attr($settings['hand_hour_thick']); ?>px; height: <?php echo esc_attr($settings['hand_hour_len']); ?>%; transform: scale(<?php echo esc_attr(isset($settings['analog_hand_scale']) ? $settings['analog_hand_scale'] : '1.0'); ?>);"></div>
                        <div class="h-min <?php echo esc_attr($settings['hand_min_style']); ?>" style="background-color: <?php echo esc_attr($settings['hand_min_color']); ?>; width: <?php echo esc_attr($settings['hand_min_thick']); ?>px; height: <?php echo esc_attr($settings['hand_min_len']); ?>%; transform: scale(<?php echo esc_attr(isset($settings['analog_hand_scale']) ? $settings['analog_hand_scale'] : '1.0'); ?>);"></div>
                        <div class="h-sec <?php echo esc_attr($settings['hand_sec_style']); ?>" style="background-color: <?php echo esc_attr($settings['hand_sec_color']); ?>; width: <?php echo esc_attr($settings['hand_sec_thick']); ?>px; height: <?php echo esc_attr($settings['hand_sec_len']); ?>%; transform: scale(<?php echo esc_attr(isset($settings['analog_hand_scale']) ? $settings['analog_hand_scale'] : '1.0'); ?>);"></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- BELOW ANALOG STATUS -->
            <?php if ($settings['show_status'] === 'yes' && $settings['status_pos'] === 'below_analog'): ?>
                 <div class="status-row" style="color: <?php echo ($status === 'open') ? esc_attr($settings['color_open']) : esc_attr($settings['color_closed']); ?>; font-family: '<?php echo esc_attr($settings['font_status']); ?>';"><?php echo esc_html($status_text); ?></div>
            <?php endif; ?>

            <!-- Digital & Info Section -->
            <div class="adremm-clock-info">
                <!-- ABOVE DIGITAL STATUS -->
                <?php if ($settings['show_status'] === 'yes' && $settings['status_pos'] === 'above_digital'): ?>
                    <div class="status-row" style="color: <?php echo ($status === 'open') ? esc_attr($settings['color_open']) : esc_attr($settings['color_closed']); ?>; font-family: <?php echo ($settings['font_status'] === 'Thema' ? 'inherit' : "'" . esc_attr($settings['font_status']) . "'"); ?>;"><?php echo esc_html($status_text); ?></div>
                <?php endif; ?>

                <?php if ($settings['show_digital'] === 'yes'): ?>
                    <div class="time-row digital-style-<?php echo esc_attr($settings['digital_style']); ?> <?php echo ($settings['digital_glow'] === 'yes') ? 'has-glow' : ''; ?> <?php echo ($settings['digital_orientation'] === 'vertical') ? 'vertical' : ''; ?>" style="
                        font-family: <?php echo ($settings['digital_font'] === 'Thema' ? 'inherit' : "'" . esc_attr($settings['digital_font']) . "'"); ?>;
                        color: <?php echo esc_attr($settings['digital_color']); ?>;
                        background-color: <?php echo esc_attr($settings['digital_bg']); ?>;
                        --digital-glow-color: <?php echo esc_attr($settings['digital_glow_color']); ?>;
                        --digital-glow-spread: <?php echo esc_attr($settings['digital_glow_spread']); ?>px;
                    ">
                        <?php if ($settings['digital_style'] === 'alarm'): ?>
                            <div class="radio-vintage-body">
                                <div class="nixie-tubes">
                                    <div class="nixie-tube" id="nixie-h1">0</div>
                                    <div class="nixie-tube" id="nixie-h2">0</div>
                                    <div class="nixie-tube-gap">:</div>
                                    <div class="nixie-tube" id="nixie-m1">0</div>
                                    <div class="nixie-tube" id="nixie-m2">0</div>
                                </div>

                                <div class="radio-scale-container">
                                    <div class="scale-indicator"></div>
                                    <div class="radio-scale-scroll-v">
                                        <?php for($i=0; $i<=60; $i++): ?>
                                            <div class="scale-mark"><span>-</span><?php echo adremm_str_pad($i, 2, '0', STR_PAD_LEFT); ?></div>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <div class="radio-side-panel">
                                    <div class="radio-logo-brand">
                                        <div class="logo-circle">A</div>
                                        <div class="brand-text">Radio</div>
                                    </div>
                                    <div class="radio-controls-grill">
                                        <div class="volume-knob" id="adremm-radio-toggle">
                                            <div class="knob-line"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <span class="time-digital"></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- BELOW DIGITAL STATUS (DEFAULT) -->
                <?php if ($settings['show_status'] === 'yes' && ($settings['status_pos'] === 'below_digital' || empty($settings['status_pos']))): ?>
                    <div class="status-row" style="color: <?php echo ($status === 'open') ? esc_attr($settings['color_open']) : esc_attr($settings['color_closed']); ?>; font-family: <?php echo ($settings['font_status'] === 'Thema' ? 'inherit' : "'" . esc_attr($settings['font_status']) . "'"); ?>;"><?php echo esc_html($status_text); ?></div>
                <?php endif; ?>

                <?php if ($settings['show_date'] === 'yes'): ?>
                    <div class="date-row" style="color: <?php echo esc_attr($settings['color_date']); ?>; font-family: <?php echo ($settings['font_date'] === 'Thema' ? 'inherit' : "'" . esc_attr($settings['font_date']) . "'"); ?>;">
                        <span class="date-text"></span>
                    </div>
                <?php endif; ?>

                <!-- BELOW DATE STATUS -->
                <?php if ($settings['show_status'] === 'yes' && $settings['status_pos'] === 'below_date'): ?>
                    <div class="status-row" style="color: <?php echo ($status === 'open') ? esc_attr($settings['color_open']) : esc_attr($settings['color_closed']); ?>; font-family: <?php echo ($settings['font_status'] === 'Thema' ? 'inherit' : "'" . esc_attr($settings['font_status']) . "'"); ?>;"><?php echo esc_html($status_text); ?></div>
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
                <?php if (!empty($settings['tab_arrow_img'])): ?>
                    <img src="<?php echo esc_url($settings['tab_arrow_img']); ?>" class="tab-arrow-img" style="width:15px; height:auto; margin-bottom:5px;">
                <?php else: ?>
                    <span class="tab-arrow"></span>
                <?php endif; ?>
            <?php endif; ?>
            <span class="tab-label"><?php echo esc_html($settings['tab_text']); ?></span>
        </div>
    <?php endif; ?>
</div>
