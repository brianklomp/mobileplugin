<?php
/**
 * Plugin Name: ADREMM Mobile Menu PRO
 * Description: Mobiele drawer/fullscreen navigatie met drilldown, per-laag typografie, socials/credits en RGBA colors. Dynamische Google Fonts via API.
 * Plugin URI: https://adremm.nl/developments
 * Version: 1.5.3
 * Author: ADREMM
 * Author URI: https://adremm.nl
 * License: GPLv2 or later
 * Text Domain: adremm-mobile-menu-pro
 * Requires PHP: 7.4
 */

if ( ! defined('ABSPATH') ) exit;

define('ADREMM_MMP_VERSION', '1.5.3');
define('ADREMM_MMP_SLUG', 'adremm-mm-pro');
define('ADREMM_MMP_OPT', 'adremm_mm_pro_options');
define('ADREMM_MMP_TRANS_REDIRECT', 'adremm_mm_pro_redirect');
define('ADREMM_MMP_TRANS_UPDATED', 'adremm_mm_pro_updated');
define('ADREMM_MMP_CACHE_BUSTER', 'v' . ADREMM_MMP_VERSION . '_' . gmdate('YmdHis', filemtime(__FILE__)));

// PHP versie check
if (version_compare(PHP_VERSION, '7.4', '<')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p><strong>ADREMM Mobile Menu PRO</strong> vereist PHP 7.4 of hoger (jouw versie: ' . PHP_VERSION . '). Update PHP voor optimale stabiliteit.</p></div>';
    });
    return;
}

/**
 * Plugin actie links + row meta
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    if (!current_user_can('manage_options')) return $links;
    $settings_url = admin_url('admin.php?page=' . ADREMM_MMP_SLUG);
    array_unshift($links, '<a href="' . esc_url($settings_url) . '">Instellingen</a>');
    return $links;
});

add_filter('plugin_row_meta', function ($meta, $file) {
    if ($file !== plugin_basename(__FILE__)) return $meta;
    $meta[] = '<a href="https://adremm.nl" target="_blank" rel="noopener">ADREMM.nl</a>';
    return $meta;
}, 10, 2);

/**
 * Update detectie voor auto-redirect
 */
add_action('upgrader_process_complete', function ($upgrader, $hook_extra) {
    if (empty($hook_extra['type']) || $hook_extra['type'] !== 'plugin') return;
    $plugins = [];
    if (!empty($hook_extra['plugins']) && is_array($hook_extra['plugins'])) {
        $plugins = $hook_extra['plugins'];
    } elseif (!empty($hook_extra['plugin'])) {
        $plugins = [(string)$hook_extra['plugin']];
    }
    if (in_array(plugin_basename(__FILE__), $plugins, true)) {
        set_transient(ADREMM_MMP_TRANS_UPDATED, 1, 5 * MINUTE_IN_SECONDS);
    }
}, 10, 2);

/**
 * Google Fonts API integratie (gebaseerd op jouw originele code)
 */
function adremm_mmp_google_fonts() {
    $options = adremm_mmp_get_options();
    $api_key = isset($options['google_fonts_api_key']) ? trim($options['google_fonts_api_key']) : '';
    $fonts = get_transient('adremm_mmp_google_fonts_list');
    
    if (false === $fonts) {
        $fonts = array();
        if (!empty($api_key)) {
            $url = add_query_arg(
                array(
                    'key'   => $api_key,
                    'sort'  => 'popularity',
                    'fields' => 'items/family',
                ),
                'https://www.googleapis.com/webfonts/v1/webfonts'
            );
            $response = wp_remote_get($url, array('timeout' => 10));
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($body['items']) && is_array($body['items'])) {
                    foreach ($body['items'] as $font) {
                        $fonts[] = $font['family'];
                    }
                }
            }
        }
        if (empty($fonts)) {
            $fonts = array(
                'Inter','Poppins','Outfit','Roboto','Montserrat','Open Sans','Lato','Nunito','Raleway','Ubuntu',
                'Playfair Display','Oswald','Rubik','Manrope','DM Sans','Merriweather','Source Sans Pro','Fira Sans',
                'Quicksand','Mulish','Work Sans','Plus Jakarta Sans','Figtree','Satoshi','Clash Grotesk','Outfit'
            );
        }
        sort($fonts);
        $cache_duration = empty($api_key) ? DAY_IN_SECONDS : 7 * DAY_IN_SECONDS;
        set_transient('adremm_mmp_google_fonts_list', $fonts, $cache_duration);
    }
    return $fonts;
}

/**
 * Social platforms met fallback icons
 */
function adremm_mmp_social_platforms() {
    $chevron = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M16.172 11H4v2h12.172l-5.586 5.586 1.414 1.414L20 12l-8-8-1.414 1.414z"/></svg>';
    $caret = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>';
    $plus = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>';
    
    return array(
        'facebook'  => array('label'=>'Facebook',  'svg'=>str_replace('currentColor', '#1877F2', $chevron)),
        'instagram' => array('label'=>'Instagram', 'svg'=>str_replace('currentColor', '#E1306C', $chevron)),
        'linkedin'  => array('label'=>'LinkedIn',  'svg'=>str_replace('currentColor', '#0077B5', $chevron)),
        'x'         => array('label'=>'X',         'svg'=>str_replace('currentColor', '#000000', $chevron)),
        'youtube'   => array('label'=>'YouTube',   'svg'=>str_replace('currentColor', '#FF0000', $chevron)),
        'tiktok'    => array('label'=>'TikTok',    'svg'=>str_replace('currentColor', '#000000', $chevron)),
        'pinterest' => array('label'=>'Pinterest', 'svg'=>str_replace('currentColor', '#E60023', $chevron)),
        'snapchat'  => array('label'=>'SnapChat',  'svg'=>str_replace('currentColor', '#FFFC00', $chevron)),
        'whatsapp'  => array('label'=>'WhatsApp',  'svg'=>str_replace('currentColor', '#25D366', $chevron)),
        'telegram'  => array('label'=>'Telegram',  'svg'=>str_replace('currentColor', '#0088CC', $chevron)),
        'discord'   => array('label'=>'Discord',   'svg'=>str_replace('currentColor', '#5865F2', $chevron)),
        'github'    => array('label'=>'GitHub',    'svg'=>str_replace('currentColor', '#181717', $chevron)),
        'behance'   => array('label'=>'Behance',   'svg'=>str_replace('currentColor', '#0057FF', $chevron)),
        'dribbble'  => array('label'=>'Dribbble',  'svg'=>str_replace('currentColor', '#EA4C89', $chevron)),
        'threads'   => array('label'=>'Threads',   'svg'=>str_replace('currentColor', '#000000', $chevron)),
    );
}

function adremm_mmp_units() {
    return array('px'=>'px','em'=>'em','rem'=>'rem');
}

function adremm_mmp_unit_select($name, $current, $units) {
    $current = $current ? $current : 'px';
    if(!is_array($units) || empty($units)) $units = adremm_mmp_units();
    $out = '<select class="adremm-unit" name="'.esc_attr(ADREMM_MMP_OPT).'['.esc_attr($name).']">';
    foreach($units as $u=>$t) {
        $out .= '<option value="'.esc_attr($u).'" '.selected($current, $u, false).'>'.esc_html($t).'</option>';
    }
    $out .= '</select>';
    return $out;
}

function adremm_mmp_defaults() {
    $layer = function() {
        return array(
            'mode' => 'theme',
            'family' => 'inherit',
            'size' => '16',
            'unit' => 'px',
            'weight' => '500',
            'line' => '1.2',
            'color' => 'rgba(17,17,17,1)',
            'hover' => '',
            'active' => '',
            'pad_y' => '10',
            'pad_x' => '12',
            'gap'   => '8',
            'underline' => 'none',
            'ul_thick' => '2',
            'ul_offset'=> '3'
        );
    };
    
    $social_defaults = array();
    foreach(adremm_mmp_social_platforms() as $k=>$v) {
        $social_defaults[$k] = array('on'=>'0','url'=>'','custom_icon_url'=>'');
    }
    
    return array(
        'label'       => 'Menu',
        'back_label'  => 'Terug',
        'z_index'     => '2147483647', // Max 32-bit integer voor Divi compatibiliteit
        'global_font' => 'inherit',
        'drawer'      => 'right',
        'easing'      => 'cubic-bezier(0.16, 1, 0.3, 1)',
        'breakpoint'  => '980',
        'breakpoint_unit' => 'px',
        'menu_src'    => 'auto',
        'toggle_align'=> 'right',
        'toggle_fixed'=> '0',
        'toggle_top'  => '20',
        'toggle_side' => '20',
        'toggle_top_unit'  => 'px',
        'toggle_side_unit' => 'px',
        'toggle_bg'   => 'rgba(255,255,255,0)',
        'panel_w'     => '90vw',
        'panel_max'   => '420px',
        'fs_nav_max'  => '520px',
        'panel_bg'    => 'rgba(255,255,255,1)',
        'panel_border'=> 'rgba(0,0,0,0.12)',
        'panel_border_width' => '1',
        'overlay'     => 'rgba(0,0,0,0.45)',
        'burger_off'  => 'rgba(17,17,17,1)',
        'burger_on'   => 'rgba(214,33,33,1)',
        'burger_anim' => 'swap',
        'close_size'  => '16',
        'close_unit'  => 'px',
        'close_color' => 'rgba(17,17,17,1)',
        'back_position' => 'header',
        'show_close'    => '1',
        'submenu_icon'      => 'chevron',
        'submenu_icon_url'  => '',
        'submenu_icon_size' => '14',
        'submenu_icon_unit' => 'px',
        'back_icon'         => 'chevron',
        'back_icon_url'     => '',
        'back_icon_size'    => '14',
        'back_icon_unit'    => 'px',
        'layers' => array(
            'menu_label' => $layer(),
            'items'      => $layer(),
            'subitems'   => $layer(),
            'back'       => $layer(),
        ),
        'socials_on'   => '0',
        'socials_size' => '25',
        'socials_unit' => 'px',
        'socials'      => $social_defaults,
        'credits_on'   => '0',
        'credits_text' => 'ADREMM',
        'credits_url'  => 'https://adremm.nl',
        'credits_icon_url' => '',
        'credits_size' => '25',
        'credits_unit' => 'px',
        'google_fonts_api_key' => '',
    );
}

