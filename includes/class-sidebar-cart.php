<?php
/**
 * Sidebar Cart Class
 * Advanced sliding cart with live updates and premium features
 */

if (!defined('ABSPATH')) {
    exit;
}

class NaniMade_Sidebar_Cart {
    
    public function __construct() {
        add_action('wp_ajax_add_to_cart_sidebar', array($this, 'add_to_cart_sidebar'));
        add_action('wp_ajax_nopriv_add_to_cart_sidebar', array($this, 'add_to_cart_sidebar'));
        add_action('wp_ajax_remove_from_cart_sidebar', array($this, 'remove_from_cart_sidebar'));
        add_action('wp_ajax_nopriv_remove_from_cart_sidebar', array($this, 'remove_from_cart_sidebar'));
        add_action('wp_ajax_update_cart_quantity', array($this, 'update_cart_quantity'));
        add_action('wp_ajax_nopriv_update_cart_quantity', array($this, 'update_cart_quantity'));
        add_action('wp_ajax_get_cart_recommendations', array($this, 'get_cart_recommendations'));
        add_action('wp_ajax_nopriv_get_cart_recommendations', array($this, 'get_cart_recommendations'));
        add_action('wp_ajax_apply_coupon_sidebar', array($this, 'apply_coupon_sidebar'));
        add_action('wp_ajax_nopriv_apply_coupon_sidebar', array($this, 'apply_coupon_sidebar'));
        add_action('wp_ajax_save_for_later', array($this, 'save_for_later'));
        add_action('wp_ajax_nopriv_save_for_later', array($this, 'save_for_later'));
        
        add_filter('woocommerce_add_to_cart_fragments', array($this, 'cart_fragments'));
    }
    
    public function add_to_cart_sidebar() {
        check_ajax_referer('nanimade_nonce', 'nonce');
        
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']) ?: 1;
        $variation_id = intval($_POST['variation_id']) ?: 0;
        
        $result = WC()->cart->add_to_cart($product_id, $quantity, $variation_id);
        
        if ($result) {
            $this->track_cart_event('add_to_cart', $product_id, $quantity);
            
            wp_send_json_success(array(
                'message' => __('Product added to cart!', 'nanimade-suite'),
                'cart_count' => WC()->cart->get_cart_contents_count(),
                'cart_total' => WC()->cart->get_cart_total(),
                'cart_html' => $this->get_cart_html()
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to add product to cart.', 'nanimade-suite')
            ));
        }
    }
    
    public function remove_from_cart_sidebar() {
        check_ajax_referer('nanimade_nonce', 'nonce');
        
        $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
        
        if (WC()->cart->remove_cart_item($cart_item_key)) {
            $this->track_cart_event('remove_from_cart', 0, 0);
            
            wp_send_json_success(array(
                'message' => __('Product removed from cart!', 'nanimade-suite'),
                'cart_count' => WC()->cart->get_cart_contents_count(),
                'cart_total' => WC()->cart->get_cart_total(),
                'cart_html' => $this->get_cart_html()
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to remove product from cart.', 'nanimade-suite')
            ));
        }
    }
    
    public function update_cart_quantity() {
        check_ajax_referer('nanimade_nonce', 'nonce');
        
        $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity <= 0) {
            WC()->cart->remove_cart_item($cart_item_key);
        } else {
            WC()->cart->set_quantity($cart_item_key, $quantity);
        }
        
        $this->track_cart_event('update_quantity', 0, $quantity);
        
