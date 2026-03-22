<?php
/**
 * Plugin Name: ADREMM Klok
 * Description: Een uiterst gebruiksvriendelijke, meertalige klokplugin met live previews, openingstijden en schaalbare weergave.
 * Version: 1.3.2
 * Author: ADREMM
 * Author URI: https://adremm.nl
 * License: GPLv2 or later
 * Text Domain: adremm-clock-plugin
 * Domain Path: /languages
 */

if ( ! defined('ABSPATH') ) exit;

if ( ! class_exists( 'Adremm_Clock_Plugin' ) ) :

class Adremm_Clock_Plugin {

    /**
     * @var string
     */
    public $version = '1.3.2';

    /**
     * @var Adremm_Clock_Plugin
     */
    private static $instance;

    /**
     * Main Adremm_Clock_Plugin Instance.
     */
    public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Adremm_Clock_Plugin ) ) {
            self::$instance = new Adremm_Clock_Plugin();
            self::$instance->setup_constants();
            self::$instance->includes();
            self::$instance->init_hooks();
        }
        return self::$instance;
    }

    /**
     * Setup constants.
     */
    private function setup_constants() {
        if ( ! defined( 'ADREMM_CLOCK_VERSION' ) ) {
            define( 'ADREMM_CLOCK_VERSION', $this->version );
        }
        if ( ! defined( 'ADREMM_CLOCK_PATH' ) ) {
            define( 'ADREMM_CLOCK_PATH', plugin_dir_path( __FILE__ ) );
        }
        if ( ! defined( 'ADREMM_CLOCK_URL' ) ) {
            define( 'ADREMM_CLOCK_URL', plugin_dir_url( __FILE__ ) );
        }
        if ( ! defined( 'ADREMM_CLOCK_BASENAME' ) ) {
            define( 'ADREMM_CLOCK_BASENAME', plugin_basename( __FILE__ ) );
        }
    }

    /**
     * Include required files.
     */
    private function includes() {
        require_once ADREMM_CLOCK_PATH . 'settings.php';
        require_once ADREMM_CLOCK_PATH . 'functions.php';
    }

    /**
     * Register hooks.
     */
    private function init_hooks() {
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        add_action( 'activated_plugin', array( $this, 'handle_activation_redirect' ) );
        add_action( 'admin_init', array( $this, 'check_version' ) );
        add_action( 'wp_footer', array( $this, 'render_frontend' ) );
    }

    /**
     * Check version and run activation logic if updated.
     */
    public function check_version() {
        if ( ! is_admin() ) return;
        $installed_version = get_option( 'adremm_clock_version' );
        if ( $installed_version !== $this->version ) {
            $this->activate();
            update_option( 'adremm_clock_version', $this->version );
        }
    }

    /**
     * Activation logic.
     */
    public function activate() {
        $default_settings = adremm_clock_get_default_settings();
        $current_settings = get_option( 'adremm_clock_settings', array() );

        // Merge defaults with current settings to ensure new keys exist
        $new_settings = wp_parse_args( (array) $current_settings, $default_settings );
        update_option( 'adremm_clock_settings', $new_settings );

        if ( ! get_option( 'adremm_clock_version' ) ) {
            update_option( 'adremm_clock_version', $this->version );
        }
    }

    /**
     * Redirect to settings on activation.
     */
    public function handle_activation_redirect($plugin) {
        if ( $plugin == ADREMM_CLOCK_BASENAME ) {
            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;
            if ( isset( $_GET['activate-multi'] ) ) return;

            // Ensure we are redirecting from the plugins page activation
            if ( strpos( $_SERVER['PHP_SELF'], 'plugins.php' ) !== false ) {
                wp_safe_redirect( admin_url( 'admin.php?page=adremm-clock-settings' ) );
                exit;
            }
        }
    }

    /**
     * Safe str_pad wrapper.
     */
    public static function str_pad( $input, $pad_length, $pad_string = " ", $pad_type = STR_PAD_LEFT ) {
        return str_pad( (string) $input, $pad_length, $pad_string, $pad_type );
    }

    /**
     * Render the clock in footer.
     */
    public function render_frontend() {
        if ( is_admin() ) return;

        $settings = wp_parse_args( get_option( 'adremm_clock_settings', array() ), adremm_clock_get_default_settings() );
        $status_data = adremm_clock_get_status();
        $status = $status_data['status'];
        $status_text = $status_data['text'];

        if ( file_exists( ADREMM_CLOCK_PATH . 'clock-template.php' ) ) {
            include ADREMM_CLOCK_PATH . 'clock-template.php';
        }
    }
}

/**
 * Initialize the plugin.
 */
function adremm_clock_init() {
    return Adremm_Clock_Plugin::instance();
}

adremm_clock_init();

endif;

/**
 * Global helper for templates if needed.
 */
if ( ! function_exists( 'adremm_str_pad' ) ) {
    function adremm_str_pad( $input, $pad_length, $pad_string = " ", $pad_type = STR_PAD_LEFT ) {
        return Adremm_Clock_Plugin::str_pad( $input, $pad_length, $pad_string, $pad_type );
    }
}