function adremm_mmp_get_options() {
    $d = adremm_mmp_defaults();
    $o = get_option(ADREMM_MMP_OPT, array());
    if(!is_array($o)) $o = array();
    $m = array_merge($d, $o);
    
    if(!isset($m['layers']) || !is_array($m['layers'])) $m['layers'] = $d['layers'];
    foreach($d['layers'] as $k=>$lv) {
        if(!isset($m['layers'][$k]) || !is_array($m['layers'][$k])) $m['layers'][$k] = $lv;
        else $m['layers'][$k] = array_merge($lv, $m['layers'][$k]);
    }
    
    if(!isset($m['socials']) || !is_array($m['socials'])) $m['socials'] = $d['socials'];
    foreach($d['socials'] as $k=>$sv) {
        if(!isset($m['socials'][$k]) || !is_array($m['socials'][$k])) $m['socials'][$k] = $sv;
        else $m['socials'][$k] = array_merge($sv, $m['socials'][$k]);
    }
    
    return $m;
}

/**
 * Redirect na activatie/update
 */
register_activation_hook(__FILE__, function () {
    if (!is_admin()) return;
    set_transient(ADREMM_MMP_TRANS_UPDATED, 1, 60);
});

add_action('admin_init', function () {
    if (!current_user_can('manage_options')) return;
    if (wp_doing_ajax()) return;
    
    // Settings opgeslagen melding
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] && !isset($_GET['adremm_error'])) {
        add_settings_error(ADREMM_MMP_OPT, 'adremm_settings_saved', 'Opgeslagen!', 'updated');
    } elseif (isset($_GET['adremm_error'])) {
        add_settings_error(ADREMM_MMP_OPT, 'adremm_settings_error', 'Er ging iets mis: Controleer je velden en probeer het nog eens. Kom je er niet uit? Neem dan contact op met support.', 'error');
    }
    
    // Auto-redirect na update/activatie
    $flag = get_transient(ADREMM_MMP_TRANS_UPDATED);
    if (!$flag) return;
    
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $settings_url = admin_url('admin.php?page=' . ADREMM_MMP_SLUG);
    
    if (strpos($current_url, $settings_url) !== false) {
        delete_transient(ADREMM_MMP_TRANS_UPDATED);
        return;
    }
    
    delete_transient(ADREMM_MMP_TRANS_UPDATED);
    wp_safe_redirect($settings_url);
    exit;
});

/**
 * Sanitization
 */
add_action('admin_init', function(){
    register_setting(ADREMM_MMP_OPT, ADREMM_MMP_OPT, array(
        'type'=>'array',
        'sanitize_callback'=>'adremm_mmp_sanitize',
        'default'=>adremm_mmp_defaults(),
    ));
});

function adremm_mmp_sanitize_rgba($v) {
    $v = trim((string)$v);
    if($v==='') return '';
    if(preg_match('/^rgba\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*(0(\.\d+)?|1(\.0+)?)\s*\)$/', $v)) return $v;
    if(preg_match('/^rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)$/', $v)) {
        $nums = preg_replace('/[^0-9,]/','',$v);
        $p = explode(',',$nums);
        if(count($p)===3) return 'rgba('.intval($p[0]).','.intval($p[1]).','.intval($p[2]).',1)';
    }
    if(preg_match('/^#?[0-9a-fA-F]{6}$/', $v)) {
        $h = ltrim($v,'#');
        $r = hexdec(substr($h,0,2)); $g=hexdec(substr($h,2,2)); $b=hexdec(substr($h,4,2));
        return 'rgba('.$r.','.$g.','.$b.',1)';
    }
    return '';
}

function adremm_mmp_sanitize_layer($in, $d) {
    if(!is_array($in)) $in=array();
    $out = $d;
    $out['mode'] = in_array($in['mode'] ?? 'theme', array('theme','google','custom'), true) ? $in['mode'] : 'theme';
    $out['family'] = sanitize_text_field($in['family'] ?? 'inherit');
    $out['size'] = preg_replace('/[^0-9]/','', (string)($in['size'] ?? $d['size']));
    $out['unit'] = in_array(($in['unit'] ?? 'px'), array('px','rem','em'), true) ? $in['unit'] : 'px';
    $out['weight'] = preg_replace('/[^0-9]/','', (string)($in['weight'] ?? $d['weight']));
    $out['line'] = preg_replace('/[^0-9.]/','', (string)($in['line'] ?? $d['line']));
    $out['color'] = adremm_mmp_sanitize_rgba($in['color'] ?? $d['color']);
    $out['hover'] = adremm_mmp_sanitize_rgba($in['hover'] ?? '');
    $out['active']= adremm_mmp_sanitize_rgba($in['active'] ?? '');
    $out['pad_y'] = preg_replace('/[^0-9]/','', (string)($in['pad_y'] ?? $d['pad_y']));
    $out['pad_x'] = preg_replace('/[^0-9]/','', (string)($in['pad_x'] ?? $d['pad_x']));
    $out['gap']   = preg_replace('/[^0-9]/','', (string)($in['gap'] ?? $d['gap']));
    $out['underline'] = in_array(($in['underline'] ?? 'none'), array('none','solid','dashed','dotted','double'), true) ? $in['underline'] : 'none';
    $out['ul_thick'] = preg_replace('/[^0-9]/','', (string)($in['ul_thick'] ?? $d['ul_thick']));
    $out['ul_offset']= preg_replace('/[^0-9]/','', (string)($in['ul_offset'] ?? $d['ul_offset']));
    return $out;
}

function adremm_mmp_sanitize($in) {
    $d = adremm_mmp_defaults();
    if(!is_array($in)) $in=array();
    $out = array();
    
    $simple = array(
        'label','back_label','z_index','drawer','easing','breakpoint','menu_src','toggle_align','toggle_fixed','toggle_top','toggle_side','toggle_bg','global_font',
        'panel_w','panel_max','fs_nav_max',
        'panel_bg','panel_border','panel_border_width','overlay',
        'burger_off','burger_on','burger_anim',
        'close_size','close_color','back_position','show_close',
        'submenu_icon','submenu_icon_url','submenu_icon_size',
        'back_icon','back_icon_url','back_icon_size',
        'socials_on','socials_size',
        'credits_on','credits_text','credits_url','credits_icon_url','credits_size',
        'google_fonts_api_key'
    );
    foreach($simple as $k) {
        $v = isset($in[$k]) ? wp_unslash($in[$k]) : ($d[$k] ?? '');
        $out[$k] = is_string($v) ? sanitize_text_field($v) : $v;
    }
    
    foreach(array('breakpoint','z_index','submenu_icon_size','back_icon_size','close_size','socials_size','credits_size','panel_border_width') as $nk) {
        $out[$nk] = preg_replace('/[^0-9]/','', (string)($out[$nk] ?? ''));
    }
    
    foreach(array('panel_bg','panel_border','overlay','burger_off','burger_on','close_color') as $ck) {
        $out[$ck] = adremm_mmp_sanitize_rgba($out[$ck] ?? $d[$ck]);
        if($out[$ck]==='') $out[$ck] = $d[$ck];
    }
    
    $out['layers'] = array();
    $din = isset($in['layers']) && is_array($in['layers']) ? $in['layers'] : array();
    foreach($d['layers'] as $lk=>$ld) {
        $out['layers'][$lk] = adremm_mmp_sanitize_layer($din[$lk] ?? array(), $ld);
    }
    
    $out['socials'] = array();
    $sin = isset($in['socials']) && is_array($in['socials']) ? $in['socials'] : array();
    foreach($d['socials'] as $sk=>$sd) {
        $row = isset($sin[$sk]) && is_array($sin[$sk]) ? $sin[$sk] : array();
        $out['socials'][$sk] = array(
            'on' => (!empty($row['on']) && $row['on']!=='0') ? '1' : '0',
            'url' => esc_url_raw($row['url'] ?? ''),
            'custom_icon_url' => esc_url_raw($row['custom_icon_url'] ?? '')
        );
    }
    
    if(!in_array($out['drawer'], array('left','right','fullscreen'), true)) $out['drawer'] = $d['drawer'];
    if(!in_array($out['submenu_icon'], array('chevron','caret','plus','custom'), true)) $out['submenu_icon'] = $d['submenu_icon'];
    if(!in_array($out['back_icon'], array('chevron','caret','plus','custom'), true)) $out['back_icon'] = $d['back_icon'];
    if(!in_array($out['burger_anim'], array('swap','squeeze','morph','spin','arrow'), true)) $out['burger_anim'] = $d['burger_anim'];
    if(!in_array($out['back_position'], array('header','footer','replace_close'), true)) $out['back_position'] = $d['back_position'];
    $out['show_close'] = (!empty($out['show_close']) && $out['show_close']!=='0') ? '1' : '0';
    $out['socials_on'] = (!empty($out['socials_on']) && $out['socials_on']!=='0') ? '1' : '0';
    $out['credits_on'] = (!empty($out['credits_on']) && $out['credits_on']!=='0') ? '1' : '0';
    
    return $out;
}

