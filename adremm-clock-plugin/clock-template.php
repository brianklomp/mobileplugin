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

// Get menu if selected
$menu_html = '';
if (!empty($settings['menu_id']) && $settings['menu_id'] !== 'none') {
    $menu_html = wp_nav_menu(array(
        'menu' => $settings['menu_id'],
        'container' => 'nav',
        'container_class' => 'adremm-clock-nav',
        'fallback_cb' => false,
        'echo' => false
    ));
}
?>

<div id="adremm-clock-root" class="<?php echo esc_attr($position_class); ?> <?php echo esc_attr($theme_class); ?>" style="<?php echo esc_attr($style_vars); ?>">
    <!-- Floating Panel -->
    <div class="adremm-clock-panel">
        <button class="adremm-clock-close-btn" title="<?php _e('Sluiten', 'adremm-clock-plugin'); ?>">&times;</button>
        <div class="adremm-clock-inner">
            <div class="time-wrap">
                <span class="time"></span>
            </div>
            <div class="date-wrap">
                <span class="date"></span>
            </div>
            <?php if ($menu_html): ?>
                <div class="adremm-clock-menu-wrap">
                    <?php echo $menu_html; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Collapsed Tab (25x100px) -->
    <div class="adremm-clock-tab" style="display: none;">
        <span class="tab-label"><?php _e('KLOK', 'adremm-clock-plugin'); ?></span>
    </div>
</div>
