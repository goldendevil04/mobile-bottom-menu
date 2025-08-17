<?php
/**
 * Plugin Name: Mobile Bottom Menu Pro
 * Plugin URI: https://yoursite.com/plugins/mobile-bottom-menu-pro
 * Description: A comprehensive mobile bottom menu plugin with WooCommerce support, animated icons, and sticky product actions for mobile devices.
 * Version: 1.0.0
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
define('MBM_VERSION', '1.0.0');

class MobileBottomMenuPro {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Hook into WordPress
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'render_bottom_menu'));
        add_action('wp_footer', array($this, 'render_product_sticky_bar'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_ajax_mbm_add_to_cart', array($this, 'ajax_add_to_cart'));
        add_action('wp_ajax_nopriv_mbm_add_to_cart', array($this, 'ajax_add_to_cart'));
        
        // WooCommerce hooks
        if (class_exists('WooCommerce')) {
            add_action('woocommerce_single_product_summary', array($this, 'hide_default_add_to_cart'), 1);
        }
    }
    
    public function enqueue_scripts() {
        wp_enqueue_script('mbm-script', MBM_PLUGIN_URL . 'assets/js/mobile-bottom-menu.js', array('jquery'), MBM_VERSION, true);
        wp_enqueue_style('mbm-style', MBM_PLUGIN_URL . 'assets/css/mobile-bottom-menu.css', array(), MBM_VERSION);
        
        // Localize script for AJAX
        wp_localize_script('mbm-script', 'mbm_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mbm_nonce'),
            'wc_ajax_url' => class_exists('WooCommerce') ? WC_AJAX::get_endpoint('%%endpoint%%') : ''
        ));
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Mobile Bottom Menu Settings',
            'Mobile Bottom Menu',
            'manage_options',
            'mobile-bottom-menu',
            array($this, 'admin_page')
        );
    }
    
    public function register_settings() {
        register_setting('mbm_settings', 'mbm_options');
        
        add_settings_section(
            'mbm_general_section',
            'General Settings',
            null,
            'mobile-bottom-menu'
        );
        
        add_settings_field(
            'enable_animations',
            'Enable Icon Animations',
            array($this, 'checkbox_callback'),
            'mobile-bottom-menu',
            'mbm_general_section',
            array('name' => 'enable_animations')
        );
        
        add_settings_field(
            'enable_woocommerce',
            'Enable WooCommerce Features',
            array($this, 'checkbox_callback'),
            'mobile-bottom-menu',
            'mbm_general_section',
            array('name' => 'enable_woocommerce')
        );
        
        add_settings_field(
            'menu_items',
            'Menu Items',
            array($this, 'menu_items_callback'),
            'mobile-bottom-menu',
            'mbm_general_section'
        );
    }
    
    public function checkbox_callback($args) {
        $options = get_option('mbm_options');
        $value = isset($options[$args['name']]) ? $options[$args['name']] : 0;
        echo '<input type="checkbox" name="mbm_options[' . $args['name'] . ']" value="1" ' . checked(1, $value, false) . ' />';
    }
    
    public function menu_items_callback() {
        $options = get_option('mbm_options');
        $menu_items = isset($options['menu_items']) ? $options['menu_items'] : array();
        ?>
        <div id="mbm-menu-items">
            <?php foreach ($menu_items as $index => $item): ?>
            <div class="mbm-menu-item" data-index="<?php echo $index; ?>">
                <input type="text" name="mbm_options[menu_items][<?php echo $index; ?>][label]" 
                       placeholder="Label" value="<?php echo esc_attr($item['label']); ?>" />
                <input type="text" name="mbm_options[menu_items][<?php echo $index; ?>][icon]" 
                       placeholder="Icon Class (e.g., fas fa-home)" value="<?php echo esc_attr($item['icon']); ?>" />
                <input type="url" name="mbm_options[menu_items][<?php echo $index; ?>][url]" 
                       placeholder="URL" value="<?php echo esc_attr($item['url']); ?>" />
                <button type="button" class="button mbm-remove-item">Remove</button>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="mbm-add-item" class="button">Add Menu Item</button>
        <script>
        jQuery(document).ready(function($) {
            var itemIndex = <?php echo count($menu_items); ?>;
            
            $('#mbm-add-item').click(function() {
                var html = '<div class="mbm-menu-item" data-index="' + itemIndex + '">' +
                    '<input type="text" name="mbm_options[menu_items][' + itemIndex + '][label]" placeholder="Label" />' +
                    '<input type="text" name="mbm_options[menu_items][' + itemIndex + '][icon]" placeholder="Icon Class" />' +
                    '<input type="url" name="mbm_options[menu_items][' + itemIndex + '][url]" placeholder="URL" />' +
                    '<button type="button" class="button mbm-remove-item">Remove</button>' +
                    '</div>';
                $('#mbm-menu-items').append(html);
                itemIndex++;
            });
            
            $(document).on('click', '.mbm-remove-item', function() {
                $(this).parent().remove();
            });
        });
        </script>
        <?php
    }
    
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>Mobile Bottom Menu Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('mbm_settings');
                do_settings_sections('mobile-bottom-menu');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    public function render_bottom_menu() {
        // Only show on mobile devices and NOT on single product pages
        if (is_product()) {
            return; // Don't show bottom menu on product pages
        }
        
        $options = get_option('mbm_options');
        $enable_animations = isset($options['enable_animations']) ? $options['enable_animations'] : 0;
        $menu_items = isset($options['menu_items']) ? $options['menu_items'] : array();
        
        if (!empty($menu_items)) {
            ?>
            <div id="mbm-bottom-menu" class="mbm-bottom-menu <?php echo $enable_animations ? 'mbm-animated' : ''; ?>">
                <div class="mbm-menu-container">
                    <?php foreach ($menu_items as $item): ?>
                    <a href="<?php echo esc_url($item['url']); ?>" class="mbm-menu-item">
                        <i class="<?php echo esc_attr($item['icon']); ?>"></i>
                        <span class="mbm-label"><?php echo esc_html($item['label']); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
        }
    }
    
    public function render_product_sticky_bar() {
        // Debug: Always show on single product pages for now
        if (!is_product()) {
            return;
        }
        
        // Force enable WooCommerce features if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        global $product;
        if (!$product) {
            global $post;
            $product = wc_get_product($post->ID);
        }
        
        if (!$product) return;
        
        ?>
        <!-- MBM Debug Info -->
        <!-- Product ID: <?php echo $product->get_id(); ?> -->
        <!-- Product Type: <?php echo $product->get_type(); ?> -->
        <!-- Is Product Page: <?php echo is_product() ? 'Yes' : 'No'; ?> -->
        
        <div id="mbm-product-sticky" class="mbm-product-sticky">
            <?php if ($product->is_type('variable')): ?>
                <div class="mbm-variations-container">
                    <?php
                    $attributes = $product->get_variation_attributes();
                    foreach ($attributes as $attribute_name => $options): ?>
                        <div class="mbm-variation-group">
                            <label><?php echo wc_attribute_label($attribute_name); ?>:</label>
                            <select name="<?php echo esc_attr($attribute_name); ?>" class="mbm-variation-select">
                                <option value="">Choose <?php echo wc_attribute_label($attribute_name); ?></option>
                                <?php foreach ($options as $option): ?>
                                <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="mbm-sticky-actions">
                <div class="mbm-quantity-selector">
                    <button type="button" class="mbm-qty-btn mbm-qty-minus">−</button>
                    <input type="number" class="mbm-quantity" value="1" min="1" max="<?php echo $product->get_stock_quantity() ?: 999; ?>" readonly>
                    <button type="button" class="mbm-qty-btn mbm-qty-plus">+</button>
                </div>
                
                <button class="mbm-add-to-cart-btn" data-product-id="<?php echo $product->get_id(); ?>">
                    <span class="mbm-cart-text">Add to Cart</span>
                </button>
            </div>
        </div>
        <?php
    }
    
    public function ajax_add_to_cart() {
        check_ajax_referer('mbm_nonce', 'nonce');
        
        if (!class_exists('WooCommerce')) {
            wp_die();
        }
        
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
                'message' => 'Product added to cart successfully',
                'cart_count' => WC()->cart->get_cart_contents_count()
            ));
        } else {
            wp_send_json_error('Failed to add product to cart');
        }
    }
    
    public function hide_default_add_to_cart() {
        if (wp_is_mobile()) {
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        }
    }
}

// Initialize the plugin
new MobileBottomMenuPro();

// Create CSS file content
function mbm_create_css_file() {
    $css_content = '
/* Mobile Bottom Menu Styles */
.mbm-bottom-menu {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #e0e0e0;
    z-index: 9999;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}

