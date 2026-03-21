<?php
/**
 * Advanced Clock Template for ADREMM Clock Plugin
 */

if ( ! defined('ABSPATH') ) exit;

// $settings already parsed in adremm-clock-plugin.php
$status_data = adremm_clock_get_status();
$pos = $status_data['pos'] !== 'inherit' ? $status_data['pos'] : (isset($settings['position']) ? $settings['position'] : 'bottom-right');

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
    '--user-bg: %s; --user-text: %s; font-family: %s; --digital-font-size-base: %spx;',
    isset($settings['bg_color']) ? $settings['bg_color'] : '#fff',
    isset($settings['text_color']) ? $settings['text_color'] : '#000',
    (isset($settings['theme_font']) && $settings['theme_font'] === 'inherit' ? 'inherit' : '"' . (isset($settings['theme_font']) ? $settings['theme_font'] : 'Inter') . '", sans-serif'),
    isset($settings['digital_font_size']) ? $settings['digital_font_size'] : '32'
);
?>

<div id="adremm-clock-wrapper" class="<?php echo esc_attr($layout_class); ?> <?php echo esc_attr($position_class); ?> <?php echo esc_attr($theme_class); ?> panel-size-<?php echo esc_attr(isset($settings['panel_size']) ? $settings['panel_size'] : 'normal'); ?> <?php echo esc_attr($mobile_vis_class); ?> <?php echo esc_attr($mobile_pos_class); ?> <?php echo esc_attr($mobile_size_class); ?> <?php echo (isset($settings['panel_custom_override']) && $settings['panel_custom_override'] === 'yes') ? 'custom-width-active' : ''; ?>" style="<?php echo esc_attr($style_vars); ?> <?php if(isset($settings['panel_custom_override']) && $settings['panel_custom_override'] === 'yes') { echo '--panel-width: ' . esc_attr($settings['panel_width']) . 'px;'; } ?>">

    <div class="adremm-clock-container" style="box-shadow: <?php echo esc_attr(isset($settings['panel_shadow']) ? $settings['panel_shadow'] : 'none'); ?>; border: <?php echo esc_attr(isset($settings['panel_border']) ? $settings['panel_border'] : 'none'); ?>; width: 100%; --digital-width-final: <?php echo (isset($settings['digital_style']) && $settings['digital_style'] === 'custom') ? 'calc(' . esc_attr($settings['digital_width']) . 'px * var(--panel-scale))' : '100%'; ?>; --digital-height-final: <?php echo (isset($settings['digital_style']) && $settings['digital_style'] === 'custom') ? 'calc(' . esc_attr($settings['digital_height']) . 'px * var(--panel-scale))' : 'auto'; ?>;">
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

        <div class="adremm-clock-main" style="display: flex; flex-direction: column; <?php
            if (isset($settings['status_pos']) && $settings['status_pos'] === 'above_digital') { echo 'order: 0;'; }
        ?>">
            <!-- Analog Section -->
            <?php if (isset($settings['show_analog']) && $settings['show_analog'] === 'yes'): ?>
                <div class="adremm-clock-analog" style="<?php
                    if (isset($settings['status_pos']) && $settings['status_pos'] === 'above_analog') { echo 'order: -1;'; }
                    elseif (isset($settings['status_pos']) && $settings['status_pos'] === 'below_analog') { echo 'order: 0;'; }
                ?>">
                    <div class="face <?php echo (isset($settings['analog_theme']) && $settings['analog_theme'] === 'mondriaan') ? 'theme-mondriaan' : ''; ?>" style="
                        background-color: <?php echo (isset($settings['analog_theme']) && $settings['analog_theme'] === 'mondriaan') ? '#fff' : esc_attr(isset($settings['analog_bg_color']) ? $settings['analog_bg_color'] : '#000'); ?>;
                        background-image: <?php echo (!empty($settings['analog_bg_image']) && (!isset($settings['analog_theme']) || $settings['analog_theme'] !== 'mondriaan')) ? 'url('.esc_url($settings['analog_bg_image']).')' : 'none'; ?>;
                        background-size: cover;
                        background-position: center;
                        border-color: <?php echo (isset($settings['analog_theme']) && $settings['analog_theme'] === 'mondriaan') ? '#000' : esc_attr(isset($settings['analog_ring_color']) ? $settings['analog_ring_color'] : 'transparent'); ?>;
                        border-width: <?php echo (isset($settings['analog_theme']) && $settings['analog_theme'] === 'mondriaan') ? '8px' : esc_attr(isset($settings['analog_ring_size']) ? $settings['analog_ring_size'] : '0'); ?>px;
                    ">
                        <?php if (isset($settings['analog_theme']) && $settings['analog_theme'] === 'mondriaan'): ?>
                            <div class="mondriaan-elements">
                                <div class="mondriaan-mark-12"></div>
                                <div class="mondriaan-mark-9"></div>
                                <div class="mondriaan-hub"></div>
                            </div>
                        <?php endif; ?>
                        <div class="notations hour-notations <?php echo (isset($settings['analog_not_above']) && $settings['analog_not_above'] === 'yes') ? 'above' : ''; ?>" style="color: <?php echo esc_attr(isset($settings['analog_hour_color']) ? $settings['analog_hour_color'] : '#fff'); ?>; --not-thick: <?php echo esc_attr(isset($settings['analog_hour_thick']) ? $settings['analog_hour_thick'] : '2'); ?>px; --not-len: <?php echo esc_attr(isset($settings['analog_hour_length']) ? $settings['analog_hour_length'] : '10'); ?>px; transform: scale(<?php echo esc_attr(isset($settings['analog_not_scale']) ? $settings['analog_not_scale'] : '1.0'); ?>);">
                            <?php for($i=1; $i<=12; $i++): ?><i style="transform: rotate(<?php echo $i*30; ?>deg)"></i><?php endfor; ?>
                        </div>
                        <div class="notations min-notations <?php echo (isset($settings['analog_not_above']) && $settings['analog_not_above'] === 'yes') ? 'above' : ''; ?>" style="color: <?php echo esc_attr(isset($settings['analog_min_color']) ? $settings['analog_min_color'] : '#fff'); ?>; --not-thick: <?php echo esc_attr(isset($settings['analog_min_thick']) ? $settings['analog_min_thick'] : '1'); ?>px; --not-len: <?php echo esc_attr(isset($settings['analog_min_length']) ? $settings['analog_min_length'] : '5'); ?>px; transform: scale(<?php echo esc_attr(isset($settings['analog_not_scale']) ? $settings['analog_not_scale'] : '1.0'); ?>);">
                            <?php for($i=1; $i<=60; $i++): if($i%5!==0): ?><i style="transform: rotate(<?php echo $i*6; ?>deg)"></i><?php endif; endfor; ?>
                        </div>
                        <div class="h-hour <?php echo esc_attr(isset($settings['hand_hour_style']) ? $settings['hand_hour_style'] : 'rectangle'); ?> <?php echo (isset($settings['analog_overshoot']) && $settings['analog_overshoot'] === 'yes') ? 'has-overshoot' : ''; ?>" style="background-color: <?php echo esc_attr(isset($settings['hand_hour_color']) ? $settings['hand_hour_color'] : '#fff'); ?>; color: <?php echo esc_attr(isset($settings['hand_hour_color']) ? $settings['hand_hour_color'] : '#fff'); ?>; width: <?php echo esc_attr(isset($settings['hand_hour_thick']) ? $settings['hand_hour_thick'] : '4'); ?>px; height: <?php echo esc_attr(isset($settings['hand_hour_len']) ? $settings['hand_hour_len'] : '50'); ?>%; --adremm-scale: <?php echo esc_attr(isset($settings['analog_hand_scale']) ? $settings['analog_hand_scale'] : '1.0'); ?>;">
                            <?php if (isset($settings['analog_center_ring']) && $settings['analog_center_ring'] === 'yes'): ?>
                                <div class="center-ring" style="width:<?php echo esc_attr(isset($settings['analog_center_ring_size']) ? $settings['analog_center_ring_size'] : '8'); ?>px; height:<?php echo esc_attr(isset($settings['analog_center_ring_size']) ? $settings['analog_center_ring_size'] : '8'); ?>px; background:<?php echo esc_attr(isset($settings['hand_hour_color']) ? $settings['hand_hour_color'] : '#fff'); ?>;"></div>
                            <?php endif; ?>
                        </div>
                        <div class="h-min <?php echo esc_attr(isset($settings['hand_min_style']) ? $settings['hand_min_style'] : 'rectangle'); ?> <?php echo (isset($settings['analog_overshoot']) && $settings['analog_overshoot'] === 'yes') ? 'has-overshoot' : ''; ?>" style="background-color: <?php echo esc_attr(isset($settings['hand_min_color']) ? $settings['hand_min_color'] : '#fff'); ?>; color: <?php echo esc_attr(isset($settings['hand_min_color']) ? $settings['hand_min_color'] : '#fff'); ?>; width: <?php echo esc_attr(isset($settings['hand_min_thick']) ? $settings['hand_min_thick'] : '3'); ?>px; height: <?php echo esc_attr(isset($settings['hand_min_len']) ? $settings['hand_min_len'] : '70'); ?>%; --adremm-scale: <?php echo esc_attr(isset($settings['analog_hand_scale']) ? $settings['analog_hand_scale'] : '1.0'); ?>;">
                            <?php if (isset($settings['analog_center_ring']) && $settings['analog_center_ring'] === 'yes'): ?>
                                <div class="center-ring" style="width:<?php echo esc_attr(isset($settings['analog_center_ring_size']) ? $settings['analog_center_ring_size'] : '8'); ?>px; height:<?php echo esc_attr(isset($settings['analog_center_ring_size']) ? $settings['analog_center_ring_size'] : '8'); ?>px; background:<?php echo esc_attr(isset($settings['hand_min_color']) ? $settings['hand_min_color'] : '#fff'); ?>;"></div>
                            <?php endif; ?>
                        </div>
                        <div class="h-sec <?php echo esc_attr(isset($settings['hand_sec_style']) ? $settings['hand_sec_style'] : 'point'); ?> <?php echo (isset($settings['analog_overshoot']) && $settings['analog_overshoot'] === 'yes') ? 'has-overshoot' : ''; ?>" style="background-color: <?php echo esc_attr(isset($settings['hand_sec_color']) ? $settings['hand_sec_color'] : '#f00'); ?>; color: <?php echo esc_attr(isset($settings['hand_sec_color']) ? $settings['hand_sec_color'] : '#f00'); ?>; width: <?php echo esc_attr(isset($settings['hand_sec_thick']) ? $settings['hand_sec_thick'] : '1'); ?>px; height: <?php echo esc_attr(isset($settings['hand_sec_len']) ? $settings['hand_sec_len'] : '80'); ?>%; --adremm-scale: <?php echo esc_attr(isset($settings['analog_hand_scale']) ? $settings['analog_hand_scale'] : '1.0'); ?>;">
                            <?php if (isset($settings['analog_center_ring']) && $settings['analog_center_ring'] === 'yes'): ?>
                                <div class="center-ring" style="width:<?php echo esc_attr(isset($settings['analog_center_ring_size']) ? $settings['analog_center_ring_size'] : '8'); ?>px; height:<?php echo esc_attr(isset($settings['analog_center_ring_size']) ? $settings['analog_center_ring_size'] : '8'); ?>px; background:<?php echo esc_attr(isset($settings['hand_sec_color']) ? $settings['hand_sec_color'] : '#f00'); ?>;"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Digital & Info Section -->
            <div class="adremm-clock-info" style="display:contents;">
                <?php if ($settings['show_digital'] === 'yes'): ?>
                        <?php if (isset($settings['digital_style']) && $settings['digital_style'] === 'custom' && !empty($settings['digital_font_url'])): ?>
                            <style>@font-face { font-family: 'CustomFont'; src: url('<?php echo esc_url($settings['digital_font_url']); ?>'); }</style>
                        <?php endif; ?>
                        <div class="time-row digital-style-<?php echo esc_attr(isset($settings['digital_style']) ? $settings['digital_style'] : 'custom'); ?> <?php echo (isset($settings['digital_glow']) && $settings['digital_glow'] === 'yes') ? 'has-glow' : ''; ?> <?php echo (isset($settings['digital_orientation']) && $settings['digital_orientation'] === 'vertical') ? 'vertical' : ''; ?>" style="
                        font-family: <?php
                            if (isset($settings['digital_style']) && $settings['digital_style'] === 'custom' && !empty($settings['digital_font_url'])) {
                                echo "'CustomFont', sans-serif";
                            } else {
                                echo (isset($settings['digital_font']) && $settings['digital_font'] === 'Thema' ? 'inherit' : "'" . esc_attr(isset($settings['digital_font']) ? $settings['digital_font'] : 'Inter') . "'");
                            }
                        ?>;
                        color: <?php echo esc_attr(isset($settings['digital_color']) ? $settings['digital_color'] : '#000'); ?>;
                        font-size: calc(var(--digital-font-size-base) * var(--panel-scale));
                        font-weight: <?php echo esc_attr(isset($settings['digital_font_weight']) ? $settings['digital_font_weight'] : '400'); ?>;
                        font-style: <?php echo (isset($settings['digital_italic']) && $settings['digital_italic'] === 'yes') ? 'italic' : 'normal'; ?>;
                        border: <?php echo esc_attr(isset($settings['digital_border_size']) ? $settings['digital_border_size'] : '0'); ?>px solid <?php echo esc_attr(isset($settings['digital_border_color']) ? $settings['digital_border_color'] : 'transparent'); ?>;
                        border-radius: <?php echo esc_attr(isset($settings['digital_border_radius']) ? $settings['digital_border_radius'] : '0'); ?>px;
                        background-color: <?php echo esc_attr(isset($settings['digital_bg']) ? $settings['digital_bg'] : 'transparent'); ?>;
                        --digital-glow-color: <?php echo esc_attr(isset($settings['digital_glow_color']) ? $settings['digital_glow_color'] : 'transparent'); ?>;
                        --digital-glow-spread: <?php echo esc_attr(isset($settings['digital_glow_spread']) ? $settings['digital_glow_spread'] : '0'); ?>px;
                        <?php if(isset($settings['digital_style']) && $settings['digital_style'] === 'custom'): ?>
                        width: calc(<?php echo esc_attr($settings['digital_width']); ?>px * var(--panel-scale));
                        height: calc(<?php echo esc_attr($settings['digital_height']); ?>px * var(--panel-scale));
                        display: flex; align-items: center; justify-content: center;
                        <?php endif; ?>
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
                                        <?php if (!empty($settings['vintage_logo'])): ?>
                                            <img src="<?php echo esc_url($settings['vintage_logo']); ?>" class="logo-circle" style="object-fit:cover; border:none;">
                                        <?php else: ?>
                                            <div class="logo-circle">A</div>
                                        <?php endif; ?>
                                        <div class="brand-text">Radio</div>
                                    </div>
                                    <div class="radio-controls-grid">
                                        <div class="power-btn" id="adremm-radio-power"><i class="dashicons dashicons-marker"></i></div>
                                        <div class="volume-knob" id="adremm-radio-volume">
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

                <?php if (isset($settings['show_status']) && $settings['show_status'] === 'yes'): ?>
                    <div class="status-row" style="color: <?php echo ($status_data['status'] === 'open') ? esc_attr(isset($settings['color_open']) ? $settings['color_open'] : '#0f0') : esc_attr(isset($settings['color_closed']) ? $settings['color_closed'] : '#f00'); ?>; font-family: <?php echo (isset($settings['font_status']) && $settings['font_status'] === 'Thema' ? 'inherit' : "'" . esc_attr(isset($settings['font_status']) ? $settings['font_status'] : 'Inter') . "'"); ?>; <?php
                        if (isset($settings['status_pos'])) {
                            if ($settings['status_pos'] === 'above_digital') echo 'order: -1;';
                            elseif ($settings['status_pos'] === 'above_analog') echo 'order: -2;';
                            elseif ($settings['status_pos'] === 'below_analog') echo 'order: 1;';
                            elseif ($settings['status_pos'] === 'below_date') echo 'order: 5;';
                        }
                    ?>"><?php echo esc_html($status_data['text']); ?></div>
                <?php endif; ?>

                <?php if (isset($settings['show_date']) && $settings['show_date'] === 'yes'): ?>
                    <div class="date-row" style="order: 4; color: <?php echo esc_attr(isset($settings['color_date']) ? $settings['color_date'] : '#ccc'); ?>; font-family: <?php echo (isset($settings['font_date']) && $settings['font_date'] === 'Thema' ? 'inherit' : "'" . esc_attr(isset($settings['font_date']) ? $settings['font_date'] : 'Inter') . "'"); ?>;">
                        <span class="date-text"></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($settings['extra_message'])): ?>
                    <div class="extra-row" style="order: 10; color: <?php echo esc_attr(isset($settings['extra_color']) ? $settings['extra_color'] : '#aaa'); ?>; font-size: <?php echo esc_attr(isset($settings['extra_font_size']) ? $settings['extra_font_size'] : '11'); ?>px;">
                        <span><?php echo esc_html($settings['extra_message']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!$is_bar && isset($settings['is_collapsible']) && $settings['is_collapsible'] === 'yes'): ?>
        <div class="adremm-clock-tab" style="display: none; background-color: <?php echo esc_attr(isset($settings['tab_bg']) ? $settings['tab_bg'] : '#fff'); ?>; color: <?php echo esc_attr(isset($settings['tab_color']) ? $settings['tab_color'] : '#000'); ?>; font-family: '<?php echo esc_attr(isset($settings['tab_font']) ? $settings['tab_font'] : 'Inter'); ?>'; box-shadow: <?php echo esc_attr(isset($settings['tab_shadow']) ? $settings['tab_shadow'] : 'none'); ?>; z-index: 2147483647;">
            <?php if (isset($settings['tab_arrow']) && $settings['tab_arrow'] === 'yes'): ?>
                <?php if (!empty($settings['tab_arrow_img'])): ?>
                    <img src="<?php echo esc_url($settings['tab_arrow_img']); ?>" class="tab-arrow-img" style="width:15px; height:auto; margin-bottom:5px;">
                <?php else: ?>
                    <span class="tab-arrow"></span>
                <?php endif; ?>
            <?php endif; ?>
            <span class="tab-label"><?php echo esc_html(isset($settings['tab_text']) ? $settings['tab_text'] : 'CLOCK'); ?></span>
        </div>
    <?php endif; ?>
</div>