/**
 * Admin menu met jouw custom icon
 */
add_action('admin_menu', function(){
    add_menu_page(
        'ADREMM Mobile Menu — Instellingen',
        'ADREMM Mobile Menu',
        'manage_options',
        ADREMM_MMP_SLUG,
        'adremm_mmp_render_admin',
        'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgd2lkdGg9IjI0IiBoZWlnaHQ9IjI0Ij48cGF0aCBmaWxsPSIjNjY2IiBkPSJNMCAwaDI0djI0SDB6IiBvcGFjaXR5PSIwIi8+PHBhdGggZmlsbD0iIzY2NiIgZD0iTTEyLjEzNCAxMy44NDhMMTIgMTUuMDM3TDExLjUyIDE0LjUxOCAyLjY4IDUuNzI3QzEuNDA2IDQuNjE2IDEuNDA2IDMuMjIyIDMuMzI3IDIuMTI5YzEgLTEgMjIuMjA1IDAgMjIuMjA1IDAgMjEuMzI3IDAgMjIuMjA1IDAgMjIuMjA1IDB2MjEuMzI3YzAgMS4zOTQgLTEuMzkzIDMuMjIyIDMuMzI3IDQuMzQ5TDIuNjggMjIuMTI5YzEuOTIgMS4xMDYgMy43MzUgMS4xMDYgNS42NjYgMC4wMDVMMTIgMTUuMDM3TDEyLjEzNCAxMy44NDh6Ii8+PHBhdGggZmlsbD0iIzY2NiIgZD0iTTEyLjEzNCAxMy44NDhMMTIgMTUuMDM3TDExLjUyIDE0LjUxOCAyLjY4IDUuNzI3QzEuNDA2IDQuNjE2IDEuNDA2IDMuMjIyIDMuMzI3IDIuMTI5YzEgLTEgMjIuMjA1IDAgMjIuMjA1IDAgMjEuMzI3IDAgMjIuMjA1IDAgMjIuMjA1IDB2MjEuMzI3YzAgMS4zOTQgLTEuMzkzIDMuMjIyIDMuMzI3IDQuMzQ5TDIuNjggMjIuMTI5YzEuOTIgMS4xMDYgMy43MzUgMS4xMDYgNS42NjYgMC4wMDVMMTIgMTUuMDM3TDEyLjEzNCAxMy44NDh6Ii8+PC9zdmc+',
        59
    );
}, 9);

/**
 * Menu selectie HTML (originele functionaliteit)
 */
function adremm_mmp_menu_select_html($cur) {
    $out  = '<select name="'.esc_attr(ADREMM_MMP_OPT).'[menu_src]" class="regular-text">';
    $out .= '<option value="auto" '.selected($cur,'auto',false).'>Automatisch (primary → eerste menu)</option>';
    $locs = get_registered_nav_menus();
    if($locs) {
        $out .= '<optgroup label="Theme locations">';
        foreach($locs as $slug=>$desc) {
            $val='location:'.$slug;
            $out .= '<option value="'.esc_attr($val).'" '.selected($cur,$val,false).'>'.esc_html($slug.' — '.$desc).'</option>';
        }
        $out .= '</optgroup>';
    }
    $menus = wp_get_nav_menus();
    if($menus) {
        $out .= '<optgroup label="Specifieke menu’s">';
        foreach($menus as $m) {
            $val='menu:'.$m->term_id;
            $out .= '<option value="'.esc_attr($val).'" '.selected($cur,$val,false).'>'.esc_html($m->name.' (ID '.$m->term_id.')').'</option>';
        }
        $out .= '</optgroup>';
    }
    $out .= '</select>';
    return $out;
}

/**
 * Typografie laag renderer (volledige originele functionaliteit)
 */
function adremm_mmp_render_layer_row($key, $title, $layer) {
    $modes = array('theme'=>'Thema','google'=>'Google','custom'=>'Custom');
    $units = adremm_mmp_units();
    $ul = array('none'=>'Geen','solid'=>'Solid','dashed'=>'Dashed','dotted'=>'Dotted','double'=>'Double');
    ?>
    <div class="adremm-layer">
        <h3><?php echo esc_html($title); ?></h3>
        <div class="adremm-row">
            <label>Font modus</label>
            <select class="adremm-mode" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][mode]">
                <?php foreach($modes as $k=>$v): ?>
                <option value="<?php echo esc_attr($k); ?>" <?php selected($layer['mode'],$k); ?>><?php echo esc_html($v); ?></option>
                <?php endforeach; ?>
            </select>
            <label>Family</label>
            <select class="adremm-google" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][family]" <?php echo ($layer['mode']==='google')?'':'disabled'; ?>>
                <?php foreach(adremm_mmp_google_fonts() as $f): ?>
                <option value="<?php echo esc_attr($f); ?>" <?php selected($layer['family'],$f); ?>><?php echo esc_html($f); ?></option>
                <?php endforeach; ?>
            </select>
            <input class="adremm-custom" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][family]" value="<?php echo esc_attr($layer['family']); ?>" <?php echo ($layer['mode']==='custom')?'':'disabled'; ?> placeholder="bijv: Outfit, system-ui, sans-serif">
        </div>
        <div class="adremm-row">
            <label>Grootte</label>
            <input class="adremm-num" type="number" min="8" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][size]" value="<?php echo esc_attr($layer['size']); ?>">
            <?php echo adremm_mmp_unit_select('layers['.$key.'][unit]', $layer['unit'], $units); ?>
            <label>Gewicht</label>
            <input class="adremm-num" type="number" min="100" step="100" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][weight]" value="<?php echo esc_attr($layer['weight']); ?>">
            <label>Line</label>
            <input class="adremm-num" type="number" min="0.8" step="0.1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][line]" value="<?php echo esc_attr($layer['line']); ?>">
        </div>
        <div class="adremm-row">
            <label>Kleur</label>
            <input class="adremm-color adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][color]" value="<?php echo esc_attr($layer['color']); ?>">
            <label>Hover</label>
            <input class="adremm-color adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][hover]" value="<?php echo esc_attr($layer['hover']); ?>" placeholder="leeg = kleur">
            <label>Click</label>
            <input class="adremm-color adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][active]" value="<?php echo esc_attr($layer['active']); ?>" placeholder="leeg = kleur">
        </div>
        <div class="adremm-row">
            <label>Padding Y</label>
            <input class="adremm-num" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][pad_y]" value="<?php echo esc_attr($layer['pad_y']); ?>"> px
            <label>Padding X</label>
            <input class="adremm-num" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][pad_x]" value="<?php echo esc_attr($layer['pad_x']); ?>"> px
            <label>Gap</label>
            <input class="adremm-num" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][gap]" value="<?php echo esc_attr($layer['gap']); ?>"> px
        </div>
        <div class="adremm-row">
            <label>Underline</label>
            <select class="adremm-ul" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][underline]">
                <?php foreach($ul as $u=>$t): ?><option value="<?php echo esc_attr($u); ?>" <?php selected($layer['underline'],$u); ?>><?php echo esc_html($t); ?></option><?php endforeach; ?>
            </select>
            <label>Dikte</label>
            <input class="adremm-num" type="number" min="1" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][ul_thick]" value="<?php echo esc_attr($layer['ul_thick']); ?>"> px
            <label>Afstand</label>
            <input class="adremm-num" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][ul_offset]" value="<?php echo esc_attr($layer['ul_offset']); ?>"> px
        </div>
    </div>
    <?php
}

/**
 * Admin interface met verbeterde UI + alle originele functionaliteit
 */
