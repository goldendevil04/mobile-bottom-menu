<?php
/**
 * Plugin Name: Mobile Bottom Menu Pro
 * Plugin URI: https://yoursite.com/plugins/mobile-bottom-menu-pro
 * Description: Professional mobile bottom menu with advanced WooCommerce cart widget, full Elementor integration, and premium customization options.
 * Version: 3.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: mobile-bottom-menu
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MBM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MBM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MBM_VERSION', '3.0.0');

class MobileBottomMenuPro {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Hook into WordPress
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'render_mobile_widgets'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // AJAX handlers
        add_action('wp_ajax_mbm_add_to_cart', array($this, 'ajax_add_to_cart'));
        add_action('wp_ajax_nopriv_mbm_add_to_cart', array($this, 'ajax_add_to_cart'));
        add_action('wp_ajax_mbm_get_cart_count', array($this, 'ajax_get_cart_count'));
        add_action('wp_ajax_nopriv_mbm_get_cart_count', array($this, 'ajax_get_cart_count'));
        add_action('wp_ajax_mbm_get_variation_data', array($this, 'ajax_get_variation_data'));
        add_action('wp_ajax_nopriv_mbm_get_variation_data', array($this, 'ajax_get_variation_data'));
        
        // Elementor integration
        add_action('elementor/widgets/widgets_registered', array($this, 'register_elementor_widgets'));
        add_action('elementor/elements/categories_registered', array($this, 'add_elementor_category'));
        
        // WooCommerce hooks
        if (class_exists('WooCommerce')) {
            add_action('woocommerce_add_to_cart', array($this, 'update_cart_fragments'));
            add_filter('woocommerce_add_to_cart_fragments', array($this, 'cart_count_fragment'));
        }
    }
    
    public function enqueue_scripts() {
        wp_enqueue_script('mbm-script', MBM_PLUGIN_URL . 'assets/js/mobile-bottom-menu.js', array('jquery'), MBM_VERSION, true);
        wp_enqueue_style('mbm-style', MBM_PLUGIN_URL . 'assets/css/mobile-bottom-menu.css', array(), MBM_VERSION);
        
        // Localize script for AJAX
        wp_localize_script('mbm-script', 'mbm_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mbm_nonce'),
            'wc_ajax_url' => class_exists('WooCommerce') ? WC_AJAX::get_endpoint('%%endpoint%%') : '',
            'cart_url' => class_exists('WooCommerce') ? wc_get_cart_url() : '',
            'checkout_url' => class_exists('WooCommerce') ? wc_get_checkout_url() : '',
            'currency_symbol' => class_exists('WooCommerce') ? get_woocommerce_currency_symbol() : '$'
        ));
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Mobile Bottom Menu Pro Settings',
            'Mobile Menu Pro',
            'manage_options',
            'mobile-bottom-menu-pro',
            array($this, 'admin_page')
        );
    }
    
    public function register_settings() {
        register_setting('mbm_settings', 'mbm_options');
        
        add_settings_section(
            'mbm_general_section',
            'General Settings',
            null,
            'mobile-bottom-menu-pro'
        );
        
        // Add settings fields
        $fields = array(
            'enable_mobile_menu' => 'Enable Mobile Menu',
            'enable_mobile_cart' => 'Enable Mobile Cart Widget',
            'enable_animations' => 'Enable Smooth Animations',
            'hide_menu_on_product' => 'Hide Menu on Product Pages',
            'show_cart_on_product_only' => 'Show Cart Widget Only on Product Pages',
            'enable_sticky_widgets' => 'Enable Sticky Positioning',
            'enable_cart_badge' => 'Enable Cart Count Badge'
        );
        
        foreach ($fields as $field => $label) {
            add_settings_field(
                $field,
                $label,
                array($this, 'checkbox_callback'),
                'mobile-bottom-menu-pro',
                'mbm_general_section',
                array('name' => $field)
            );
        }
    }
    
    public function checkbox_callback($args) {
        $options = get_option('mbm_options');
        $value = isset($options[$args['name']]) ? $options[$args['name']] : 1;
        echo '<input type="checkbox" name="mbm_options[' . $args['name'] . ']" value="1" ' . checked(1, $value, false) . ' />';
    }
    
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>Mobile Bottom Menu Pro Settings</h1>
            <div class="mbm-admin-header">
                <p>Configure your professional mobile widgets. For advanced customization, use the Elementor widgets.</p>
            </div>
            
            <div class="mbm-admin-content">
                <div class="mbm-settings-panel">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('mbm_settings');
                        do_settings_sections('mobile-bottom-menu-pro');
                        submit_button('Save Settings', 'primary', 'submit', true, array('class' => 'mbm-save-btn'));
                        ?>
                    </form>
                </div>
                
                <div class="mbm-help-panel">
                    <h2>🎨 Elementor Integration Guide</h2>
                    <div class="mbm-help-steps">
                        <div class="mbm-step">
                            <h3>1. Mobile Menu Widget</h3>
                            <p>• Search for "Mobile Menu Pro" in Elementor</p>
                            <p>• Customize colors, icons, and styles</p>
                            <p>• Add unlimited menu items</p>
                        </div>
                        <div class="mbm-step">
                            <h3>2. Mobile Cart Widget</h3>
                            <p>• Search for "Mobile Cart Pro" in Elementor</p>
                            <p>• Customize price display and badges</p>
                            <p>• Configure quantity selectors</p>
                        </div>
                        <div class="mbm-step">
                            <h3>3. Advanced Features</h3>
                            <p>• Discount badges with custom colors</p>
                            <p>• Variable product support</p>
                            <p>• Sticky positioning options</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .mbm-admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .mbm-admin-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .mbm-settings-panel, .mbm-help-panel {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .mbm-step {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        .mbm-step h3 {
            margin-top: 0;
            color: #667eea;
        }
        .mbm-save-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            border-radius: 6px !important;
            padding: 12px 24px !important;
        }
        </style>
        <?php
    }
    
    public function render_mobile_widgets() {
        $options = get_option('mbm_options');
        $enable_mobile_menu = isset($options['enable_mobile_menu']) ? $options['enable_mobile_menu'] : 1;
        $enable_mobile_cart = isset($options['enable_mobile_cart']) ? $options['enable_mobile_cart'] : 1;
        $hide_menu_on_product = isset($options['hide_menu_on_product']) ? $options['hide_menu_on_product'] : 1;
        $show_cart_on_product_only = isset($options['show_cart_on_product_only']) ? $options['show_cart_on_product_only'] : 1;
        
        // Show mobile menu (everywhere except product pages if setting enabled)
        if ($enable_mobile_menu && (!$hide_menu_on_product || !is_product())) {
            $this->render_default_mobile_menu();
        }
        
        // Show mobile cart widget (only on product pages if setting enabled)
        if ($enable_mobile_cart && class_exists('WooCommerce') && 
            (!$show_cart_on_product_only || is_product())) {
            $this->render_default_mobile_cart();
        }
    }
    
    private function render_default_mobile_menu() {
        $default_items = array(
            array('label' => 'Home', 'icon' => 'fas fa-home', 'url' => home_url()),
            array('label' => 'Shop', 'icon' => 'fas fa-shopping-bag', 'url' => class_exists('WooCommerce') ? wc_get_page_permalink('shop') : '#'),
            array('label' => 'Cart', 'icon' => 'fas fa-shopping-cart', 'url' => class_exists('WooCommerce') ? wc_get_cart_url() : '#'),
            array('label' => 'Account', 'icon' => 'fas fa-user', 'url' => class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : '#'),
        );
        
        ?>
        <div class="mbm-mobile-menu mbm-default-menu mbm-sticky">
            <div class="mbm-menu-container">
                <?php foreach ($default_items as $item): ?>
                <a href="<?php echo esc_url($item['url']); ?>" class="mbm-menu-item">
                    <div class="mbm-icon-wrapper">
                        <i class="<?php echo esc_attr($item['icon']); ?>"></i>
                        <?php if ($item['icon'] === 'fas fa-shopping-cart' && class_exists('WooCommerce')): ?>
                        <span class="mbm-cart-badge"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="mbm-label"><?php echo esc_html($item['label']); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    private function render_default_mobile_cart() {
        if (!is_product()) return;
        
        global $product;
        if (!$product) {
            global $post;
            $product = wc_get_product($post->ID);
        }
        
        if (!$product) return;
        
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        $current_price = $product->get_price();
        $discount_percentage = 0;
        
        if ($sale_price && $regular_price && $regular_price > $sale_price) {
            $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
        }
        
        $currency_symbol = get_woocommerce_currency_symbol();
        
        ?>
        <div class="mbm-mobile-cart mbm-default-cart mbm-sticky" data-product-id="<?php echo $product->get_id(); ?>">
            
            <?php if ($product->is_type('variable')): ?>
            <div class="mbm-variations-container">
                <div class="mbm-variations-header">
                    <h4>Select Options</h4>
                </div>
                <?php
                $attributes = $product->get_variation_attributes();
                foreach ($attributes as $attribute_name => $options): ?>
                    <div class="mbm-variation-group">
                        <label><?php echo wc_attribute_label($attribute_name); ?>:</label>
                        <select name="<?php echo esc_attr($attribute_name); ?>" class="mbm-variation-select" data-attribute="<?php echo esc_attr($attribute_name); ?>">
                            <option value="">Choose <?php echo wc_attribute_label($attribute_name); ?></option>
                            <?php foreach ($options as $option): ?>
                            <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="mbm-cart-main">
                <div class="mbm-price-section">
                    <?php if ($discount_percentage > 0): ?>
                    <div class="mbm-price-box mbm-sale-box">
                        <div class="mbm-discount-badge">
                            <span class="mbm-discount-text"><?php echo $discount_percentage; ?>% OFF</span>
                        </div>
                        <div class="mbm-price-content">
                            <div class="mbm-current-price"><?php echo $currency_symbol . $sale_price; ?></div>
                            <div class="mbm-original-price"><?php echo $currency_symbol . $regular_price; ?></div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="mbm-price-box mbm-regular-box">
                        <div class="mbm-price-content">
                            <div class="mbm-current-price"><?php echo $currency_symbol . $current_price; ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="mbm-actions-section">
                    <div class="mbm-quantity-wrapper">
                        <label class="mbm-qty-label">Qty:</label>
                        <div class="mbm-quantity-selector">
                            <button type="button" class="mbm-qty-btn mbm-qty-minus">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="mbm-quantity" value="1" min="1" max="<?php echo $product->get_stock_quantity() ?: 999; ?>" readonly>
                            <button type="button" class="mbm-qty-btn mbm-qty-plus">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button class="mbm-add-to-cart-btn" data-product-id="<?php echo $product->get_id(); ?>">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="mbm-cart-text">Add to Cart</span>
                        <div class="mbm-loading-spinner"></div>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    // AJAX handlers
    public function ajax_add_to_cart() {
        check_ajax_referer('mbm_nonce', 'nonce');
        
        if (!class_exists('WooCommerce')) {
            wp_send_json_error('WooCommerce not active');
        }
        
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
        $variations = isset($_POST['variations']) ? $_POST['variations'] : array();
        
        // Validate product
        $product = wc_get_product($product_id);
        if (!$product) {
            wp_send_json_error('Invalid product');
        }
        
        // Handle variable products
        if ($product->is_type('variable') && empty($variation_id)) {
            // Find variation ID based on attributes
            $data_store = WC_Data_Store::load('product');
            $variation_id = $data_store->find_matching_product_variation($product, $variations);
            
            if (!$variation_id) {
                wp_send_json_error('Please select all product options');
            }
        }
        
        if ($variation_id) {
            $result = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variations);
        } else {
            $result = WC()->cart->add_to_cart($product_id, $quantity);
        }
        
        if ($result) {
            wp_send_json_success(array(
                'message' => 'Product added to cart successfully!',
                'cart_count' => WC()->cart->get_cart_contents_count(),
                'cart_total' => WC()->cart->get_cart_total(),
                'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array())
            ));
        } else {
            wp_send_json_error('Failed to add product to cart');
        }
    }
    
    public function ajax_get_cart_count() {
        if (class_exists('WooCommerce')) {
            wp_send_json_success(array(
                'cart_count' => WC()->cart->get_cart_contents_count()
            ));
        } else {
            wp_send_json_error('WooCommerce not active');
        }
    }
    
    public function ajax_get_variation_data() {
        check_ajax_referer('mbm_nonce', 'nonce');
        
        if (!class_exists('WooCommerce')) {
            wp_send_json_error('WooCommerce not active');
        }
        
        $product_id = intval($_POST['product_id']);
        $variations = $_POST['variations'];
        
        $product = wc_get_product($product_id);
        if (!$product || !$product->is_type('variable')) {
            wp_send_json_error('Invalid variable product');
        }
        
        $data_store = WC_Data_Store::load('product');
        $variation_id = $data_store->find_matching_product_variation($product, $variations);
        
        if ($variation_id) {
            $variation = wc_get_product($variation_id);
            $regular_price = $variation->get_regular_price();
            $sale_price = $variation->get_sale_price();
            $current_price = $variation->get_price();
            $discount_percentage = 0;
            
            if ($sale_price && $regular_price && $regular_price > $sale_price) {
                $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
            }
            
            wp_send_json_success(array(
                'variation_id' => $variation_id,
                'price' => $current_price,
                'regular_price' => $regular_price,
                'sale_price' => $sale_price,
                'discount_percentage' => $discount_percentage,
                'currency_symbol' => get_woocommerce_currency_symbol(),
                'in_stock' => $variation->is_in_stock(),
                'stock_quantity' => $variation->get_stock_quantity()
            ));
        } else {
            wp_send_json_error('Variation not found');
        }
    }
    
    public function cart_count_fragment($fragments) {
        $fragments['.mbm-cart-badge'] = '<span class="mbm-cart-badge">' . WC()->cart->get_cart_contents_count() . '</span>';
        return $fragments;
    }
    
    // Elementor Integration
    public function add_elementor_category($elements_manager) {
        $elements_manager->add_category(
            'mobile-bottom-menu',
            array(
                'title' => __('Mobile Menu Pro', 'mobile-bottom-menu'),
                'icon' => 'fa fa-mobile-alt',
            )
        );
    }
    
    public function register_elementor_widgets() {
        if (defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')) {
            require_once(MBM_PLUGIN_PATH . 'elementor-widgets/mobile-menu-widget.php');
            require_once(MBM_PLUGIN_PATH . 'elementor-widgets/mobile-cart-widget.php');
            
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \MBM_Mobile_Menu_Widget());
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \MBM_Mobile_Cart_Widget());
        }
    }
}

// Initialize the plugin
new MobileBottomMenuPro();

// Activation hook
register_activation_hook(__FILE__, 'mbm_create_plugin_files');

function mbm_create_plugin_files() {
    // Create assets directory
    $assets_dir = MBM_PLUGIN_PATH . 'assets';
    if (!file_exists($assets_dir)) {
        wp_mkdir_p($assets_dir);
        wp_mkdir_p($assets_dir . '/css');
        wp_mkdir_p($assets_dir . '/js');
    }
    
    // Create elementor widgets directory
    $widgets_dir = MBM_PLUGIN_PATH . 'elementor-widgets';
    if (!file_exists($widgets_dir)) {
        wp_mkdir_p($widgets_dir);
    }
    
    // Create CSS file
    file_put_contents($assets_dir . '/css/mobile-bottom-menu.css', mbm_create_professional_css());
    
    // Create JS file
    file_put_contents($assets_dir . '/js/mobile-bottom-menu.js', mbm_create_professional_js());
}

function mbm_create_professional_css() {
    return '
/* Mobile Bottom Menu Pro - Professional Design System */
:root {
    --mbm-primary: #ff6b35;
    --mbm-primary-dark: #e55a2b;
    --mbm-secondary: #f7931e;
    --mbm-success: #28a745;
    --mbm-success-dark: #218838;
    --mbm-danger: #dc3545;
    --mbm-warning: #ffc107;
    --mbm-info: #17a2b8;
    --mbm-light: #f8f9fa;
    --mbm-dark: #343a40;
    --mbm-white: #ffffff;
    --mbm-black: #000000;
    --mbm-gray-100: #f8f9fa;
    --mbm-gray-200: #e9ecef;
    --mbm-gray-300: #dee2e6;
    --mbm-gray-400: #ced4da;
    --mbm-gray-500: #adb5bd;
    --mbm-gray-600: #6c757d;
    --mbm-gray-700: #495057;
    --mbm-gray-800: #343a40;
    --mbm-gray-900: #212529;
    
    /* Shadows */
    --mbm-shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
    --mbm-shadow: 0 4px 12px rgba(0,0,0,0.15);
    --mbm-shadow-lg: 0 8px 25px rgba(0,0,0,0.15);
    --mbm-shadow-xl: 0 12px 35px rgba(0,0,0,0.2);
    
    /* Border Radius */
    --mbm-radius-sm: 6px;
    --mbm-radius: 12px;
    --mbm-radius-lg: 16px;
    --mbm-radius-xl: 20px;
    
    /* Transitions */
    --mbm-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --mbm-transition-fast: all 0.15s ease;
    --mbm-transition-slow: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    
    /* Typography */
    --mbm-font-weight-normal: 400;
    --mbm-font-weight-medium: 500;
    --mbm-font-weight-semibold: 600;
    --mbm-font-weight-bold: 700;
    
    /* Spacing */
    --mbm-space-1: 4px;
    --mbm-space-2: 8px;
    --mbm-space-3: 12px;
    --mbm-space-4: 16px;
    --mbm-space-5: 20px;
    --mbm-space-6: 24px;
    --mbm-space-8: 32px;
}

