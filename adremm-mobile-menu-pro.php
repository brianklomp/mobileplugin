<?php
/**
 * Plugin Name: ADREMM Mobile Menu PRO
 * Description: De ultieme mobiele menu oplossing. Geoptimaliseerd voor Divi, snelle UX en volledige controle over typografie, socials en credits.
 * Plugin URI: https://adremm.nl/
 * Version: 1.5.3
 * Author: ADREMM
 * Author URI: https://adremm.nl
 * License: GPLv2 or later
 * Text Domain: adremm-mobile-menu-pro
 */

if ( ! defined('ABSPATH') ) exit;

define('ADREMM_MMP_VERSION', '1.5.3');
define('ADREMM_MMP_SLUG', 'adremm-mm-pro');
define('ADREMM_MMP_OPT', 'adremm_mm_pro_options');
define('ADREMM_MMP_TRANS_REDIRECT', 'adremm_mm_pro_redirect');

/**
 * 1. ACTIVATIE & REDIRECT
 */
register_activation_hook(__FILE__, function() {
    set_transient(ADREMM_MMP_TRANS_REDIRECT, 1, 60);
});

add_action('admin_init', function() {
    if (get_transient(ADREMM_MMP_TRANS_REDIRECT)) {
        delete_transient(ADREMM_MMP_TRANS_REDIRECT);
        if (!isset($_GET['activate-multi'])) {
            wp_safe_redirect(admin_url('options-general.php?page=' . ADREMM_MMP_SLUG));
            exit;
        }
    }
});

/**
 * 2. DATA & DEFAULTS
 */
function adremm_mmp_get_google_fonts() {
    return array('Inter', 'Poppins', 'Outfit', 'Roboto', 'Montserrat', 'Open Sans', 'Lato', 'Nunito', 'Raleway', 'Ubuntu', 'Playfair Display', 'Oswald', 'Rubik', 'Manrope', 'DM Sans');
}

function adremm_mmp_get_defaults() {
    return array(
        'label'           => 'Menu',
        'back_label'      => 'Terug',
        'z_index'         => '9999999',
        'drawer'          => 'right',
        'easing'          => 'cubic-bezier(0.16, 1, 0.3, 1)',
        'breakpoint'      => '980',
        'menu_src'        => 'auto',
        // Kleuren & Panel
        'panel_bg'        => 'rgba(255,255,255,1)',
        'panel_border_on' => '1',
        'panel_border'    => 'rgba(0,0,0,0.1)',
        'overlay'         => 'rgba(0,0,0,0.6)',
        'burger_off'      => 'rgba(17,17,17,1)',
        'burger_on'       => 'rgba(0,0,0,1)',
        // Typografie
        'font_family'     => 'Inter',
        'font_size'       => '18',
        'font_weight'     => '500',
        'text_color'      => 'rgba(17,17,17,1)',
        'item_gap'        => '0',
        'item_padding'    => '15',
        // Socials
        'socials_on'      => '1',
        'socials'         => array(
            'facebook'  => array('on' => '0', 'url' => ''),
            'instagram' => array('on' => '0', 'url' => ''),
            'linkedin'  => array('on' => '0', 'url' => ''),
            'x'         => array('on' => '0', 'url' => ''),
        ),
        // Credits
        'credits_on'      => '1',
        'credits_text'    => 'Powered by ADREMM',
    );
}

function adremm_mmp_get_options() {
    return array_replace_recursive(adremm_mmp_get_defaults(), (array)get_option(ADREMM_MMP_OPT, array()));
}

// Registreer instellingen
add_action('admin_init', function() {
    register_setting(ADREMM_MMP_OPT, ADREMM_MMP_OPT);
});

/**
 * 3. ADMIN INTERFACE
 */
add_action('admin_menu', function() {
    add_options_page('ADREMM Mobile Menu', 'ADREMM Menu PRO', 'manage_options', ADREMM_MMP_SLUG, 'adremm_mmp_render_admin');
});

add_action('admin_enqueue_scripts', function($hook) {
    if ('settings_page_' . ADREMM_MMP_SLUG !== $hook) return;
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
});

