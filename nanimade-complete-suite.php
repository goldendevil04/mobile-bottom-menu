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
        // Load core classes
        $this->load_dependencies();
        
        // Initialize components
        $this->init_mobile_menu();
        $this->init_elementor_widgets();
        $this->init_pwa_features();
        $this->init_analytics();
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Admin menu
        add_action('admin_menu', array($this, 'admin_menu'));
        
        // AJAX handlers
        add_action('wp_ajax_nanimade_add_to_cart', array($this, 'ajax_add_to_cart'));
        add_action('wp_ajax_nopriv_nanimade_add_to_cart', array($this, 'ajax_add_to_cart'));
        add_action('wp_ajax_nanimade_update_cart', array($this, 'ajax_update_cart'));
        add_action('wp_ajax_nopriv_nanimade_update_cart', array($this, 'ajax_update_cart'));
        
        // WooCommerce hooks
        if (class_exists('WooCommerce')) {
            add_action('woocommerce_add_to_cart', array($this, 'update_cart_count'));
            add_action('woocommerce_cart_item_removed', array($this, 'update_cart_count'));
        }
    }
    
    private function load_dependencies() {
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/class-mobile-menu.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/class-sidebar-cart.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/class-pwa-manager.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/class-analytics.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/class-elementor-integration.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/class-pickle-customizer.php';
    }
    
    private function init_mobile_menu() {
        new NaniMade_Mobile_Menu();
    }
    
    private function init_elementor_widgets() {
        if (did_action('elementor/loaded')) {
            new NaniMade_Elementor_Integration();
        }
    }
    
    private function init_pwa_features() {
        new NaniMade_PWA_Manager();
    }
    
    private function init_analytics() {
        new NaniMade_Analytics();
    }
    
    public function enqueue_scripts() {
        // Main plugin styles
        wp_enqueue_style(
            'nanimade-suite-styles',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/nanimade-suite.css',
            array(),
            NANIMADE_SUITE_VERSION
        );
        
        // Mobile app styles
        wp_enqueue_style(
            'nanimade-mobile-app',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/mobile-app-styles.css',
            array(),
            NANIMADE_SUITE_VERSION
        );
        
        // Pickle theme styles
        wp_enqueue_style(
            'nanimade-pickle-theme',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/pickle-theme.css',
            array(),
            NANIMADE_SUITE_VERSION
        );
        
        // Font Awesome
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            array(),
            '6.4.0'
        );
        
        // Main JavaScript
        wp_enqueue_script(
            'nanimade-suite-js',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/js/nanimade-suite.js',
            array('jquery'),
            NANIMADE_SUITE_VERSION,
            true
        );
        
        // Touch interactions
        wp_enqueue_script(
            'nanimade-touch',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/js/touch-interactions.js',
            array('jquery'),
            NANIMADE_SUITE_VERSION,
            true
        );
        
        // PWA features
        wp_enqueue_script(
            'nanimade-pwa',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/js/pwa-features.js',
            array(),
            NANIMADE_SUITE_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('nanimade-suite-js', 'nanimade_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nanimade_nonce'),
            'cart_url' => wc_get_cart_url(),
            'checkout_url' => wc_get_checkout_url(),
            'currency_symbol' => get_woocommerce_currency_symbol(),
            'cart_count' => WC()->cart ? WC()->cart->get_cart_contents_count() : 0,
            'strings' => array(
                'added_to_cart' => __('Added to cart!', 'nanimade-suite'),
                'cart_updated' => __('Cart updated!', 'nanimade-suite'),
                'error' => __('Something went wrong. Please try again.', 'nanimade-suite'),
                'loading' => __('Loading...', 'nanimade-suite'),
                'select_options' => __('Please select product options', 'nanimade-suite'),
            )
        ));
    }
    
    public function admin_enqueue_scripts($hook) {
        if ('settings_page_nanimade-suite' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'nanimade-admin-styles',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/admin-styles.css',
            array(),
            NANIMADE_SUITE_VERSION
        );
        
        wp_enqueue_script(
            'nanimade-admin-js',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/js/admin-scripts.js',
            array('jquery', 'wp-color-picker'),
            NANIMADE_SUITE_VERSION,
            true
        );
        
        wp_enqueue_style('wp-color-picker');
    }
    
    public function admin_menu() {
        add_options_page(
            __('NaniMade Suite Settings', 'nanimade-suite'),
            __('NaniMade Suite', 'nanimade-suite'),
            'manage_options',
            'nanimade-suite',
            array($this, 'admin_page')
        );
    }
    
    public function admin_page() {
        include NANIMADE_SUITE_PLUGIN_PATH . 'templates/admin/settings-page.php';
    }
    
    public function ajax_add_to_cart() {
        check_ajax_referer('nanimade_nonce', 'nonce');
        
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
        $variations = isset($_POST['variations']) ? $_POST['variations'] : array();
        
        if ($variation_id) {
            $result = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variations);
        } else {
            $result = WC()->cart->add_to_cart($product_id, $quantity);
        }
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Product added to cart!', 'nanimade-suite'),
                'cart_count' => WC()->cart->get_cart_contents_count(),
                'cart_total' => WC()->cart->get_cart_total(),
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to add product to cart.', 'nanimade-suite')
            ));
        }
    }
    
    public function ajax_update_cart() {
        check_ajax_referer('nanimade_nonce', 'nonce');
        
        $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity <= 0) {
            WC()->cart->remove_cart_item($cart_item_key);
        } else {
            WC()->cart->set_quantity($cart_item_key, $quantity);
        }
        
        wp_send_json_success(array(
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_total(),
            'cart_html' => $this->get_cart_html(),
        ));
    }
    
    private function get_cart_html() {
        ob_start();
        include NANIMADE_SUITE_PLUGIN_PATH . 'templates/cart/sidebar-cart-content.php';
        return ob_get_clean();
    }
    
    public function update_cart_count() {
        // This will be handled by JavaScript for real-time updates
    }
    
    public function load_textdomain() {
        load_plugin_textdomain(
            'nanimade-suite',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }
    
    public function activate() {
        // Create default settings
        $default_settings = array(
            'mobile_menu_enabled' => true,
            'sidebar_cart_enabled' => true,
            'pwa_enabled' => true,
            'animations_enabled' => true,
            'design_style' => 'pickle-modern',
            'primary_color' => '#ff6b35',
            'secondary_color' => '#28a745',
            'accent_color' => '#ffc107',
        );
        
        add_option('nanimade_suite_settings', $default_settings);
        
        // Create necessary database tables
        $this->create_analytics_tables();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        // Clean up if needed
        flush_rewrite_rules();
    }
    
    private function create_analytics_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'nanimade_analytics';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            event_data longtext,
            user_id bigint(20),
            session_id varchar(100),
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_type (event_type),
            KEY timestamp (timestamp)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Initialize the plugin
function nanimade_suite_init() {
    return NaniMade_Complete_Suite::get_instance();
}

// Start the plugin
nanimade_suite_init();
?>