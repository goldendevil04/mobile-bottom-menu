<?php
/**
 * Plugin Name: NaniMade Complete Pickle Commerce Suite
 * Plugin URI: https://nanimade.com
 * Description: Complete mobile commerce solution for pickle businesses with advanced Elementor Pro integration, PWA features, and modern mobile app aesthetics.
 * Version: 1.0.0
 * Author: NaniMade
 * Text Domain: nanimade-suite
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('NANIMADE_SUITE_VERSION', '1.0.0');
define('NANIMADE_SUITE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('NANIMADE_SUITE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('NANIMADE_SUITE_PLUGIN_FILE', __FILE__);

/**
 * Main NaniMade Suite Class
 */
class NaniMade_Complete_Suite {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        // Load core classes
        $this->load_dependencies();
        $this->init_hooks();
        
        // Initialize components
        new NaniMade_Mobile_Menu();
        new NaniMade_Sidebar_Cart();
        new NaniMade_PWA_Features();
        new NaniMade_Elementor_Integration();
        new NaniMade_Analytics_Dashboard();
    }
    
    private function load_dependencies() {
        // Core functionality
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/class-mobile-menu.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/class-sidebar-cart.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/class-pwa-features.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/class-analytics-dashboard.php';
        
        // Elementor integration
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/elementor/class-elementor-integration.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/elementor/widgets/class-pickle-customizer-widget.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/elementor/widgets/class-recipe-story-widget.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/elementor/widgets/class-taste-profile-widget.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/elementor/widgets/class-smart-gallery-widget.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/elementor/widgets/class-trust-signals-widget.php';
        
        // API integrations
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/api/class-icon-api-integration.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/api/class-image-api-integration.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/api/class-font-api-integration.php';
        
        // Utilities
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/class-performance-optimizer.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/class-cache-manager.php';
    }
    
    private function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_head', array($this, 'add_meta_tags'));
        add_action('wp_footer', array($this, 'add_mobile_menu_html'));
    }
    
    public function enqueue_scripts() {
        // Core mobile functionality
        wp_enqueue_script(
            'nanimade-mobile-core',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/js/mobile-core.js',
            array('jquery'),
            NANIMADE_SUITE_VERSION,
            true
        );
        
        // Touch interactions
        wp_enqueue_script(
            'nanimade-touch-interactions',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/js/touch-interactions.js',
            array('nanimade-mobile-core'),
            NANIMADE_SUITE_VERSION,
            true
        );
        
        // Sidebar cart functionality
        wp_enqueue_script(
            'nanimade-sidebar-cart',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/js/sidebar-cart.js',
            array('nanimade-mobile-core', 'wc-cart-fragments'),
            NANIMADE_SUITE_VERSION,
            true
        );
        
        // PWA features
        wp_enqueue_script(
            'nanimade-pwa',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/js/pwa-features.js',
            array('nanimade-mobile-core'),
            NANIMADE_SUITE_VERSION,
            true
        );
        
        // Localize script with data
        wp_localize_script('nanimade-mobile-core', 'nanimade_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nanimade_nonce'),
            'cart_url' => wc_get_cart_url(),
            'checkout_url' => wc_get_checkout_url(),
            'is_mobile' => wp_is_mobile(),
            'currency_symbol' => get_woocommerce_currency_symbol(),
            'settings' => $this->get_plugin_settings()
        ));
    }
    
    public function enqueue_styles() {
        // Main mobile styles
        wp_enqueue_style(
            'nanimade-mobile-styles',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/mobile-styles.css',
            array(),
            NANIMADE_SUITE_VERSION
        );
        
        // Animations and micro-interactions
        wp_enqueue_style(
            'nanimade-animations',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/animations.css',
            array('nanimade-mobile-styles'),
            NANIMADE_SUITE_VERSION
        );
        
        // Elementor widget styles
        wp_enqueue_style(
            'nanimade-elementor-widgets',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/elementor-widgets.css',
            array('nanimade-mobile-styles'),
            NANIMADE_SUITE_VERSION
        );
        
        // PWA styles
        wp_enqueue_style(
            'nanimade-pwa-styles',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/pwa-styles.css',
            array('nanimade-mobile-styles'),
            NANIMADE_SUITE_VERSION
        );
    }
    
    public function add_meta_tags() {
        echo '<meta name="theme-color" content="#4CAF50">' . "\n";
        echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">' . "\n";
        echo '<meta name="apple-mobile-web-app-title" content="' . get_bloginfo('name') . '">' . "\n";
        echo '<link rel="manifest" href="' . NANIMADE_SUITE_PLUGIN_URL . 'assets/manifest.json">' . "\n";
    }
    
    public function add_mobile_menu_html() {
        if (wp_is_mobile() || $this->is_mobile_menu_enabled()) {
            include NANIMADE_SUITE_PLUGIN_PATH . 'templates/mobile-menu.php';
            include NANIMADE_SUITE_PLUGIN_PATH . 'templates/sidebar-cart.php';
        }
    }
    
    private function is_mobile_menu_enabled() {
        return get_option('nanimade_mobile_menu_enabled', true);
    }
    
    private function get_plugin_settings() {
        return array(
            'mobile_menu_enabled' => get_option('nanimade_mobile_menu_enabled', true),
            'sidebar_cart_enabled' => get_option('nanimade_sidebar_cart_enabled', true),
            'pwa_enabled' => get_option('nanimade_pwa_enabled', true),
            'animations_enabled' => get_option('nanimade_animations_enabled', true),
            'touch_gestures_enabled' => get_option('nanimade_touch_gestures_enabled', true),
            'haptic_feedback_enabled' => get_option('nanimade_haptic_feedback_enabled', true),
            'dark_mode_enabled' => get_option('nanimade_dark_mode_enabled', true),
            'menu_style' => get_option('nanimade_menu_style', 'floating'),
            'cart_animation' => get_option('nanimade_cart_animation', 'slide-right'),
            'primary_color' => get_option('nanimade_primary_color', '#4CAF50'),
            'secondary_color' => get_option('nanimade_secondary_color', '#FF9800'),
            'accent_color' => get_option('nanimade_accent_color', '#E91E63')
        );
    }
    
    public function woocommerce_missing_notice() {
        echo '<div class="notice notice-error"><p>';
        echo __('NaniMade Complete Suite requires WooCommerce to be installed and active.', 'nanimade-suite');
        echo '</p></div>';
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('nanimade-suite', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function activate() {
        // Set default options
        add_option('nanimade_mobile_menu_enabled', true);
        add_option('nanimade_sidebar_cart_enabled', true);
        add_option('nanimade_pwa_enabled', true);
        add_option('nanimade_animations_enabled', true);
        add_option('nanimade_touch_gestures_enabled', true);
        add_option('nanimade_haptic_feedback_enabled', true);
        add_option('nanimade_dark_mode_enabled', true);
        add_option('nanimade_menu_style', 'floating');
        add_option('nanimade_cart_animation', 'slide-right');
        add_option('nanimade_primary_color', '#4CAF50');
        add_option('nanimade_secondary_color', '#FF9800');
        add_option('nanimade_accent_color', '#E91E63');
        
        // Create necessary database tables
        $this->create_analytics_tables();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        // Clean up temporary data
        wp_cache_flush();
        flush_rewrite_rules();
    }
    
    private function create_analytics_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // User interactions table
        $table_name = $wpdb->prefix . 'nanimade_user_interactions';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            session_id varchar(255) NOT NULL,
            interaction_type varchar(100) NOT NULL,
            element_id varchar(255) DEFAULT NULL,
            page_url text NOT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            data longtext DEFAULT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY session_id (session_id),
            KEY interaction_type (interaction_type),
            KEY timestamp (timestamp)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Cart analytics table
        $table_name = $wpdb->prefix . 'nanimade_cart_analytics';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            session_id varchar(255) NOT NULL,
            action varchar(50) NOT NULL,
            product_id bigint(20) DEFAULT NULL,
            quantity int(11) DEFAULT NULL,
            cart_total decimal(10,2) DEFAULT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY session_id (session_id),
            KEY action (action),
            KEY product_id (product_id),
            KEY timestamp (timestamp)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
}

// Initialize the plugin
function nanimade_complete_suite() {
    return NaniMade_Complete_Suite::get_instance();
}

// Start the plugin
nanimade_complete_suite();