.mbm-menu-container {
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 8px 0;
    max-width: 100%;
}

.mbm-menu-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #666;
    padding: 5px 10px;
    transition: all 0.3s ease;
    flex: 1;
    max-width: 80px;
}

.mbm-menu-item:hover {
    color: #007cba;
    text-decoration: none;
}

.mbm-menu-item i {
    font-size: 20px;
    margin-bottom: 4px;
}

.mbm-label {
    font-size: 11px;
    text-align: center;
    line-height: 1.2;
}

/* Animations */
.mbm-animated .mbm-menu-item {
    transition: transform 0.2s ease;
}

.mbm-animated .mbm-menu-item:active {
    transform: scale(0.95);
}

.mbm-animated .mbm-menu-item i {
    transition: transform 0.3s ease;
}

.mbm-animated .mbm-menu-item:hover i {
    transform: translateY(-2px);
}

/* Product Sticky Bar - Matching your design */
.mbm-product-sticky {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #e0e0e0;
    padding: 0;
    z-index: 9999;
    box-shadow: 0 -2px 15px rgba(0,0,0,0.1);
}

.mbm-variations-container {
    background: #f8f9fa;
    padding: 10px 15px;
    border-bottom: 1px solid #e0e0e0;
}

.mbm-variation-group {
    margin-bottom: 8px;
}