/* Base Mobile Menu Styles */
.mbm-mobile-menu {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--mbm-white);
    z-index: 9999;
    box-shadow: var(--mbm-shadow-lg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: var(--mbm-radius-xl) var(--mbm-radius-xl) 0 0;
    margin: 0 var(--mbm-space-2) 0 var(--mbm-space-2);
    border: 1px solid var(--mbm-gray-200);
}

.mbm-mobile-menu.mbm-sticky {
    position: fixed;
}

.mbm-menu-container {
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: var(--mbm-space-4) var(--mbm-space-3) var(--mbm-space-3) var(--mbm-space-3);
    position: relative;
}

.mbm-menu-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: var(--mbm-gray-600);
    padding: var(--mbm-space-3) var(--mbm-space-4);
    transition: var(--mbm-transition);
    flex: 1;
    max-width: 80px;
    border-radius: var(--mbm-radius);
    position: relative;
    overflow: hidden;
    background: transparent;
}

.mbm-menu-item:hover,
.mbm-menu-item:focus {
    color: var(--mbm-primary);
    text-decoration: none;
    transform: translateY(-3px) scale(1.05);
    background: linear-gradient(135deg, rgba(255, 107, 53, 0.1) 0%, rgba(247, 147, 30, 0.1) 100%);
    box-shadow: var(--mbm-shadow);
}