function adremm_mmp_render_admin() {
    if ( ! current_user_can('manage_options') ) return;
    $o = adremm_mmp_get_options();
    $units = adremm_mmp_units();
    ?>
    <div class="wrap adremm-mmp-wrap">
        <h1>ADREMM Mobile Menu PRO <small>v<?php echo ADREMM_MMP_VERSION; ?></small></h1>
        
        <?php settings_errors(ADREMM_MMP_OPT); ?>
        
        <form method="post" action="options.php" id="adremm-pro-form">
            <?php settings_fields(ADREMM_MMP_OPT); ?>
            
            <div class="adremm-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#tab-general" class="nav-tab nav-tab-active">Algemeen</a>
                    <a href="#tab-behavior" class="nav-tab">Gedrag</a>
                    <a href="#tab-typo" class="nav-tab">Typografie</a>
                    <a href="#tab-socials" class="nav-tab">Socials</a>
                    <a href="#tab-credits" class="nav-tab">Credits</a>
                    <a href="#tab-colors" class="nav-tab">Kleuren</a>
                </nav>

                <div class="tab-content">
                    <!-- ALGEMEEN -->
                    <div id="tab-general" class="tab-pane active">
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row">Menu kiezen</th>
                                <td><?php echo adremm_mmp_menu_select_html($o['menu_src']); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Menu label</th>
                                <td><input class="regular-text" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[label]" value="<?php echo esc_attr($o['label']); ?>"></td>
                            </tr>
                            <tr>
                                <th scope="row">Toggle uitlijning</th>
                                <td>
                                    <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_align]">
                                        <option value="left" <?php selected($o['toggle_align'],'left'); ?>>Links</option>
                                        <option value="center" <?php selected($o['toggle_align'],'center'); ?>>Midden</option>
                                        <option value="right" <?php selected($o['toggle_align'],'right'); ?>>Rechts</option>
                                    </select>
                                    <label class="adremm-inline">Fixed</label>
                                    <select class="adremm-w90" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_fixed]">
                                        <option value="0" <?php selected($o['toggle_fixed'],'0'); ?>>Nee</option>
                                        <option value="1" <?php selected($o['toggle_fixed'],'1'); ?>>Ja</option>
                                    </select>
                                    <label class="adremm-inline">Top</label>
                                    <input class="adremm-w90" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_top]" value="<?php echo esc_attr($o['toggle_top']); ?>">
                                    <?php echo adremm_mmp_unit_select('toggle_top_unit', $o['toggle_top_unit'], $units); ?>
                                    <label class="adremm-inline">Zij</label>
                                    <input class="adremm-w90" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_side]" value="<?php echo esc_attr($o['toggle_side']); ?>">
                                    <?php echo adremm_mmp_unit_select('toggle_side_unit', $o['toggle_side_unit'], $units); ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Toggle achtergrond</th>
                                <td><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_bg]" value="<?php echo esc_attr($o['toggle_bg']); ?>"></td>
                            </tr>
                            <tr>
                                <th scope="row">Breakpoint</th>
                                <td>
                                    <input class="adremm-w150" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[breakpoint]" value="<?php echo esc_attr($o['breakpoint']); ?>">
                                    <?php echo adremm_mmp_unit_select('breakpoint_unit', $o['breakpoint_unit'], $units); ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Z-index</th>
                                <td>
                                    <input class="adremm-w150" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[z_index]" value="<?php echo esc_attr($o['z_index']); ?>">
                                    <p class="description">Standaard: 2147483647. Verhoog dit als het menu onder de Divi Header verdwijnt.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Google Fonts API Key</th>
                                <td>
                                    <input class="regular-text" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[google_fonts_api_key]" value="<?php echo esc_attr($o['google_fonts_api_key']); ?>" placeholder="AIza...">
                                    <p class="description">Vul hier je Google Fonts API key in voor de actuele fontlijst. <a href="https://console.cloud.google.com/apis/library/webfonts.googleapis.com" target="_blank">API activeren</a></p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- GEDRAG -->
                    <div id="tab-behavior" class="tab-pane">
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row">Drawer</th>
                                <td>
                                    <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[drawer]">
                                        <option value="left" <?php selected($o['drawer'],'left'); ?>>Left</option>
                                        <option value="right" <?php selected($o['drawer'],'right'); ?>>Right</option>
                                        <option value="fullscreen" <?php selected($o['drawer'],'fullscreen'); ?>>Fullscreen</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Easing</th>
                                <td>
                                    <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[easing]">
                                        <option value="ease" <?php selected($o['easing'],'ease'); ?>>ease</option>
                                        <option value="ease-in" <?php selected($o['easing'],'ease-in'); ?>>ease-in</option>
                                        <option value="ease-out" <?php selected($o['easing'],'ease-out'); ?>>ease-out</option>
                                        <option value="ease-in-out" <?php selected($o['easing'],'ease-in-out'); ?>>ease-in-out</option>
                                        <option value="linear" <?php selected($o['easing'],'linear'); ?>>linear</option>
                                        <option value="cubic-bezier(0.16, 1, 0.3, 1)" <?php selected($o['easing'],'cubic-bezier(0.16, 1, 0.3, 1)'); ?>>Smooth (aanbevolen)</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Hamburger animatie</th>
                                <td>
                                    <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_anim]">
                                        <option value="swap" <?php selected($o['burger_anim'],'swap'); ?>>Swap (kruis)</option>
                                        <option value="squeeze" <?php selected($o['burger_anim'],'squeeze'); ?>>Squeeze</option>
                                        <option value="morph" <?php selected($o['burger_anim'],'morph'); ?>>Morph</option>
                                        <option value="spin" <?php selected($o['burger_anim'],'spin'); ?>>Spin</option>
                                        <option value="arrow" <?php selected($o['burger_anim'],'arrow'); ?>>Arrow</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Submenu indicator</th>
                                <td>
                                    <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[submenu_icon]">
                                        <option value="chevron" <?php selected($o['submenu_icon'],'chevron'); ?>>Chevron</option>
                                        <option value="caret" <?php selected($o['submenu_icon'],'caret'); ?>>Caret</option>
                                        <option value="plus" <?php selected($o['submenu_icon'],'plus'); ?>>Plus</option>
                                        <option value="custom" <?php selected($o['submenu_icon'],'custom'); ?>>Eigen (URL)</option>
                                    </select>
                                    <label class="adremm-inline">URL</label>
                                    <input id="adremm_submenu_icon_url" class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[submenu_icon_url]" value="<?php echo esc_attr($o['submenu_icon_url']); ?>" placeholder="https://.../icon.svg">
                                    <button type="button" class="button adremm-inline adremm-media-btn" data-target="adremm_submenu_icon_url">Kies</button>
                                    <label class="adremm-inline">Size</label>
                                    <input class="adremm-w90" type="number" min="6" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[submenu_icon_size]" value="<?php echo esc_attr($o['submenu_icon_size']); ?>">
                                    <?php echo adremm_mmp_unit_select('submenu_icon_unit', $o['submenu_icon_unit'], $units); ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Terug knop</th>
                                <td>
                                    <input class="adremm-w150" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_label]" value="<?php echo esc_attr($o['back_label']); ?>">
                                    <label class="adremm-inline">Positie</label>
                                    <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_position]">
                                        <option value="header" <?php selected($o['back_position'],'header'); ?>>Header</option>
                                        <option value="footer" <?php selected($o['back_position'],'footer'); ?>>Footer</option>
                                        <option value="replace_close" <?php selected($o['back_position'],'replace_close'); ?>>I.p.v. sluit</option>
                                    </select>
                                    <label class="adremm-inline">Icon</label>
                                    <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_icon]">
                                        <option value="chevron" <?php selected($o['back_icon'],'chevron'); ?>>Chevron</option>
                                        <option value="caret" <?php selected($o['back_icon'],'caret'); ?>>Caret</option>
                                        <option value="plus" <?php selected($o['back_icon'],'plus'); ?>>Plus</option>
                                        <option value="custom" <?php selected($o['back_icon'],'custom'); ?>>Eigen (URL)</option>
                                    </select>
                                    <label class="adremm-inline">URL</label>
                                    <input id="adremm_back_icon_url" class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_icon_url]" value="<?php echo esc_attr($o['back_icon_url']); ?>" placeholder="https://.../icon.svg">
                                    <button type="button" class="button adremm-inline adremm-media-btn" data-target="adremm_back_icon_url">Kies</button>
                                    <label class="adremm-inline">Size</label>
                                    <input class="adremm-w90" type="number" min="6" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_icon_size]" value="<?php echo esc_attr($o['back_icon_size']); ?>">
                                    <?php echo adremm_mmp_unit_select('back_icon_unit', $o['back_icon_unit'], $units); ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Sluitkruis</th>
                                <td>
                                    <label class="adremm-inline">Tonen</label>
                                    <select class="adremm-w90" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[show_close]">
                                        <option value="1" <?php selected($o['show_close'],'1'); ?>>Ja</option>
                                        <option value="0" <?php selected($o['show_close'],'0'); ?>>Nee</option>
                                    </select>
                                    <label class="adremm-inline">Size</label>
                                    <input class="adremm-w90" type="number" min="10" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[close_size]" value="<?php echo esc_attr($o['close_size']); ?>">
                                    <?php echo adremm_mmp_unit_select('close_unit', $o['close_unit'], $units); ?>
                                    <label class="adremm-inline">Kleur</label>
                                    <input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[close_color]" value="<?php echo esc_attr($o['close_color']); ?>">
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- TYPOGRAFIE -->
                    <div id="tab-typo" class="tab-pane">
                        <div class="adremm-row" style="margin-bottom:14px;">
                            <label>Globaal font</label>
                            <input class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[global_font]" value="<?php echo esc_attr($o['global_font']); ?>" placeholder="inherit of bv. geom-graphic, sans-serif">
                            <span class="adremm-help-inline">Basis font-family voor het menu. Per-laag op 'Thema' gebruikt dit als fallback.</span>
                        </div>
                        <h2>Typografie per laag</h2>
                        <?php
                        adremm_mmp_render_layer_row('menu_label','Menu label',$o['layers']['menu_label']);
                        adremm_mmp_render_layer_row('items','Navigatie items',$o['layers']['items']);
                        adremm_mmp_render_layer_row('subitems','Subnav',$o['layers']['subitems']);
                        adremm_mmp_render_layer_row('back','Terug knop',$o['layers']['back']);
                        ?>
                    </div>

                    <!-- SOCIALS -->
                    <div id="tab-socials" class="tab-pane">
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row">Socials</th>
                                <td>
                                    <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials_on]">
                                        <option value="0" <?php selected($o['socials_on'],'0'); ?>>Uit</option>
                                        <option value="1" <?php selected($o['socials_on'],'1'); ?>>Aan</option>
                                    </select>
                                    <label class="adremm-inline">Icon size</label>
                                    <input class="adremm-w90" type="number" min="12" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials_size]" value="<?php echo esc_attr($o['socials_size']); ?>">
                                    <?php echo adremm_mmp_unit_select('socials_unit', $o['socials_unit'], $units); ?>
                                </td>
                            </tr>
                        </table>
                        <div class="adremm-social-grid">
                            <?php foreach(adremm_mmp_social_platforms() as $k=>$p): $row = $o['socials'][$k] ?? array('on'=>'0','url'=>'','custom_icon_url'=>''); ?>
                            <div class="adremm-social-row">
                                <label class="adremm-check">
                                    <input type="checkbox" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials][<?php echo esc_attr($k); ?>][on]" value="1" <?php checked($row['on'],'1'); ?>>
                                    <?php echo esc_html($p['label']); ?>
                                </label>
                                <input class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials][<?php echo esc_attr($k); ?>][url]" value="<?php echo esc_attr($row['url']); ?>" placeholder="https://...">
                                <input id="adremm_social_<?php echo esc_attr($k); ?>_icon" class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials][<?php echo esc_attr($k); ?>][custom_icon_url]" value="<?php echo esc_attr($row['custom_icon_url']); ?>" placeholder="Custom icon (URL)">
                                <button type="button" class="button adremm-media-btn" data-target="adremm_social_<?php echo esc_attr($k); ?>_icon">Kies</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- CREDITS -->
                    <div id="tab-credits" class="tab-pane">
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row">Credits</th>
                                <td>
                                    <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_on]">
                                        <option value="0" <?php selected($o['credits_on'],'0'); ?>>Uit</option>
                                        <option value="1" <?php selected($o['credits_on'],'1'); ?>>Aan</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Tekst</th>
                                <td><input class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_text]" value="<?php echo esc_attr($o['credits_text']); ?>"></td>
                            </tr>
                            <tr>
                                <th scope="row">URL</th>
                                <td><input class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_url]" value="<?php echo esc_attr($o['credits_url']); ?>"></td>
                            </tr>
                            <tr>
                                <th scope="row">Icon URL</th>
                                <td>
                                    <input id="adremm_credits_icon_url" class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_icon_url]" value="<?php echo esc_attr($o['credits_icon_url']); ?>" placeholder="https://.../icon.svg (optioneel)">
                                    <button type="button" class="button adremm-inline adremm-media-btn" data-target="adremm_credits_icon_url">Kies</button>
                                    <label class="adremm-inline">Size</label>
                                    <input class="adremm-w90" type="number" min="12" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_size]" value="<?php echo esc_attr($o['credits_size']); ?>">
                                    <?php echo adremm_mmp_unit_select('credits_unit', $o['credits_unit'], $units); ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- KLEUREN -->
                    <div id="tab-colors" class="tab-pane">
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row">Panel achtergrond</th>
                                <td><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[panel_bg]" value="<?php echo esc_attr($o['panel_bg']); ?>"></td>
                            </tr>
                            <tr>
                                <th scope="row">Panel randkleur</th>
                                <td><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[panel_border]" value="<?php echo esc_attr($o['panel_border']); ?>"></td>
                            </tr>
                            <tr>
                                <th scope="row">Randbreedte (px)</th>
                                <td>
                                    <input class="adremm-w90" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[panel_border_width]" value="<?php echo esc_attr($o['panel_border_width']); ?>"> px (0 = geen rand)
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Overlay</th>
                                <td><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[overlay]" value="<?php echo esc_attr($o['overlay']); ?>"></td>
                            </tr>
                            <tr>
                                <th scope="row">Burger kleur dicht</th>
                                <td><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_off]" value="<?php echo esc_attr($o['burger_off']); ?>"></td>
                            </tr>
                            <tr>
                                <th scope="row">Burger kleur open</th>
                                <td><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_on]" value="<?php echo esc_attr($o['burger_on']); ?>"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary button-large" value="Wijzigingen opslaan">
            </p>
        </form>
        
        <div class="adremm-footer">
            <span>ADREMM Mobile Menu PRO v<?php echo esc_html(ADREMM_MMP_VERSION); ?></span>
            <a class="button button-secondary adremm-support" href="https://adremm.nl" target="_blank" rel="noopener noreferrer">Support</a>
            <span><?php echo esc_html(date('Y')); ?> ADREMM</span>
        </div>
    </div>

    <style>
        .adremm-mmp-wrap { max-width: 1100px; margin-top: 20px; }
        .adremm-mmp-wrap h1 { background: linear-gradient(135deg, #111 0%, #222 100%); color: #fff; padding: 25px 30px; margin: 0 0 20px 0; border-radius: 8px 8px 0 0; box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
        .adremm-mmp-wrap h1 small { font-size: 16px; font-weight: 300; opacity: 0.8; margin-left: 10px; }
        
        .nav-tab-wrapper {
            background: #f6f7f7;
            border-bottom: 1px solid #ccd0d4;
            padding: 0 20px;
            border-radius: 8px 8px 0 0;
        }
        .nav-tab {
            padding: 12px 20px;
            margin: 0 5px;
            font-weight: 500;
            border: none;
            border-radius: 6px 6px 0 0 !important;
            background: transparent;
            color: #646970;
            transition: all 0.2s;
        }
        .nav-tab:hover { background: rgba(0,0,0,0.03); color: #111; }
        .nav-tab.nav-tab-active { background: #fff !important; color: #111 !important; box-shadow: 0 -2px 0 #2271b1 inset; }
        
        .tab-content { background: #fff; padding: 30px; border: 1px solid #ccd0d4; border-top: none; border-radius: 0 0 8px 8px; box-shadow: 0 1px 5px rgba(0,0,0,0.05); }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }
        
        .form-table { margin-top: 0; }
        .form-table th { width: 220px; padding: 16px 10px 16px 0; font-weight: 500; color: #1d2327; }
        .form-table td { padding: 15px 10px; vertical-align: top; }
        .form-table input[type="text"], .form-table input[type="number"], .form-table select { max-width: 400px; }
        .form-table .regular-text { width: 100%; max-width: 400px; }
        .description { color: #646970; font-size: 13px; margin-top: 4px; line-height: 1.4; }
        
        .adremm-inline { margin-left: 10px; margin-right: 8px; display: inline-block; }
        .adremm-w90 { width: 90px; }
        .adremm-w150 { width: 150px; }
        .adremm-w250 { width: 250px; }
        .adremm-unit { width: 70px; margin-left: 6px; }
        
        .adremm-layer { border-top: 1px solid #e5e5e5; padding-top: 10px; margin-top: 10px; }
        .adremm-row { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin: 8px 0; }
        .adremm-row label { width: 150px; }
        .adremm-row select, .adremm-row input { height: 34px; }
        .adremm-num { width: 150px; }
        .adremm-color { width: 250px; }
        .adremm-google, .adremm-custom { width: 250px; }
        
        .adremm-social-grid { display: grid; grid-template-columns: 1fr; gap: 8px; margin-top: 15px; }
        .adremm-social-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; border: 1px solid #e5e5e5; padding: 8px; border-radius: 4px; }
        .adremm-check { min-width: 160px; font-weight: 500; }
        
        .adremm-footer { display: flex; gap: 12px; align-items: center; margin-top: 25px; padding: 15px; background: #f8f9f9; border-radius: 8px; }
        .adremm-support { background: #222 !important; color: #fff !important; border-color: #222 !important; }
        
        @media (max-width: 782px) {
            .form-table th { width: auto; display: block; padding-bottom: 8px; }
            .form-table td { display: block; padding-top: 8px; }
            .nav-tab { padding: 10px 15px; font-size: 14px; }
            .adremm-row { flex-direction: column; align-items: flex-start; }
            .adremm-row label { width: auto; margin-bottom: 5px; }
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Tab navigation
        $('.nav-tab').click(function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            $('.tab-pane').removeClass('active');
            $(target).addClass('active');
        });
        
        // Layer mode switching
        $('.adremm-mode').on('change', function() {
            var $row = $(this).closest('.adremm-layer');
            var mode = $(this).val();
            $row.find('.adremm-google').prop('disabled', mode !== 'google');
            $row.find('.adremm-custom').prop('disabled', mode !== 'custom');
        });
        
        // Form submit feedback
        $('#adremm-pro-form').on('submit', function() {
            $('#submit').prop('disabled', true).val('Opslaan...');
        });
        
        // Media buttons
        $(document).on('click', '.adremm-media-btn', function(e) {
            e.preventDefault();
            var targetId = $(this).data('target');
            var targetInput = $('#' + targetId);
            
            if (typeof wp === 'undefined' || !wp.media) return;
            
            var frame = wp.media({
                title: 'Kies media',
                button: { text: 'Gebruik deze' },
                multiple: false
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                if (attachment && attachment.url) {
                    targetInput.val(attachment.url).trigger('change');
                }
            });
            
            frame.open();
        });
        
        // RGBA color pickers (basic fallback)
        $('.adremm-rgba').wpColorPicker();
    });
    </script>
    <?php
}

/**
 * Admin assets
 */
add_action('admin_enqueue_scripts', function($hook) {
    if ('toplevel_page_' . ADREMM_MMP_SLUG !== $hook) return;
    
    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
});

/**
 * Frontend output
 */
add_action('wp_footer', function() {
    if (is_admin()) return;
    
    $o = adremm_mmp_get_options();
    $args = array(
        'theme_location' => 'primary',
        'container' => false,
        'echo' => false,
        'fallback_cb' => false,
        'depth' => 0
    );
    
    if (strpos($o['menu_src'], 'location:') === 0) {
        $args = array('theme_location' => substr($o['menu_src'], 9), 'container' => false, 'fallback_cb' => false, 'echo' => false);
    } elseif (strpos($o['menu_src'], 'menu:') === 0) {
        $args = array('menu' => intval(substr($o['menu_src'], 5)), 'container' => false, 'fallback_cb' => false, 'echo' => false);
    }
    
    $menu_html = wp_nav_menu($args);
    if (!$menu_html) {
        $menus = get_nav_menu_locations();
        if (isset($menus['primary'])) {
            $menu_html = wp_nav_menu(array('theme_location' => 'primary','container'=>false,'fallback_cb'=>false,'echo'=>false));
        }
        if (!$menu_html) {
            $first = wp_get_nav_menus();
            if ($first && !empty($first)) {
                $menu_html = wp_nav_menu(array('menu' => $first[0]->term_id,'container'=>false,'fallback_cb'=>false,'echo'=>false));
            }
        }
    }
    if (!$menu_html) {
        $menu_html = '<ul class="menu"><li><a href="#">(Nog geen menu ingesteld)</a></li></ul>';
    }
    
    // CSS vars
    $css_vars = "
        --mm-base-ff: {$o['global_font']};
        --mm-panel-bg: {$o['panel_bg']};
        --mm-panel-border: {$o['panel_border']};
        --mm-border-width: {$o['panel_border_width']}px;
        --mm-overlay: {$o['overlay']};
        --mm-burger-off: {$o['burger_off']};
        --mm-burger-on: {$o['burger_on']};
        --mm-ease: {$o['easing']};
        --mm-z: {$o['z_index']};
        --mm-global-ff: {$o['global_font']};
        --mm-ind-size: {$o['submenu_icon_size']}{$o['submenu_icon_unit']};
        --mm-back-ind-size: {$o['back_icon_size']}{$o['back_icon_unit']};
        --mm-anim: {$o['burger_anim']};
        --mm-panel-w: {$o['panel_w']};
        --mm-panel-max: {$o['panel_max']};
        --mm-fs-nav-max: {$o['fs_nav_max']};
        --mm-close-size: {$o['close_size']}{$o['close_unit']};
        --mm-close-color: {$o['close_color']};
        --mm-toggle-top: {$o['toggle_top']}{$o['toggle_top_unit']};
        --mm-toggle-side: {$o['toggle_side']}{$o['toggle_side_unit']};
        --mm-toggle-bg: {$o['toggle_bg']};
        --mm-social-size: {$o['socials_size']}{$o['socials_unit']};
        --mm-credits-size: {$o['credits_size']}{$o['credits_unit']};
    ";
    
    // Layer CSS vars
    $layer_vars = function($layer, $prefix) {
        $mode = $layer['mode'] ?? 'theme';
        $family = $layer['family'] ?? 'inherit';
        if($mode==='theme') $family = 'var(--mm-global-ff, inherit)';
        elseif($mode==='google') $family = $family ? ($family.', system-ui, sans-serif') : 'inherit';
        else $family = $family ? $family : 'inherit';
        
        $color = $layer['color'] ?? 'rgba(17,17,17,1)';
        $hover = $layer['hover'] ?? $color;
        $active= $layer['active'] ?? $color;
        
        return "
            --mm-{$prefix}-ff: {$family};
            --mm-{$prefix}-fs: {$layer['size']}{$layer['unit']};
            --mm-{$prefix}-fw: {$layer['weight']};
            --mm-{$prefix}-lh: {$layer['line']};
            --mm-{$prefix}-c: {$color};
            --mm-{$prefix}-ch: {$hover};
            --mm-{$prefix}-ca: {$active};
            --mm-{$prefix}-py: {$layer['pad_y']}px;
            --mm-{$prefix}-px: {$layer['pad_x']}px;
            --mm-{$prefix}-gap: {$layer['gap']}px;
            --mm-{$prefix}-ul: {$layer['underline']};
            --mm-{$prefix}-ult: {$layer['ul_thick']}px;
            --mm-{$prefix}-ulo: {$layer['ul_offset']}px;
        ";
    };
    
    $css_vars .= $layer_vars($o['layers']['menu_label'], 'label');
    $css_vars .= $layer_vars($o['layers']['items'], 'item');
    $css_vars .= $layer_vars($o['layers']['subitems'], 'sub');
    $css_vars .= $layer_vars($o['layers']['back'], 'back');
    ?>
    <!-- ADREMM Mobile Menu PRO v<?php echo ADREMM_MMP_VERSION; ?> - Cache Key: <?php echo ADREMM_MMP_CACHE_BUSTER; ?> -->
    <div class="adremm-mm adremm-mm--drawer-<?php echo esc_attr($o['drawer']); ?> adremm-mm--back-<?php echo esc_attr($o['back_position']); ?> adremm-mm--align-<?php echo esc_attr($o['toggle_align']); ?> <?php echo ($o['toggle_fixed']==='1')?'adremm-mm--fixed':''; ?>"
         data-subicon="<?php echo esc_attr($o['submenu_icon']); ?>"
         data-backicon="<?php echo esc_attr($o['back_icon']); ?>"
         data-showclose="<?php echo esc_attr($o['show_close']); ?>"
         data-socials="<?php echo esc_attr($o['socials_on']); ?>"
         data-credits="<?php echo esc_attr($o['credits_on']); ?>"
         style="<?php echo $css_vars; ?>">
        <div class="adremm-mm__toggle-wrap">
            <button class="adremm-mm__toggle" aria-expanded="false" type="button">
                <span class="adremm-mm__burger" aria-hidden="true"><i></i><i></i><i></i></span>
                <span class="adremm-mm__label"><?php echo esc_html($o['label']); ?></span>
            </button>
        </div>
        <div class="adremm-mm__overlay" hidden></div>
        <div class="adremm-mm__panel" hidden>
            <div class="adremm-mm__header">
                <button class="adremm-mm__back adremm-mm__back--header" type="button" hidden>
                    <span class="adremm-mm__backicon" aria-hidden="true"></span>
                    <span class="adremm-mm__backtext"><?php echo esc_html($o['back_label']); ?></span>
                </button>
                <button class="adremm-mm__close" type="button" aria-label="Sluit menu">
                    <span class="adremm-mm__cross" aria-hidden="true"></span>
                </button>
            </div>
            <nav class="adremm-mm__nav">
                <div class="adremm-mm__views">
                    <div class="adremm-mm__view is-root"><?php echo $menu_html; ?></div>
                </div>
            </nav>
            <div class="adremm-mm__bottom">
                <button class="adremm-mm__back adremm-mm__back--footer" type="button" hidden>
                    <span class="adremm-mm__backicon" aria-hidden="true"></span>
                    <span class="adremm-mm__backtext"><?php echo esc_html($o['back_label']); ?></span>
                </button>
                <div class="adremm-mm__socials" hidden></div>
                <div class="adremm-mm__credits" hidden></div>
            </div>
        </div>
    </div>

    <style id="adremm-mmp-styles-<?php echo sanitize_html_class(ADREMM_MMP_CACHE_BUSTER); ?>">
        .adremm-mm { position: relative; z-index: var(--mm-z, 999999) !important; }
        .adremm-mm * { box-sizing: border-box; z-index: var(--mm-z, 999999) !important; }
        .adremm-mm ul, .adremm-mm li { list-style: none; margin: 0; padding: 0; }
        .adremm-mm a { text-decoration: none; }
        
        .adremm-mm__toggle-wrap {
            display: flex;
            justify-content: flex-end;
            position: relative;
            z-index: calc(var(--mm-z, 999999) + 3) !important;
        }
        .adremm-mm--align-left .adremm-mm__toggle-wrap { justify-content: flex-start; }
        .adremm-mm--align-center .adremm-mm__toggle-wrap { justify-content: center; }
        .adremm-mm--fixed .adremm-mm__toggle-wrap {
            position: fixed;
            top: var(--mm-toggle-top, 20px);
            right: var(--mm-toggle-side, 20px);
            left: auto;
            z-index: calc(var(--mm-z, 999999) + 50) !important;
        }
        .adremm-mm--fixed.adremm-mm--align-left .adremm-mm__toggle-wrap {
            left: var(--mm-toggle-side, 20px);
            right: auto;
        }
        .adremm-mm--fixed.adremm-mm--align-center .adremm-mm__toggle-wrap {
            left: 50%;
            right: auto;
            transform: translateX(-50%);
        }
        
        .adremm-mm__toggle {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--mm-panel-border);
            background: var(--mm-toggle-bg);
            padding: var(--mm-label-py, 10px) var(--mm-label-px, 12px);
            cursor: pointer;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        .adremm-mm__toggle:hover {
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            transform: translateY(-1px);
        }
        
        .adremm-mm__label {
            font-family: var(--mm-label-ff);
            font-size: var(--mm-label-fs);
            font-weight: var(--mm-label-fw);
            line-height: var(--mm-label-lh);
            color: var(--mm-label-c);
        }
        
        .adremm-mm__burger {
            position: relative;
            width: 22px;
            height: 16px;
            display: inline-block;
        }
        .adremm-mm__burger i {
            position: absolute;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--mm-burger-off);
            transition: transform 0.28s var(--mm-ease), background 0.28s var(--mm-ease), top 0.28s var(--mm-ease), opacity 0.28s var(--mm-ease);
            display: block;
        }
        .adremm-mm__burger i:first-child { top: 3px; }
        .adremm-mm__burger i:nth-child(2) { top: 8px; }
        .adremm-mm__burger i:last-child { bottom: 3px; }
        
        .adremm-mm__overlay {
            position: fixed;
            inset: 0;
            background: var(--mm-overlay);
            opacity: 0;
            transition: opacity 0.25s var(--mm-ease);
            z-index: calc(var(--mm-z, 999999) - 1) !important;
            pointer-events: none;
        }
        
        .adremm-mm__panel {
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            width: min(var(--mm-panel-w, 90vw), var(--mm-panel-max, 420px));
            background: var(--mm-panel-bg);
            border-left: var(--mm-border-width, 1px) solid var(--mm-panel-border);
            transform: translateX(105%);
            transition: transform 0.4s var(--mm-ease);
            z-index: var(--mm-z, 999999) !important;
            display: grid;
            grid-template-rows: auto 1fr auto;
            box-shadow: -10px 0 30px rgba(0,0,0,0.1);
        }
        .adremm-mm--drawer-left .adremm-mm__panel {
            left: 0;
            right: auto;
            border-left: none;
            border-right: var(--mm-border-width, 1px) solid var(--mm-panel-border);
            transform: translateX(-105%);
        }
        .adremm-mm--drawer-fullscreen .adremm-mm__panel {
            left: 0;
            right: 0;
            width: 100vw;
            transform: translateY(100%);
            border-left: none;
            border-right: none;
        }
        
        .adremm-mm.is-open .adremm-mm__overlay {
            opacity: 1;
            pointer-events: auto;
        }
        .adremm-mm.is-open .adremm-mm__panel {
            transform: translateX(0);
        }
        .adremm-mm--drawer-left.is-open .adremm-mm__panel {
            transform: translateX(0);
        }
        .adremm-mm--drawer-fullscreen.is-open .adremm-mm__panel {
            transform: translateY(0);
        }
        
        .adremm-mm__header {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: space-between;
            padding: 15px 20px;
            border-bottom: 1px solid var(--mm-panel-border);
            min-height: 70px;
        }
        .adremm-mm__close {
            background: none;
            border: none;
            font-size: 32px;
            cursor: pointer;
            color: var(--mm-close-color);
            line-height: 1;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        .adremm-mm__close:hover {
            transform: rotate(90deg);
        }
        .adremm-mm__cross {
            width: var(--mm-close-size, 16px);
            height: var(--mm-close-size, 16px);
            position: relative;
            display: block;
        }
        .adremm-mm__cross:before,
        .adremm-mm__cross:after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            height: 2px;
            background: currentColor;
            transform-origin: center;
        }
        .adremm-mm__cross:before { transform: translateY(-50%) rotate(45deg); }
        .adremm-mm__cross:after { transform: translateY(-50%) rotate(-45deg); }
        
        .adremm-mm__nav { overflow: hidden; position: relative; }
        .adremm-mm__views {
            display: flex;
            flex-direction: row;
            width: 100%;
            transition: transform 0.35s var(--mm-ease);
        }
        .adremm-mm__view {
            min-width: 100%;
            padding: 15px 20px;
            overflow-y: auto;
        }
        .adremm-mm__view ul {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .adremm-mm__view li.menu-item-has-children {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: nowrap;
            position: relative;
        }
        .adremm-mm__view li.menu-item-has-children > a {
            flex: 1;
            min-width: 0;
            justify-content: flex-start;
            padding-right: 40px !important;
        }
        .adremm-mm__view a {
            display: block;
            padding: var(--mm-item-py) var(--mm-item-px);
            font-family: var(--mm-item-ff);
            font-size: var(--mm-item-fs);
            font-weight: var(--mm-item-fw);
            line-height: var(--mm-item-lh);
            color: var(--mm-item-c);
            position: relative;
            transition: all 0.2s;
        }
        .adremm-mm__view a:hover {
            color: var(--mm-item-ch);
            transform: translateX(4px);
        }
        .adremm-mm__view a:active {
            color: var(--mm-item-ca);
        }
        .adremm-mm__view .sub-menu a {
            padding: var(--mm-sub-py) var(--mm-sub-px);
            font-family: var(--mm-sub-ff);
            font-size: var(--mm-sub-fs);
            font-weight: var(--mm-sub-fw);
            line-height: var(--mm-sub-lh);
            color: var(--mm-sub-c);
        }
        .adremm-mm__view .sub-menu a:hover {
            color: var(--mm-sub-ch);
        }
        .adremm-mm__view .sub-menu a:active {
            color: var(--mm-sub-ca);
        }
        
        .adremm-mm__view a:hover:after,
        .adremm-mm__view a.is-active:after {
            content: '';
            position: absolute;
            left: var(--mm-item-px);
            right: var(--mm-item-px);
            bottom: var(--mm-item-ulo);
            border-bottom: var(--mm-item-ult) var(--mm-item-ul) currentColor;
        }
        .adremm-mm__view .sub-menu a:hover:after,
        .adremm-mm__view .sub-menu a.is-active:after {
            content: '';
            position: absolute;
            left: var(--mm-sub-px);
            right: var(--mm-sub-px);
            bottom: var(--mm-sub-ulo);
            border-bottom: var(--mm-sub-ult) var(--mm-sub-ul) currentColor;
        }
        
        .adremm-mm__next {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 50px;
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            opacity: 0.5;
            transition: opacity 0.2s;
        }
        .adremm-mm__next:hover {
            opacity: 1;
            background: rgba(0,0,0,0.02);
        }
        .adremm-mm__ind {
            width: var(--mm-ind-size, 14px);
            height: var(--mm-ind-size, 14px);
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.9;
        }
        
        .adremm-mm__back {
            background: none;
            border: 1px solid var(--mm-panel-border);
            color: var(--mm-back-c);
            padding: var(--mm-back-py, 10px) var(--mm-back-px, 12px);
            cursor: pointer;
            display: inline-flex;
            gap: 10px;
            align-items: center;
            font-family: var(--mm-back-ff);
            font-size: var(--mm-back-fs);
            font-weight: var(--mm-back-fw);
            line-height: var(--mm-back-lh);
            border-radius: 4px;
            transition: all 0.2s;
        }
        .adremm-mm__back:hover {
            color: var(--mm-back-ch);
            background: rgba(0,0,0,0.02);
        }
        .adremm-mm__back:active {
            color: var(--mm-back-ca);
        }
        .adremm-mm__back:hover:after,
        .adremm-mm__back.is-active:after {
            content: '';
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: var(--mm-back-ulo);
            border-bottom: var(--mm-back-ult) var(--mm-back-ul) currentColor;
        }
        
        .adremm-mm__backicon {
            width: var(--mm-back-ind-size, 14px);
            height: var(--mm-back-ind-size, 14px);
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.9;
        }
        
        .adremm-mm__back--header,
        .adremm-mm__back--footer { display: none; }
        .adremm-mm--in-sub.adremm-mm--back-header .adremm-mm__back--header { display: inline-flex; }
        .adremm-mm--in-sub.adremm-mm--back-footer .adremm-mm__back--footer { display: inline-flex; }
        .adremm-mm--in-sub.adremm-mm--back-replace_close .adremm-mm__back--header { display: inline-flex; }
        .adremm-mm--back-replace_close .adremm-mm__close { display: none; }
        .adremm-mm[data-showclose='0'] .adremm-mm__close { display: none; }
        
        .adremm-mm__bottom {
            border-top: 1px solid var(--mm-panel-border);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: rgba(0,0,0,0.02);
        }
        .adremm-mm__socials {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: flex-start;
        }
        .adremm-mm__socials a {
            width: var(--mm-social-size);
            height: var(--mm-social-size);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--mm-item-c);
            opacity: 0.85;
            transition: all 0.2s;
        }
        .adremm-mm__socials a:hover {
            opacity: 1;
            transform: translateY(-2px);
        }
        .adremm-mm__socials svg,
        .adremm-mm__socials img {
            width: 100%;
            height: 100%;
        }
        .adremm-mm__credits a {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--mm-item-c);
            font-size: 12px;
            opacity: 0.6;
        }
        .adremm-mm__credits img,
        .adremm-mm__credits svg {
            width: var(--mm-credits-size);
            height: var(--mm-credits-size);
        }
        
        .adremm-mm.is-open .adremm-mm__burger i { background: var(--mm-burger-on); }
        .adremm-mm.is-open[style*='--mm-anim:swap'] .adremm-mm__burger i:first-child { top: 8px; transform: rotate(45deg); }
        .adremm-mm.is-open[style*='--mm-anim:swap'] .adremm-mm__burger i:nth-child(2) { opacity: 0; }
        .adremm-mm.is-open[style*='--mm-anim:swap'] .adremm-mm__burger i:last-child { top: 8px; transform: rotate(-45deg); }
        
        @media screen and (min-width: <?php echo esc_attr($o['breakpoint']); ?><?php echo esc_attr($o['breakpoint_unit']); ?>) {
            .adremm-mm { display: none !important; }
        }
        
        @media (max-width: 480px) {
            .adremm-mm__panel { width: 100%; max-width: 100%; }
            .adremm-mm--fixed .adremm-mm__toggle-wrap {
                top: 15px;
                right: 15px;
                left: auto;
            }
            .adremm-mm--fixed.adremm-mm--align-left .adremm-mm__toggle-wrap {
                left: 15px;
                right: auto;
            }
        }
    </style>

    <script id="adremm-mmp-script-<?php echo sanitize_html_class(ADREMM_MMP_CACHE_BUSTER); ?>">
    document.addEventListener('DOMContentLoaded', function() {
        var roots = document.querySelectorAll('.adremm-mm');
        if (!roots.length) return;
        
        roots.forEach(function(root) {
            if (root.dataset.adremmInit) return;
            root.dataset.adremmInit = '1';
            
            var toggle = root.querySelector('.adremm-mm__toggle');
            var overlay = root.querySelector('.adremm-mm__overlay');
            var closeBtn = root.querySelector('.adremm-mm__close');
            var panel = root.querySelector('.adremm-mm__panel');
            var views = root.querySelector('.adremm-mm__views');
            var backHeader = root.querySelector('.adremm-mm__back--header');
            var backFooter = root.querySelector('.adremm-mm__back--footer');
            var stack = [0];
            
            function openPanel() {
                root.classList.add('is-open');
                panel.hidden = false;
                overlay.hidden = false;
                document.body.style.overflow = 'hidden';
            }
            
            function closePanel() {
                root.classList.remove('is-open');
                panel.hidden = true;
                overlay.hidden = true;
                document.body.style.overflow = '';
                stack = [0];
                if (views) views.style.transform = 'translateX(0)';
                if (backHeader) backHeader.hidden = true;
                if (backFooter) backFooter.hidden = true;
                root.classList.remove('adremm-mm--in-sub');
            }
            
            if (toggle) toggle.addEventListener('click', function(e) {
                e.preventDefault();
                root.classList.contains('is-open') ? closePanel() : openPanel();
            });
            
            if (overlay) overlay.addEventListener('click', function(e) {
                e.preventDefault();
                closePanel();
            });
            
            if (closeBtn) closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closePanel();
            });
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && root.classList.contains('is-open')) {
                    closePanel();
                }
            });
            
            var rootView = root.querySelector('.adremm-mm__view.is-root');
            if (!rootView || !views) return;
            
            var subIconType = root.getAttribute('data-subicon') || 'chevron';
            var backIconType = root.getAttribute('data-backicon') || 'chevron';
            
            function svgData(name) {
                var svg = '';
                if (name === 'chevron') svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12"><path d="M4 2l4 4-4 4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                if (name === 'caret') svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12"><path fill="currentColor" d="M4 2l4 4-4 4"/></svg>';
                if (name === 'plus') svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12"><path fill="currentColor" d="M5 2h2v8H5zM2 5h8v2H2z"/></svg>';
                return 'url(data:image/svg+xml;utf8,' + encodeURIComponent(svg) + ')';
            }
            
            function setIndicatorBg(ind, type) {
                if (type === 'custom') {
                    var urlVar = getComputedStyle(root).getPropertyValue('--mm-icon-url') || 'none';
                    if (urlVar && urlVar !== 'none') {
                        ind.style.backgroundImage = urlVar;
                        return;
                    }
                }
                ind.style.backgroundImage = svgData(type);
            }
            
            root.querySelectorAll('.adremm-mm__backicon').forEach(function(ind) {
                var urlVar = getComputedStyle(root).getPropertyValue('--mm-back-icon-url') || 'none';
                if (backIconType === 'custom' && urlVar && urlVar !== 'none') {
                    ind.style.backgroundImage = urlVar;
                } else {
                    ind.style.backgroundImage = svgData(backIconType);
                }
            });
            
            function updateBack() {
                var show = (stack.length > 1);
                if (show) {
                    root.classList.add('adremm-mm--in-sub');
                } else {
                    root.classList.remove('adremm-mm--in-sub');
                }
                if (backHeader && (root.classList.contains('adremm-mm--back-header') || root.classList.contains('adremm-mm--back-replace_close'))) {
                    backHeader.hidden = !show;
                }
                if (backFooter && root.classList.contains('adremm-mm--back-footer')) {
                    backFooter.hidden = !show;
                }
            }
            
            function prepareView(viewEl) {
                if (!viewEl) return;
                
                viewEl.querySelectorAll('li').forEach(function(li) {
                    if (li.querySelector(':scope > ul')) li.classList.add('menu-item-has-children');
                });
                
                var items = Array.prototype.slice.call(viewEl.querySelectorAll('li.menu-item-has-children'));
                items.forEach(function(li) {
                    if (li.dataset.adremmPrep) return;
                    var submenu = li.querySelector(':scope > ul');
                    if (!submenu) return;
                    
                    var view = document.createElement('div');
                    view.className = 'adremm-mm__view';
                    view.appendChild(submenu.cloneNode(true));
                    views.appendChild(view);
                    var viewIndex = views.children.length - 1;
                    
                    li.dataset.adremmPrep = '1';
                    var a = li.querySelector(':scope > a');
                    if (a) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'adremm-mm__next';
                        var ind = document.createElement('span');
                        ind.className = 'adremm-mm__ind';
                        setIndicatorBg(ind, subIconType);
                        btn.appendChild(ind);
                        a.after(btn);
                        
                        btn.addEventListener('click', function(ev) {
                            ev.preventDefault();
                            ev.stopPropagation();
                            stack.push(viewIndex);
                            views.style.transform = 'translateX(-' + (viewIndex * 100) + '%)';
                            updateBack();
                            prepareView(view);
                        });
                    }
                });
            }
            
            prepareView(rootView);
            updateBack();
            
            function goBack() {
                if (stack.length > 1) {
                    stack.pop();
                    var t = stack[stack.length - 1] || 0;
                    views.style.transform = 'translateX(-' + (t * 100) + '%)';
                    updateBack();
                }
            }
            
            if (backHeader) backHeader.addEventListener('click', goBack);
            if (backFooter) backFooter.addEventListener('click', goBack);
            
            // Socials
            if (root.getAttribute('data-socials') === '1') {
                var socialWrap = root.querySelector('.adremm-mm__socials');
                if (socialWrap) {
                    var platforms = <?php echo json_encode(adremm_mmp_social_platforms()); ?>;
                    var socialsData = <?php echo json_encode($o['socials']); ?>;
                    var hasSocials = false;
                    
                    Object.keys(platforms).forEach(function(key) {
                        var item = socialsData[key] || {};
                        if (item.on === '1' && item.url) {
                            hasSocials = true;
                            var a = document.createElement('a');
                            a.href = item.url;
                            a.target = '_blank';
                            a.rel = 'noopener noreferrer';
                            a.setAttribute('aria-label', platforms[key].label);
                            
                            if (item.custom_icon_url) {
                                var img = document.createElement('img');
                                img.src = item.custom_icon_url;
                                img.alt = platforms[key].label;
                                a.appendChild(img);
                            } else {
                                a.innerHTML = platforms[key].svg;
                            }
                            
                            socialWrap.appendChild(a);
                        }
                    });
                    
                    if (hasSocials) {
                        socialWrap.hidden = false;
                    }
                }
            }
            
            // Credits
            if (root.getAttribute('data-credits') === '1' && <?php echo ($o['credits_on'] === '1') ? 'true' : 'false'; ?>) {
                var creditsWrap = root.querySelector('.adremm-mm__credits');
                if (creditsWrap) {
                    var a = document.createElement('a');
                    a.href = '<?php echo esc_url($o['credits_url']); ?>';
                    a.target = '_blank';
                    a.rel = 'noopener noreferrer';
                    
                    if ('<?php echo esc_url($o['credits_icon_url']); ?>') {
                        var img = document.createElement('img');
                        img.src = '<?php echo esc_url($o['credits_icon_url']); ?>';
                        img.alt = '<?php echo esc_attr($o['credits_text']); ?>';
                        a.appendChild(img);
                    }
                    
                    var span = document.createElement('span');
                    span.textContent = '<?php echo esc_html($o['credits_text']); ?>';
                    a.appendChild(span);
                    
                    creditsWrap.appendChild(a);
                    creditsWrap.hidden = false;
                }
            }
        });
    });
    </script>
    <?php
}, 9999999);