function adremm_mmp_render_admin() {
    $o = adremm_mmp_get_options();
    $fonts = adremm_mmp_get_google_fonts();
    ?>
    <div class="wrap adremm-admin-wrap">
        <div class="adremm-header">
            <h1>ADREMM Mobile Menu PRO <small>v<?php echo ADREMM_MMP_VERSION; ?></small></h1>
        </div>

        <?php if (isset($_GET['settings-updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Opgeslagen!</strong> Uw wijzigingen zijn succesvol verwerkt.</p></div>
        <?php endif; ?>

        <form method="post" action="options.php" id="adremm-pro-form">
            <?php settings_fields(ADREMM_MMP_OPT); ?>
            
            <div class="adremm-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#tab-general" class="nav-tab nav-tab-active">1. Algemeen</a>
                    <a href="#tab-behavior" class="nav-tab">2. Gedrag</a>
                    <a href="#tab-typo" class="nav-tab">3. Typografie</a>
                    <a href="#tab-socials" class="nav-tab">4. Socials</a>
                    <a href="#tab-credits" class="nav-tab">5. Credits</a>
                    <a href="#tab-colors" class="nav-tab">6. Kleuren</a>
                </nav>

                <div class="tab-content">
                    <!-- 1. ALGEMEEN -->
                    <div id="tab-general" class="tab-pane active">
                        <table class="form-table">
                            <tr>
                                <th>Menu Label</th>
                                <td><input type="text" name="<?php echo ADREMM_MMP_OPT; ?>[label]" value="<?php echo esc_attr($o['label']); ?>" class="regular-text"></td>
                            </tr>
                            <tr>
                                <th>Z-Index</th>
                                <td>
                                    <input type="number" name="<?php echo ADREMM_MMP_OPT; ?>[z_index]" value="<?php echo esc_attr($o['z_index']); ?>">
                                    <p class="description">Zorgt ervoor dat het menu boven de Divi header zweeft.</p>
                                </td>
                            </tr>
                            <tr>
                                <th>Breakpoint (px)</th>
                                <td><input type="number" name="<?php echo ADREMM_MMP_OPT; ?>[breakpoint]" value="<?php echo esc_attr($o['breakpoint']); ?>"></td>
                            </tr>
                        </table>
                    </div>

                    <!-- 2. GEDRAG -->
                    <div id="tab-behavior" class="tab-pane">
                        <table class="form-table">
                            <tr>
                                <th>Drawer Positie</th>
                                <td>
                                    <select name="<?php echo ADREMM_MMP_OPT; ?>[drawer]">
                                        <option value="left" <?php selected($o['drawer'], 'left'); ?>>Links</option>
                                        <option value="right" <?php selected($o['drawer'], 'right'); ?>>Rechts</option>
                                        <option value="fullscreen" <?php selected($o['drawer'], 'fullscreen'); ?>>Fullscreen</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>Back Button Label</th>
                                <td><input type="text" name="<?php echo ADREMM_MMP_OPT; ?>[back_label]" value="<?php echo esc_attr($o['back_label']); ?>"></td>
                            </tr>
                        </table>
                    </div>

                    <!-- 3. TYPOGRAFIE -->
                    <div id="tab-typo" class="tab-pane">
                        <table class="form-table">
                            <tr>
                                <th>Font Family</th>
                                <td>
                                    <select name="<?php echo ADREMM_MMP_OPT; ?>[font_family]">
                                        <?php foreach($fonts as $f): ?>
                                            <option value="<?php echo $f; ?>" <?php selected($o['font_family'], $f); ?>><?php echo $f; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>Font Size (px)</th>
                                <td><input type="number" name="<?php echo ADREMM_MMP_OPT; ?>[font_size]" value="<?php echo esc_attr($o['font_size']); ?>"></td>
                            </tr>
                            <tr>
                                <th>Tekst Kleur</th>
                                <td><input type="text" name="<?php echo ADREMM_MMP_OPT; ?>[text_color]" value="<?php echo esc_attr($o['text_color']); ?>" class="color-picker"></td>
                            </tr>
                            <tr>
                                <th>Item Padding (px)</th>
                                <td><input type="number" name="<?php echo ADREMM_MMP_OPT; ?>[item_padding]" value="<?php echo esc_attr($o['item_padding']); ?>"></td>
                            </tr>
                        </table>
                    </div>

                    <!-- 4. SOCIALS -->
                    <div id="tab-socials" class="tab-pane">
                        <table class="form-table">
                            <tr>
                                <th>Socials Activeren</th>
                                <td><input type="checkbox" name="<?php echo ADREMM_MMP_OPT; ?>[socials_on]" value="1" <?php checked($o['socials_on'], '1'); ?>></td>
                            </tr>
                            <?php foreach($o['socials'] as $key => $data): ?>
                            <tr>
                                <th style="text-transform: capitalize;"><?php echo $key; ?></th>
                                <td>
                                    <input type="checkbox" name="<?php echo ADREMM_MMP_OPT; ?>[socials][<?php echo $key; ?>][on]" value="1" <?php checked($data['on'] ?? '0', '1'); ?>>
                                    <input type="text" name="<?php echo ADREMM_MMP_OPT; ?>[socials][<?php echo $key; ?>][url]" value="<?php echo esc_attr($data['url'] ?? ''); ?>" placeholder="https://..." class="regular-text">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>

                    <!-- 5. CREDITS -->
                    <div id="tab-credits" class="tab-pane">
                        <table class="form-table">
                            <tr>
                                <th>Credits Tonen</th>
                                <td><input type="checkbox" name="<?php echo ADREMM_MMP_OPT; ?>[credits_on]" value="1" <?php checked($o['credits_on'], '1'); ?>></td>
                            </tr>
                            <tr>
                                <th>Credits Tekst</th>
                                <td><input type="text" name="<?php echo ADREMM_MMP_OPT; ?>[credits_text]" value="<?php echo esc_attr($o['credits_text']); ?>" class="regular-text"></td>
                            </tr>
                        </table>
                    </div>

                    <!-- 6. KLEUREN -->
                    <div id="tab-colors" class="tab-pane">
                        <table class="form-table">
                            <tr>
                                <th>Achtergrond Paneel</th>
                                <td><input type="text" name="<?php echo ADREMM_MMP_OPT; ?>[panel_bg]" value="<?php echo esc_attr($o['panel_bg']); ?>" class="color-picker"></td>
                            </tr>
                            <tr>
                                <th>Overlay Kleur</th>
                                <td><input type="text" name="<?php echo ADREMM_MMP_OPT; ?>[overlay]" value="<?php echo esc_attr($o['overlay']); ?>" class="color-picker"></td>
                            </tr>
                            <tr>
                                <th>Rand Tonen</th>
                                <td><input type="checkbox" name="<?php echo ADREMM_MMP_OPT; ?>[panel_border_on]" value="1" <?php checked($o['panel_border_on'], '1'); ?>></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary button-large" value="Wijzigingen opslaan">
            </p>
        </form>
    </div>

    <style>
        .adremm-admin-wrap { max-width: 1000px; margin-top: 20px; }
        .adremm-header h1 { background: #111; color: #fff; padding: 20px; margin: 0; border-radius: 5px 5px 0 0; }
        .tab-content { background: #fff; padding: 20px; border: 1px solid #ccc; border-top: none; min-height: 300px; }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }
        .nav-tab-wrapper { margin-bottom: 0 !important; }
        .form-table th { width: 220px; font-weight: 600; }
    </style>

    <script>
        jQuery(document).ready(function($) {
            $('.color-picker').wpColorPicker();
            $('.nav-tab').click(function(e) {
                e.preventDefault();
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.tab-pane').removeClass('active');
                $($(this).attr('href')).addClass('active');
            });
        });
    </script>
    <?php
}

/**
 * 4. FRONTEND OUTPUT
 */
add_action('wp_enqueue_scripts', function() {
    $o = adremm_mmp_get_options();
    $font = str_replace(' ', '+', $o['font_family']);
    wp_enqueue_style('adremm-google-fonts', "https://fonts.googleapis.com/css2?family={$font}:wght@400;500;700&display=swap", false);
});

add_action('wp_footer', function() {
    if (is_admin()) return;
    $o = adremm_mmp_get_options();
    
    $menu_args = array('theme_location' => 'primary', 'container' => false, 'echo' => false, 'fallback_cb' => false);
    $menu_html = wp_nav_menu($menu_args);
    if (!$menu_html) $menu_html = '<ul><li>Menu niet geconfigureerd.</li></ul>';

    $css_vars = "
        --amm-z: {$o['z_index']};
        --amm-bg: {$o['panel_bg']};
        --amm-overlay: {$o['overlay']};
        --amm-border: " . ($o['panel_border_on'] ? $o['panel_border'] : 'transparent') . ";
        --amm-burger-off: {$o['burger_off']};
        --amm-burger-on: {$o['burger_on']};
        --amm-ease: {$o['easing']};
        --amm-font: '{$o['font_family']}', sans-serif;
        --amm-fsize: {$o['font_size']}px;
        --amm-fweight: {$o['font_weight']};
        --amm-tcolor: {$o['text_color']};
        --amm-padding: {$o['item_padding']}px;
    ";
    ?>
    <div id="adremm-mmp-root" class="adremm-mmp amm-pos-<?php echo $o['drawer']; ?>" style="<?php echo $css_vars; ?>">
        <div class="amm-toggle-btn-wrap">
            <button class="amm-toggle-btn">
                <span class="amm-burger"><i></i><i></i><i></i></span>
                <span class="amm-label"><?php echo esc_html($o['label']); ?></span>
            </button>
        </div>

        <div class="amm-overlay"></div>

        <div class="amm-panel">
            <div class="amm-header">
                <button class="amm-back-btn" style="display:none;">&larr; <?php echo esc_html($o['back_label']); ?></button>
                <div class="amm-spacer"></div>
                <button class="amm-close-btn">&times;</button>
            </div>

            <div class="amm-content">
                <div class="amm-slider">
                    <div class="amm-view amm-root-view">
                        <?php echo $menu_html; ?>
                    </div>
                </div>
            </div>

            <div class="amm-footer">
                <?php if ($o['socials_on']): ?>
                <div class="amm-socials">
                    <?php 
                    $icons = array(
                        'facebook' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h2V2.14c-.326-.043-1.557-.14-2.857-.14C12.445 2 10 3.657 10 6.5v3H7v4h3V22h4v-8.5z"/></svg>',
                        'instagram' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2.717 0 3.056.01 4.122.058 1.066.048 1.79.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.637.417 1.361.465 2.427.048 1.066.058 1.405.058 4.122s-.01 3.056-.058 4.122c-.048 1.066-.218 1.79-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.637.247-1.361.417-2.427.465-1.066.048-1.405.058-4.122.058s-3.056-.01-4.122-.058c-1.066-.048-1.79-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.637-.417-1.361-.465-2.427C2.01 15.056 2 14.717 2 12s.01-3.056.058-4.122c.048-1.066.218-1.79.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.448 2.523c.637-.247 1.361-.417 2.427-.465C8.944 2.01 9.283 2 12 2zm0 5a5 5 0 100 10 5 5 0 000-10zm6.5-.25a1.25 1.25 0 10-2.5 0 1.25 1.25 0 002.5 0zM12 9a3 3 0 110 6 3 3 0 010-6z"/></svg>',
                        'linkedin' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14m-.5 15.5v-5.3a3.26 3.26 0 00-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 011.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 001.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 00-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>',
                        'x' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'
                    );
                    foreach($o['socials'] as $key => $data):
                        if(!empty($data['on']) && !empty($data['url'])): ?>
                        <a href="<?php echo esc_url($data['url']); ?>" target="_blank" class="amm-social-link"><?php echo $icons[$key]; ?></a>
                    <?php endif; endforeach; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($o['credits_on']): ?>
                <div class="amm-credits"><?php echo esc_html($o['credits_text']); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <style>
        #adremm-mmp-root { position: fixed; inset: 0; width: 100%; height: 0; z-index: var(--amm-z); font-family: var(--amm-font); color: var(--amm-tcolor); }
        .amm-toggle-btn-wrap { position: fixed; top: 20px; right: 20px; z-index: calc(var(--amm-z) + 10); }
        .amm-toggle-btn { background: #fff; border: 1px solid #ddd; padding: 10px 15px; border-radius: 50px; display: flex; align-items: center; gap: 10px; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.1); font-family: inherit; }
        .amm-burger { width: 20px; height: 14px; position: relative; display: block; }
        .amm-burger i { position: absolute; left: 0; width: 100%; height: 2px; background: var(--amm-burger-off); transition: 0.3s; }
        .amm-burger i:nth-child(1) { top: 0; }
        .amm-burger i:nth-child(2) { top: 6px; }
        .amm-burger i:nth-child(3) { top: 12px; }

        .amm-overlay { position: fixed; inset: 0; background: var(--amm-overlay); opacity: 0; pointer-events: none; transition: 0.4s; }
        .amm-panel { position: fixed; top: 0; bottom: 0; right: 0; width: 85vw; max-width: 400px; background: var(--amm-bg); border-left: 1px solid var(--amm-border); transform: translateX(105%); transition: 0.4s var(--amm-ease); display: flex; flex-direction: column; box-shadow: -10px 0 30px rgba(0,0,0,0.1); }
        
        .amm-pos-left .amm-panel { right: auto; left: 0; transform: translateX(-105%); border-left: none; border-right: 1px solid var(--amm-border); }
        .amm-pos-fullscreen .amm-panel { width: 100%; max-width: 100%; transform: translateY(100%); }

        .amm-open .amm-overlay { opacity: 1; pointer-events: auto; }
        .amm-open .amm-panel { transform: translate(0); }
        .amm-open .amm-burger i { background: var(--amm-burger-on); }
        .amm-open .amm-burger i:nth-child(1) { transform: rotate(45deg); top: 6px; }
        .amm-open .amm-burger i:nth-child(2) { opacity: 0; }
        .amm-open .amm-burger i:nth-child(3) { transform: rotate(-45deg); top: 6px; }

        .amm-header { padding: 20px; border-bottom: 1px solid var(--amm-border); display: flex; align-items: center; min-height: 70px; }
        .amm-spacer { flex: 1; }
        .amm-close-btn { background: none; border: none; font-size: 32px; cursor: pointer; color: inherit; line-height: 1; }
        .amm-back-btn { background: none; border: 1px solid #ddd; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-family: inherit; font-size: 14px; }
        
        .amm-content { flex: 1; overflow: hidden; position: relative; }
        .amm-slider { display: flex; height: 100%; transition: 0.4s var(--amm-ease); }
        .amm-view { min-width: 100%; padding: 20px; overflow-y: auto; }
        .amm-view ul { list-style: none; padding: 0; margin: 0; }
        .amm-view li { border-bottom: 1px solid rgba(0,0,0,0.05); position: relative; }
        .amm-view a { display: block; padding: var(--amm-padding) 0; text-decoration: none; color: inherit; font-size: var(--amm-fsize); font-weight: var(--amm-fweight); }
        
        .amm-view .menu-item-has-children > a { padding-right: 40px; }
        .amm-next-btn { position: absolute; right: 0; top: 0; height: 100%; width: 50px; background: none; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 24px; opacity: 0.5; }

        .amm-footer { padding: 20px; border-top: 1px solid var(--amm-border); background: rgba(0,0,0,0.02); z-index: 100; position: relative; }
        .amm-socials { display: flex; gap: 15px; margin-bottom: 10px; position: relative; z-index: 200; }
        .amm-social-link { display: block; width: 24px; height: 24px; color: inherit; opacity: 0.8; transition: 0.3s; }
        .amm-social-link:hover { opacity: 1; transform: translateY(-2px); }
        .amm-credits { font-size: 11px; opacity: 0.5; text-transform: uppercase; letter-spacing: 1px; }

        @media screen and (min-width: <?php echo $o['breakpoint']; ?>px) {
            #adremm-mmp-root { display: none !important; }
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const root = document.getElementById('adremm-mmp-root');
        if(!root) return;
        const toggle = root.querySelector('.amm-toggle-btn');
        const overlay = root.querySelector('.amm-overlay');
        const close = root.querySelector('.amm-close-btn');
        const slider = root.querySelector('.amm-slider');
        const backBtn = root.querySelector('.amm-back-btn');
        
        let stack = [0];

        toggle.addEventListener('click', () => root.classList.toggle('amm-open'));
        overlay.addEventListener('click', () => root.classList.remove('amm-open'));
        close.addEventListener('click', () => root.classList.remove('amm-open'));

        function initDrilldown(parentView) {
            const items = parentView.querySelectorAll('.menu-item-has-children');
            items.forEach(li => {
                const sub = li.querySelector('ul');
                if(!sub) return;

                const next = document.createElement('button');
                next.className = 'amm-next-btn';
                next.innerHTML = '&rsaquo;';
                li.appendChild(next);

                const newView = document.createElement('div');
                newView.className = 'amm-view';
                newView.appendChild(sub);
                slider.appendChild(newView);
                const index = Array.from(slider.children).indexOf(newView);

                next.addEventListener('click', (e) => {
                    e.preventDefault();
                    stack.push(index);
                    slider.style.transform = `translateX(-${index * 100}%)`;
                    backBtn.style.display = 'block';
                });

                initDrilldown(newView);
            });
        }

        initDrilldown(root.querySelector('.amm-root-view'));

        backBtn.addEventListener('click', () => {
            stack.pop();
            const idx = stack[stack.length - 1];
            slider.style.transform = `translateX(-${idx * 100}%)`;
            if(stack.length === 1) backBtn.style.display = 'none';
        });
    });
    </script>
    <?php
}, 9999999);
