<?php
/**
 * Mobile & Tablet Settings View for ADREMM Clock Plugin
 */
if ( ! defined('ABSPATH') ) exit;

$settings = wp_parse_args(get_option('adremm_clock_settings', array()), adremm_clock_get_default_settings());
?>
<div class="wrap adremm-mobile-settings">
    <h1><?php _e('ADREMM Klok – Mobiel & Tablet', 'adremm-clock-plugin'); ?></h1>

    <div class="adremm-clock-editor">
        <div class="adremm-settings-panels">
            <form method="post" action="options.php" id="adremm-clock-mobile-form">
                <?php settings_fields('adremm_clock_options'); ?>
                <!-- Hidden fields for other settings to prevent overwriting -->
                <?php
                foreach($settings as $key => $val) {
                    if (strpos($key, 'mobile_') === false && $key !== 'tablet_breakpoint') {
                        echo '<input type="hidden" name="adremm_clock_settings['.esc_attr($key).']" value="'.esc_attr($val).'">';
                    }
                }
                ?>

                <div class="adremm-panel is-active">
                    <h3>Zichtbaarheid & Breakpoints</h3>
                    <table class="form-table">
                        <tr>
                            <th>Zichtbaar op:</th>
                            <td>
                                <select name="adremm_clock_settings[mobile_visibility]">
                                    <option value="both" <?php selected($settings['mobile_visibility'], 'both'); ?>>Overal (Desktop, Tablet, Mobiel)</option>
                                    <option value="desktop" <?php selected($settings['mobile_visibility'], 'desktop'); ?>>Alleen Desktop</option>
                                    <option value="mobile" <?php selected($settings['mobile_visibility'], 'mobile'); ?>>Alleen Mobiel</option>
                                    <option value="tablet" <?php selected($settings['mobile_visibility'], 'tablet'); ?>>Alleen Tablet</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Mobile Breakpoint (px):</th>
                            <td>
                                <select name="adremm_clock_settings[mobile_breakpoint]">
                                    <option value="480" <?php selected($settings['mobile_breakpoint'], '480'); ?>>480px (Smartphone)</option>
                                    <option value="600" <?php selected($settings['mobile_breakpoint'], '600'); ?>>600px (Grote smartphone)</option>
                                    <option value="768" <?php selected($settings['mobile_breakpoint'], '768'); ?>>768px (Default)</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Tablet Breakpoint (px):</th>
                            <td>
                                <select name="adremm_clock_settings[tablet_breakpoint]">
                                    <option value="992" <?php selected($settings['tablet_breakpoint'], '992'); ?>>992px (Kleine Tablet)</option>
                                    <option value="1024" <?php selected($settings['tablet_breakpoint'], '1024'); ?>>1024px (iPad/Tablet)</option>
                                    <option value="1200" <?php selected($settings['tablet_breakpoint'], '1200'); ?>>1200px (Grote Tablet / Laptop)</option>
                                </select>
                            </td>
                        </tr>
                    </table>

                    <h3>Mobile Overrides</h3>
                    <table class="form-table">
                        <tr>
                            <th>Mobile Positie:</th>
                            <td>
                                <select name="adremm_clock_settings[mobile_position]">
                                    <option value="top-right" <?php selected($settings['mobile_position'], 'top-right'); ?>>Rechtsboven</option>
                                    <option value="bottom-right" <?php selected($settings['mobile_position'], 'bottom-right'); ?>>Rechtsonder</option>
                                    <option value="top-left" <?php selected($settings['mobile_position'], 'top-left'); ?>>Linksboven</option>
                                    <option value="bottom-left" <?php selected($settings['mobile_position'], 'bottom-left'); ?>>Linksonder</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Mobile Grootte:</th>
                            <td>
                                <select name="adremm_clock_settings[mobile_size]">
                                    <option value="small" <?php selected($settings['mobile_size'], 'small'); ?>>Klein (Aanbevolen)</option>
                                    <option value="normal" <?php selected($settings['mobile_size'], 'normal'); ?>>Normaal</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php submit_button(__('Instellingen Opslaan', 'adremm-clock-plugin')); ?>
            </form>
        </div>

        <div class="adremm-preview-col">
            <div class="adremm-preview-card mobile-preview">
                <h3>Mobiele Preview</h3>
                <div class="mobile-frame">
                    <div id="clock-mobile-view">
                        <div class="analog-preview">
                            <div class="clock-face">
                                <div class="hand hour"></div>
                                <div class="hand min"></div>
                                <div class="hand sec"></div>
                            </div>
                        </div>
                        <div class="clock-info">
                            <div class="preview-time">12:34:56</div>
                            <div class="preview-status">Wij zijn geopend</div>
                            <div class="preview-date">Maandag 1 januari</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.mobile-frame {
    width: 320px;
    height: 568px;
    border: 10px solid #222;
    border-radius: 30px;
    margin: 20px auto;
    background: #f1f1f1;
    position: relative;
    overflow: hidden;
    transform: scale(0.8);
    transform-origin: top center;
}
#clock-mobile-view {
    width: 100%;
    height: 100%;
}
</style>
