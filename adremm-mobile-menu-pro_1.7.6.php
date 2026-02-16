<?php
/**
 * Plugin Name: ADREMM Mobile Menu PRO
 * Description: Mobiele drawer/fullscreen navigatie met drilldown, per-laag typografie, socials/credits en RGBA colors.
 * Plugin URI: https://adremm.nl/developments
 * Version: 1.7.6
 * Author: ADREMM
 * Author URI: https://adremm.nl
 * License: GPLv2 or later
 * Text Domain: adremm-mobile-menu-pro
 */

if ( ! defined('ABSPATH') ) exit;

define('ADREMM_MMP_VERSION', '1.7.6');
define('ADREMM_MMP_SLUG','adremm-mm-pro');
define('ADREMM_MMP_OPT','adremm_mm_pro_options');
define('ADREMM_MMP_TRANS_REDIRECT','adremm_mm_pro_redirect');
define('ADREMM_MMP_TRANS_UPDATED','adremm_mm_pro_updated');

/**
 * Bootstrap all hooks after WP is ready.
 */
add_action('plugins_loaded', function () {
  /** Admin: plugin action links + row meta */
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
});

/**
 * Google Fonts (statische lijst, maar uitbreidbaar)
 */
function adremm_mmp_google_fonts(){
  return array(
    'Inter','Poppins','Outfit','Roboto','Open Sans','Montserrat','Lato','Nunito','Source Sans 3','Raleway',
    'Ubuntu','Work Sans','Manrope','DM Sans','Merriweather','Playfair Display','Oswald','Rubik','Archivo','Mulish',
    'PT Sans','PT Serif','Noto Sans','Noto Serif','Fira Sans','Fira Code','Inconsolata','Karla','Heebo','Jost',
    'Varela Round','Quicksand','Dosis','Cabin','Hind','Sora','Space Grotesk','Plus Jakarta Sans','Barlow','Barlow Condensed',
    'Exo 2','Titillium Web','Bebas Neue','Cormorant Garamond','EB Garamond','Libre Baskerville','Cinzel','Alegreya','Arvo','Pacifico'
  );
}

/**
 * Social platforms (met placeholder SVG)
 */
function adremm_mmp_social_platforms(){
  $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 3a7 7 0 1 1-7 7 7 7 0 0 1 7-7z"/></svg>';
  return array(
    'facebook'  => array('label'=>'Facebook',  'svg'=>$icon),
    'instagram' => array('label'=>'Instagram', 'svg'=>$icon),
    'linkedin'  => array('label'=>'LinkedIn',  'svg'=>$icon),
    'x'         => array('label'=>'X',         'svg'=>$icon),
    'youtube'   => array('label'=>'YouTube',   'svg'=>$icon),
    'tiktok'    => array('label'=>'TikTok',    'svg'=>$icon),
    'pinterest' => array('label'=>'Pinterest', 'svg'=>$icon),
    'snapchat'  => array('label'=>'Snapchat',  'svg'=>$icon),
    'whatsapp'  => array('label'=>'WhatsApp',  'svg'=>$icon),
    'telegram'  => array('label'=>'Telegram',  'svg'=>$icon),
    'discord'   => array('label'=>'Discord',   'svg'=>$icon),
    'github'    => array('label'=>'GitHub',    'svg'=>$icon),
    'behance'   => array('label'=>'Behance',   'svg'=>$icon),
    'dribbble'  => array('label'=>'Dribbble',  'svg'=>$icon),
    'threads'   => array('label'=>'Threads',   'svg'=>$icon),
  );
}

function adremm_mmp_units(){
  return array('px'=>'px','em'=>'em','rem'=>'rem');
}

function adremm_mmp_unit_select($name, $current, $units){
  $current = $current ? $current : 'px';
  if(!is_array($units) || empty($units)) $units = adremm_mmp_units();
  $out = '<select class="adremm-unit" name="'.esc_attr(ADREMM_MMP_OPT).'['.esc_attr($name).']">';
  foreach($units as $u=>$t){
    $out .= '<option value="'.esc_attr($u).'" '.selected($current, $u, false).'>'.esc_html($t).'</option>';
  }
  $out .= '</select>';
  return $out;
}

