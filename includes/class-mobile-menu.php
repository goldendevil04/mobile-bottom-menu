<?php
if (!defined('ABSPATH')) {
    exit;
}

class NaniMade_Mobile_Menu {
    
    public function __construct() {
        add_action('wp_footer', array($this, 'render_mobile_menu'));
        add_action('wp_footer', array($this, 'render_sidebar_cart'));
        add_action('wp_head', array($this, 'add_meta_tags'));
    }
    
    public function add_meta_tags() {
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="theme-color" content="#ff6b35">
        <?php
    }
    
    public function render_mobile_menu() {
        $settings = get_option('nanimade_suite_settings', array());
        
        if (!isset($settings['mobile_menu_enabled']) || !$settings['mobile_menu_enabled']) {
            return;
        }
        
        $menu_items = $this->get_default_menu_items();
        $design_style = isset($settings['design_style']) ? $settings['design_style'] : 'pickle-modern';
        
        ?>
        <div class="nanimade-mobile-menu nanimade-style-<?php echo esc_attr($design_style); ?> nanimade-animated" id="nanimadeMobileMenu">
            <div class="nanimade-menu-container">
                <?php foreach ($menu_items as $item): ?>
                <a href="<?php echo esc_url($item['url']); ?>" class="nanimade-menu-item" data-item="<?php echo esc_attr($item['key']); ?>">
                    <div class="nanimade-icon-wrapper">
                        <i class="<?php echo esc_attr($item['icon']); ?>"></i>
                        <?php if (isset($item['badge']) && $item['badge']): ?>
                        <span class="nanimade-cart-badge" id="nanimadeCartBadge">
                            <?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?>
                        </span>
                        <?php endif; ?>
                        
                        <!-- Pickle-specific animations -->
                        <div class="nanimade-pickle-bubbles">
                            <span class="bubble bubble-1"></span>
                            <span class="bubble bubble-2"></span>
                            <span class="bubble bubble-3"></span>
                        </div>
                    </div>
                    <span class="nanimade-label"><?php echo esc_html($item['label']); ?></span>
                    
                    <!-- Spice level indicator for certain items -->
                    <?php if ($item['key'] === 'shop'): ?>
                    <div class="nanimade-spice-indicator">
                        <span class="chili chili-1">🌶️</span>
                        <span class="chili chili-2">🌶️</span>
                        <span class="chili chili-3">🌶️</span>
                    </div>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Floating action button for special offers -->
            <div class="nanimade-fab" id="nanimadeFab">
                <i class="fas fa-gift"></i>
                <span class="nanimade-fab-text"><?php _e('Special Offer', 'nanimade-suite'); ?></span>
            </div>
        </div>
        
        <!-- Cart overlay -->
        <div class="nanimade-cart-overlay" id="nanimadeCartOverlay"></div>
        <?php
    }
    
