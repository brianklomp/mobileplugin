<?php
/**
 * Redesigned Settings Page View for ADREMM Clock Plugin
 */
if ( ! defined('ABSPATH') ) exit;

$settings = get_option('adremm_clock_settings', adremm_clock_get_default_settings());
$fonts = adremm_clock_get_google_fonts();
?>
<div class="wrap adremm-clock-v2">
    <h1><?php _e('SET-UP Clock – Instellingen', 'adremm-clock-plugin'); ?></h1>

    <div class="adremm-nav-tabs">
        <a href="#tab-thema" class="nav-tab is-active"><?php _e('Thema', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-wijzerplaat" class="nav-tab"><?php _e('Wijzerplaat', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-wijzers" class="nav-tab"><?php _e('Wijzers', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-digitaal" class="nav-tab"><?php _e('Digitaal', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-status" class="nav-tab"><?php _e('Status', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-extra" class="nav-tab"><?php _e('Extra', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-positie" class="nav-tab"><?php _e('Positie', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-inklapbaar" class="nav-tab"><?php _e('Inklapbaar', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-taal" class="nav-tab"><?php _e('Taal', 'adremm-clock-plugin'); ?></a>
    </div>

    <form method="post" action="options.php" id="adremm-clock-form">
        <?php settings_fields('adremm_clock_options'); ?>

        <div class="adremm-clock-editor">
            <!-- Left: Settings Panels -->
            <div class="adremm-settings-panels">

                <!-- STATUS TAB (Matching Image) -->
                <div id="tab-status" class="adremm-panel is-active">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Toon Status', 'adremm-clock-plugin'); ?><br><small>Toon Status</small></th>
                            <td>
                                <label><input type="radio" name="adremm_clock_settings[show_status]" value="yes" <?php checked($settings['show_status'], 'yes'); ?>> Ja</label>
                                <label style="margin-left:15px;"><input type="radio" name="adremm_clock_settings[show_status]" value="no" <?php checked($settings['show_status'], 'no'); ?>> Nee</label>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Tekst Open', 'adremm-clock-plugin'); ?><br><small>Tekst Open</small></th>
                            <td><input type="text" name="adremm_clock_settings[text_open]" value="<?php echo esc_attr($settings['text_open']); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><?php _e('Tekst Gesloten', 'adremm-clock-plugin'); ?><br><small>Tekst Gesloten</small></th>
                            <td><input type="text" name="adremm_clock_settings[text_closed]" value="<?php echo esc_attr($settings['text_closed']); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><?php _e('Kleur Open', 'adremm-clock-plugin'); ?><br><small>Kleur Open</small></th>
                            <td><input type="text" name="adremm_clock_settings[color_open]" value="<?php echo esc_attr($settings['color_open']); ?>" class="adremm-color-picker" data-alpha="true"></td>
                        </tr>
                        <tr>
                            <th><?php _e('Kleur Gesloten', 'adremm-clock-plugin'); ?><br><small>Kleur Gesloten</small></th>
                            <td><input type="text" name="adremm_clock_settings[color_closed]" value="<?php echo esc_attr($settings['color_closed']); ?>" class="adremm-color-picker" data-alpha="true"></td>
                        </tr>
                        <tr>
                            <th><?php _e('Lettertype', 'adremm-clock-plugin'); ?><br><small>Lettertype</small></th>
                            <td>
                                <select name="adremm_clock_settings[font_status]" class="adremm-font-select">
                                    <?php foreach ($fonts as $font) : ?>
                                        <option value="<?php echo esc_attr($font); ?>" <?php selected($settings['font_status'], $font); ?> style="font-family: '<?php echo esc_attr($font); ?>';"><?php echo esc_html($font); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Toon Datum', 'adremm-clock-plugin'); ?><br><small>Toon Datum</small></th>
                            <td>
                                <label><input type="radio" name="adremm_clock_settings[show_date]" value="yes" <?php checked($settings['show_date'], 'yes'); ?>> Ja</label>
                                <label style="margin-left:15px;"><input type="radio" name="adremm_clock_settings[show_date]" value="no" <?php checked($settings['show_date'], 'no'); ?>> Nee</label>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Kleur Datum', 'adremm-clock-plugin'); ?><br><small>Kleur Datum</small></th>
                            <td><input type="text" name="adremm_clock_settings[color_date]" value="<?php echo esc_attr($settings['color_date']); ?>" class="adremm-color-picker" data-alpha="true"></td>
                        </tr>
                    </table>
                </div>

                <!-- POSITIE TAB (Joystick) -->
                <div id="tab-positie" class="adremm-panel">
                    <h3><?php _e('Positie (Joystick)', 'adremm-clock-plugin'); ?></h3>
                    <div class="adremm-joystick-container">
                        <div class="joystick-grid">
                            <?php
                            $joystick = array(
                                'top-left' => '↖', 'top-center' => '↑', 'top-right' => '↗',
                                'middle-left' => '←', 'center' => '●', 'middle-right' => '→',
                                'bottom-left' => '↙', 'bottom-center' => '↓', 'bottom-right' => '↘'
                            );
                            foreach ($joystick as $key => $icon):
                                $active = ($settings['position'] === $key) ? 'is-selected' : '';
                                if ($key === 'center') {
                                    echo '<div class="joy-btn joy-center">'.$icon.'</div>';
                                    continue;
                                }
                            ?>
                                <label class="joy-btn <?php echo $active; ?>" title="<?php echo esc_attr($key); ?>">
                                    <input type="radio" name="adremm_clock_settings[position]" value="<?php echo $key; ?>" <?php checked($settings['position'], $key); ?>>
                                    <span><?php echo $icon; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <p class="description"><?php _e('HD (↑) en FT (↓) worden als 60px hoge balken weergegeven.', 'adremm-clock-plugin'); ?></p>
                </div>

                <!-- EXTRA TAB -->
                <div id="tab-extra" class="adremm-panel">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Extra Bericht', 'adremm-clock-plugin'); ?></th>
                            <td><textarea name="adremm_clock_settings[extra_message]" class="large-text" rows="4"><?php echo esc_textarea($settings['extra_message']); ?></textarea></td>
                        </tr>
                        <tr>
                            <th><?php _e('Kleur Bericht', 'adremm-clock-plugin'); ?></th>
                            <td><input type="text" name="adremm_clock_settings[extra_color]" value="<?php echo esc_attr($settings['extra_color']); ?>" class="adremm-color-picker" data-alpha="true"></td>
                        </tr>
                    </table>
                </div>

                <!-- OTHER TABS (Placeholders) -->
                <div id="tab-thema" class="adremm-panel"><h3>Thema Instellingen</h3></div>
                <div id="tab-wijzerplaat" class="adremm-panel"><h3>Wijzerplaat Instellingen</h3></div>
                <div id="tab-wijzers" class="adremm-panel"><h3>Wijzers Instellingen</h3></div>
                <div id="tab-digitaal" class="adremm-panel"><h3>Digitaal Instellingen</h3></div>
                <div id="tab-inklapbaar" class="adremm-panel"><h3>Inklapbaar Instellingen</h3></div>
                <div id="tab-taal" class="adremm-panel"><h3>Taal Instellingen</h3></div>

            </div>

            <!-- Right: Live Preview Box (Matching Image) -->
            <div class="adremm-preview-sidebar">
                <div class="adremm-preview-card">
                    <h3>Live preview</h3>
                    <div class="preview-stage">
                        <div id="clock-live-view">
                            <!-- Analog Clock Placeholder -->
                            <div class="analog-preview">
                                <div class="clock-face">
                                    <div class="hand hour"></div>
                                    <div class="hand min"></div>
                                    <div class="hand sec"></div>
                                </div>
                            </div>
                            <!-- Info Area -->
                            <div class="clock-info">
                                <div class="preview-time">12:34:56</div>
                                <div class="preview-status"><?php echo esc_html($settings['text_open']); ?></div>
                                <div class="preview-date">Maandag 1 januari</div>
                                <div class="preview-extra"><?php echo esc_html($settings['extra_message']); ?></div>
                            </div>
                        </div>
                    </div>
                    <p class="preview-footer">WYSIWYG - Direct zichtbaar</p>
                </div>
                <?php submit_button(__('Wijzigingen Opslaan', 'adremm-clock-plugin'), 'primary button-large'); ?>
            </div>
        </div>
    </form>
</div>
<style>
/* Simplified Joystick & Sidebar CSS */
.adremm-clock-v2 { max-width: 1200px; margin-top: 20px; }
.adremm-nav-tabs { margin-bottom: 20px; border-bottom: 1px solid #ccc; display: flex; flex-wrap: wrap; }
.adremm-nav-tabs .nav-tab { margin-bottom: -1px; }

.adremm-clock-editor { display: grid; grid-template-columns: 1fr 320px; gap: 30px; align-items: start; }
.adremm-panel { display: none; background: #fff; padding: 20px; border: 1px solid #ccc; }
.adremm-panel.is-active { display: block; }

/* Joystick */
.adremm-joystick-container { background: #f0f0f1; padding: 20px; display: inline-block; border-radius: 8px; }
.joystick-grid { display: grid; grid-template-columns: repeat(3, 50px); grid-template-rows: repeat(3, 50px); gap: 10px; }
.joy-btn { background: #fff; border: 1px solid #ccc; border-radius: 4px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 20px; position: relative; }
.joy-btn input { display: none; }
.joy-btn:hover { background: #f0f6fb; border-color: #2271b1; }
.joy-btn.is-selected { background: #0073aa; color: #fff; border-color: #0073aa; }
.joy-center { background: #ddd; cursor: default; }

/* Preview Card */
.adremm-preview-card { background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; padding: 15px; position: sticky; top: 40px; }
.preview-stage { background: #fff; border: 1px solid #eee; border-radius: 4px; padding: 20px; min-height: 300px; display: flex; align-items: center; justify-content: center; text-align: center; }
.analog-preview { margin-bottom: 15px; }
.clock-face { width: 100px; height: 100px; border-radius: 50%; background: #000; border: 2px solid #3399ff; position: relative; margin: 0 auto; }
.hand { position: absolute; bottom: 50%; left: 50%; transform-origin: bottom center; background: #fff; border-radius: 4px; }
.hand.hour { height: 30px; width: 4px; margin-left: -2px; }
.hand.min { height: 40px; width: 3px; margin-left: -1.5px; }
.hand.sec { height: 45px; width: 1px; background: red; margin-left: -0.5px; }

.preview-time { font-size: 18px; margin-bottom: 5px; }
.preview-status { font-weight: bold; font-size: 16px; margin-bottom: 5px; }
.preview-date { font-size: 14px; opacity: 0.6; margin-bottom: 10px; }
.preview-extra { font-size: 11px; line-height: 1.4; color: #666; }
.preview-footer { font-size: 11px; opacity: 0.5; margin-top: 10px; text-align: center; }
</style>