.mbm-menu-item:active {
    transform: translateY(-1px) scale(1.02);
}

.mbm-icon-wrapper {
    position: relative;
    margin-bottom: var(--mbm-space-2);
}

.mbm-menu-item i {
    font-size: 24px;
    transition: var(--mbm-transition);
    display: block;
}

.mbm-menu-item:hover i {
    transform: scale(1.2);
    color: var(--mbm-primary);
}

.mbm-label {
    font-size: 11px;
    text-align: center;
    line-height: 1.2;
    font-weight: var(--mbm-font-weight-semibold);
    transition: var(--mbm-transition);
    margin: 0;
}

.mbm-cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: linear-gradient(135deg, var(--mbm-danger) 0%, #c82333 100%);
    color: var(--mbm-white);
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: var(--mbm-font-weight-bold);
    box-shadow: var(--mbm-shadow);
    border: 2px solid var(--mbm-white);
    animation: mbm-pulse 2s infinite;
}

/* Mobile Cart Widget Styles */
.mbm-mobile-cart {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--mbm-white);
    z-index: 9999;
    box-shadow: var(--mbm-shadow-xl);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: var(--mbm-radius-xl) var(--mbm-radius-xl) 0 0;
    border: 1px solid var(--mbm-gray-200);
    border-bottom: none;
}

