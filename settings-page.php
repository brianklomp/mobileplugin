<?php
/**
 * Settings Page View for ADREMM Clock Plugin
 */
if ( ! defined('ABSPATH') ) exit;

$settings = get_option('adremm_clock_settings', adremm_clock_get_default_settings());
$fonts = adremm_clock_get_google_fonts(); // To be implemented in functions.php
?>
<div class="wrap adremm-clock-admin">
    <h1><?php _e('ADREMM Klok Instellingen', 'adremm-clock-plugin'); ?></h1>

    <form method="post" action="options.php">
        <?php
        settings_fields('adremm_clock_options');
        ?>

        <div class="adremm-clock-layout-grid">
            <!-- Left Column: Settings -->
            <div class="adremm-clock-settings-panel">

                <h3><?php _e('Positie Selecteren', 'adremm-clock-plugin'); ?></h3>
                <div class="position-selector-grid">
                    <?php
                    $positions = array(
                        'top-left' => array('label' => __('Links boven', 'adremm-clock-plugin'), 'class' => 'pos-tl'),
                        'top-center' => array('label' => __('Midden boven (Header)', 'adremm-clock-plugin'), 'class' => 'pos-tc hd', 'display' => 'HD'),
                        'top-right' => array('label' => __('Rechts boven', 'adremm-clock-plugin'), 'class' => 'pos-tr'),
                        'middle-left' => array('label' => __('Links midden', 'adremm-clock-plugin'), 'class' => 'pos-ml'),
                        'middle-right' => array('label' => __('Rechts midden', 'adremm-clock-plugin'), 'class' => 'pos-mr'),
                        'bottom-left' => array('label' => __('Links onder', 'adremm-clock-plugin'), 'class' => 'pos-bl'),
                        'bottom-center' => array('label' => __('Midden onder (Footer)', 'adremm-clock-plugin'), 'class' => 'pos-bc ft', 'display' => 'FT'),
                        'bottom-right' => array('label' => __('Rechts onder', 'adremm-clock-plugin'), 'class' => 'pos-br'),
                    );
                    foreach ($positions as $pos_key => $pos_data) :
                        $checked = ($settings['position'] === $pos_key) ? 'checked' : '';
                        $display_label = isset($pos_data['display']) ? $pos_data['display'] : '';
                    ?>
                        <label class="pos-box <?php echo esc_attr($pos_data['class']); ?>" title="<?php echo esc_attr($pos_data['label']); ?>">
                            <input type="radio" name="adremm_clock_settings[position]" value="<?php echo esc_attr($pos_key); ?>" <?php echo $checked; ?>>
                            <span><?php echo esc_html($display_label); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Thema', 'adremm-clock-plugin'); ?></th>
                        <td>
                            <select name="adremm_clock_settings[theme]" id="clock-theme-select">
                                <option value="modern" <?php selected($settings['theme'], 'modern'); ?>>Modern</option>
                                <option value="classic" <?php selected($settings['theme'], 'classic'); ?>>Classic</option>
                                <option value="digital" <?php selected($settings['theme'], 'digital'); ?>>Digital</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Font', 'adremm-clock-plugin'); ?></th>
                        <td>
                            <select name="adremm_clock_settings[font_family]" id="clock-font-select">
                                <?php foreach ($fonts as $font) : ?>
                                    <option value="<?php echo esc_attr($font); ?>" <?php selected($settings['font_family'], $font); ?> style="font-family: '<?php echo $font; ?>', sans-serif;">
                                        <?php echo esc_html($font); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Achtergrond Kleur', 'adremm-clock-plugin'); ?></th>
                        <td><input type="text" name="adremm_clock_settings[bg_color]" value="<?php echo esc_attr($settings['bg_color']); ?>" class="color-picker" data-alpha="true"></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Tekst Kleur', 'adremm-clock-plugin'); ?></th>
                        <td><input type="text" name="adremm_clock_settings[text_color]" value="<?php echo esc_attr($settings['text_color']); ?>" class="color-picker" data-alpha="true"></td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </div>

            <!-- Right Column: Live Preview -->
            <div class="adremm-clock-preview-panel">
                <h3><?php _e('Live Preview', 'adremm-clock-plugin'); ?></h3>
                <div id="adremm-clock-preview-container">
                    <div id="adremm-clock-preview" class="theme-<?php echo $settings['theme']; ?>" style="font-family: '<?php echo $settings['font_family']; ?>'; background-color: <?php echo $settings['bg_color']; ?>; color: <?php echo $settings['text_color']; ?>;">
                        <div class="clock-content">
                            <div class="time">12:34:56</div>
                            <div class="date">Maandag 17 Maart</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
