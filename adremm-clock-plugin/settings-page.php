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
                            <th><?php _e('Kies Basis Thema', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <div class="theme-selector-wrap">
                                    <label class="theme-option">
                                        <input type="radio" name="adremm_clock_settings[theme]" value="modern" <?php checked($settings['theme'], 'modern'); ?>>
                                        <span class="theme-card modern">Modern</span>
                                    </label>
                                    <label class="theme-option">
                                        <input type="radio" name="adremm_clock_settings[theme]" value="classic" <?php checked($settings['theme'], 'classic'); ?>>
                                        <span class="theme-card classic">Classic</span>
                                    </label>
                                    <label class="theme-option">
                                        <input type="radio" name="adremm_clock_settings[theme]" value="digital" <?php checked($settings['theme'], 'digital'); ?>>
                                        <span class="theme-card digital">Digital</span>
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
                            <th><?php _e('Paneel Grootte', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <select name="adremm_clock_settings[panel_size]">
                                    <option value="small" <?php selected($settings['panel_size'], 'small'); ?>>Klein</option>
                                    <option value="normal" <?php selected($settings['panel_size'], 'normal'); ?>>Normaal</option>
                                    <option value="large" <?php selected($settings['panel_size'], 'large'); ?>>Groot</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Achtergrond Kleur', 'adremm-clock-plugin'); ?></th>
                            <td><input type="text" name="adremm_clock_settings[bg_color]" value="<?php echo esc_attr($settings['bg_color']); ?>" class="adremm-color-picker" data-alpha="true"></td>
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
                            <td><input type="text" name="adremm_clock_settings[analog_bg_color]" value="<?php echo esc_attr($settings['analog_bg_color']); ?>" class="adremm-color-picker" data-alpha="true"></td>
                        </tr>
                        <tr>
                            <th><?php _e('Ringkleur & Grootte', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <input type="text" name="adremm_clock_settings[analog_ring_color]" value="<?php echo esc_attr($settings['analog_ring_color']); ?>" class="adremm-color-picker" data-alpha="true">
                                <input type="number" name="adremm_clock_settings[analog_ring_size]" value="<?php echo esc_attr($settings['analog_ring_size']); ?>" style="width:60px;"> px
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Uur Notatie', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <input type="text" name="adremm_clock_settings[analog_hour_color]" value="<?php echo esc_attr($settings['analog_hour_color']); ?>" class="adremm-color-picker" data-alpha="true">
                                Dikte: <input type="number" name="adremm_clock_settings[analog_hour_thick]" value="<?php echo esc_attr($settings['analog_hour_thick']); ?>" style="width:50px;">
                                Lengte: <input type="number" name="adremm_clock_settings[analog_hour_length]" value="<?php echo esc_attr($settings['analog_hour_length']); ?>" style="width:50px;">
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Minuut Notatie', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <input type="text" name="adremm_clock_settings[analog_min_color]" value="<?php echo esc_attr($settings['analog_min_color']); ?>" class="adremm-color-picker" data-alpha="true">
                                Dikte: <input type="number" name="adremm_clock_settings[analog_min_thick]" value="<?php echo esc_attr($settings['analog_min_thick']); ?>" style="width:50px;">
                                Lengte: <input type="number" name="adremm_clock_settings[analog_min_length]" value="<?php echo esc_attr($settings['analog_min_length']); ?>" style="width:50px;">
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Notatie Boven Wijzers?', 'adremm-clock-plugin'); ?></th>
                            <td><input type="checkbox" name="adremm_clock_settings[analog_not_above]" value="yes" <?php checked($settings['analog_not_above'], 'yes'); ?>></td>
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
                                        <input type="text" name="adremm_clock_settings[hand_<?php echo $h; ?>_color]" value="<?php echo esc_attr($settings['hand_'.$h.'_color']); ?>" class="adremm-color-picker" data-alpha="true">
                                        <input type="number" name="adremm_clock_settings[hand_<?php echo $h; ?>_thick]" value="<?php echo esc_attr($settings['hand_'.$h.'_thick']); ?>" style="width:60px;"> px
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
                                        </select>
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
                                    <option value="smooth" <?php selected($settings['hand_sweep'], 'smooth'); ?>>Vloeiend (Rolex)</option>
                                    <option value="ticking" <?php selected($settings['hand_sweep'], 'ticking'); ?>>Tikkend (Seiko 5)</option>
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
                            <th><?php _e('Digital Style', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <select name="adremm_clock_settings[digital_style]">
                                    <option value="alarm" <?php selected($settings['digital_style'], 'alarm'); ?>>1. Wekker</option>
                                    <option value="wall" <?php selected($settings['digital_style'], 'wall'); ?>>2. Muurklok</option>
                                    <option value="blocks" <?php selected($settings['digital_style'], 'blocks'); ?>>3. Blokjes</option>
                                    <option value="dots" <?php selected($settings['digital_style'], 'dots'); ?>>4. Dots</option>
                                    <option value="design" <?php selected($settings['digital_style'], 'design'); ?>>5. Design</option>
                                    <option value="custom" <?php selected($settings['digital_style'], 'custom'); ?>>6. Custom</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Glow Effect (Font)', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <input type="checkbox" name="adremm_clock_settings[digital_glow]" value="yes" <?php checked($settings['digital_glow'], 'yes'); ?>> Actief
                                <input type="text" name="adremm_clock_settings[digital_glow_color]" value="<?php echo esc_attr($settings['digital_glow_color']); ?>" class="adremm-color-picker" data-alpha="true">
                                <input type="number" name="adremm_clock_settings[digital_glow_spread]" value="<?php echo esc_attr($settings['digital_glow_spread']); ?>" style="width:60px;"> px
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Font Overrides', 'adremm-clock-plugin'); ?></th>
                            <td>
                                <select name="adremm_clock_settings[digital_font]" class="adremm-font-select">
                                    <?php foreach ($fonts as $font) : ?>
                                        <option value="<?php echo esc_attr($font); ?>" <?php selected($settings['digital_font'], $font); ?>><?php echo esc_html($font); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="adremm_clock_settings[digital_color]" value="<?php echo esc_attr($settings['digital_color']); ?>" class="adremm-color-picker" data-alpha="true">
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
                                <input type="text" name="adremm_clock_settings[text_open]" value="<?php echo esc_attr($settings['text_open']); ?>">
                                <input type="text" name="adremm_clock_settings[color_open]" value="<?php echo esc_attr($settings['color_open']); ?>" class="adremm-color-picker" data-alpha="true">
                                <br><br>
                                <input type="text" name="adremm_clock_settings[text_closed]" value="<?php echo esc_attr($settings['text_closed']); ?>">
                                <input type="text" name="adremm_clock_settings[color_closed]" value="<?php echo esc_attr($settings['color_closed']); ?>" class="adremm-color-picker" data-alpha="true">
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- EXTRA TAB -->
                <div id="tab-extra" class="adremm-panel">
                    <table class="form-table">
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
                                <input type="range" name="adremm_clock_settings[extra_speed]" min="1" max="20" value="<?php echo esc_attr($settings['extra_speed']); ?>">
                                <input type="number" name="adremm_clock_settings[extra_font_size]" value="<?php echo esc_attr($settings['extra_font_size']); ?>" style="width:60px;"> px
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
                                'center'        => array('label' => '',   'title' => __('Midden', 'adremm-clock-plugin'), 'hidden' => true),
                                'middle-right'  => array('label' => '',   'title' => __('Midden rechts', 'adremm-clock-plugin')),
                                'bottom-left'   => array('label' => '',   'title' => __('Linksonder', 'adremm-clock-plugin')),
                                'bottom-center' => array('label' => 'FT', 'title' => __('Onder (Footer)', 'adremm-clock-plugin')),
                                'bottom-right'  => array('label' => '',   'title' => __('Rechtsonder', 'adremm-clock-plugin')),
                            );
                            foreach($joy_map as $k => $data):
                                if (!empty($data['hidden'])) { echo '<div class="joy-gap"></div>'; continue; }
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
                    <h3>Inklapbaar & Styling</h3>
                    <div class="adremm-sub-tabs">
                        <button type="button" class="sub-tab-btn active" data-sub="sub-general">Algemeen</button>
                        <button type="button" class="sub-tab-btn" data-sub="sub-tab-styling">Styling Gesloten Tab</button>
                    </div>

                    <div id="sub-general" class="adremm-sub-panel active">
                         <table class="form-table">
                            <tr>
                                <th>Inklapbaar Panel</th>
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
                                    <input type="text" name="adremm_clock_settings[color_close_x]" value="<?php echo esc_attr($settings['color_close_x']); ?>" class="adremm-color-picker" data-alpha="true">
                                </td>
                            </tr>
                            <tr>
                                <th>Sluit Label</th>
                                <td>
                                    <input type="hidden" name="adremm_clock_settings[show_close_label]" value="no">
                                    <input type="checkbox" name="adremm_clock_settings[show_close_label]" value="yes" <?php checked($settings['show_close_label'], 'yes'); ?>>
                                    Tekst: <input type="text" name="adremm_clock_settings[close_label]" value="<?php echo esc_attr($settings['close_label']); ?>">
                                    <input type="text" name="adremm_clock_settings[color_close_label]" value="<?php echo esc_attr($settings['color_close_label']); ?>" class="adremm-color-picker" data-alpha="true">
                                </td>
                            </tr>
                            <tr>
                                <th>Schaduw Paneel</th>
                                <td>
                                    <input type="text" name="adremm_clock_settings[panel_shadow]" value="<?php echo esc_attr($settings['panel_shadow']); ?>" class="large-text">
                                    <p class="description">CSS box-shadow format (bijv: 0 10px 40px rgba(0,0,0,0.2))</p>
                                </td>
                            </tr>
                         </table>
                    </div>

                    <div id="sub-tab-styling" class="adremm-sub-panel">
                        <table class="form-table">
                            <tr>
                                <th><?php _e('Tab Tekst', 'adremm-clock-plugin'); ?></th>
                                <td><input type="text" name="adremm_clock_settings[tab_text]" value="<?php echo esc_attr($settings['tab_text']); ?>"></td>
                            </tr>
                            <tr>
                                <th><?php _e('Pijltje op tab', 'adremm-clock-plugin'); ?></th>
                                <td>
                                    <input type="hidden" name="adremm_clock_settings[tab_arrow]" value="no">
                                    <input type="checkbox" name="adremm_clock_settings[tab_arrow]" value="yes" <?php checked($settings['tab_arrow'], 'yes'); ?>>
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Schaduw Tab', 'adremm-clock-plugin'); ?></th>
                                <td><input type="text" name="adremm_clock_settings[tab_shadow]" value="<?php echo esc_attr($settings['tab_shadow']); ?>" class="large-text"></td>
                            </tr>
                            <tr>
                                <th><?php _e('Achtergrond & Kleur', 'adremm-clock-plugin'); ?></th>
                                <td>
                                    <input type="text" name="adremm_clock_settings[tab_bg]" value="<?php echo esc_attr($settings['tab_bg']); ?>" class="adremm-color-picker" data-alpha="true">
                                    <input type="text" name="adremm_clock_settings[tab_color]" value="<?php echo esc_attr($settings['tab_color']); ?>" class="adremm-color-picker" data-alpha="true">
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Tab Font', 'adremm-clock-plugin'); ?></th>
                                <td>
                                    <select name="adremm_clock_settings[tab_font]" class="adremm-font-select">
                                        <?php foreach ($fonts as $font) : ?>
                                            <option value="<?php echo esc_attr($font); ?>" <?php selected($settings['tab_font'], $font); ?>><?php echo esc_html($font); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
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