.mbm-variation-group:last-child {
    margin-bottom: 0;
}

.mbm-variation-group label {
    display: block;
    font-size: 12px;
    color: #666;
    margin-bottom: 4px;
    font-weight: 500;
}

.mbm-variation-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #fff;
    font-size: 14px;
    color: #333;
}

.mbm-sticky-actions {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    gap: 12px;
}

.mbm-quantity-selector {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #fff;
    overflow: hidden;
    min-width: 120px;
}

.mbm-qty-btn {
    background: #f8f9fa;
    border: none;
    padding: 12px 16px;
    cursor: pointer;
    font-weight: bold;
    font-size: 18px;
    color: #333;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
}

.mbm-qty-btn:hover {
    background: #e9ecef;
}

.mbm-qty-btn:active {
    background: #dee2e6;
    transform: scale(0.95);
}

.mbm-quantity {
    border: none;
    padding: 12px 8px;
    width: 50px;
    text-align: center;
    background: #fff;
    font-size: 16px;
    font-weight: 600;
    color: #333;
    min-height: 48px;
    box-sizing: border-box;
}

.mbm-add-to-cart-btn {
    flex: 1;
    padding: 14px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #dc3545;
    color: #fff;
    min-height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-transform: none;
}

.mbm-add-to-cart-btn:hover {
    background: #c82333;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.mbm-add-to-cart-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
}

.mbm-add-to-cart-btn:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.mbm-cart-text {
    font-size: 16px;
    font-weight: 600;
}

/* Add bottom padding to body when menu is active */
body.mbm-active {
    padding-bottom: 70px;
}

body.mbm-product-active {
    padding-bottom: 80px;
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .mbm-menu-item i {
        font-size: 18px;
    }
    
    .mbm-label {
        font-size: 10px;
    }
    
    .mbm-product-actions {
        gap: 5px;
    }
    
    .mbm-add-to-cart, .mbm-buy-now {
        padding: 10px 15px;
        font-size: 14px;
    }
}

/* Admin Styles */
.mbm-menu-item {
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.mbm-menu-item input {
    margin-right: 10px;
    margin-bottom: 5px;
}
';

    return $css_content;
}