function adremm_mmp_defaults(){
  $layer = function(){
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
  foreach(adremm_mmp_social_platforms() as $k=>$v){
    $social_defaults[$k] = array('on'=>'0','url'=>'','custom_icon_url'=>'');
  }

  return array(
    'label'       => 'Menu',
    'back_label'  => 'Terug',
    'z_index'     => '2147483647',
    'global_font' => 'inherit',
    'global_font_mode' => 'theme',
    'global_font_family' => 'inherit',
    'drawer'      => 'right',
    'easing'      => 'ease-in-out',
    'breakpoint'  => '980',
    'breakpoint_unit' => 'px',
    'menu_src'    => 'auto',

    // Toggle / label
    'toggle_align'=> 'right',
    'toggle_fixed'=> '0',
    'toggle_top'  => '20',
    'toggle_side' => '20',
    'toggle_top_unit'  => 'px',
    'toggle_side_unit' => 'px',
    'toggle_bg'   => 'rgba(255,255,255,0)',
    'toggle_border_radius' => '50',
    'toggle_border_radius_unit' => 'px',

    // Paneel sizing
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
    'burger_style' => 'default',
    'burger_width' => '22',
    'burger_height' => '16',
    'burger_thickness' => '2',
    'burger_spacing' => '5',
    'burger_custom_color' => 'rgba(17,17,17,1)',

    'close_size'  => '16',
    'close_unit'  => 'px',
    'close_color' => 'rgba(17,17,17,1)',
    'back_position' => 'header',
    'show_close'    => '1',
    'show_label_in_header' => '0',

    'submenu_icon'      => 'chevron',
    'submenu_icon_url'  => '',
    'submenu_icon_size' => '14',
    'submenu_icon_unit' => 'px',
    'back_icon'         => 'chevron',
    'back_icon_url'     => '',
    'back_icon_size'    => '14',
    'back_icon_unit' => 'px',

    'submenu_style'     => 'drilldown',

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

    // SuperAdmin opties (toegevoegd uit 1.7.4)
    'super_admin_icon' => '',
    'super_admin_name' => 'ADREMM Mobile Menu',
  );
}

function adremm_mmp_get_options(){
  $d = adremm_mmp_defaults();
  $o = get_option(ADREMM_MMP_OPT, array());
  if(!is_array($o)) $o = array();
  $m = array_merge($d, $o);

  if(!isset($m['layers']) || !is_array($m['layers'])) $m['layers'] = $d['layers'];
  foreach($d['layers'] as $k=>$lv){
    if(!isset($m['layers'][$k]) || !is_array($m['layers'][$k])) $m['layers'][$k] = $lv;
    else $m['layers'][$k] = array_merge($lv, $m['layers'][$k]);
  }

  if(!isset($m['socials']) || !is_array($m['socials'])) $m['socials'] = $d['socials'];
  foreach($d['socials'] as $k=>$sv){
    if(!isset($m['socials'][$k]) || !is_array($m['socials'][$k])) $m['socials'][$k] = $sv;
    else $m['socials'][$k] = array_merge($sv, $m['socials'][$k]);
  }
  return $m;
}

/**
 * Redirect handling after activation/update
 */
register_activation_hook(__FILE__, function () {
    if (!is_admin()) return;
    set_transient(ADREMM_MMP_TRANS_UPDATED, 1, 60);
});

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

add_action('admin_init', function () {
    if (!current_user_can('manage_options')) return;
    if (wp_doing_ajax()) return;

    // Show saved notice
    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        add_settings_error(ADREMM_MMP_OPT, 'adremm_saved', 'Instellingen opgeslagen!', 'success');
    }

    // Redirect after activation/update
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

add_action('admin_init', function(){
  register_setting(ADREMM_MMP_OPT, ADREMM_MMP_OPT, array(
    'type'=>'array',
    'sanitize_callback'=>'adremm_mmp_sanitize',
    'default'=>adremm_mmp_defaults(),
  ));
});

function adremm_mmp_sanitize_rgba($v){
  $v = trim((string)$v);
  if($v==='') return '';
  if(preg_match('/^rgba\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*(0(\.\d+)?|1(\.0+)?)\s*\)$/', $v)) return $v;
  if(preg_match('/^rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)$/', $v)){
    $nums = preg_replace('/[^0-9,]/','',$v);
    $p = explode(',',$nums);
    if(count($p)===3) return 'rgba('.intval($p[0]).','.intval($p[1]).','.intval($p[2]).',1)';
  }
  if(preg_match('/^#?[0-9a-fA-F]{6}$/', $v)){
    $h = ltrim($v,'#');
    $r = hexdec(substr($h,0,2)); $g=hexdec(substr($h,2,2)); $b=hexdec(substr($h,4,2));
    return 'rgba('.$r.','.$g.','.$b.',1)';
  }
  return '';
}

function adremm_mmp_sanitize_css_len($v, $fallback){
  $v = trim((string)$v);
  if($v==='') return $fallback;
  if(preg_match('/^(0|[0-9]+(\.[0-9]+)?)(px|rem|em|vw|vh|%){0,1}$/', $v)) return $v;
  return $fallback;
}

function adremm_mmp_sanitize_layer($in, $d){
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

function adremm_mmp_sanitize($in){
  $d = adremm_mmp_defaults();
  if(!is_array($in)) $in=array();
  $out = array();

  $simple = array(
    'label','back_label','z_index','drawer','easing','breakpoint','menu_src','toggle_align','toggle_fixed','toggle_top','toggle_side','toggle_bg','global_font','global_font_mode','global_font_family',
    'panel_w','panel_max','fs_nav_max',
    'panel_bg','panel_border','panel_border_width','overlay',
    'burger_off','burger_on','burger_anim','burger_style','burger_width','burger_height','burger_thickness','burger_spacing','burger_custom_color',
    'close_size','close_color','back_position','show_close','show_label_in_header',
    'submenu_icon','submenu_icon_url','submenu_icon_size','back_icon','back_icon_url','back_icon_size',
    'socials_on','socials_size','credits_on','credits_text','credits_url','credits_icon_url','credits_size',
    'google_fonts_api_key','submenu_style','toggle_border_radius','toggle_border_radius_unit',
    // SuperAdmin opties toevoegen
    'super_admin_icon','super_admin_name'
  );

  foreach($simple as $k){
    $v = isset($in[$k]) ? wp_unslash($in[$k]) : ($d[$k] ?? '');
    $out[$k] = is_string($v) ? sanitize_text_field($v) : $v;
  }

  $numeric = array('breakpoint','z_index','submenu_icon_size','back_icon_size','close_size','socials_size','credits_size','panel_border_width','toggle_border_radius','burger_width','burger_height','burger_thickness','burger_spacing');
  foreach($numeric as $nk){
    $out[$nk] = preg_replace('/[^0-9]/','', (string)($out[$nk] ?? ''));
  }

  $out['panel_w']    = adremm_mmp_sanitize_css_len($out['panel_w'] ?? $d['panel_w'], $d['panel_w']);
  $out['panel_max']  = adremm_mmp_sanitize_css_len($out['panel_max'] ?? $d['panel_max'], $d['panel_max']);
  $out['fs_nav_max'] = adremm_mmp_sanitize_css_len($out['fs_nav_max'] ?? $d['fs_nav_max'], $d['fs_nav_max']);

  $rgba_fields = array('panel_bg','panel_border','overlay','burger_off','burger_on','close_color','toggle_bg','burger_custom_color');
  foreach($rgba_fields as $ck){
    $out[$ck] = adremm_mmp_sanitize_rgba($out[$ck] ?? $d[$ck]);
    if($out[$ck]==='') $out[$ck] = $d[$ck];
  }

  $out['layers'] = array();
  $din = isset($in['layers']) && is_array($in['layers']) ? $in['layers'] : array();
  foreach($d['layers'] as $lk=>$ld){
    $out['layers'][$lk] = adremm_mmp_sanitize_layer($din[$lk] ?? array(), $ld);
  }

  $out['socials'] = array();
  $sin = isset($in['socials']) && is_array($in['socials']) ? $in['socials'] : array();
  foreach($d['socials'] as $sk=>$sd){
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
  if(!in_array($out['burger_style'], array('default','custom'), true)) $out['burger_style'] = $d['burger_style'];
  if(!in_array($out['submenu_style'], array('drilldown','dropdown','accordion'), true)) $out['submenu_style'] = $d['submenu_style'];
  if(!in_array($out['global_font_mode'], array('theme','google','custom'), true)) $out['global_font_mode'] = $d['global_font_mode'];
  if(!in_array($out['toggle_align'], array('left','center','right'), true)) $out['toggle_align'] = $d['toggle_align'];

  $out['show_close'] = (!empty($out['show_close']) && $out['show_close']!=='0') ? '1' : '0';
  $out['socials_on'] = (!empty($out['socials_on']) && $out['socials_on']!=='0') ? '1' : '0';
  $out['credits_on'] = (!empty($out['credits_on']) && $out['credits_on']!=='0') ? '1' : '0';
  $out['toggle_fixed'] = (!empty($out['toggle_fixed']) && $out['toggle_fixed']!=='0') ? '1' : '0';
  $out['show_label_in_header'] = (!empty($out['show_label_in_header']) && $out['show_label_in_header']!=='0') ? '1' : '0';

  return $out;
}

/**
 * Admin menu (met SuperAdmin opties voor icoon en naam) - overgenomen uit 1.7.4
 */
add_action('admin_menu', function(){
    $options = adremm_mmp_get_options();
    $menu_name = !empty($options['super_admin_name']) ? $options['super_admin_name'] : 'ADREMM Mobile Menu';
    $icon = !empty($options['super_admin_icon']) ? $options['super_admin_icon'] : 'dashicons-menu';
    add_menu_page(
        $menu_name . ' — Instellingen',
        $menu_name,
        'manage_options',
        ADREMM_MMP_SLUG,
        'adremm_mmp_render_admin',
        $icon,
        59
    );
}, 9);

// Extra CSS om admin icoon te schalen (uit 1.7.4)
add_action('admin_head', function() {
    echo '<style>
        #adminmenu .toplevel_page_adremm-mm-pro .wp-menu-image img {
            max-width: 20px;
            max-height: 20px;
            width: auto;
            height: auto;
        }
    </style>';
});

function adremm_mmp_menu_select_html($cur){
  $out  = '<select name="'.esc_attr(ADREMM_MMP_OPT).'[menu_src]" class="regular-text">';
  $out .= '<option value="auto" '.selected($cur,'auto',false).'>Automatisch (primary → eerste menu)</option>';

  $locs = get_registered_nav_menus();
  if($locs){
    $out .= '<optgroup label="Theme locations">';
    foreach($locs as $slug=>$desc){
      $val='location:'.$slug;
      $out .= '<option value="'.esc_attr($val).'" '.selected($cur,$val,false).'>'.esc_html($slug.' — '.$desc).'</option>';
    }
    $out .= '</optgroup>';
  }

  $menus = wp_get_nav_menus();
  if($menus){
    $out .= '<optgroup label="Specifieke menu’s">';
    foreach($menus as $m){
      $val='menu:'.$m->term_id;
      $out .= '<option value="'.esc_attr($val).'" '.selected($cur,$val,false).'>'.esc_html($m->name.' (ID '.$m->term_id.')').'</option>';
    }
    $out .= '</optgroup>';
  }

  $out .= '</select>';
  return $out;
}

function adremm_mmp_render_layer_row($key, $title, $layer){
  $modes = array('theme'=>'Thema','google'=>'Google','custom'=>'Custom');
  $units = adremm_mmp_units();
  $ul = array('none'=>'Geen','solid'=>'Solid','dashed'=>'Dashed','dotted'=>'Dotted','double'=>'Double');
  ?>
  <div class="adremm-layer">
    <h3><?php echo esc_html($title); ?></h3>

    <div class="adremm-row">
      <div class="adremm-field">
        <label>Font modus</label>
        <select class="adremm-mode" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][mode]">
          <?php foreach($modes as $k=>$v): ?>
            <option value="<?php echo esc_attr($k); ?>" <?php selected($layer['mode'],$k); ?>><?php echo esc_html($v); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="adremm-field adremm-field-grow">
        <label>Family</label>
        <select class="adremm-google" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][family]" <?php echo ($layer['mode']==='google')?'':'disabled'; ?>>
          <?php foreach(adremm_mmp_google_fonts() as $f): ?>
            <option value="<?php echo esc_attr($f); ?>" <?php selected($layer['family'],$f); ?>><?php echo esc_html($f); ?></option>
          <?php endforeach; ?>
        </select>
        <input class="adremm-custom" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][family]" value="<?php echo esc_attr($layer['family']); ?>" <?php echo ($layer['mode']==='custom')?'':'disabled'; ?> placeholder="bijv: Outfit, system-ui, sans-serif">
      </div>
    </div>

    <div class="adremm-row">
      <div class="adremm-field">
        <label>Grootte</label>
        <input class="adremm-num" type="number" min="8" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][size]" value="<?php echo esc_attr($layer['size']); ?>">
        <?php echo adremm_mmp_unit_select('layers['.$key.'][unit]', $layer['unit'], $units); ?>
      </div>

      <div class="adremm-field">
        <label>Gewicht</label>
        <input class="adremm-num" type="number" min="100" step="100" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][weight]" value="<?php echo esc_attr($layer['weight']); ?>">
      </div>

      <div class="adremm-field">
        <label>Line-height</label>
        <input class="adremm-num" type="number" min="0.8" step="0.1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][line]" value="<?php echo esc_attr($layer['line']); ?>">
      </div>
    </div>

    <div class="adremm-row">
      <div class="adremm-field adremm-color-field">
        <label>Kleur</label>
        <input class="adremm-color-picker" type="text" data-alpha="true" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][color]" value="<?php echo esc_attr($layer['color']); ?>">
      </div>
      <div class="adremm-field adremm-color-field">
        <label>Hover</label>
        <input class="adremm-color-picker" type="text" data-alpha="true" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][hover]" value="<?php echo esc_attr($layer['hover']); ?>" placeholder="leeg = kleur">
      </div>
      <div class="adremm-field adremm-color-field">
        <label>Click</label>
        <input class="adremm-color-picker" type="text" data-alpha="true" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][active]" value="<?php echo esc_attr($layer['active']); ?>" placeholder="leeg = kleur">
      </div>
    </div>

    <div class="adremm-row">
      <div class="adremm-field">
        <label>Padding Y</label>
        <input class="adremm-num" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][pad_y]" value="<?php echo esc_attr($layer['pad_y']); ?>"> px
      </div>
      <div class="adremm-field">
        <label>Padding X</label>
        <input class="adremm-num" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][pad_x]" value="<?php echo esc_attr($layer['pad_x']); ?>"> px
      </div>
      <div class="adremm-field">
        <label>Gap</label>
        <input class="adremm-num" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][gap]" value="<?php echo esc_attr($layer['gap']); ?>"> px
      </div>
    </div>

    <div class="adremm-row">
      <div class="adremm-field">
        <label>Underline</label>
        <select class="adremm-ul" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][underline]">
          <?php foreach($ul as $u=>$t): ?><option value="<?php echo esc_attr($u); ?>" <?php selected($layer['underline'],$u); ?>><?php echo esc_html($t); ?></option><?php endforeach; ?>
        </select>
      </div>

      <div class="adremm-field">
        <label>Dikte</label>
        <input class="adremm-num" type="number" min="1" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][ul_thick]" value="<?php echo esc_attr($layer['ul_thick']); ?>"> px
      </div>

      <div class="adremm-field">
        <label>Afstand</label>
        <input class="adremm-num" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][ul_offset]" value="<?php echo esc_attr($layer['ul_offset']); ?>"> px
      </div>
    </div>
  </div>
  <?php
}