.mbm-mobile-cart.mbm-sticky {
    position: fixed;
}

/* Variations Container */
.mbm-variations-container {
    background: linear-gradient(135deg, var(--mbm-gray-100) 0%, var(--mbm-gray-200) 100%);
    padding: var(--mbm-space-4);
    border-bottom: 1px solid var(--mbm-gray-300);
    border-radius: var(--mbm-radius-xl) var(--mbm-radius-xl) 0 0;
}

.mbm-variations-header {
    margin-bottom: var(--mbm-space-3);
}

.mbm-variations-header h4 {
    margin: 0;
    font-size: 14px;
    font-weight: var(--mbm-font-weight-semibold);
    color: var(--mbm-gray-800);
}

.mbm-variation-group {
    margin-bottom: var(--mbm-space-3);
}

.mbm-variation-group:last-child {
    margin-bottom: 0;
}

.mbm-variation-group label {
    display: block;
    font-size: 13px;
    color: var(--mbm-gray-700);
    margin-bottom: var(--mbm-space-2);
    font-weight: var(--mbm-font-weight-semibold);
}

.mbm-variation-select {
    width: 100%;
    padding: var(--mbm-space-3) var(--mbm-space-4);
    border: 2px solid var(--mbm-gray-300);
    border-radius: var(--mbm-radius);
    background: var(--mbm-white);
    font-size: 14px;
    color: var(--mbm-gray-800);
    transition: var(--mbm-transition);
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3e%3cpath stroke=\'%236b7280\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/%3e%3c/svg%3e");
    background-position: right var(--mbm-space-3) center;
    background-repeat: no-repeat;
    background-size: 16px;
    padding-right: 40px;
    font-weight: var(--mbm-font-weight-medium);
}