    public function render_sidebar_cart() {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        ?>
        <div class="nanimade-sidebar-cart" id="nanimadeSidebarCart">
            <div class="nanimade-cart-header">
                <h3 class="nanimade-cart-title">
                    <i class="fas fa-shopping-basket"></i>
                    <?php _e('Your Pickle Selection', 'nanimade-suite'); ?>
                </h3>
                <button class="nanimade-cart-close" id="nanimadeCartClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="nanimade-cart-content" id="nanimadeCartContent">
                <?php $this->render_cart_content(); ?>
            </div>
            
            <div class="nanimade-cart-footer">
                <div class="nanimade-cart-total">
                    <span class="nanimade-total-label"><?php _e('Total:', 'nanimade-suite'); ?></span>
                    <span class="nanimade-total-amount" id="nanimadeCartTotal">
                        <?php echo WC()->cart ? WC()->cart->get_cart_total() : wc_price(0); ?>
                    </span>
                </div>
                
                <div class="nanimade-cart-actions">
                    <a href="<?php echo wc_get_cart_url(); ?>" class="nanimade-btn nanimade-btn-secondary">
                        <i class="fas fa-shopping-cart"></i>
                        <?php _e('View Cart', 'nanimade-suite'); ?>
                    </a>
                    <a href="<?php echo wc_get_checkout_url(); ?>" class="nanimade-btn nanimade-btn-primary">
                        <i class="fas fa-credit-card"></i>
                        <?php _e('Checkout', 'nanimade-suite'); ?>
                    </a>
                </div>
                
                <!-- Trust signals -->
                <div class="nanimade-trust-signals">
                    <div class="nanimade-trust-item">
                        <i class="fas fa-shield-alt"></i>
                        <span><?php _e('Handmade Fresh', 'nanimade-suite'); ?></span>
                    </div>
                    <div class="nanimade-trust-item">
                        <i class="fas fa-leaf"></i>
                        <span><?php _e('No Preservatives', 'nanimade-suite'); ?></span>
                    </div>
                    <div class="nanimade-trust-item">
                        <i class="fas fa-heart"></i>
                        <span><?php _e('Made by Nani', 'nanimade-suite'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    private function render_cart_content() {
        if (!WC()->cart || WC()->cart->is_empty()) {
            ?>
            <div class="nanimade-empty-cart">
                <div class="nanimade-empty-icon">
                    <i class="fas fa-jar"></i>
                </div>
                <h4><?php _e('Your jar is empty!', 'nanimade-suite'); ?></h4>
                <p><?php _e('Add some delicious pickles to get started.', 'nanimade-suite'); ?></p>
                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="nanimade-btn nanimade-btn-primary">
                    <i class="fas fa-shopping-bag"></i>
                    <?php _e('Start Shopping', 'nanimade-suite'); ?>
                </a>
            </div>
            <?php
            return;
        }
        
        ?>
        <div class="nanimade-cart-items">
            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item): ?>
                <?php
                $product = $cart_item['data'];
                $product_id = $cart_item['product_id'];
                $quantity = $cart_item['quantity'];
                $product_permalink = $product->is_visible() ? $product->get_permalink($cart_item) : '';
                ?>
                <div class="nanimade-cart-item" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                    <div class="nanimade-item-image">
                        <?php
                        $thumbnail = $product->get_image('thumbnail');
                        if ($product_permalink) {
                            echo '<a href="' . esc_url($product_permalink) . '">' . $thumbnail . '</a>';
                        } else {
                            echo $thumbnail;
                        }
                        ?>
                        
                        <!-- Freshness indicator -->
                        <div class="nanimade-freshness-badge">
                            <i class="fas fa-clock"></i>
                            <span><?php _e('Fresh', 'nanimade-suite'); ?></span>
                        </div>
                    </div>
                    
                    <div class="nanimade-item-details">
                        <h4 class="nanimade-item-name">
                            <?php
                            if ($product_permalink) {
                                echo '<a href="' . esc_url($product_permalink) . '">' . $product->get_name() . '</a>';
                            } else {
                                echo $product->get_name();
                            }
                            ?>
                        </h4>
                        
                        <?php if ($product->is_type('variation')): ?>
                        <div class="nanimade-item-variations">
                            <?php
                            $attributes = $product->get_variation_attributes();
                            foreach ($attributes as $name => $value) {
                                echo '<span class="nanimade-variation">' . esc_html($name) . ': ' . esc_html($value) . '</span>';
                            }
                            ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="nanimade-item-meta">
                            <div class="nanimade-item-price">
                                <?php echo WC()->cart->get_product_price($product); ?>
                            </div>
                            
                            <div class="nanimade-quantity-controls">
                                <button class="nanimade-qty-btn nanimade-qty-minus" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="nanimade-quantity"><?php echo $quantity; ?></span>
                                <button class="nanimade-qty-btn nanimade-qty-plus" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <button class="nanimade-remove-item" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Recommended products -->
        <div class="nanimade-recommendations">
            <h4><?php _e('You might also like', 'nanimade-suite'); ?></h4>
            <div class="nanimade-recommended-items">
                <?php $this->render_recommended_products(); ?>
            </div>
        </div>
        <?php
    }
    
    private function render_recommended_products() {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 3,
            'meta_query' => array(
                array(
                    'key' => '_featured',
                    'value' => 'yes'
                )
            )
        );
        
        $products = new WP_Query($args);
        
        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                global $product;
                ?>
                <div class="nanimade-recommended-item">
                    <a href="<?php echo get_permalink(); ?>">
                        <?php echo $product->get_image('thumbnail'); ?>
                        <span class="nanimade-rec-name"><?php echo $product->get_name(); ?></span>
                        <span class="nanimade-rec-price"><?php echo $product->get_price_html(); ?></span>
                    </a>
                </div>
                <?php
            }
            wp_reset_postdata();
        }
    }
    
    private function get_default_menu_items() {
        return array(
            array(
                'key' => 'home',
                'label' => __('Home', 'nanimade-suite'),
                'icon' => 'fas fa-home',
                'url' => home_url(),
                'badge' => false,
            ),
            array(
                'key' => 'shop',
                'label' => __('Pickles', 'nanimade-suite'),
                'icon' => 'fas fa-pepper-hot',
                'url' => get_permalink(wc_get_page_id('shop')),
                'badge' => false,
            ),
            array(
                'key' => 'cart',
                'label' => __('Cart', 'nanimade-suite'),
                'icon' => 'fas fa-shopping-basket',
                'url' => '#',
                'badge' => true,
            ),
            array(
                'key' => 'recipes',
                'label' => __('Recipes', 'nanimade-suite'),
                'icon' => 'fas fa-book-open',
                'url' => '#',
                'badge' => false,
            ),
            array(
                'key' => 'account',
                'label' => __('Account', 'nanimade-suite'),
                'icon' => 'fas fa-user-circle',
                'url' => get_permalink(wc_get_page_id('myaccount')),
                'badge' => false,
            ),
        );
    }
}
?>