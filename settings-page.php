<?php
/**
 * Settings Page View for ADREMM Clock Plugin - FULL IMPLEMENTATION
 */
if ( ! defined('ABSPATH') ) exit;

$settings = wp_parse_args(get_option('adremm_clock_settings', array()), adremm_clock_get_default_settings());
$fonts = adremm_clock_get_google_fonts();
?>
<div class="wrap adremm-clock-v2">
    <h1><?php _e('ADREMM Klok – Instellingen', 'adremm-clock-plugin'); ?></h1>
    <?php settings_errors(); ?>

    <div class="adremm-nav-tabs">
        <a href="#tab-thema" class="nav-tab is-active"><?php _e('Thema', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-analoog" class="nav-tab"><?php _e('Analoge klok', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-wijzers" class="nav-tab"><?php _e('Wijzers', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-digitaal" class="nav-tab"><?php _e('Digitaal', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-status" class="nav-tab"><?php _e('Status', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-extra" class="nav-tab"><?php _e('Extra', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-positie" class="nav-tab"><?php _e('Positie', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-paneel" class="nav-tab"><?php _e('Tijdpaneel', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-algemeen" class="nav-tab"><?php _e('Algemeen', 'adremm-clock-plugin'); ?></a>
    </div>

    <form method="post" action="options.php" id="adremm-clock-form">
        <?php settings_fields('adremm_clock_options'); ?>
        <input type="hidden" name="adremm_clock_settings[opening_hours]" value="<?php echo esc_attr($settings['opening_hours']); ?>">

        <div class="adremm-clock-editor">
            <!-- Left: Settings Panels -->
            <div class="adremm-settings-panels">

                <!-- THEMA TAB -->
                <div id="tab-thema" class="adremm-panel is-active">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Thema', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <div class="theme-selector-wrap">
                                    <label class="theme-option">
                                        <input type="radio" name="adremm_clock_settings[theme_mode]" value="light" <?php checked($settings['theme_mode'], 'light'); ?>>
                                        <span class="theme-card">Light</span>
                                    </label>
                                    <label class="theme-option">
                                        <input type="radio" name="adremm_clock_settings[theme_mode]" value="dark" <?php checked($settings['theme_mode'], 'dark'); ?>>
                                        <span class="theme-card">Dark</span>
                                    </label>
                                    <label class="theme-option">
                                        <input type="radio" name="adremm_clock_settings[theme_mode]" value="auto" <?php checked($settings['theme_mode'], 'auto'); ?>>
                                        <span class="theme-card">Auto</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Thema Google Font', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <select name="adremm_clock_settings[theme_font]" class="adremm-font-select">
                                    <?php foreach ($fonts as $font) : ?>
                                        <option value="<?php echo esc_attr($font); ?>" <?php selected($settings['theme_font'], $font); ?>><?php echo esc_html($font); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Tijdpaneel grootte', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <select name="adremm_clock_settings[panel_size]" id="adremm-panel-size-select">
                                    <option value="small" <?php selected($settings['panel_size'], 'small'); ?>>Klein (250px)</option>
                                    <option value="normal" <?php selected($settings['panel_size'], 'normal'); ?>>Normaal (350px)</option>
                                    <option value="large" <?php selected($settings['panel_size'], 'large'); ?>>Groot (450px)</option>
                                </select>
                                <div style="margin-top:10px;">
                                    <input type="hidden" name="adremm_clock_settings[panel_custom_override]" value="no">
                                    <label><input type="checkbox" name="adremm_clock_settings[panel_custom_override]" id="adremm-panel-custom-check" value="yes" <?php checked(isset($settings['panel_custom_override']) ? $settings['panel_custom_override'] : 'no', 'yes'); ?>> <?php _e('Custom Override (vinkje aan voor handmatig)', 'adremm-clock-plugin'); ?></label>
                                    <span style="margin-left:15px;"><?php _e('Breedte:', 'adremm-clock-plugin'); ?></span>
                                    <input type="number" name="adremm_clock_settings[panel_width_custom]" id="adremm-panel-width-custom" value="<?php echo esc_attr(isset($settings['panel_width_custom']) ? $settings['panel_width_custom'] : $settings['panel_width']); ?>" style="width:60px;" step="0.1" <?php disabled(isset($settings['panel_custom_override']) ? $settings['panel_custom_override'] : 'no', 'no'); ?>> px
                                    <input type="hidden" name="adremm_clock_settings[panel_width]" id="adremm-panel-width-hidden" value="<?php echo esc_attr($settings['panel_width']); ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Achtergrond Kleur', 'adremm-clock-plugin'); ?></th>
                            <td><input type="text" name="adremm_clock_settings[bg_color]" value="<?php echo esc_attr($settings['bg_color']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba"></td>
                        </tr>
                        <tr>
                            <th><?php _e('Padding (binnen)', 'adremm-clock-plugin'); ?></th>
                            <td><input type="number" name="adremm_clock_settings[panel_padding]" value="<?php echo esc_attr($settings['panel_padding']); ?>"> px</td>
                        </tr>
                    </table>
                </div>

                <!-- ANALOGE KLOK TAB -->
                <div id="tab-analoog" class="adremm-panel">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Analoge Klok Thema', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <select name="adremm_clock_settings[analog_theme]">
                                    <option value="classic" <?php selected(isset($settings['analog_theme']) ? $settings['analog_theme'] : 'classic', 'classic'); ?>>Classic</option>
                                    <option value="mondriaan" <?php selected(isset($settings['analog_theme']) ? $settings['analog_theme'] : '', 'mondriaan'); ?>>Mondriaan</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Notatie & Wijzer Schaal (%)', 'adremm-clock-plugin'); ?></th>
                            <td>
                                Notatie: <input type="number" name="adremm_clock_settings[analog_not_scale_pct]" value="<?php echo esc_attr(isset($settings['analog_not_scale']) ? $settings['analog_not_scale']*100 : '100'); ?>" style="width:60px;"> %
                                <input type="hidden" name="adremm_clock_settings[analog_not_scale]" value="<?php echo esc_attr($settings['analog_not_scale']); ?>">
                                <span style="margin-left:15px;">Wijzer:</span>
                                <input type="number" name="adremm_clock_settings[analog_hand_scale_pct]" value="<?php echo esc_attr(isset($settings['analog_hand_scale']) ? $settings['analog_hand_scale']*100 : '100'); ?>" style="width:60px;"> %
                                <input type="hidden" name="adremm_clock_settings[analog_hand_scale]" value="<?php echo esc_attr($settings['analog_hand_scale']); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Toon Analoge Klok', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <input type="hidden" name="adremm_clock_settings[show_analog]" value="no">
                                <label><input type="radio" name="adremm_clock_settings[show_analog]" value="yes" <?php checked($settings['show_analog'], 'yes'); ?>> Ja</label>
                                <label style="margin-left:15px;"><input type="radio" name="adremm_clock_settings[show_analog]" value="no" <?php checked($settings['show_analog'], 'no'); ?>> Nee</label>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Achtergrond Wijzerplaat', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <input type="text" name="adremm_clock_settings[analog_bg_image]" value="<?php echo esc_attr($settings['analog_bg_image']); ?>" class="regular-text adremm-media-url">
                                <button type="button" class="button adremm-media-upload">Kies afbeelding</button>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Of Achtergrond Kleur', 'adremm-clock-plugin'); ?></th>
                            <td><input type="text" name="adremm_clock_settings[analog_bg_color]" value="<?php echo esc_attr($settings['analog_bg_color']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba"></td>
                        </tr>
                        <tr>
                            <th><?php _e('Ringkleur & Grootte', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <input type="text" name="adremm_clock_settings[analog_ring_color]" value="<?php echo esc_attr($settings['analog_ring_color']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba">
                                <input type="number" name="adremm_clock_settings[analog_ring_size]" value="<?php echo esc_attr($settings['analog_ring_size']); ?>" style="width:60px;" step="0.1"> px
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Uur Notatie', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <input type="text" name="adremm_clock_settings[analog_hour_color]" value="<?php echo esc_attr($settings['analog_hour_color']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba">
                                Dikte: <input type="number" name="adremm_clock_settings[analog_hour_thick]" value="<?php echo esc_attr($settings['analog_hour_thick']); ?>" style="width:50px;" step="0.1">
                                Lengte: <input type="number" name="adremm_clock_settings[analog_hour_length]" value="<?php echo esc_attr($settings['analog_hour_length']); ?>" style="width:50px;" step="0.1">
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Minuut Notatie', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <input type="text" name="adremm_clock_settings[analog_min_color]" value="<?php echo esc_attr($settings['analog_min_color']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba">
                                Dikte: <input type="number" name="adremm_clock_settings[analog_min_thick]" value="<?php echo esc_attr($settings['analog_min_thick']); ?>" style="width:50px;" step="0.1">
                                Lengte: <input type="number" name="adremm_clock_settings[analog_min_length]" value="<?php echo esc_attr($settings['analog_min_length']); ?>" style="width:50px;" step="0.1">
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Notatie Boven Wijzers?', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <input type="hidden" name="adremm_clock_settings[analog_not_above]" value="no">
                                <input type="checkbox" name="adremm_clock_settings[analog_not_above]" value="yes" <?php checked($settings['analog_not_above'], 'yes'); ?>>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Middenring & Overshoot', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <label><input type="checkbox" name="adremm_clock_settings[analog_center_ring]" value="yes" <?php checked(isset($settings['analog_center_ring']) ? $settings['analog_center_ring'] : 'yes', 'yes'); ?>> Middenring</label>
                                Grootte: <input type="number" name="adremm_clock_settings[analog_center_ring_size]" value="<?php echo esc_attr(isset($settings['analog_center_ring_size']) ? $settings['analog_center_ring_size'] : '8'); ?>" style="width:50px;">
                                Kleur: <input type="text" name="adremm_clock_settings[analog_center_ring_color]" value="<?php echo esc_attr(isset($settings['analog_center_ring_color']) ? $settings['analog_center_ring_color'] : '#ffffff'); ?>" class="adremm-color-picker" data-alpha-enabled="true">
                                <br><br>
                                <label><input type="checkbox" name="adremm_clock_settings[analog_overshoot]" value="yes" <?php checked(isset($settings['analog_overshoot']) ? $settings['analog_overshoot'] : 'no', 'yes'); ?>> Overshoot (wijzers steken door center)</label>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- WIJZERS TAB -->
                <div id="tab-wijzers" class="adremm-panel">
                    <h3><?php _e('Wijzers Instellen', 'adremm-clock-plugin'); ?></h3>
                    <?php
                    $hands = array('hour' => 'Urenwijzer', 'min' => 'Minutenwijzer', 'sec' => 'Secondenwijzer');
                    foreach($hands as $h => $label): ?>
                        <div class="hand-settings-group">
                            <h4><?php echo $label; ?></h4>
                            <table class="form-table">
                                <tr>
                                    <th>Kleur & Dikte</th>
                                    <td>
                                        <input type="text" name="adremm_clock_settings[hand_<?php echo $h; ?>_color]" value="<?php echo esc_attr($settings['hand_'.$h.'_color']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba">
                                        <input type="number" name="adremm_clock_settings[hand_<?php echo $h; ?>_thick]" value="<?php echo esc_attr($settings['hand_'.$h.'_thick']); ?>" style="width:60px;" step="0.1"> px
                                    </td>
                                </tr>
                                <tr>
                                    <th>Stijl</th>
                                    <td>
                                        <select name="adremm_clock_settings[hand_<?php echo $h; ?>_style]">
                                            <option value="rectangle" <?php selected($settings['hand_'.$h.'_style'], 'rectangle'); ?>>Rechthoek</option>
                                            <option value="rounded" <?php selected($settings['hand_'.$h.'_style'], 'rounded'); ?>>Afgerond</option>
                                            <option value="point" <?php selected($settings['hand_'.$h.'_style'], 'point'); ?>>Punt</option>
                                            <option value="heart" <?php selected($settings['hand_'.$h.'_style'], 'heart'); ?>>Hart</option>
                                            <option value="arrow" <?php selected($settings['hand_'.$h.'_style'], 'arrow'); ?>>Arrow</option>
                                            <option value="steampunk" <?php selected($settings['hand_'.$h.'_style'], 'steampunk'); ?>>Steampunk</option>
                                        </select>
                                    </td>
                                </tr>
                        <tr>
                            <th>Lengte</th>
                            <td>
                                <input type="number" name="adremm_clock_settings[hand_<?php echo $h; ?>_len]" value="<?php echo esc_attr($settings['hand_'.$h.'_len']); ?>" style="width:60px;" step="0.1"> %
                            </td>
                        </tr>
                            </table>
                        </div>
                    <?php endforeach; ?>
                    <table class="form-table">
                        <tr>
                            <th>Sweep Style</th>
                            <td>
                                <select name="adremm_clock_settings[hand_sweep]">
                                    <option value="smooth" <?php selected($settings['hand_sweep'], 'smooth'); ?>>Vloeiend</option>
                                    <option value="classy" <?php selected($settings['hand_sweep'], 'classy'); ?>>Classy</option>
                                    <option value="ticking" <?php selected($settings['hand_sweep'], 'ticking'); ?>>Tikkend</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- DIGITAAL TAB -->
                <div id="tab-digitaal" class="adremm-panel">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Toon Digitale Klok', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <input type="hidden" name="adremm_clock_settings[show_digital]" value="no">
                                <label><input type="radio" name="adremm_clock_settings[show_digital]" value="yes" <?php checked($settings['show_digital'], 'yes'); ?>> Ja</label>
                                <label style="margin-left:15px;"><input type="radio" name="adremm_clock_settings[show_digital]" value="no" <?php checked($settings['show_digital'], 'no'); ?>> Nee</label>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Digitaal Thema', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <select name="adremm_clock_settings[digital_style]">
                                    <option value="alarm" <?php selected($settings['digital_style'], 'alarm'); ?>>1. Vintage Radiowekker</option>
                                    <option value="wall" <?php selected($settings['digital_style'], 'wall'); ?>>2. Muurklok</option>
                                    <option value="blocks" <?php selected($settings['digital_style'], 'blocks'); ?>>3. Matrix</option>
                                    <option value="pixels" <?php selected($settings['digital_style'], 'pixels'); ?>>4. PIXELS</option>
                                    <option value="design" <?php selected($settings['digital_style'], 'design'); ?>>5. Minimalist</option>
                                    <option value="custom" <?php selected($settings['digital_style'], 'custom'); ?>>6. Custom</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Glow Effect (Font)', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <div>
                                    <input type="hidden" name="adremm_clock_settings[digital_glow]" value="no">
                                    <label><input type="checkbox" name="adremm_clock_settings[digital_glow]" value="yes" <?php checked($settings['digital_glow'], 'yes'); ?>> Actief</label>
                                    <input type="text" name="adremm_clock_settings[digital_glow_color]" value="<?php echo esc_attr($settings['digital_glow_color']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba">
                                    <input type="number" name="adremm_clock_settings[digital_glow_spread]" value="<?php echo esc_attr($settings['digital_glow_spread']); ?>" style="width:60px;"> px
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Digital Custom & Minimalist', 'adremm-clock-plugin'); ?></th>
                            <td>
                                 Breedte: <input type="number" name="adremm_clock_settings[digital_width]" value="<?php echo esc_attr(isset($settings['digital_width']) ? $settings['digital_width'] : '200'); ?>" style="width:60px;">
                                 Hoogte: <input type="number" name="adremm_clock_settings[digital_height]" value="<?php echo esc_attr(isset($settings['digital_height']) ? $settings['digital_height'] : '60'); ?>" style="width:60px;">
                                 Toon Sec: <input type="checkbox" name="adremm_clock_settings[digital_show_sec]" value="yes" <?php checked($settings['digital_show_sec'], 'yes'); ?>>
                                 Oriëntatie:
                                 <select name="adremm_clock_settings[digital_orientation]">
                                     <option value="horizontal" <?php selected($settings['digital_orientation'], 'horizontal'); ?>>Horizontaal</option>
                                     <option value="vertical" <?php selected($settings['digital_orientation'], 'vertical'); ?>>Verticaal</option>
                                 </select>
                                 <div style="margin-top:10px; border-top:1px solid #eee; padding-top:10px;">
                                     <strong>Custom & Minimalist Design:</strong><br>
                                     <em>(In thema Custom kun je een eigen font URL (.woff) opgeven:)</em><br>
                                     Font URL: <input type="text" name="adremm_clock_settings[digital_font_url]" value="<?php echo esc_attr(isset($settings['digital_font_url']) ? $settings['digital_font_url'] : ''); ?>" class="regular-text"><br>
                                     Font Size: <input type="number" name="adremm_clock_settings[digital_font_size]" value="<?php echo esc_attr(isset($settings['digital_font_size']) ? $settings['digital_font_size'] : '32'); ?>" style="width:50px;"> px
                                     Weight: <select name="adremm_clock_settings[digital_font_weight]">
                                        <option value="300" <?php selected(isset($settings['digital_font_weight']) && $settings['digital_font_weight'] == '300'); ?>>300 (Light)</option>
                                        <option value="400" <?php selected(isset($settings['digital_font_weight']) && $settings['digital_font_weight'] == '400'); ?>>400 (Normal)</option>
                                        <option value="700" <?php selected(isset($settings['digital_font_weight']) && $settings['digital_font_weight'] == '700'); ?>>700 (Bold)</option>
                                     </select>
                                     Italic: <input type="checkbox" name="adremm_clock_settings[digital_italic]" value="yes" <?php checked(isset($settings['digital_italic']) && $settings['digital_italic'] == 'yes'); ?>>
                                     <br>
                                     Padding: <input type="number" name="adremm_clock_settings[minimalist_padding]" value="<?php echo esc_attr(isset($settings['minimalist_padding']) ? $settings['minimalist_padding'] : '10'); ?>" style="width:50px;">
                                     Radius: <input type="number" name="adremm_clock_settings[minimalist_radius]" value="<?php echo esc_attr(isset($settings['minimalist_radius']) ? $settings['minimalist_radius'] : '5'); ?>" style="width:50px;">
                                     BG: <input type="text" name="adremm_clock_settings[minimalist_bg]" value="<?php echo esc_attr(isset($settings['minimalist_bg']) ? $settings['minimalist_bg'] : 'transparent'); ?>" class="adremm-color-picker" data-alpha-enabled="true">
                                     <br>
                                     Border Size: <input type="number" name="adremm_clock_settings[digital_border_size]" value="<?php echo esc_attr(isset($settings['digital_border_size']) ? $settings['digital_border_size'] : '0'); ?>" style="width:50px;">
                                     Border Color: <input type="text" name="adremm_clock_settings[digital_border_color]" value="<?php echo esc_attr(isset($settings['digital_border_color']) ? $settings['digital_border_color'] : '#cccccc'); ?>" class="adremm-color-picker" data-alpha-enabled="true">
                                 </div>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Font Overrides', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <div>
                                    <select name="adremm_clock_settings[digital_font]" class="adremm-font-select">
                                        <option value="Thema" <?php selected($settings['digital_font'], 'Thema'); ?>>Thema</option>
                                        <?php foreach ($fonts as $font) : ?>
                                            <option value="<?php echo esc_attr($font); ?>" <?php selected($settings['digital_font'], $font); ?>><?php echo esc_html($font); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" name="adremm_clock_settings[digital_color]" value="<?php echo esc_attr($settings['digital_color']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba">
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- STATUS TAB -->
                <div id="tab-status" class="adremm-panel">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Bericht Positie', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <select name="adremm_clock_settings[status_pos]">
                                    <option value="above_digital" <?php selected($settings['status_pos'], 'above_digital'); ?>>Boven digitale klok</option>
                                    <option value="below_digital" <?php selected($settings['status_pos'], 'below_digital'); ?>>Onder digitale klok</option>
                                    <option value="above_analog" <?php selected($settings['status_pos'], 'above_analog'); ?>>Boven analoge klok</option>
                                    <option value="below_analog" <?php selected($settings['status_pos'], 'below_analog'); ?>>Onder analoge klok</option>
                                    <option value="below_date" <?php selected($settings['status_pos'], 'below_date'); ?>>Onder datum</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Tekst Open/Dicht', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                                    <span style="min-width:60px;">Open:</span>
                                    <input type="text" name="adremm_clock_settings[text_open]" value="<?php echo esc_attr($settings['text_open']); ?>">
                                    <input type="text" name="adremm_clock_settings[color_open]" value="<?php echo esc_attr($settings['color_open']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba">
                                </div>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <span style="min-width:60px;">Dicht:</span>
                                    <input type="text" name="adremm_clock_settings[text_closed]" value="<?php echo esc_attr($settings['text_closed']); ?>">
                                    <input type="text" name="adremm_clock_settings[color_closed]" value="<?php echo esc_attr($settings['color_closed']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Custom Font', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <select name="adremm_clock_settings[font_status]" class="adremm-font-select">
                                    <option value="Thema" <?php selected(isset($settings['font_status']) ? $settings['font_status'] : '', 'Thema'); ?>>Thema</option>
                                    <?php foreach ($fonts as $font) : ?>
                                        <option value="<?php echo esc_attr($font); ?>" <?php selected(isset($settings['font_status']) ? $settings['font_status'] : '', $font); ?>><?php echo esc_html($font); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- EXTRA TAB -->
                <div id="tab-extra" class="adremm-panel">
                    <table class="form-table">
                        <tr>
                            <th>Radio kanaal & Logo</th>
                            <td>
                                <input type="hidden" name="adremm_clock_settings[radio_enabled]" value="no">
                                <input type="checkbox" name="adremm_clock_settings[radio_enabled]" value="yes" <?php checked($settings['radio_enabled'], 'yes'); ?>> Actief
                                <select name="adremm_clock_settings[radio_channel]" style="margin-left:10px;">
                                    <option value="techno" <?php selected($settings['radio_channel'], 'techno'); ?>>TECHNO</option>
                                    <option value="disco" <?php selected($settings['radio_channel'], 'disco'); ?>>DISCO</option>
                                    <option value="hits" <?php selected($settings['radio_channel'], 'hits'); ?>>HITS</option>
                                    <option value="concert" <?php selected($settings['radio_channel'], 'concert'); ?>>Concert</option>
                                    <option value="classics" <?php selected($settings['radio_channel'], 'classics'); ?>>Classics</option>
                                    <option value="blues" <?php selected($settings['radio_channel'], 'blues'); ?>>Blues</option>
                                </select>
                                <div style="margin-top:10px;">
                                    <span>Logo (Vintage):</span>
                                    <input type="text" name="adremm_clock_settings[vintage_logo]" value="<?php echo esc_attr(isset($settings['vintage_logo']) ? $settings['vintage_logo'] : ''); ?>" class="adremm-media-url regular-text" style="width:150px;">
                                    <button type="button" class="button adremm-media-upload">Kies Logo</button>
                                    <p class="description"><?php _e('Aangeraden maat: 48x48px.', 'adremm-clock-plugin'); ?></p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Lichtslang (Marquee)', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <input type="hidden" name="adremm_clock_settings[extra_marquee]" value="no">
                                <label><input type="radio" name="adremm_clock_settings[extra_marquee]" value="yes" <?php checked($settings['extra_marquee'], 'yes'); ?>> Ja</label>
                                <label style="margin-left:15px;"><input type="radio" name="adremm_clock_settings[extra_marquee]" value="no" <?php checked($settings['extra_marquee'], 'no'); ?>> Nee</label>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Snelheid & Grootte', 'adremm-clock-plugin'); ?></th>
                            <td>
                                Snelheid: <input type="range" name="adremm_clock_settings[extra_speed]" min="1" max="20" value="<?php echo esc_attr($settings['extra_speed']); ?>">
                                <span id="extra-speed-val"><?php echo (21 - esc_attr($settings['extra_speed'])) * 100; ?> ms</span>
                                <br>
                                Grootte: <input type="number" name="adremm_clock_settings[extra_font_size]" value="<?php echo esc_attr($settings['extra_font_size']); ?>" style="width:60px;"> px
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Extra Bericht', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <textarea name="adremm_clock_settings[extra_message]" rows="4" class="large-text"><?php echo esc_textarea($settings['extra_message']); ?></textarea>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- POSITIE TAB -->
                <div id="tab-positie" class="adremm-panel">
                    <h3><?php _e('Joystick Positie', 'adremm-clock-plugin'); ?></h3>
                    <p class="description"><?php _e('Kies de positie van de klok op de website. HD = Header, FT = Footer.', 'adremm-clock-plugin'); ?></p>
                    <div class="adremm-joystick-box">
                         <div class="joy-grid">
                            <?php
                            $joy_map = array(
                                'top-left'      => array('label' => '',   'title' => __('Linksboven', 'adremm-clock-plugin')),
                                'top-center'    => array('label' => 'HD', 'title' => __('Boven (Header)', 'adremm-clock-plugin')),
                                'top-right'     => array('label' => '',   'title' => __('Rechtsboven', 'adremm-clock-plugin')),
                                'middle-left'   => array('label' => '',   'title' => __('Midden links', 'adremm-clock-plugin')),
                                'center'        => array('label' => ' ',   'title' => __('Midden', 'adremm-clock-plugin'), 'is_gap' => true),
                                'middle-right'  => array('label' => '',   'title' => __('Midden rechts', 'adremm-clock-plugin')),
                                'bottom-left'   => array('label' => '',   'title' => __('Linksonder', 'adremm-clock-plugin')),
                                'bottom-center' => array('label' => 'FT', 'title' => __('Onder (Footer)', 'adremm-clock-plugin')),
                                'bottom-right'  => array('label' => 'X',  'title' => __('Rechtsonder', 'adremm-clock-plugin')),
                            );
                            foreach($joy_map as $k => $data):
                                if (!empty($data['is_gap'])) { echo '<div class="joy-gap">'.esc_html($data['label']).'</div>'; continue; }
                                ?>
                                <label class="joy-item <?php echo ($settings['position'] === $k) ? 'active' : ''; ?>" title="<?php echo esc_attr($data['title']); ?>">
                                    <input type="radio" name="adremm_clock_settings[position]" value="<?php echo $k; ?>" <?php checked($settings['position'], $k); ?>>
                                    <span><?php echo esc_html($data['label']); ?></span>
                                </label>
                            <?php endforeach; ?>
                         </div>
                    </div>
                </div>

                <!-- TIJDPANEEL TAB -->
                <div id="tab-paneel" class="adremm-panel">
                    <h3>Inklapbaar paneel & Styling</h3>
                    <table class="form-table">
                        <tr>
                            <th>Inklapbaar paneel</th>
                            <td>
                                <input type="hidden" name="adremm_clock_settings[is_collapsible]" value="no">
                                <input type="checkbox" name="adremm_clock_settings[is_collapsible]" value="yes" <?php checked($settings['is_collapsible'], 'yes'); ?>>
                            </td>
                        </tr>
                        <tr>
                            <th>Sluitkruis & Kleur</th>
                            <td>
                                <input type="hidden" name="adremm_clock_settings[show_close_x]" value="no">
                                <input type="checkbox" name="adremm_clock_settings[show_close_x]" value="yes" <?php checked($settings['show_close_x'], 'yes'); ?>>
                                Grootte: <input type="number" name="adremm_clock_settings[close_x_size]" value="<?php echo esc_attr($settings['close_x_size']); ?>" style="width:50px;"> px
                                <input type="text" name="adremm_clock_settings[color_close_x]" value="<?php echo esc_attr($settings['color_close_x']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba">
                            </td>
                        </tr>
                        <tr>
                            <th>Sluit Animatie</th>
                            <td>
                                <select name="adremm_clock_settings[close_x_anim]">
                                    <option value="fade" <?php selected($settings['close_x_anim'], 'fade'); ?>>Fade</option>
                                    <option value="rotate" <?php selected($settings['close_x_anim'], 'rotate'); ?>>Rotate</option>
                                    <option value="bounce" <?php selected($settings['close_x_anim'], 'bounce'); ?>>Bounce</option>
                                    <option value="pulse" <?php selected($settings['close_x_anim'], 'pulse'); ?>>Pulse</option>
                                    <option value="zoom" <?php selected($settings['close_x_anim'], 'zoom'); ?>>Zoom</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Sluit Label</th>
                            <td>
                                <input type="hidden" name="adremm_clock_settings[show_close_label]" value="no">
                                <input type="checkbox" name="adremm_clock_settings[show_close_label]" value="yes" <?php checked($settings['show_close_label'], 'yes'); ?>>
                                Tekst: <input type="text" name="adremm_clock_settings[close_label]" value="<?php echo esc_attr($settings['close_label']); ?>">
                                <input type="text" name="adremm_clock_settings[color_close_label]" value="<?php echo esc_attr($settings['color_close_label']); ?>" class="adremm-color-picker" data-alpha-enabled="true">
                                Grootte: <input type="number" name="adremm_clock_settings[close_label_font_size]" value="<?php echo esc_attr(isset($settings['close_label_font_size']) ? $settings['close_label_font_size'] : '14'); ?>" style="width:50px;"> px
                            </td>
                        </tr>
                        <tr>
                            <th>Schaduw Paneel</th>
                            <td>
                                <input type="text" name="adremm_clock_settings[panel_shadow]" value="<?php echo esc_attr($settings['panel_shadow']); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr>
                            <th>Tab Tekst</th>
                            <td><input type="text" name="adremm_clock_settings[tab_text]" value="<?php echo esc_attr($settings['tab_text']); ?>"></td>
                        </tr>
                        <tr>
                            <th>Pijltje op tab</th>
                            <td>
                                <input type="hidden" name="adremm_clock_settings[tab_arrow]" value="no">
                                <input type="checkbox" name="adremm_clock_settings[tab_arrow]" value="yes" <?php checked($settings['tab_arrow'], 'yes'); ?>>
                                <input type="text" name="adremm_clock_settings[tab_arrow_img]" value="<?php echo esc_attr($settings['tab_arrow_img']); ?>" class="adremm-media-url">
                                <button type="button" class="button adremm-media-upload">Kies afbeelding</button>
                            </td>
                        </tr>
                        <tr>
                            <th>Achtergrond & Tekstkleur Tab</th>
                            <td>
                                <input type="text" name="adremm_clock_settings[tab_bg]" value="<?php echo esc_attr($settings['tab_bg']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba">
                                <input type="text" name="adremm_clock_settings[tab_color]" value="<?php echo esc_attr($settings['tab_color']); ?>" class="adremm-color-picker" data-alpha-enabled="true" data-alpha-color-type="rgba">
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- ALGEMEEN TAB -->
                <div id="tab-algemeen" class="adremm-panel">
                    <table class="form-table">
                        <tr>
                            <th>Taal</th>
                            <td><select name="adremm_clock_settings[language]"><option value="auto">Auto (Multilinguaal)</option></select></td>
                        </tr>
                    </table>
                </div>

            </div>

            <!-- Right: Fixed Live Preview -->
            <div class="adremm-preview-col">
                <div class="adremm-preview-card">
                    <h3>Live preview</h3>
                    <div class="preview-stage">
                        <div id="clock-live-view">
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
                                <div class="preview-extra">Extra bericht tekst...</div>
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