function adremm_mmp_render_admin(){
  if ( ! current_user_can('manage_options') ) return;
  $o = adremm_mmp_get_options();
  $units = adremm_mmp_units();
  ?>
  <div class="wrap adremm-mmp-wrap">
    <h1>ADREMM Mobile Menu PRO <small>v<?php echo ADREMM_MMP_VERSION; ?></small></h1>

    <?php settings_errors(); ?>

    <form method="post" action="options.php">
      <?php settings_fields(ADREMM_MMP_OPT); ?>

      <div class="adremm-tabs">
        <button type="button" class="adremm-tab is-active" data-tab="general">Algemeen</button>
        <button type="button" class="adremm-tab" data-tab="behavior">Gedrag</button>
        <button type="button" class="adremm-tab" data-tab="typography">Typografie</button>
        <button type="button" class="adremm-tab" data-tab="socials">Socials</button>
        <button type="button" class="adremm-tab" data-tab="credits">Credits</button>
        <button type="button" class="adremm-tab" data-tab="colors">Kleuren</button>
        <!-- Nieuw tabblad SuperAdmin -->
        <button type="button" class="adremm-tab" data-tab="superadmin">SuperAdmin</button>
      </div>

      <!-- Algemeen -->
      <div class="adremm-panel is-active" data-panel="general">
        <div class="adremm-row">
          <div class="adremm-field adremm-field-grow">
            <label>Menu kiezen</label>
            <?php echo adremm_mmp_menu_select_html($o['menu_src']); ?>
          </div>
          <div class="adremm-field">
            <label>Menu label</label>
            <input type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[label]" value="<?php echo esc_attr($o['label']); ?>">
          </div>
        </div>

        <div class="adremm-row">
          <div class="adremm-field">
            <label>Toggle uitlijning</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_align]">
              <option value="left" <?php selected($o['toggle_align'],'left'); ?>>Links</option>
              <option value="center" <?php selected($o['toggle_align'],'center'); ?>>Midden</option>
              <option value="right" <?php selected($o['toggle_align'],'right'); ?>>Rechts</option>
            </select>
          </div>
          <div class="adremm-field">
            <label>Fixed</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_fixed]">
              <option value="0" <?php selected($o['toggle_fixed'],'0'); ?>>Nee</option>
              <option value="1" <?php selected($o['toggle_fixed'],'1'); ?>>Ja</option>
            </select>
          </div>
          <div class="adremm-field">
            <label>Top</label>
            <input type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_top]" value="<?php echo esc_attr($o['toggle_top']); ?>">
            <?php echo adremm_mmp_unit_select('toggle_top_unit', $o['toggle_top_unit'], $units); ?>
          </div>
          <div class="adremm-field">
            <label>Zij</label>
            <input type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_side]" value="<?php echo esc_attr($o['toggle_side']); ?>">
            <?php echo adremm_mmp_unit_select('toggle_side_unit', $o['toggle_side_unit'], $units); ?>
          </div>
        </div>

        <div class="adremm-row">
          <div class="adremm-field adremm-color-field">
            <label>Toggle achtergrond</label>
            <input class="adremm-color-picker" type="text" data-alpha="true" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_bg]" value="<?php echo esc_attr($o['toggle_bg']); ?>">
          </div>
          <div class="adremm-field">
            <label>Border radius</label>
            <input type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_border_radius]" value="<?php echo esc_attr($o['toggle_border_radius']); ?>">
            <?php echo adremm_mmp_unit_select('toggle_border_radius_unit', $o['toggle_border_radius_unit'], $units); ?>
          </div>
          <div class="adremm-field">
            <label>Breakpoint</label>
            <input type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[breakpoint]" value="<?php echo esc_attr($o['breakpoint']); ?>">
            <?php echo adremm_mmp_unit_select('breakpoint_unit', $o['breakpoint_unit'], $units); ?>
          </div>
          <div class="adremm-field">
            <label>Z-index</label>
            <input type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[z_index]" value="<?php echo esc_attr($o['z_index']); ?>">
          </div>
        </div>

        <div class="adremm-row">
          <div class="adremm-field adremm-field-grow">
            <label>Google Fonts API Key (optioneel)</label>
            <input type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[google_fonts_api_key]" value="<?php echo esc_attr($o['google_fonts_api_key']); ?>" placeholder="AIza...">
            <p class="description">Voor toekomstige dynamische fontlijst.</p>
          </div>
        </div>
      </div>

      <!-- Gedrag -->
      <div class="adremm-panel" data-panel="behavior">
        <div class="adremm-row">
          <div class="adremm-field">
            <label>Drawer</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[drawer]">
              <option value="left" <?php selected($o['drawer'],'left'); ?>>Left</option>
              <option value="right" <?php selected($o['drawer'],'right'); ?>>Right</option>
              <option value="fullscreen" <?php selected($o['drawer'],'fullscreen'); ?>>Fullscreen</option>
            </select>
          </div>
          <div class="adremm-field">
            <label>Easing</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[easing]">
              <option value="ease" <?php selected($o['easing'],'ease'); ?>>ease</option>
              <option value="ease-in" <?php selected($o['easing'],'ease-in'); ?>>ease-in</option>
              <option value="ease-out" <?php selected($o['easing'],'ease-out'); ?>>ease-out</option>
              <option value="ease-in-out" <?php selected($o['easing'],'ease-in-out'); ?>>ease-in-out</option>
              <option value="linear" <?php selected($o['easing'],'linear'); ?>>linear</option>
            </select>
          </div>
          <div class="adremm-field">
            <label>Submenu stijl</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[submenu_style]">
              <option value="drilldown" <?php selected($o['submenu_style'],'drilldown'); ?>>Drilldown</option>
              <option value="dropdown" <?php selected($o['submenu_style'],'dropdown'); ?>>Dropdown</option>
              <option value="accordion" <?php selected($o['submenu_style'],'accordion'); ?>>Accordion</option>
            </select>
            <p class="description">Alleen drilldown werkt momenteel.</p>
          </div>
          <div class="adremm-field">
            <label>Hamburger stijl</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_style]" id="burger_style">
              <option value="default" <?php selected($o['burger_style'],'default'); ?>>Standaard</option>
              <option value="custom" <?php selected($o['burger_style'],'custom'); ?>>Custom</option>
            </select>
          </div>
        </div>

        <!-- Custom burger opties -->
        <div class="adremm-custom-burger" <?php echo ($o['burger_style']==='custom')?'':'style="display:none;"'; ?>>
          <div class="adremm-row">
            <div class="adremm-field">
              <label>Breedte (px)</label>
              <input type="number" min="10" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_width]" value="<?php echo esc_attr($o['burger_width']); ?>">
            </div>
            <div class="adremm-field">
              <label>Hoogte (px)</label>
              <input type="number" min="10" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_height]" value="<?php echo esc_attr($o['burger_height']); ?>">
            </div>
            <div class="adremm-field">
              <label>Dikte (px)</label>
              <input type="number" min="1" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_thickness]" value="<?php echo esc_attr($o['burger_thickness']); ?>">
            </div>
            <div class="adremm-field">
              <label>Tussenruimte (px)</label>
              <input type="number" min="1" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_spacing]" value="<?php echo esc_attr($o['burger_spacing']); ?>">
            </div>
          </div>
          <div class="adremm-row">
            <div class="adremm-field adremm-color-field">
              <label>Kleur</label>
              <input class="adremm-color-picker" type="text" data-alpha="true" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_custom_color]" value="<?php echo esc_attr($o['burger_custom_color']); ?>">
            </div>
            <div class="adremm-field">
              <label>Animatie</label>
              <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_anim]">
                <option value="swap" <?php selected($o['burger_anim'],'swap'); ?>>Swap (kruis)</option>
                <option value="squeeze" <?php selected($o['burger_anim'],'squeeze'); ?>>Squeeze</option>
                <option value="morph" <?php selected($o['burger_anim'],'morph'); ?>>Morph</option>
                <option value="spin" <?php selected($o['burger_anim'],'spin'); ?>>Spin</option>
                <option value="arrow" <?php selected($o['burger_anim'],'arrow'); ?>>Arrow</option>
              </select>
            </div>
          </div>
        </div>

        <div class="adremm-row">
          <div class="adremm-field adremm-field-grow">
            <label>Submenu indicator</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[submenu_icon]">
              <option value="chevron" <?php selected($o['submenu_icon'],'chevron'); ?>>Chevron</option>
              <option value="caret" <?php selected($o['submenu_icon'],'caret'); ?>>Caret</option>
              <option value="plus" <?php selected($o['submenu_icon'],'plus'); ?>>Plus</option>
              <option value="custom" <?php selected($o['submenu_icon'],'custom'); ?>>Eigen (URL)</option>
            </select>
          </div>
          <div class="adremm-field adremm-field-grow">
            <label>URL</label>
            <input id="adremm_submenu_icon_url" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[submenu_icon_url]" value="<?php echo esc_attr($o['submenu_icon_url']); ?>" placeholder="https://.../icon.svg">
            <button type="button" class="button adremm-media-btn" data-target="adremm_submenu_icon_url">Kies</button>
          </div>
          <div class="adremm-field">
            <label>Size</label>
            <input type="number" min="6" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[submenu_icon_size]" value="<?php echo esc_attr($o['submenu_icon_size']); ?>">
            <?php echo adremm_mmp_unit_select('submenu_icon_unit', $o['submenu_icon_unit'], $units); ?>
          </div>
        </div>

        <div class="adremm-row">
          <div class="adremm-field">
            <label>Terug knop tekst</label>
            <input type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_label]" value="<?php echo esc_attr($o['back_label']); ?>">
          </div>
          <div class="adremm-field">
            <label>Positie</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_position]">
              <option value="header" <?php selected($o['back_position'],'header'); ?>>Header</option>
              <option value="footer" <?php selected($o['back_position'],'footer'); ?>>Footer</option>
              <option value="replace_close" <?php selected($o['back_position'],'replace_close'); ?>>I.p.v. sluit</option>
            </select>
          </div>
          <div class="adremm-field">
            <label>Icon type</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_icon]">
              <option value="chevron" <?php selected($o['back_icon'],'chevron'); ?>>Chevron</option>
              <option value="caret" <?php selected($o['back_icon'],'caret'); ?>>Caret</option>
              <option value="plus" <?php selected($o['back_icon'],'plus'); ?>>Plus</option>
              <option value="custom" <?php selected($o['back_icon'],'custom'); ?>>Eigen (URL)</option>
            </select>
          </div>
        </div>

        <div class="adremm-row">
          <div class="adremm-field adremm-field-grow">
            <label>Terug icon URL</label>
            <input id="adremm_back_icon_url" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_icon_url]" value="<?php echo esc_attr($o['back_icon_url']); ?>" placeholder="https://.../icon.svg">
            <button type="button" class="button adremm-media-btn" data-target="adremm_back_icon_url">Kies</button>
          </div>
          <div class="adremm-field">
            <label>Size</label>
            <input type="number" min="6" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_icon_size]" value="<?php echo esc_attr($o['back_icon_size']); ?>">
            <?php echo adremm_mmp_unit_select('back_icon_unit', $o['back_icon_unit'], $units); ?>
          </div>
        </div>

        <div class="adremm-row">
          <div class="adremm-field">
            <label>Sluitkruis tonen</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[show_close]">
              <option value="1" <?php selected($o['show_close'],'1'); ?>>Ja</option>
              <option value="0" <?php selected($o['show_close'],'0'); ?>>Nee</option>
            </select>
          </div>
          <div class="adremm-field">
            <label>Size</label>
            <input type="number" min="10" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[close_size]" value="<?php echo esc_attr($o['close_size']); ?>">
            <?php echo adremm_mmp_unit_select('close_unit', $o['close_unit'], $units); ?>
          </div>
          <div class="adremm-field adremm-color-field">
            <label>Kleur</label>
            <input class="adremm-color-picker" type="text" data-alpha="true" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[close_color]" value="<?php echo esc_attr($o['close_color']); ?>">
          </div>
        </div>

        <div class="adremm-row">
          <div class="adremm-field">
            <label>Toon menu label in header</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[show_label_in_header]">
              <option value="0" <?php selected($o['show_label_in_header'],'0'); ?>>Nee</option>
              <option value="1" <?php selected($o['show_label_in_header'],'1'); ?>>Ja</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Typografie -->
      <div class="adremm-panel" data-panel="typography">
        <div class="adremm-row" style="margin-bottom:14px;">
          <div class="adremm-field">
            <label>Globaal font modus</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[global_font_mode]" id="global_font_mode">
              <option value="theme" <?php selected($o['global_font_mode'],'theme'); ?>>Thema</option>
              <option value="google" <?php selected($o['global_font_mode'],'google'); ?>>Google</option>
              <option value="custom" <?php selected($o['global_font_mode'],'custom'); ?>>Custom</option>
            </select>
          </div>
          <div class="adremm-field adremm-field-grow" id="global_font_google" <?php echo ($o['global_font_mode']!=='google')?'style="display:none;"':''; ?>>
            <label>Google Font family</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[global_font_family]">
              <?php foreach(adremm_mmp_google_fonts() as $f): ?>
                <option value="<?php echo esc_attr($f); ?>" <?php selected($o['global_font_family'],$f); ?>><?php echo esc_html($f); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="adremm-field adremm-field-grow" id="global_font_custom" <?php echo ($o['global_font_mode']!=='custom')?'style="display:none;"':''; ?>>
            <label>Custom font family</label>
            <input type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[global_font]" value="<?php echo esc_attr($o['global_font']); ?>" placeholder="bijv: Outfit, sans-serif">
          </div>
        </div>
        <p class="description">Het globale font wordt gebruikt als basis voor lagen met modus 'Thema'.</p>

        <h2>Typografie per laag</h2>
        <?php
          adremm_mmp_render_layer_row('menu_label','Menu label',$o['layers']['menu_label']);
          adremm_mmp_render_layer_row('items','Navigatie items',$o['layers']['items']);
          adremm_mmp_render_layer_row('subitems','Subnav',$o['layers']['subitems']);
          adremm_mmp_render_layer_row('back','Terug knop',$o['layers']['back']);
        ?>
      </div>

      <!-- Socials -->
      <div class="adremm-panel" data-panel="socials">
        <div class="adremm-row">
          <div class="adremm-field">
            <label>Socials</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials_on]">
              <option value="0" <?php selected($o['socials_on'],'0'); ?>>Uit</option>
              <option value="1" <?php selected($o['socials_on'],'1'); ?>>Aan</option>
            </select>
          </div>
          <div class="adremm-field">
            <label>Icon size</label>
            <input type="number" min="12" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials_size]" value="<?php echo esc_attr($o['socials_size']); ?>">
            <?php echo adremm_mmp_unit_select('socials_unit', $o['socials_unit'], $units); ?>
          </div>
        </div>

        <div class="adremm-social-grid">
          <?php foreach(adremm_mmp_social_platforms() as $k=>$p): $row = $o['socials'][$k] ?? array('on'=>'0','url'=>'','custom_icon_url'=>''); ?>
            <div class="adremm-social-row">
              <label class="adremm-check">
                <input type="checkbox" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials][<?php echo esc_attr($k); ?>][on]" value="1" <?php checked($row['on'],'1'); ?>>
                <?php echo esc_html($p['label']); ?>
              </label>
              <input type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials][<?php echo esc_attr($k); ?>][url]" value="<?php echo esc_attr($row['url']); ?>" placeholder="https://...">
              <input id="adremm_social_<?php echo esc_attr($k); ?>_icon" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials][<?php echo esc_attr($k); ?>][custom_icon_url]" value="<?php echo esc_attr($row['custom_icon_url']); ?>" placeholder="Custom icon URL">
              <button type="button" class="button adremm-media-btn" data-target="adremm_social_<?php echo esc_attr($k); ?>_icon">Kies</button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Credits -->
      <div class="adremm-panel" data-panel="credits">
        <div class="adremm-row">
          <div class="adremm-field">
            <label>Credits</label>
            <select name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_on]">
              <option value="0" <?php selected($o['credits_on'],'0'); ?>>Uit</option>
              <option value="1" <?php selected($o['credits_on'],'1'); ?>>Aan</option>
            </select>
          </div>
          <div class="adremm-field">
            <label>Tekst</label>
            <input type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_text]" value="<?php echo esc_attr($o['credits_text']); ?>">
          </div>
          <div class="adremm-field">
            <label>URL</label>
            <input type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_url]" value="<?php echo esc_attr($o['credits_url']); ?>">
          </div>
        </div>

        <div class="adremm-row">
          <div class="adremm-field adremm-field-grow">
            <label>Icon URL</label>
            <input id="adremm_credits_icon_url" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_icon_url]" value="<?php echo esc_attr($o['credits_icon_url']); ?>" placeholder="https://.../icon.svg">
            <button type="button" class="button adremm-media-btn" data-target="adremm_credits_icon_url">Kies</button>
          </div>
          <div class="adremm-field">
            <label>Size</label>
            <input type="number" min="12" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_size]" value="<?php echo esc_attr($o['credits_size']); ?>">
            <?php echo adremm_mmp_unit_select('credits_unit', $o['credits_unit'], $units); ?>
          </div>
        </div>
      </div>

      <!-- Kleuren -->
      <div class="adremm-panel" data-panel="colors">
        <div class="adremm-row">
          <div class="adremm-field adremm-color-field">
            <label>Panel achtergrond</label>
            <input class="adremm-color-picker" type="text" data-alpha="true" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[panel_bg]" value="<?php echo esc_attr($o['panel_bg']); ?>">
          </div>
          <div class="adremm-field adremm-color-field">
            <label>Panel border</label>
            <input class="adremm-color-picker" type="text" data-alpha="true" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[panel_border]" value="<?php echo esc_attr($o['panel_border']); ?>">
          </div>
          <div class="adremm-field">
            <label>Randbreedte (px)</label>
            <input type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[panel_border_width]" value="<?php echo esc_attr($o['panel_border_width']); ?>">
          </div>
          <div class="adremm-field adremm-color-field">
            <label>Overlay</label>
            <input class="adremm-color-picker" type="text" data-alpha="true" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[overlay]" value="<?php echo esc_attr($o['overlay']); ?>">
          </div>
        </div>
        <div class="adremm-row">
          <div class="adremm-field adremm-color-field">
            <label>Burger kleur dicht</label>
            <input class="adremm-color-picker" type="text" data-alpha="true" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_off]" value="<?php echo esc_attr($o['burger_off']); ?>">
          </div>
          <div class="adremm-field adremm-color-field">
            <label>Burger kleur open</label>
            <input class="adremm-color-picker" type="text" data-alpha="true" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_on]" value="<?php echo esc_attr($o['burger_on']); ?>">
          </div>
        </div>
      </div>

      <!-- SuperAdmin tab (nieuw) -->
      <div class="adremm-panel" data-panel="superadmin">
        <h2>SuperAdmin instellingen</h2>
        <div class="adremm-row">
          <div class="adremm-field adremm-field-grow">
            <label>Plugin icoon (SVG/PNG)</label>
            <input id="super_admin_icon" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[super_admin_icon]" value="<?php echo esc_attr($o['super_admin_icon']); ?>" placeholder="URL naar icoon" class="regular-text">
            <button type="button" class="button adremm-media-btn" data-target="super_admin_icon">Kies afbeelding</button>
            <p class="description">Upload een SVG of PNG. Het icoon wordt in het admin-menu getoond (schaalt automatisch).</p>
          </div>
          <div class="adremm-field adremm-field-grow">
            <label>Plugin naam in menu</label>
            <input type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[super_admin_name]" value="<?php echo esc_attr($o['super_admin_name']); ?>" placeholder="Bijv. Mijn Menu" class="regular-text">
            <p class="description">Wijzig de naam van de plugin in het WordPress menu.</p>
          </div>
        </div>
      </div>

      <?php submit_button('Wijzigingen opslaan'); ?>
    </form>

    <div class="adremm-footer">
      <span>ADREMM Mobile Menu PRO v<?php echo esc_html(ADREMM_MMP_VERSION); ?></span>
      <a class="button button-secondary adremm-support" href="https://adremm.nl" target="_blank" rel="noopener noreferrer">Support</a>
      <span><?php echo esc_html(date('Y')); ?> ADREMM</span>
    </div>
  </div>

  <style>
    .adremm-mmp-wrap { max-width: 1200px; margin-top: 20px; }
    .adremm-mmp-wrap h1 { background: linear-gradient(135deg, #111 0%, #222 100%); color: #fff; padding: 20px 30px; border-radius: 5px; }
    .adremm-mmp-wrap h1 small { font-size: 14px; opacity: 0.8; margin-left: 10px; }

    .adremm-tabs { display: flex; gap: 8px; margin: 12px 0; flex-wrap: wrap; }
    .adremm-tab { border: 1px solid #ccd0d4; background: #fff; padding: 8px 10px; cursor: pointer; }
    .adremm-tab.is-active { background: #111; color: #fff; border-color: #111; }
    .adremm-panel { display: none; border: 1px solid #ccd0d4; background: #fff; padding: 20px; margin-bottom: 14px; }
    .adremm-panel.is-active { display: block; }

    /* Flex rows met max-width 200px */
    .adremm-row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 20px;
      align-items: flex-start;
    }
    .adremm-field {
      flex: 0 1 200px;
      max-width: 200px;
    }
    .adremm-field-grow {
      flex: 1 1 300px;
      max-width: none;
    }
    .adremm-field label {
      display: block;
      font-weight: 600;
      margin-bottom: 4px;
      font-size: 12px;
      color: #1d2327;
    }
    .adremm-field input,
    .adremm-field select {
      width: 100%;
      max-width: 200px;
    }
    .adremm-field-grow input,
    .adremm-field-grow select {
      max-width: none;
    }
    .adremm-unit {
      width: 70px;
      margin-left: 5px;
    }
    .adremm-color-field input {
      max-width: 200px;
    }
    .adremm-field .description {
      margin-top: 5px;
      color: #646970;
      font-size: 12px;
    }

    .adremm-layer {
      border-top: 1px solid #e5e5e5;
      padding-top: 10px;
      margin-top: 10px;
    }
    .adremm-layer h3 { margin-top: 0; }

    .adremm-social-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 8px;
    }
    .adremm-social-row {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
      border: 1px solid #e5e5e5;
      padding: 8px;
    }
    .adremm-check { min-width: 160px; }

    .adremm-footer {
      display: flex;
      gap: 12px;
      align-items: center;
      margin-top: 12px;
      padding: 10px;
      background: #f8f9f9;
      border-radius: 5px;
    }
    .adremm-support { background: #222 !important; color: #fff !important; border-color: #222 !important; }

    /* Notificatie fade */
    .notice-success {
      animation: adremmFade 6s forwards;
    }
    @keyframes adremmFade {
      0% { opacity: 1; background: #d4edda; color: #155724; border-color: #c3e6cb; }
      70% { opacity: 1; background: #d4edda; }
      100% { opacity: 0; display: none; }
    }
  </style>

  <script>
  jQuery(document).ready(function($) {
    // Tabs
    $('.adremm-tab').click(function(e) {
      e.preventDefault();
      var key = $(this).data('tab');
      $('.adremm-tab').removeClass('is-active');
      $(this).addClass('is-active');
      $('.adremm-panel').removeClass('is-active');
      $('.adremm-panel[data-panel="'+key+'"]').addClass('is-active');
    });

    // Layer mode switching
    $(document).on('change', '.adremm-mode', function() {
      var $layer = $(this).closest('.adremm-layer');
      var mode = $(this).val();
      $layer.find('.adremm-google').prop('disabled', mode !== 'google');
      $layer.find('.adremm-custom').prop('disabled', mode !== 'custom');
    });

    // Global font mode switching
    $('#global_font_mode').on('change', function() {
      var mode = $(this).val();
      $('#global_font_google, #global_font_custom').hide();
      if (mode === 'google') $('#global_font_google').show();
      if (mode === 'custom') $('#global_font_custom').show();
    }).trigger('change');

    // Media buttons
    $(document).on('click', '.adremm-media-btn', function(e) {
      e.preventDefault();
      var targetId = $(this).data('target');
      var input = $('#' + targetId);
      if (typeof wp === 'undefined' || !wp.media) return;
      var frame = wp.media({ title: 'Kies media', button: { text: 'Gebruik deze' }, multiple: false });
      frame.on('select', function() {
        var att = frame.state().get('selection').first().toJSON();
        if (att && att.url) {
          input.val(att.url).trigger('change');
        }
      });
      frame.open();
    });

    // Custom burger toggle
    $('#burger_style').on('change', function() {
      if ($(this).val() === 'custom') {
        $('.adremm-custom-burger').show();
      } else {
        $('.adremm-custom-burger').hide();
      }
    });

    // Color pickers
    $('.adremm-color-picker').wpColorPicker();
  });
  </script>
  <?php
}

add_action('admin_enqueue_scripts', function(){
  if ( isset($_GET['page']) && $_GET['page'] === ADREMM_MMP_SLUG ) {
    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker-alpha', plugin_dir_url(__FILE__) . 'assets/wp-color-picker-alpha.min.js', array('wp-color-picker'), '3.0.0', true);
  }
});

function adremm_mmp_resolve_menu_args($src){
  if (strpos($src, 'location:') === 0) return array('theme_location' => substr($src, 9), 'container' => false, 'fallback_cb' => false, 'echo' => false);
  if (strpos($src, 'menu:') === 0) return array('menu' => intval(substr($src, 5)), 'container' => false, 'fallback_cb' => false, 'echo' => false);

  $menus = get_nav_menu_locations();
  if (isset($menus['primary'])) return array('theme_location' => 'primary','container'=>false,'fallback_cb'=>false,'echo'=>false);

  $first = wp_get_nav_menus();
  if ($first && !empty($first)) return array('menu' => $first[0]->term_id,'container'=>false,'fallback_cb'=>false,'echo'=>false);

  return array('echo'=>false,'fallback_cb'=>false,'container'=>false);
}

/**
 * Helper om laag CSS variabelen te genereren, met globale font override indien nodig
 */
function adremm_mmp_layer_css_vars($layer, $prefix, $global_font, $global_font_mode){
  $mode = $layer['mode'] ?? 'theme';
  $family = $layer['family'] ?? 'inherit';

  // Als globaal font niet 'theme' is, overschrijven we de familie met het globale font
  if ($global_font_mode !== 'theme') {
    $family = $global_font;
  } else {
    // Anders gebruiken we de laag-specifieke familie
    if($mode==='theme') $family = 'var(--mm-global-ff, inherit)';
    elseif($mode==='google') $family = $family ? ($family.', system-ui, sans-serif') : 'inherit';
    else $family = $family ? $family : 'inherit';
  }

  $color = $layer['color'] ?? 'rgba(17,17,17,1)';
  $hover = $layer['hover'] ?? $color;
  $active= $layer['active'] ?? $color;

  return array(
    "--mm-{$prefix}-ff" => $family,
    "--mm-{$prefix}-fs" => ($layer['size'] ?? '16').($layer['unit'] ?? 'px'),
    "--mm-{$prefix}-fw" => ($layer['weight'] ?? '500'),
    "--mm-{$prefix}-lh" => ($layer['line'] ?? '1.2'),
    "--mm-{$prefix}-c"  => $color,
    "--mm-{$prefix}-ch" => $hover,
    "--mm-{$prefix}-ca" => $active,
    "--mm-{$prefix}-py" => ($layer['pad_y'] ?? '10').'px',
    "--mm-{$prefix}-px" => ($layer['pad_x'] ?? '12').'px',
    "--mm-{$prefix}-gap"=> ($layer['gap'] ?? '8').'px',
    "--mm-{$prefix}-ul" => ($layer['underline'] ?? 'none'),
    "--mm-{$prefix}-ult"=> ($layer['ul_thick'] ?? '2').'px',
    "--mm-{$prefix}-ulo"=> ($layer['ul_offset'] ?? '3').'px'
  );
}

function adremm_mmp_style_attr($vars){
  $s='';
  foreach($vars as $k=>$v) $s .= $k.':'.esc_attr($v).';';
  return $s;
}

function adremm_mmp_shortcode($atts){
  $o = adremm_mmp_get_options();
  $a = shortcode_atts(array(
    'label'  => $o['label'],
    'drawer' => $o['drawer'],
    'easing' => $o['easing'],
    'z'      => $o['z_index'],
  ), $atts, 'adremm_mobile_menu');

  $args = adremm_mmp_resolve_menu_args($o['menu_src']);
  $menu_html = wp_nav_menu($args);
  if (!$menu_html) $menu_html = '<ul class="menu"><li><a href="#">(Nog geen menu ingesteld)</a></li></ul>';

  // Bepaal globaal font op basis van modus
  $global_font = $o['global_font'];
  if ($o['global_font_mode'] === 'google') {
    $global_font = $o['global_font_family'] . ', system-ui, sans-serif';
  } elseif ($o['global_font_mode'] === 'theme') {
    $global_font = 'inherit';
  }

  $vars = array(
    '--mm-global-ff' => $global_font,
    '--mm-panel-bg' => $o['panel_bg'],
    '--mm-panel-border' => $o['panel_border'],
    '--mm-border-width' => $o['panel_border_width'].'px',
    '--mm-overlay' => $o['overlay'],
    '--mm-burger-off' => $o['burger_off'],
    '--mm-burger-on'  => $o['burger_on'],
    '--mm-ease' => $a['easing'],
    '--mm-z'    => $a['z'],
    '--mm-ind-size' => $o['submenu_icon_size'].$o['submenu_icon_unit'],
    '--mm-back-ind-size' => $o['back_icon_size'].$o['back_icon_unit'],
    '--mm-anim' => $o['burger_anim'],
    '--mm-panel-w' => $o['panel_w'],
    '--mm-panel-max' => $o['panel_max'],
    '--mm-fs-nav-max' => $o['fs_nav_max'],
    '--mm-close-size' => $o['close_size'].$o['close_unit'],
    '--mm-close-color'=> $o['close_color'],
    '--mm-icon-url' => ($o['submenu_icon']==='custom' && $o['submenu_icon_url']) ? 'url('.esc_url($o['submenu_icon_url']).')' : 'none',
    '--mm-back-icon-url' => ($o['back_icon']==='custom' && $o['back_icon_url']) ? 'url('.esc_url($o['back_icon_url']).')' : 'none',
    '--mm-social-size' => $o['socials_size'].$o['socials_unit'],
    '--mm-credits-size'=> $o['credits_size'].$o['credits_unit'],
    '--mm-toggle-top' => $o['toggle_top'].$o['toggle_top_unit'],
    '--mm-toggle-side' => $o['toggle_side'].$o['toggle_side_unit'],
    '--mm-toggle-bg' => $o['toggle_bg'],
    '--mm-toggle-radius' => $o['toggle_border_radius'].$o['toggle_border_radius_unit'],
  );

  // Custom burger vars
  if ($o['burger_style'] === 'custom') {
    $vars['--mm-burger-width'] = $o['burger_width'].'px';
    $vars['--mm-burger-height'] = $o['burger_height'].'px';
    $vars['--mm-burger-thickness'] = $o['burger_thickness'].'px';
    $vars['--mm-burger-spacing'] = $o['burger_spacing'].'px';
    $vars['--mm-burger-color'] = $o['burger_custom_color'];
  }

  $vars = array_merge($vars, adremm_mmp_layer_css_vars($o['layers']['menu_label'], 'label', $global_font, $o['global_font_mode']));
  $vars = array_merge($vars, adremm_mmp_layer_css_vars($o['layers']['items'], 'item', $global_font, $o['global_font_mode']));
  $vars = array_merge($vars, adremm_mmp_layer_css_vars($o['layers']['subitems'], 'sub', $global_font, $o['global_font_mode']));
  $vars = array_merge($vars, adremm_mmp_layer_css_vars($o['layers']['back'], 'back', $global_font, $o['global_font_mode']));

  ob_start(); ?>
  <div class="adremm-mm adremm-mm--drawer-<?php echo esc_attr($a['drawer']); ?> adremm-mm--back-<?php echo esc_attr($o['back_position']); ?> adremm-mm--align-<?php echo esc_attr($o['toggle_align']); ?> <?php echo ($o['toggle_fixed']==='1')?'adremm-mm--fixed':''; ?>"
       data-subicon="<?php echo esc_attr($o['submenu_icon']); ?>"
       data-backicon="<?php echo esc_attr($o['back_icon']); ?>"
       data-showclose="<?php echo esc_attr($o['show_close']); ?>"
       data-socials="<?php echo esc_attr($o['socials_on']); ?>"
       data-credits="<?php echo esc_attr($o['credits_on']); ?>"
       data-burgerstyle="<?php echo esc_attr($o['burger_style']); ?>"
       data-showlabelheader="<?php echo esc_attr($o['show_label_in_header']); ?>"
       style="<?php echo adremm_mmp_style_attr($vars); ?>">
    <div class="adremm-mm__toggle-wrap">
      <button class="adremm-mm__toggle" aria-expanded="false" type="button">
        <span class="adremm-mm__burger" aria-hidden="true"><i></i><i></i></span>
        <span class="adremm-mm__label"><?php echo esc_html($a['label']); ?></span>
      </button>
    </div>

    <div class="adremm-mm__overlay" hidden></div>

    <div class="adremm-mm__panel" hidden>
      <div class="adremm-mm__header">
        <?php if ($o['show_label_in_header'] === '1'): ?>
          <span class="adremm-mm__header-label"><?php echo esc_html($a['label']); ?></span>
        <?php endif; ?>
        <button class="adremm-mm__back adremm-mm__back--header" type="button" hidden>
          <span class="adremm-mm__backicon" aria-hidden="true"></span><span class="adremm-mm__backtext"><?php echo esc_html($o['back_label']); ?></span>
        </button>
        <button class="adremm-mm__close" type="button" aria-label="Sluit menu"><span class="adremm-mm__cross" aria-hidden="true"></span></button>
      </div>

      <nav class="adremm-mm__nav">
        <div class="adremm-mm__views">
          <div class="adremm-mm__view is-root"><?php echo $menu_html; ?></div>
        </div>
      </nav>

      <div class="adremm-mm__bottom">
        <button class="adremm-mm__back adremm-mm__back--footer" type="button" hidden>
          <span class="adremm-mm__backicon" aria-hidden="true"></span><span class="adremm-mm__backtext"><?php echo esc_html($o['back_label']); ?></span>
        </button>
        <div class="adremm-mm__socials" hidden></div>
        <div class="adremm-mm__credits" hidden></div>
      </div>
    </div>
  </div>
  <?php return ob_get_clean();
}
add_shortcode('adremm_mobile_menu','adremm_mmp_shortcode');

add_action('wp_enqueue_scripts', function(){
  $o = adremm_mmp_get_options();

  // Google fonts voor lagen (alleen als globaal font mode niet google is, anders laden we al het globale)
  if ($o['global_font_mode'] !== 'google') {
    foreach(adremm_mmp_collect_google_families($o) as $fam){
      $slug = 'adremm-mmp-gf-'.sanitize_key($fam);
      $f = str_replace(' ', '+', trim($fam));
      wp_enqueue_style($slug, 'https://fonts.googleapis.com/css2?family='.rawurlencode($f).':wght@300;400;500;600;700&display=swap', array(), null);
    }
  }

  // Google fonts voor globaal font (indien van toepassing)
  if ($o['global_font_mode'] === 'google' && !empty($o['global_font_family'])) {
    $fam = $o['global_font_family'];
    $slug = 'adremm-mmp-gf-global-'.sanitize_key($fam);
    $f = str_replace(' ', '+', trim($fam));
    wp_enqueue_style($slug, 'https://fonts.googleapis.com/css2?family='.rawurlencode($f).':wght@300;400;500;600;700&display=swap', array(), null);
  }

  // ----- AANGEPASTE CSS MET FORCE Z-INDEX EN GROTERE KLIKGEBIED -----
  $css = "
/* FORCEREN DAT MENU BOVEN ALLES UITKOMT — OOK DIVI */
.adremm-mm,
.adremm-mm__panel,
.adremm-mm__overlay,
.adremm-mm__toggle-wrap {
    z-index: 2147483647 !important;
}
/* GROTE TOUCH-TARGET VOOR TERUGKNOP EN SUBMENU INDICATOR */
.adremm-mm__back,
.adremm-mm__next {
    min-height: 44px !important;
    min-width: 44px !important;
    padding: 12px 16px !important;
    cursor: pointer !important;
}
/* Zorg dat de knop niet wordt verborgen door andere elementen */
.adremm-mm__next {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}
/* Originele CSS van 1.6.5 (hieronder geplakt) */
.adremm-mm{position:relative;z-index:var(--mm-z,999999)}
.adremm-mm *{box-sizing:border-box}
.adremm-mm ul,.adremm-mm li{list-style:none;margin:0;padding:0}
.adremm-mm a{text-decoration:none}
.adremm-mm__toggle-wrap{display:flex;justify-content:flex-end;position:relative;z-index:calc(var(--mm-z,999999) + 3)}
.adremm-mm--align-left .adremm-mm__toggle-wrap{justify-content:flex-start}
.adremm-mm--align-center .adremm-mm__toggle-wrap{justify-content:center}
.adremm-mm--fixed .adremm-mm__toggle-wrap{position:fixed;top:var(--mm-toggle-top,20px);right:var(--mm-toggle-side,20px);left:auto;z-index:calc(var(--mm-z,999999) + 50)}
.adremm-mm--fixed.adremm-mm--align-left .adremm-mm__toggle-wrap{left:var(--mm-toggle-side,20px);right:auto}
.adremm-mm--fixed.adremm-mm--align-center .adremm-mm__toggle-wrap{left:50%;right:auto;transform:translateX(-50%)}
.adremm-mm__toggle{display:inline-flex;align-items:center;gap:10px;border:1px solid var(--mm-panel-border);background:var(--mm-toggle-bg);padding:var(--mm-label-py,10px) var(--mm-label-px,12px);cursor:pointer;border-radius:var(--mm-toggle-radius,50px)}
.adremm-mm__label{font-family:var(--mm-label-ff);font-size:var(--mm-label-fs);font-weight:var(--mm-label-fw);line-height:var(--mm-label-lh);color:var(--mm-label-c)}
.adremm-mm__burger{position:relative;width:22px;height:16px;display:inline-block}
.adremm-mm__burger i{position:absolute;left:0;right:0;height:2px;background:var(--mm-burger-off);transition:transform .28s var(--mm-ease), background .28s var(--mm-ease), top .28s var(--mm-ease), bottom .28s var(--mm-ease);display:block}
.adremm-mm__burger i:first-child{top:3px}
.adremm-mm__burger i:last-child{bottom:3px}

/* Custom burger */
.adremm-mm[data-burgerstyle='custom'] .adremm-mm__burger {
  width: var(--mm-burger-width,22px);
  height: var(--mm-burger-height,16px);
}
.adremm-mm[data-burgerstyle='custom'] .adremm-mm__burger i {
  height: var(--mm-burger-thickness,2px);
  background: var(--mm-burger-color,var(--mm-burger-off));
}
.adremm-mm[data-burgerstyle='custom'] .adremm-mm__burger i:first-child { top: 0; }
.adremm-mm[data-burgerstyle='custom'] .adremm-mm__burger i:last-child { bottom: 0; }
.adremm-mm[data-burgerstyle='custom'] .adremm-mm__burger i:nth-child(2) {
  top: calc(var(--mm-burger-spacing,5px) + var(--mm-burger-thickness,2px)/2);
}

.adremm-mm__overlay{position:fixed;inset:0;background:var(--mm-overlay);opacity:0;transition:opacity .25s var(--mm-ease);z-index:calc(var(--mm-z,999999) + 1)}
.adremm-mm__panel{position:fixed;top:0;bottom:0;right:0;width:min(var(--mm-panel-w,90vw), var(--mm-panel-max,420px));background:var(--mm-panel-bg);border-left:var(--mm-border-width,1px) solid var(--mm-panel-border);transform:translateX(100%);transition:transform .4s var(--mm-ease);z-index:calc(var(--mm-z,999999) + 2);display:grid;grid-template-rows:auto 1fr auto}
.adremm-mm--drawer-left .adremm-mm__panel{left:0;right:auto;border-left:none;border-right:var(--mm-border-width,1px) solid var(--mm-panel-border);transform:translateX(-100%)}
.adremm-mm--drawer-fullscreen .adremm-mm__panel{left:0;right:0;width:100vw;transform:translateY(-100%);border-left:none;border-right:none}
.adremm-mm__header{display:flex;align-items:center;gap:10px;justify-content:space-between;padding:10px;border-bottom:1px solid var(--mm-panel-border)}
.adremm-mm__header-label{font-family:var(--mm-label-ff);font-size:var(--mm-label-fs);font-weight:var(--mm-label-fw);color:var(--mm-label-c)}
.adremm-mm__close{border:1px solid var(--mm-panel-border);background:transparent;padding:8px;cursor:pointer;font-size:20px;line-height:1;color:var(--mm-close-color)}
.adremm-mm__cross{width:var(--mm-close-size,16px);height:var(--mm-close-size,16px);position:relative;display:block}
.adremm-mm__cross:before,.adremm-mm__cross:after{content:'';position:absolute;left:0;right:0;top:50%;height:2px;background:currentColor;transform-origin:center}
.adremm-mm__cross:before{transform:translateY(-50%) rotate(45deg)}
.adremm-mm__cross:after{transform:translateY(-50%) rotate(-45deg)}

.adremm-mm__nav{overflow:hidden;position:relative}
.adremm-mm__views{display:flex;flex-direction:row;width:100%;transition:transform .35s var(--mm-ease)}
.adremm-mm__view{min-width:100%;padding:8px 12px;overflow:auto}
.adremm-mm__view ul{display:flex;flex-direction:column;gap:6px}
.adremm-mm__view li.menu-item-has-children{display:flex;align-items:center;gap:10px;flex-wrap:nowrap}
.adremm-mm__view li.menu-item-has-children > a{flex:1;min-width:0;justify-content:flex-start}
.adremm-mm__view a{display:flex;justify-content:space-between;align-items:center;padding:var(--mm-item-py) var(--mm-item-px);border:1px solid var(--mm-panel-border);font-family:var(--mm-item-ff);font-size:var(--mm-item-fs);font-weight:var(--mm-item-fw);line-height:var(--mm-item-lh);color:var(--mm-item-c);position:relative}
.adremm-mm__view .sub-menu a{padding:var(--mm-sub-py) var(--mm-sub-px);font-family:var(--mm-sub-ff);font-size:var(--mm-sub-fs);font-weight:var(--mm-sub-fw);line-height:var(--mm-sub-lh);color:var(--mm-sub-c)}
.adremm-mm__view a:hover{color:var(--mm-item-ch)}
.adremm-mm__view .sub-menu a:hover{color:var(--mm-sub-ch)}
.adremm-mm__view a:active{color:var(--mm-item-ca)}
.adremm-mm__view .sub-menu a:active{color:var(--mm-sub-ca)}

.adremm-mm__view a:hover:after, .adremm-mm__view a.is-active:after{
  content:'';position:absolute;left:var(--mm-item-px);right:var(--mm-item-px);
  bottom:var(--mm-item-ulo);border-bottom:var(--mm-item-ult) var(--mm-item-ul) currentColor;
}
.adremm-mm__view .sub-menu a:hover:after, .adremm-mm__view .sub-menu a.is-active:after{
  content:'';position:absolute;left:var(--mm-sub-px);right:var(--mm-sub-px);
  bottom:var(--mm-sub-ulo);border-bottom:var(--mm-sub-ult) var(--mm-sub-ul) currentColor;
}

.adremm-mm__next{display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--mm-panel-border);background:transparent;margin-left:10px;cursor:pointer;padding:5px;width:auto;height:auto}
.adremm-mm__ind{width:var(--mm-ind-size,14px);height:var(--mm-ind-size,14px);background-repeat:no-repeat;background-position:center;background-size:contain;opacity:.9}

.adremm-mm__back{border:1px solid var(--mm-panel-border);background:transparent;color:var(--mm-back-c);padding:var(--mm-label-py,10px) var(--mm-label-px,12px);cursor:pointer;display:inline-flex;gap:10px;align-items:center;font-family:var(--mm-back-ff);font-size:var(--mm-back-fs);font-weight:var(--mm-back-fw);line-height:var(--mm-back-lh);position:relative}
.adremm-mm__back:hover{color:var(--mm-back-ch)}
.adremm-mm__back:active{color:var(--mm-back-ca)}
.adremm-mm__backicon{width:var(--mm-back-ind-size,14px);height:var(--mm-back-ind-size,14px);background-repeat:no-repeat;background-position:center;background-size:contain;opacity:.9}
.adremm-mm__back--header,.adremm-mm__back--footer{display:none}
.adremm-mm--in-sub.adremm-mm--back-header .adremm-mm__back--header{display:inline-flex}
.adremm-mm--in-sub.adremm-mm--back-footer .adremm-mm__back--footer{display:inline-flex}
.adremm-mm--in-sub.adremm-mm--back-replace_close .adremm-mm__back--header{display:inline-flex}

.adremm-mm__bottom{border-top:1px solid var(--mm-panel-border);padding:10px;display:flex;flex-direction:column;gap:10px}
.adremm-mm__socials{display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-start}
.adremm-mm__socials a{width:var(--mm-social-size);height:var(--mm-social-size);display:inline-flex;align-items:center;justify-content:center;color:var(--mm-item-c)}
.adremm-mm__socials svg{width:100%;height:100%}
.adremm-mm__credits a{display:inline-flex;align-items:center;gap:10px;color:var(--mm-item-c)}
.adremm-mm__credits img,.adremm-mm__credits svg{width:var(--mm-credits-size);height:var(--mm-credits-size)}
.adremm-mm.is-open .adremm-mm__overlay{opacity:1}
.adremm-mm.is-open .adremm-mm__panel{transform:translateX(0)}
.adremm-mm--drawer-left.is-open .adremm-mm__panel{transform:translateX(0)}
.adremm-mm--drawer-fullscreen.is-open .adremm-mm__panel{transform:translateY(0)}
.adremm-mm--back-replace_close .adremm-mm__close{display:none}
.adremm-mm[data-showclose='0'] .adremm-mm__close{display:none}

.adremm-mm.is-open .adremm-mm__burger i{background:var(--mm-burger-on)}
.adremm-mm.is-open[style*='--mm-anim:swap'] .adremm-mm__burger i:first-child{top:7px;transform:rotate(45deg)}
.adremm-mm.is-open[style*='--mm-anim:swap'] .adremm-mm__burger i:last-child{bottom:7px;transform:rotate(-45deg)}
.adremm-mm.is-open[style*='--mm-anim:squeeze'] .adremm-mm__burger i:first-child{top:7px;transform:rotate(45deg) scaleX(1.1)}
.adremm-mm.is-open[style*='--mm-anim:squeeze'] .adremm-mm__burger i:last-child{bottom:7px;transform:rotate(-45deg) scaleX(1.1)}
.adremm-mm.is-open[style*='--mm-anim:morph'] .adremm-mm__burger i:first-child{top:7px;transform:rotate(45deg) scaleX(.85)}
.adremm-mm.is-open[style*='--mm-anim:morph'] .adremm-mm__burger i:last-child{bottom:7px;transform:rotate(-45deg) scaleX(.85)}
.adremm-mm[style*='--mm-anim:spin'] .adremm-mm__burger{transition:transform .35s var(--mm-ease)}
.adremm-mm.is-open[style*='--mm-anim:spin'] .adremm-mm__burger{transform:rotate(180deg)}
.adremm-mm.is-open[style*='--mm-anim:spin'] .adremm-mm__burger i:first-child{top:7px;transform:rotate(45deg)}
.adremm-mm.is-open[style*='--mm-anim:spin'] .adremm-mm__burger i:last-child{bottom:7px;transform:rotate(-45deg)}
.adremm-mm.is-open[style*='--mm-anim:arrow'] .adremm-mm__burger i:first-child{top:7px;transform:rotate(45deg) translateX(1px)}
.adremm-mm.is-open[style*='--mm-anim:arrow'] .adremm-mm__burger i:last-child{bottom:7px;transform:rotate(-45deg) translateX(1px)}
";
  wp_register_style('adremm-mmp-front', false, array(), ADREMM_MMP_VERSION);
  wp_enqueue_style('adremm-mmp-front');
  wp_add_inline_style('adremm-mmp-front', $css);

  $platforms = adremm_mmp_social_platforms();
  $social_payload = array();
  foreach($platforms as $k=>$p){
    $row = $o['socials'][$k] ?? array();
    if((($row['on'] ?? '0') === '1')){
      $social_payload[] = array(
        'label'=>$p['label'],
        'url'=>($row['url'] ?? '') ? $row['url'] : '#',
        'svg'=>$p['svg'],
        'custom'=>$row['custom_icon_url'] ?? ''
      );
    }
  }
  $credits_payload = array(
    'on'=>$o['credits_on'],
    'text'=>$o['credits_text'],
    'url'=>$o['credits_url'],
    'icon'=>$o['credits_icon_url'],
  );

  $js = <<<'ADREMM_FRONT_JS'
(function(){
  var socials = '.wp_json_encode($social_payload).';
  var credits = '.wp_json_encode($credits_payload).';

  // Zet de JSON strings om naar objecten
  try {
    socials = JSON.parse(socials);
  } catch(e) {
    socials = [];
  }
  try {
    credits = JSON.parse(credits);
  } catch(e) {
    credits = {on:'0', text:'', url:'', icon:''};
  }

  function svgData(name){
    var svg='';
    if(name==='chevron') svg='<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 12 12\"><path d=\"M4 2l4 4-4 4\" stroke=\"currentColor\" stroke-width=\"2\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg>';
    if(name==='caret') svg='<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 12 12\"><path fill=\"currentColor\" d=\"M4 2l4 4-4 4\"/></svg>';
    if(name==='plus') svg='<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 12 12\"><path fill=\"currentColor\" d=\"M5 2h2v8H5zM2 5h8v2H2z\"/></svg>';
    return 'url(data:image/svg+xml;utf8,' + encodeURIComponent(svg) + ')';
  }

  function getVar(el, name){
    return (getComputedStyle(el).getPropertyValue(name)||'').trim();
  }

  function setIndicatorBg(ind, type, urlVar){
    if(type==='custom'){
      if(urlVar && urlVar!=='none'){
        ind.style.backgroundImage = urlVar;
      } else {
        ind.style.backgroundImage = svgData('chevron');
      }
    } else {
      ind.style.backgroundImage = svgData(type);
    }
  }

  function buildSocials(root){
    var wrap = root.querySelector('.adremm-mm__socials');
    if(!wrap) return;
    if(root.getAttribute('data-socials')!=='1' || !socials.length){ 
      wrap.hidden=true; 
      return; 
    }
    wrap.innerHTML='';
    socials.forEach(function(s){
      var a = document.createElement('a');
      a.href = s.url;
      a.target = '_blank';
      a.rel = 'noopener noreferrer';
      a.setAttribute('aria-label', s.label);
      if(a.getAttribute('href')==='#'){ a.addEventListener('click', function(ev){ ev.preventDefault(); }); }
      if(s.custom){
        var img = document.createElement('img');
        img.src = s.custom;
        img.alt = s.label;
        img.style.width='100%';
        img.style.height='100%';
        a.appendChild(img);
      } else {
        a.innerHTML = s.svg;
      }
      wrap.appendChild(a);
    });
    wrap.hidden=false;
  }

  function buildCredits(root){
    var wrap = root.querySelector('.adremm-mm__credits');
    if(!wrap) return;
    if(root.getAttribute('data-credits')!=='1' || !credits || credits.on!=='1'){ 
      wrap.hidden=true; 
      return; 
    }
    wrap.innerHTML='';
    var a = document.createElement('a');
    a.href = credits.url || '#';
    a.target = '_blank';
    a.rel = 'noopener noreferrer';
    if(credits.icon){
      var img = document.createElement('img');
      img.src = credits.icon;
      img.alt = credits.text || 'Credits';
      a.appendChild(img);
    }
    var span = document.createElement('span');
    span.textContent = credits.text || '';
    a.appendChild(span);
    wrap.appendChild(a);
    wrap.hidden=false;
  }

  function init(root){
    if(root.dataset.adremmInit) return;
    root.dataset.adremmInit='1';

    var toggle = root.querySelector('.adremm-mm__toggle');
    var panel  = root.querySelector('.adremm-mm__panel');
    var overlay= root.querySelector('.adremm-mm__overlay');
    var closeBtn = root.querySelector('.adremm-mm__close');
    var views  = root.querySelector('.adremm-mm__views');
    var backHeader = root.querySelector('.adremm-mm__back--header');
    var backFooter = root.querySelector('.adremm-mm__back--footer');

    var stack = [0];

    function openPanel(){
      root.classList.add('is-open');
      panel.hidden=false; overlay.hidden=false;
    }
    function closePanel(){
      root.classList.remove('is-open');
      panel.hidden=true; overlay.hidden=true;
      stack=[0];
      if(views) views.style.transform='translateX(0)';
      if(backHeader) backHeader.hidden=true;
      if(backFooter) backFooter.hidden=true;
      root.classList.remove('adremm-mm--in-sub');
    }

    if(toggle) toggle.addEventListener('click', function(e){ e.preventDefault(); root.classList.contains('is-open')?closePanel():openPanel(); });
    if(overlay) overlay.addEventListener('click', function(e){ e.preventDefault(); closePanel(); });
    if(closeBtn) closeBtn.addEventListener('click', function(e){ e.preventDefault(); closePanel(); });

    var rootView = root.querySelector('.adremm-mm__view.is-root');
    if(!rootView || !views) return;

    var subIconType = root.getAttribute('data-subicon') || 'chevron';
    var backIconType= root.getAttribute('data-backicon') || 'chevron';

    var bIconUrl = getVar(root, '--mm-back-icon-url');
    root.querySelectorAll('.adremm-mm__backicon').forEach(function(ind){
      setIndicatorBg(ind, backIconType, bIconUrl);
    });

    function updateBack(){
      var show = (stack.length > 1);
      if(show){ root.classList.add('adremm-mm--in-sub'); } else { root.classList.remove('adremm-mm--in-sub'); }
      if(backHeader && (root.classList.contains('adremm-mm--back-header') || root.classList.contains('adremm-mm--back-replace_close'))) backHeader.hidden = !show;
      if(backFooter && root.classList.contains('adremm-mm--back-footer')) backFooter.hidden = !show;
    }

    function prepareView(viewEl){
      if(!viewEl) return;
      viewEl.querySelectorAll('li').forEach(function(li){
        if(li.querySelector(':scope > ul')) li.classList.add('menu-item-has-children');
      });

      var items = Array.prototype.slice.call(viewEl.querySelectorAll('li.menu-item-has-children'));
      items.forEach(function(li){
        if(li.dataset.adremmPrep) return;
        var submenu = li.querySelector(':scope > ul');
        if(!submenu) return;

        var view = document.createElement('div');
        view.className='adremm-mm__view';
        view.appendChild(submenu);
        views.appendChild(view);
        var viewIndex = views.children.length - 1;
        li.dataset.adremmPrep='1';

        var a = li.querySelector(':scope > a');
        if(a){
          var btn = document.createElement('button');
          btn.type='button';
          btn.className='adremm-mm__next';
          var ind = document.createElement('span');
          ind.className='adremm-mm__ind';
          var iconUrl = getVar(root,'--mm-icon-url');
          setIndicatorBg(ind, subIconType, iconUrl);
          btn.appendChild(ind);
          a.after(btn);

          btn.dataset.target = String(viewIndex);
          btn.addEventListener('click', function(ev){
            ev.preventDefault();
            var t = parseInt(btn.dataset.target||'0',10);
            if(!isFinite(t) || t<0) return;
            stack.push(t);
            views.style.transform='translateX(-'+(t*100)+'%)';
            updateBack();
            prepareView(view);
          });
        }
      });
    }

    prepareView(rootView);
    updateBack();

    function goBack(){
      if(stack.length>1){
        stack.pop();
        var t = stack[stack.length-1] || 0;
        views.style.transform='translateX(-'+(t*100)+'%)';
        updateBack();
      }
    }
    if(backHeader) backHeader.addEventListener('click', goBack);
    if(backFooter) backFooter.addEventListener('click', goBack);

    document.addEventListener('keydown', function(ev){
      if(ev.key==='Escape' && root.classList.contains('is-open')) closePanel();
    });

    buildSocials(root);
    buildCredits(root);
  }

  function boot(){ document.querySelectorAll('.adremm-mm').forEach(init); }
  if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})();
ADREMM_FRONT_JS;

  wp_register_script('adremm-mmp-front', false, array(), ADREMM_MMP_VERSION, true);
  wp_enqueue_script('adremm-mmp-front');
  wp_add_inline_script('adremm-mmp-front', $js);
});

function adremm_mmp_collect_google_families($o){
  $f = array();
  if(isset($o['layers']) && is_array($o['layers'])){
    foreach($o['layers'] as $layer){
      if(($layer['mode'] ?? '') === 'google' && !empty($layer['family'])){
        $f[] = $layer['family'];
      }
    }
  }
  return array_values(array_unique(array_filter(array_map('trim',$f))));
}