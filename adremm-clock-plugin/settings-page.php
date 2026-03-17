<?php
/**
 * Settings Page View for ADREMM Clock Plugin
 */
if ( ! defined('ABSPATH') ) exit;

$settings = get_option('adremm_clock_settings', adremm_clock_get_default_settings());
$fonts = adremm_clock_get_google_fonts();
?>
<div class="wrap adremm-clock-admin-wrap">
    <div class="adremm-clock-header">
        <h1>ADREMM Klok Plugin <small>v<?php echo ADREMM_CLOCK_VERSION; ?></small></h1>
    </div>

    <div class="adremm-clock-tabs-nav">
        <a href="#tab-pos" class="adremm-clock-tab-link is-active"><?php _e('Positie & Thema', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-style" class="adremm-clock-tab-link"><?php _e('Stijl & Kleuren', 'adremm-clock-plugin'); ?></a>
        <a href="#tab-content" class="adremm-clock-tab-link"><?php _e('Inhoud & Menu', 'adremm-clock-plugin'); ?></a>
    </div>

    <form method="post" action="options.php" id="adremm-clock-form">
        <?php settings_fields('adremm_clock_options'); ?>

        <div class="adremm-clock-main-layout">
            <!-- Left Column: Settings -->
            <div class="adremm-clock-settings-col">

                <!-- Tab: Positie & Thema -->
                <div id="tab-pos" class="adremm-clock-tab-content is-active">
                    <div class="adremm-clock-section">
                        <h3><?php _e('1. Selecteer Positie', 'adremm-clock-plugin'); ?></h3>
                        <p class="description"><?php _e('Klik op een vakje om de positie van de klok op de website te bepalen.', 'adremm-clock-plugin'); ?></p>

                        <div class="position-selector-grid">
                            <?php
                            $positions = array(
                                'top-left' => array('label' => __('Links boven', 'adremm-clock-plugin'), 'class' => 'pos-tl'),
                                'top-center' => array('label' => __('Header (Midden boven)', 'adremm-clock-plugin'), 'class' => 'pos-tc hd', 'display' => 'HD'),
                                'top-right' => array('label' => __('Rechts boven', 'adremm-clock-plugin'), 'class' => 'pos-tr'),
                                'middle-left' => array('label' => __('Links midden', 'adremm-clock-plugin'), 'class' => 'pos-ml'),
                                'middle-right' => array('label' => __('Rechts midden', 'adremm-clock-plugin'), 'class' => 'pos-mr'),
                                'bottom-left' => array('label' => __('Links onder', 'adremm-clock-plugin'), 'class' => 'pos-bl'),
                                'bottom-center' => array('label' => __('Footer (Midden onder)', 'adremm-clock-plugin'), 'class' => 'pos-bc ft', 'display' => 'FT'),
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
                    </div>

                    <div class="adremm-clock-section">
                        <h3><?php _e('2. Kies Thema', 'adremm-clock-plugin'); ?></h3>
                        <div class="theme-selector-wrap">
                            <label class="theme-option">
                                <input type="radio" name="adremm_clock_settings[theme]" value="modern" <?php checked($settings['theme'], 'modern'); ?>>
                                <span class="theme-card modern"><?php _e('Modern', 'adremm-clock-plugin'); ?></span>
                            </label>
                            <label class="theme-option">
                                <input type="radio" name="adremm_clock_settings[theme]" value="classic" <?php checked($settings['theme'], 'classic'); ?>>
                                <span class="theme-card classic"><?php _e('Classic', 'adremm-clock-plugin'); ?></span>
                            </label>
                            <label class="theme-option">
                                <input type="radio" name="adremm_clock_settings[theme]" value="digital" <?php checked($settings['theme'], 'digital'); ?>>
                                <span class="theme-card digital"><?php _e('Digital', 'adremm-clock-plugin'); ?></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Tab: Stijl & Kleuren -->
                <div id="tab-style" class="adremm-clock-tab-content">
                    <div class="adremm-clock-section">
                        <h3><?php _e('Kleuren (RGBA Ondersteuning)', 'adremm-clock-plugin'); ?></h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('Achtergrond', 'adremm-clock-plugin'); ?></th>
                                <td><input type="text" name="adremm_clock_settings[bg_color]" value="<?php echo esc_attr($settings['bg_color']); ?>" class="adremm-color-picker" data-alpha="true"></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Tekst Kleur', 'adremm-clock-plugin'); ?></th>
                                <td><input type="text" name="adremm_clock_settings[text_color]" value="<?php echo esc_attr($settings['text_color']); ?>" class="adremm-color-picker" data-alpha="true"></td>
                            </tr>
                        </table>
                    </div>

                    <div class="adremm-clock-section">
                        <h3><?php _e('Typografie', 'adremm-clock-plugin'); ?></h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('Google Font', 'adremm-clock-plugin'); ?></th>
                                <td>
                                    <select name="adremm_clock_settings[font_family]" id="clock-font-select" class="adremm-select">
                                        <?php foreach ($fonts as $font) : ?>
                                            <option value="<?php echo esc_attr($font); ?>" <?php selected($settings['font_family'], $font); ?> style="font-family: '<?php echo esc_attr($font); ?>', sans-serif;">
                                                <?php echo esc_html($font); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="description"><?php _e('Het geselecteerde font wordt direct geladen in de preview.', 'adremm-clock-plugin'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tab: Inhoud & Menu -->
                <div id="tab-content" class="adremm-clock-tab-content">
                    <div class="adremm-clock-section">
                        <h3><?php _e('Menu Integratie', 'adremm-clock-plugin'); ?></h3>
                        <p class="description"><?php _e('Toon een WordPress menu onder de klok in het tijdpaneel.', 'adremm-clock-plugin'); ?></p>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('Selecteer Menu', 'adremm-clock-plugin'); ?></th>
                                <td>
                                    <?php
                                    $menus = wp_get_nav_menus();
                                    ?>
                                    <select name="adremm_clock_settings[menu_id]" class="adremm-select">
                                        <option value="none"><?php _e('Geen menu', 'adremm-clock-plugin'); ?></option>
                                        <?php foreach ($menus as $menu) : ?>
                                            <option value="<?php echo $menu->term_id; ?>" <?php selected($settings['menu_id'] ?? 'none', $menu->term_id); ?>>
                                                <?php echo esc_html($menu->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="adremm-clock-submit-wrap">
                    <?php submit_button(__('Instellingen Opslaan', 'adremm-clock-plugin'), 'primary button-large'); ?>
                </div>
            </div>

            <!-- Right Column: Live Preview -->
            <div class="adremm-clock-preview-col">
                <div class="adremm-clock-preview-sticky">
                    <h3><?php _e('Live Preview', 'adremm-clock-plugin'); ?></h3>
                    <div class="preview-window">
                        <div id="adremm-clock-live-preview" class="theme-<?php echo $settings['theme']; ?>" style="font-family: '<?php echo $settings['font_family']; ?>'; background-color: <?php echo $settings['bg_color']; ?>; color: <?php echo $settings['text_color']; ?>;">
                            <div class="preview-clock-inner">
                                <div class="time">12:34:56</div>
                                <div class="date">Maandag 17 Maart</div>
                                <div class="preview-menu-placeholder">
                                    <div class="dot"></div><div class="line"></div>
                                    <div class="dot"></div><div class="line"></div>
                                    <div class="dot"></div><div class="line"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="preview-note"><?php _e('Dit is een weergave van hoe het paneel eruit zal zien.', 'adremm-clock-plugin'); ?></p>
                </div>
            </div>
        </div>
    </form>
</div>