// Create JavaScript file content
function mbm_create_js_file() {
    $js_content = "
jQuery(document).ready(function($) {
    // Initialize mobile bottom menu
    if ($('.mbm-bottom-menu').length) {
        $('body').addClass('mbm-active');
    }
    
    if ($('.mbm-product-sticky').length) {
        $('body').addClass('mbm-product-active');
        console.log('Product sticky bar detected and activated');
    }
    
    // Debug: Log if we're on a product page
    if ($('body').hasClass('single-product')) {
        console.log('On single product page');
        console.log('Sticky bar element exists:', $('.mbm-product-sticky').length > 0);
    }
    
    // Quantity selector functionality
    $('.mbm-qty-plus').click(function() {
        var input = $(this).siblings('.mbm-quantity');
        var currentVal = parseInt(input.val());
        var maxVal = parseInt(input.attr('max'));
        
        if (currentVal < maxVal) {
            input.val(currentVal + 1);
        }
    });
    
    $('.mbm-qty-minus').click(function() {
        var input = $(this).siblings('.mbm-quantity');
        var currentVal = parseInt(input.val());
        var minVal = parseInt(input.attr('min'));
        
        if (currentVal > minVal) {
            input.val(currentVal - 1);
        }
    });
    
    // Add to cart functionality
    $('.mbm-add-to-cart-btn').click(function() {
        var button = $(this);
        var productId = button.data('product-id');
        var quantity = $('.mbm-quantity').val();
        var variations = {};
        
        // Get variation data
        $('.mbm-variation-select').each(function() {
            var name = $(this).attr('name');
            var value = $(this).val();
            if (value) {
                variations[name] = value;
            }
        });
        
        // Check if variations are required but not selected
        if ($('.mbm-variation-select').length > 0) {
            var allSelected = true;
            $('.mbm-variation-select').each(function() {
                if (!$(this).val()) {
                    allSelected = false;
                    return false;
                }
            });
            
            if (!allSelected) {
                showNotification('Please select all product options', 'error');
                return;
            }
        }
        
        // Disable button during request
        button.prop('disabled', true).find('.mbm-cart-text').text('Adding...');
        
        $.ajax({
            url: mbm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mbm_add_to_cart',
                product_id: productId,
                quantity: quantity,
                variations: variations,
                nonce: mbm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    button.find('.mbm-cart-text').text('Added!');
                    
                    // Update cart count if exists
                    $('.cart-count').text(response.data.cart_count);
                    
                    // Show success message
                    showNotification('Product added to cart!', 'success');
                    
                    setTimeout(function() {
                        button.prop('disabled', false).find('.mbm-cart-text').text('Add to Cart');
                    }, 2000);
                } else {
                    showNotification('Failed to add product to cart', 'error');
                    button.prop('disabled', false).find('.mbm-cart-text').text('Add to Cart');
                }
            },
            error: function() {
                showNotification('An error occurred', 'error');
                button.prop('disabled', false).find('.mbm-cart-text').text('Add to Cart');
            }
        });
    });
    
    // Buy now functionality
    $('.mbm-buy-now').click(function() {
        var button = $(this);
        var productId = button.data('product-id');
        var quantity = $('.mbm-quantity').val();
        
        // First add to cart, then redirect to checkout
        button.prop('disabled', true).text('Processing...');
        
        $.ajax({
            url: mbm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mbm_add_to_cart',
                product_id: productId,
                quantity: quantity,
                nonce: mbm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Redirect to checkout
                    window.location.href = mbm_ajax.wc_ajax_url.replace('%%endpoint%%', 'checkout');
                } else {
                    showNotification('Failed to process order', 'error');
                    button.prop('disabled', false).text('Buy Now');
                }
            },
            error: function() {
                showNotification('An error occurred', 'error');
                button.prop('disabled', false).text('Buy Now');
            }
        });
    });
    
    // Notification system
    function showNotification(message, type) {
        var notification = $('<div class=\"mbm-notification mbm-' + type + '\">' + message + '</div>');
        $('body').append(notification);
        
        setTimeout(function() {
            notification.addClass('show');
        }, 100);
        
        setTimeout(function() {
            notification.removeClass('show');
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Handle window resize
    $(window).resize(function() {
        if ($('.mbm-bottom-menu').length) {
            $('body').addClass('mbm-active');
        } else {
            $('body').removeClass('mbm-active');
        }
        
        if ($('.mbm-product-sticky').length) {
            $('body').addClass('mbm-product-active');
        } else {
            $('body').removeClass('mbm-product-active');
        }
    });
});

// Additional notification styles
var notificationCSS = '
.mbm-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 20px;
    border-radius: 4px;
    color: #fff;
    font-weight: bold;
    z-index: 10000;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.mbm-notification.show {
    transform: translateX(0);
}

.mbm-notification.mbm-success {
    background: #28a745;
}

.mbm-notification.mbm-error {
    background: #dc3545;
}
';

$('<style>').html(notificationCSS).appendTo('head');
";

    return $js_content;
}

// Activation hook to create necessary files
register_activation_hook(__FILE__, 'mbm_create_plugin_files');

function mbm_create_plugin_files() {
    // Create assets directory
    $assets_dir = MBM_PLUGIN_PATH . 'assets';
    if (!file_exists($assets_dir)) {
        wp_mkdir_p($assets_dir);
        wp_mkdir_p($assets_dir . '/css');
        wp_mkdir_p($assets_dir . '/js');
    }
    
    // Create CSS file
    file_put_contents($assets_dir . '/css/mobile-bottom-menu.css', mbm_create_css_file());
    
    // Create JS file
    file_put_contents($assets_dir . '/js/mobile-bottom-menu.js', mbm_create_js_file());
}
?>