.mbm-variation-select:focus {
    border-color: var(--mbm-primary);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    outline: none;
}

.mbm-variation-select.error {
    border-color: var(--mbm-danger);
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

/* Cart Main Section */
.mbm-cart-main {
    padding: var(--mbm-space-4);
    background: var(--mbm-white);
}

/* Price Section */
.mbm-price-section {
    margin-bottom: var(--mbm-space-4);
}

.mbm-price-box {
    position: relative;
    border-radius: var(--mbm-radius-lg);
    padding: var(--mbm-space-4);
    box-shadow: var(--mbm-shadow);
    overflow: hidden;
}

.mbm-price-box.mbm-sale-box {
    background: linear-gradient(135deg, var(--mbm-primary) 0%, var(--mbm-secondary) 100%);
    color: var(--mbm-white);
}

.mbm-price-box.mbm-regular-box {
    background: linear-gradient(135deg, var(--mbm-gray-100) 0%, var(--mbm-gray-200) 100%);
    color: var(--mbm-gray-800);
    border: 2px solid var(--mbm-primary);
}

.mbm-discount-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: linear-gradient(135deg, var(--mbm-danger) 0%, #c82333 100%);
    color: var(--mbm-white);
    padding: var(--mbm-space-2) var(--mbm-space-3);
    border-radius: 0 var(--mbm-radius-lg) 0 var(--mbm-radius);
    font-size: 11px;
    font-weight: var(--mbm-font-weight-bold);
    box-shadow: var(--mbm-shadow);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.mbm-discount-text {
    display: block;
}

.mbm-price-content {
    display: flex;
    flex-direction: column;
    gap: var(--mbm-space-1);
}

.mbm-current-price {
    font-size: 20px;
    font-weight: var(--mbm-font-weight-bold);
    line-height: 1.2;
}

.mbm-original-price {
    font-size: 14px;
    text-decoration: line-through;
    opacity: 0.8;
    font-weight: var(--mbm-font-weight-medium);
}

/* Actions Section */
.mbm-actions-section {
    display: flex;
    align-items: center;
    gap: var(--mbm-space-3);
}

.mbm-quantity-wrapper {
    display: flex;
    flex-direction: column;
    gap: var(--mbm-space-2);
}

.mbm-qty-label {
    font-size: 12px;
    font-weight: var(--mbm-font-weight-semibold);
    color: var(--mbm-gray-700);
    margin: 0;
}

.mbm-quantity-selector {
    display: flex;
    align-items: center;
    border: 2px solid var(--mbm-gray-300);
    border-radius: var(--mbm-radius);
    background: var(--mbm-white);
    overflow: hidden;
    box-shadow: var(--mbm-shadow-sm);
}

.mbm-qty-btn {
    background: var(--mbm-gray-100);
    border: none;
    padding: var(--mbm-space-3);
    cursor: pointer;
    font-weight: var(--mbm-font-weight-bold);
    font-size: 14px;
    color: var(--mbm-gray-700);
    transition: var(--mbm-transition);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    min-width: 44px;
}

.mbm-qty-btn:hover {
    background: var(--mbm-primary);
    color: var(--mbm-white);
    transform: scale(1.05);
}

.mbm-qty-btn:active {
    transform: scale(0.95);
}

.mbm-qty-btn:disabled {
    background: var(--mbm-gray-200);
    color: var(--mbm-gray-400);
    cursor: not-allowed;
    transform: none;
}

.mbm-quantity {
    border: none;
    padding: var(--mbm-space-3) var(--mbm-space-2);
    width: 60px;
    text-align: center;
    background: var(--mbm-white);
    font-size: 16px;
    font-weight: var(--mbm-font-weight-bold);
    color: var(--mbm-gray-800);
    min-height: 44px;
    box-sizing: border-box;
}

/* Add to Cart Button */
.mbm-add-to-cart-btn {
    background: linear-gradient(135deg, var(--mbm-success) 0%, var(--mbm-success-dark) 100%);
    color: var(--mbm-white);
    border: none;
    padding: var(--mbm-space-3) var(--mbm-space-5);
    border-radius: var(--mbm-radius);
    font-weight: var(--mbm-font-weight-bold);
    font-size: 14px;
    cursor: pointer;
    transition: var(--mbm-transition);
    display: flex;
    align-items: center;
    gap: var(--mbm-space-2);
    min-height: 48px;
    box-shadow: var(--mbm-shadow);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    flex: 1;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.mbm-add-to-cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--mbm-shadow-lg);
    background: linear-gradient(135deg, var(--mbm-success-dark) 0%, #1e7e34 100%);
}

.mbm-add-to-cart-btn:active {
    transform: translateY(0);
}

.mbm-add-to-cart-btn:disabled {
    background: var(--mbm-gray-500);
    cursor: not-allowed;
    transform: none;
    box-shadow: var(--mbm-shadow-sm);
}

.mbm-add-to-cart-btn.loading {
    pointer-events: none;
}

.mbm-add-to-cart-btn.loading .mbm-cart-text {
    opacity: 0;
}

.mbm-add-to-cart-btn.loading .mbm-loading-spinner {
    opacity: 1;
}

.mbm-add-to-cart-btn.success {
    background: linear-gradient(135deg, var(--mbm-success) 0%, #20c997 100%);
}

.mbm-add-to-cart-btn.error {
    background: linear-gradient(135deg, var(--mbm-danger) 0%, #c82333 100%);
}

.mbm-add-to-cart-btn i {
    font-size: 16px;
}

.mbm-loading-spinner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: var(--mbm-white);
    animation: mbm-spin 1s ease-in-out infinite;
    opacity: 0;
    transition: var(--mbm-transition);
}

/* Animations */
@keyframes mbm-spin {
    to {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

@keyframes mbm-pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

@keyframes mbm-bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        transform: translate3d(0, -8px, 0);
    }
    70% {
        transform: translate3d(0, -4px, 0);
    }
    90% {
        transform: translate3d(0, -2px, 0);
    }
}

@keyframes mbm-slideUp {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.mbm-mobile-menu,
.mbm-mobile-cart {
    animation: mbm-slideUp 0.3s ease-out;
}

/* Notification System */
.mbm-notification {
    position: fixed;
    top: var(--mbm-space-5);
    right: var(--mbm-space-5);
    padding: var(--mbm-space-4) var(--mbm-space-6);
    border-radius: var(--mbm-radius);
    color: var(--mbm-white);
    font-weight: var(--mbm-font-weight-semibold);
    z-index: 10000;
    transform: translateX(100%);
    transition: var(--mbm-transition);
    box-shadow: var(--mbm-shadow-lg);
    display: flex;
    align-items: center;
    gap: var(--mbm-space-2);
    max-width: 300px;
}

.mbm-notification.show {
    transform: translateX(0);
}

.mbm-notification.mbm-success {
    background: linear-gradient(135deg, var(--mbm-success) 0%, #20c997 100%);
}

.mbm-notification.mbm-error {
    background: linear-gradient(135deg, var(--mbm-danger) 0%, #c82333 100%);
}

.mbm-notification.mbm-info {
    background: linear-gradient(135deg, var(--mbm-info) 0%, #138496 100%);
}

/* Body Padding for Fixed Elements */
body.mbm-menu-active {
    padding-bottom: 90px;
}

body.mbm-cart-active {
    padding-bottom: 140px;
}

/* Elementor Widget Styles */
.elementor-widget-mbm-mobile-menu .mbm-mobile-menu,
.elementor-widget-mbm-mobile-cart .mbm-mobile-cart {
    position: relative !important;
    margin: var(--mbm-space-5) 0;
    box-shadow: var(--mbm-shadow-lg);
    animation: none;
}

/* Responsive Design */
@media (max-width: 480px) {
    .mbm-mobile-menu,
    .mbm-mobile-cart {
        margin: 0;
        border-radius: 0;
        border-left: none;
        border-right: none;
    }
    
    .mbm-menu-item i {
        font-size: 20px;
    }
    
    .mbm-label {
        font-size: 10px;
    }
    
    .mbm-actions-section {
        gap: var(--mbm-space-2);
    }
    
    .mbm-add-to-cart-btn {
        padding: var(--mbm-space-3) var(--mbm-space-4);
        font-size: 12px;
    }
    
    .mbm-quantity-selector {
        min-width: auto;
    }
    
    .mbm-qty-btn {
        min-width: 40px;
        min-height: 40px;
        padding: var(--mbm-space-2);
    }
    
    .mbm-quantity {
        width: 50px;
        min-height: 40px;
    }
    
    .mbm-current-price {
        font-size: 18px;
    }
    
    .mbm-notification {
        top: var(--mbm-space-3);
        right: var(--mbm-space-3);
        left: var(--mbm-space-3);
        max-width: none;
        transform: translateY(-100%);
    }
    
    .mbm-notification.show {
        transform: translateY(0);
    }
}

@media (max-width: 360px) {
    .mbm-menu-container {
        padding: var(--mbm-space-3) var(--mbm-space-2) var(--mbm-space-2) var(--mbm-space-2);
    }
    
    .mbm-menu-item {
        padding: var(--mbm-space-2) var(--mbm-space-3);
        max-width: 70px;
    }
    
    .mbm-actions-section {
        flex-direction: column;
        gap: var(--mbm-space-3);
    }
    
    .mbm-quantity-wrapper,
    .mbm-add-to-cart-btn {
        width: 100%;
    }
    
    .mbm-quantity-wrapper {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
    
    body.mbm-cart-active {
        padding-bottom: 160px;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    :root {
        --mbm-white: #1a1a1a;
        --mbm-light: #2a2a2a;
        --mbm-gray-100: #2a2a2a;
        --mbm-gray-200: #3a3a3a;
        --mbm-gray-300: #4a4a4a;
        --mbm-dark: #e0e0e0;
        --mbm-gray-800: #e0e0e0;
        --mbm-gray-700: #d0d0d0;
        --mbm-shadow: 0 4px 12px rgba(0,0,0,0.3);
        --mbm-shadow-lg: 0 8px 25px rgba(0,0,0,0.4);
        --mbm-shadow-xl: 0 12px 35px rgba(0,0,0,0.5);
    }
    
    .mbm-variations-container {
        background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
    }
    
    .mbm-variation-select {
        background: #333;
        color: #e0e0e0;
        border-color: #444;
    }
    
    .mbm-quantity {
        background: #333;
        color: #e0e0e0;
    }
    
    .mbm-qty-btn {
        background: #2a2a2a;
        color: #e0e0e0;
    }
    
    .mbm-price-box.mbm-regular-box {
        background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
        color: #e0e0e0;
    }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    .mbm-menu-item:hover,
    .mbm-menu-item:focus {
        background: var(--mbm-primary);
        color: var(--mbm-white);
    }
    
    .mbm-add-to-cart-btn {
        border: 2px solid var(--mbm-success-dark);
    }
    
    .mbm-variation-select:focus {
        border-width: 3px;
    }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    
    .mbm-menu-item:hover {
        transform: none;
    }
    
    .mbm-add-to-cart-btn:hover {
        transform: none;
    }
}

/* Print Styles */
@media print {
    .mbm-mobile-menu,
    .mbm-mobile-cart {
        display: none !important;
    }
}
';
}

function mbm_create_professional_js() {
    return "
jQuery(document).ready(function($) {
    'use strict';
    
    // Initialize mobile widgets
    const MBM = {
        init: function() {
            this.initializeWidgets();
            this.bindEvents();
            this.updateCartCount();
        },
        
        initializeWidgets: function() {
            // Add body classes for spacing
            if ($('.mbm-mobile-menu').length && !$('.mbm-mobile-menu').hasClass('elementor-widget')) {
                $('body').addClass('mbm-menu-active');
            }
            
            if ($('.mbm-mobile-cart').length && !$('.mbm-mobile-cart').hasClass('elementor-widget')) {
                $('body').addClass('mbm-cart-active');
            }
            
            // Initialize quantity selectors
            this.initializeQuantitySelectors();
            
            // Initialize add to cart functionality
            this.initializeAddToCart();
            
            // Initialize variation handling
            this.initializeVariations();
        },
        
        bindEvents: function() {
            // Menu item click animations
            $(document).on('click', '.mbm-menu-item', this.animateMenuItem);
            
            // Handle window resize
            $(window).on('resize', this.handleResize.bind(this));
            
            // Handle page visibility change
            $(document).on('visibilitychange', this.handleVisibilityChange.bind(this));
        },
        
        initializeQuantitySelectors: function() {
            // Quantity plus button
            $(document).on('click', '.mbm-qty-plus', function(e) {
                e.preventDefault();
                const input = $(this).siblings('.mbm-quantity');
                const currentVal = parseInt(input.val()) || 1;
                const maxVal = parseInt(input.attr('max')) || 999;
                
                if (currentVal < maxVal) {
                    input.val(currentVal + 1).trigger('change');
                    $(this).addClass('mbm-animated');
                    setTimeout(() => $(this).removeClass('mbm-animated'), 300);
                }
            });
            
            // Quantity minus button
            $(document).on('click', '.mbm-qty-minus', function(e) {
                e.preventDefault();
                const input = $(this).siblings('.mbm-quantity');
                const currentVal = parseInt(input.val()) || 1;
                const minVal = parseInt(input.attr('min')) || 1;
                
                if (currentVal > minVal) {
                    input.val(currentVal - 1).trigger('change');
                    $(this).addClass('mbm-animated');
                    setTimeout(() => $(this).removeClass('mbm-animated'), 300);
                }
            });
            
            // Quantity input change
            $(document).on('change', '.mbm-quantity', function() {
                const value = parseInt($(this).val()) || 1;
                const min = parseInt($(this).attr('min')) || 1;
                const max = parseInt($(this).attr('max')) || 999;
                
                if (value < min) $(this).val(min);
                if (value > max) $(this).val(max);
            });
        },
        
        initializeAddToCart: function() {
            $(document).on('click', '.mbm-add-to-cart-btn', function(e) {
                e.preventDefault();
                
                const button = $(this);
                const productId = button.data('product-id');
                const quantity = $('.mbm-quantity').val() || 1;
                const variations = {};
                let variationId = 0;
                
                // Get variation data
                $('.mbm-variation-select').each(function() {
                    const name = $(this).attr('name');
                    const value = $(this).val();
                    if (value) {
                        variations[name] = value;
                    }
                });
                
                // Validate variations
                if ($('.mbm-variation-select').length > 0) {
                    let allSelected = true;
                    $('.mbm-variation-select').each(function() {
                        if (!$(this).val()) {
                            allSelected = false;
                            $(this).addClass('error').focus();
                            return false;
                        } else {
                            $(this).removeClass('error');
                        }
                    });
                    
                    if (!allSelected) {
                        MBM.showNotification('Please select all product options', 'error');
                        return;
                    }
                }
                
                // Set loading state
                MBM.setButtonLoading(button, true);
                
                // AJAX request
                $.ajax({
                    url: mbm_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'mbm_add_to_cart',
                        product_id: productId,
                        quantity: quantity,
                        variation_id: variationId,
                        variations: variations,
                        nonce: mbm_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            MBM.handleAddToCartSuccess(button, response.data);
                        } else {
                            MBM.handleAddToCartError(button, response.data || 'Failed to add product to cart');
                        }
                    },
                    error: function(xhr, status, error) {
                        MBM.handleAddToCartError(button, 'Network error occurred');
                    }
                });
            });
        },
        
        initializeVariations: function() {
            $(document).on('change', '.mbm-variation-select', function() {
                const container = $(this).closest('.mbm-mobile-cart');
                const productId = container.data('product-id');
                const variations = {};
                let allSelected = true;
                
                $('.mbm-variation-select').each(function() {
                    const value = $(this).val();
                    if (!value) {
                        allSelected = false;
                    } else {
                        variations[$(this).attr('name')] = value;
                    }
                });
                
                if (allSelected && productId) {
                    MBM.updateVariationPrice(productId, variations);
                }
                
                // Enable/disable add to cart button
                $('.mbm-add-to-cart-btn').prop('disabled', !allSelected);
            });
        },
        
        updateVariationPrice: function(productId, variations) {
            $.ajax({
                url: mbm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'mbm_get_variation_data',
                    product_id: productId,
                    variations: variations,
                    nonce: mbm_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        MBM.updatePriceDisplay(data);
                        
                        // Update stock quantity
                        if (data.stock_quantity) {
                            $('.mbm-quantity').attr('max', data.stock_quantity);
                        }
                    }
                },
                error: function() {
                    console.log('Failed to get variation data');
                }
            });
        },
        
        updatePriceDisplay: function(data) {
            const priceSection = $('.mbm-price-section');
            const currencySymbol = data.currency_symbol || mbm_ajax.currency_symbol;
            
            if (data.sale_price && data.regular_price && data.regular_price > data.sale_price) {
                // Sale price display
                const discountPercentage = data.discount_percentage;
                priceSection.html(`
                    <div class=\"mbm-price-box mbm-sale-box\">
                        <div class=\"mbm-discount-badge\">
                            <span class=\"mbm-discount-text\">${discountPercentage}% OFF</span>
                        </div>
                        <div class=\"mbm-price-content\">
                            <div class=\"mbm-current-price\">${currencySymbol}${data.sale_price}</div>
                            <div class=\"mbm-original-price\">${currencySymbol}${data.regular_price}</div>
                        </div>
                    </div>
                `);
            } else {
                // Regular price display
                priceSection.html(`
                    <div class=\"mbm-price-box mbm-regular-box\">
                        <div class=\"mbm-price-content\">
                            <div class=\"mbm-current-price\">${currencySymbol}${data.price}</div>
                        </div>
                    </div>
                `);
            }
        },
        
        setButtonLoading: function(button, loading) {
            if (loading) {
                button.prop('disabled', true).addClass('loading');
                const originalText = button.find('.mbm-cart-text').text();
                button.data('original-text', originalText);
                button.find('.mbm-cart-text').text('Adding...');
            } else {
                button.prop('disabled', false).removeClass('loading success error');
                const originalText = button.data('original-text') || 'Add to Cart';
                button.find('.mbm-cart-text').text(originalText);
            }
        },
        
        handleAddToCartSuccess: function(button, data) {
            // Success state
            button.removeClass('loading').addClass('success');
            button.find('.mbm-cart-text').text('Added!');
            
            // Update cart count
            this.updateCartCount();
            
            // Show success notification
            this.showNotification(data.message || 'Product added to cart successfully!', 'success');
            
            // Reset button after delay
            setTimeout(() => {
                this.setButtonLoading(button, false);
            }, 2000);
        },
        
        handleAddToCartError: function(button, message) {
            this.setButtonLoading(button, false);
            button.addClass('error');
            
            this.showNotification(message, 'error');
            
            setTimeout(() => {
                button.removeClass('error');
            }, 3000);
        },
        
        updateCartCount: function() {
            if (typeof mbm_ajax !== 'undefined') {
                $.ajax({
                    url: mbm_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'mbm_get_cart_count',
                        nonce: mbm_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            const count = response.data.cart_count;
                            $('.mbm-cart-badge').text(count);
                            
                            // Show/hide cart count badge
                            if (count > 0) {
                                $('.mbm-cart-badge').show();
                            } else {
                                $('.mbm-cart-badge').hide();
                            }
                        }
                    }
                });
            }
        },
        
        showNotification: function(message, type) {
            // Remove existing notifications
            $('.mbm-notification').remove();
            
            const icon = this.getNotificationIcon(type);
            const notification = $(`
                <div class=\"mbm-notification mbm-${type}\">
                    <i class=\"fas fa-${icon}\"></i>
                    <span>${message}</span>
                </div>
            `);
            
            $('body').append(notification);
            
            // Show notification
            setTimeout(() => {
                notification.addClass('show');
            }, 100);
            
            // Hide notification
            setTimeout(() => {
                notification.removeClass('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 4000);
        },
        
        getNotificationIcon: function(type) {
            const icons = {
                'success': 'check-circle',
                'error': 'exclamation-circle',
                'info': 'info-circle',
                'warning': 'exclamation-triangle'
            };
            return icons[type] || 'bell';
        },
        
        animateMenuItem: function() {
            $(this).addClass('mbm-animated');
            setTimeout(() => $(this).removeClass('mbm-animated'), 600);
        },
        
        handleResize: function() {
            // Reinitialize if needed
            if ($('.mbm-mobile-menu').length && !$('body').hasClass('mbm-menu-active')) {
                $('body').addClass('mbm-menu-active');
            }
            
            if ($('.mbm-mobile-cart').length && !$('body').hasClass('mbm-cart-active')) {
                $('body').addClass('mbm-cart-active');
            }
        },
        
        handleVisibilityChange: function() {
            if (!document.hidden) {
                // Page became visible, update cart count
                this.updateCartCount();
            }
        }
    };
    
    // Initialize the plugin
    MBM.init();
    
    // Initialize on page load
    $(window).on('load', function() {
        MBM.updateCartCount();
    });
    
    // Handle WooCommerce cart fragments
    $(document.body).on('updated_wc_div', function() {
        MBM.updateCartCount();
    });
    
    // Handle AJAX cart updates
    $(document.body).on('added_to_cart', function(event, fragments, cart_hash) {
        MBM.updateCartCount();
    });
});
";
}
?>