        wp_send_json_success(array(
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_total(),
            'cart_html' => $this->get_cart_html()
        ));
    }
    
    public function get_cart_recommendations() {
        check_ajax_referer('nanimade_nonce', 'nonce');
        
        $cart_items = WC()->cart->get_cart();
        $product_ids = array();
        
        foreach ($cart_items as $cart_item) {
            $product_ids[] = $cart_item['product_id'];
        }
        
        // Get related products based on categories
        $recommendations = $this->get_related_products($product_ids);
        
        wp_send_json_success(array(
            'recommendations' => $recommendations
        ));
    }
    
    public function apply_coupon_sidebar() {
        check_ajax_referer('nanimade_nonce', 'nonce');
        
        $coupon_code = sanitize_text_field($_POST['coupon_code']);
        
        if (empty($coupon_code)) {
            wp_send_json_error(array(
                'message' => __('Please enter a coupon code.', 'nanimade-suite')
            ));
        }
        
        $result = WC()->cart->apply_coupon($coupon_code);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Coupon applied successfully!', 'nanimade-suite'),
                'cart_total' => WC()->cart->get_cart_total(),
                'cart_html' => $this->get_cart_html()
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Invalid coupon code.', 'nanimade-suite')
            ));
        }
    }
    
    public function save_for_later() {
        check_ajax_referer('nanimade_nonce', 'nonce');
        
        $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
        $cart_item = WC()->cart->get_cart_item($cart_item_key);
        
        if ($cart_item) {
            $user_id = get_current_user_id();
            if (!$user_id) {
                wp_send_json_error(array('message' => __('Please login to save items.', 'nanimade-suite')));
            }
            
            $saved_items = get_user_meta($user_id, 'nanimade_saved_for_later', true);
            if (!is_array($saved_items)) {
                $saved_items = array();
            }
            
            $saved_items[] = array(
                'product_id' => $cart_item['product_id'],
                'variation_id' => $cart_item['variation_id'],
                'quantity' => $cart_item['quantity'],
                'data' => $cart_item['data'],
                'saved_date' => current_time('mysql')
            );
            
            update_user_meta($user_id, 'nanimade_saved_for_later', $saved_items);
            WC()->cart->remove_cart_item($cart_item_key);
            
            wp_send_json_success(array(
                'message' => __('Item saved for later!', 'nanimade-suite'),
                'cart_html' => $this->get_cart_html()
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Item not found in cart.', 'nanimade-suite')
            ));
        }
    }
    
    public function cart_fragments($fragments) {
        $fragments['.nanimade-sidebar-cart-content'] = $this->get_cart_html();
        $fragments['.nanimade-cart-count'] = '<span class="nanimade-cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
        return $fragments;
    }
    
    public function get_cart_html() {
        ob_start();
        
        if (WC()->cart->is_empty()) {
            echo $this->get_empty_cart_html();
        } else {
            echo $this->get_cart_items_html();
            echo $this->get_cart_totals_html();
            echo $this->get_cart_actions_html();
        }
        
        return ob_get_clean();
    }
    
    private function get_empty_cart_html() {
        ob_start();
        ?>
        <div class="nanimade-empty-cart">
            <div class="nanimade-empty-cart-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="m1 1 4 4 2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </div>
            <h3><?php _e('Your cart is empty', 'nanimade-suite'); ?></h3>
            <p><?php _e('Add some delicious pickles to get started!', 'nanimade-suite'); ?></p>
            <a href="<?php echo wc_get_page_permalink('shop'); ?>" class="nanimade-btn nanimade-btn-primary">
                <?php _e('Start Shopping', 'nanimade-suite'); ?>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function get_cart_items_html() {
        ob_start();
        ?>
        <div class="nanimade-cart-items">
            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item): ?>
                <?php
                $product = $cart_item['data'];
                $product_id = $cart_item['product_id'];
                $quantity = $cart_item['quantity'];
                ?>
                <div class="nanimade-cart-item" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                    <div class="nanimade-cart-item-image">
                        <?php echo $product->get_image('thumbnail'); ?>
                    </div>
                    <div class="nanimade-cart-item-details">
                        <h4 class="nanimade-cart-item-name"><?php echo $product->get_name(); ?></h4>
                        <div class="nanimade-cart-item-price">
                            <?php echo wc_price($product->get_price()); ?>
                        </div>
                        <div class="nanimade-cart-item-meta">
                            <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                        </div>
                    </div>
                    <div class="nanimade-cart-item-actions">
                        <div class="nanimade-quantity-controls">
                            <button type="button" class="nanimade-qty-btn nanimade-qty-minus" data-action="decrease">-</button>
                            <input type="number" class="nanimade-qty-input" value="<?php echo $quantity; ?>" min="1" max="99">
                            <button type="button" class="nanimade-qty-btn nanimade-qty-plus" data-action="increase">+</button>
                        </div>
                        <div class="nanimade-cart-item-total">
                            <?php echo wc_price($product->get_price() * $quantity); ?>
                        </div>
                        <div class="nanimade-cart-item-remove">
                            <button type="button" class="nanimade-remove-item" title="<?php _e('Remove item', 'nanimade-suite'); ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3,6 5,6 21,6"></polyline>
                                    <path d="m19,6v14a2,2 0 0,1-2,2H7a2,2 0 0,1-2-2V6m3,0V4a2,2 0 0,1,2-2h4a2,2 0 0,1,2,2v2"></path>
                                </svg>
                            </button>
                            <button type="button" class="nanimade-save-later" title="<?php _e('Save for later', 'nanimade-suite'); ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function get_cart_totals_html() {
        ob_start();
        ?>
        <div class="nanimade-cart-totals">
            <div class="nanimade-cart-subtotal">
                <span><?php _e('Subtotal:', 'nanimade-suite'); ?></span>
                <span><?php echo WC()->cart->get_cart_subtotal(); ?></span>
            </div>
            
            <?php if (WC()->cart->get_cart_discount_total() > 0): ?>
            <div class="nanimade-cart-discount">
                <span><?php _e('Discount:', 'nanimade-suite'); ?></span>
                <span>-<?php echo wc_price(WC()->cart->get_cart_discount_total()); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="nanimade-cart-shipping">
                <span><?php _e('Shipping:', 'nanimade-suite'); ?></span>
                <span><?php echo WC()->cart->get_cart_shipping_total(); ?></span>
            </div>
            
            <div class="nanimade-cart-total">
                <span><?php _e('Total:', 'nanimade-suite'); ?></span>
                <span><?php echo WC()->cart->get_cart_total(); ?></span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function get_cart_actions_html() {
        ob_start();
        ?>
        <div class="nanimade-cart-actions">
            <div class="nanimade-coupon-section">
                <input type="text" class="nanimade-coupon-input" placeholder="<?php _e('Coupon code', 'nanimade-suite'); ?>">
                <button type="button" class="nanimade-apply-coupon nanimade-btn nanimade-btn-secondary">
                    <?php _e('Apply', 'nanimade-suite'); ?>
                </button>
            </div>
            
            <div class="nanimade-checkout-buttons">
                <a href="<?php echo wc_get_cart_url(); ?>" class="nanimade-btn nanimade-btn-outline">
                    <?php _e('View Cart', 'nanimade-suite'); ?>
                </a>
                <a href="<?php echo wc_get_checkout_url(); ?>" class="nanimade-btn nanimade-btn-primary nanimade-express-checkout">
                    <?php _e('Checkout', 'nanimade-suite'); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </a>
            </div>
            
            <div class="nanimade-shipping-estimator">
                <h4><?php _e('Shipping Calculator', 'nanimade-suite'); ?></h4>
                <select class="nanimade-shipping-country">
                    <option value=""><?php _e('Select Country', 'nanimade-suite'); ?></option>
                    <?php foreach (WC()->countries->get_shipping_countries() as $key => $value): ?>
                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" class="nanimade-shipping-postcode" placeholder="<?php _e('Postcode', 'nanimade-suite'); ?>">
                <button type="button" class="nanimade-calculate-shipping nanimade-btn nanimade-btn-small">
                    <?php _e('Calculate', 'nanimade-suite'); ?>
                </button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function get_related_products($product_ids, $limit = 4) {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => $limit,
            'post__not_in' => $product_ids,
            'meta_query' => array(
                array(
                    'key' => '_visibility',
                    'value' => array('catalog', 'visible'),
                    'compare' => 'IN'
                )
            )
        );
        
        $products = get_posts($args);
        $recommendations = array();
        
        foreach ($products as $product) {
            $wc_product = wc_get_product($product->ID);
            $recommendations[] = array(
                'id' => $product->ID,
                'name' => $product->post_title,
                'price' => $wc_product->get_price_html(),
                'image' => get_the_post_thumbnail_url($product->ID, 'thumbnail'),
                'url' => get_permalink($product->ID)
            );
        }
        
        return $recommendations;
    }
    
    private function track_cart_event($action, $product_id = 0, $quantity = 0) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'nanimade_cart_analytics';
        
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => get_current_user_id(),
                'session_id' => WC()->session->get_customer_id(),
                'action' => $action,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'cart_total' => WC()->cart->get_cart_contents_total(),
                'timestamp' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%d', '%d', '%f', '%s')
        );
    }
}