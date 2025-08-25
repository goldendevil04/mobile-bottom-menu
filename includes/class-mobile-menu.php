<?php
/**
 * Mobile Menu Class
 * Handles advanced mobile navigation with app-style bottom menu
 */

if (!defined('ABSPATH')) {
    exit;
}

class NaniMade_Mobile_Menu {
    
    public function __construct() {
        add_action('wp_ajax_get_cart_count', array($this, 'get_cart_count'));
        add_action('wp_ajax_nopriv_get_cart_count', array($this, 'get_cart_count'));
        add_action('wp_ajax_toggle_wishlist', array($this, 'toggle_wishlist'));
        add_action('wp_ajax_nopriv_toggle_wishlist', array($this, 'toggle_wishlist'));
        add_action('wp_footer', array($this, 'add_mobile_menu_styles'));
        add_filter('body_class', array($this, 'add_body_classes'));
    }
    
    public function get_cart_count() {
        check_ajax_referer('nanimade_nonce', 'nonce');
        
        $count = WC()->cart->get_cart_contents_count();
        $total = WC()->cart->get_cart_total();
        
        wp_send_json_success(array(
            'count' => $count,
            'total' => $total,
            'formatted_total' => wc_price(WC()->cart->get_cart_contents_total())
        ));
    }
    
    public function toggle_wishlist() {
        check_ajax_referer('nanimade_nonce', 'nonce');
        
        $product_id = intval($_POST['product_id']);
        $user_id = get_current_user_id();
        
        if (!$user_id) {
            wp_send_json_error(array('message' => __('Please login to use wishlist', 'nanimade-suite')));
        }
        
        $wishlist = get_user_meta($user_id, 'nanimade_wishlist', true);
        if (!is_array($wishlist)) {
            $wishlist = array();
        }
        
        $is_in_wishlist = in_array($product_id, $wishlist);
        
        if ($is_in_wishlist) {
            $wishlist = array_diff($wishlist, array($product_id));
            $action = 'removed';
        } else {
            $wishlist[] = $product_id;
            $action = 'added';
        }
        
        update_user_meta($user_id, 'nanimade_wishlist', $wishlist);
        
        wp_send_json_success(array(
            'action' => $action,
            'count' => count($wishlist),
            'is_in_wishlist' => !$is_in_wishlist
        ));
    }
    
    public function add_body_classes($classes) {
        if (wp_is_mobile() || get_option('nanimade_mobile_menu_enabled', true)) {
            $classes[] = 'nanimade-mobile-enabled';
            $classes[] = 'nanimade-menu-' . get_option('nanimade_menu_style', 'floating');
        }
        return $classes;
    }
    
    public function add_mobile_menu_styles() {
        $settings = $this->get_menu_settings();
        ?>
        <style id="nanimade-mobile-menu-dynamic-styles">
            :root {
                --nanimade-primary: <?php echo esc_attr($settings['primary_color']); ?>;
                --nanimade-secondary: <?php echo esc_attr($settings['secondary_color']); ?>;
                --nanimade-accent: <?php echo esc_attr($settings['accent_color']); ?>;
                --nanimade-menu-height: <?php echo esc_attr($settings['menu_height']); ?>px;
                --nanimade-blur-intensity: <?php echo esc_attr($settings['blur_intensity']); ?>px;
                --nanimade-animation-speed: <?php echo esc_attr($settings['animation_speed']); ?>ms;
                --nanimade-border-radius: <?php echo esc_attr($settings['border_radius']); ?>px;
            }
            
            .nanimade-mobile-menu {
                background: <?php echo $this->get_menu_background($settings); ?>;
                backdrop-filter: blur(var(--nanimade-blur-intensity));
                -webkit-backdrop-filter: blur(var(--nanimade-blur-intensity));
                border-radius: var(--nanimade-border-radius) var(--nanimade-border-radius) 0 0;
                box-shadow: 0 -2px 20px rgba(0,0,0,0.1);
            }
            
            .nanimade-menu-item {
                transition: all var(--nanimade-animation-speed) cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .nanimade-menu-item:hover,
            .nanimade-menu-item.active {
                color: var(--nanimade-primary);
                transform: translateY(-2px);
            }
            
            .nanimade-cart-badge {
                background: linear-gradient(135deg, var(--nanimade-accent), var(--nanimade-secondary));
                animation: nanimade-pulse 2s infinite;
            }
            
            @keyframes nanimade-pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
            
            <?php if ($settings['haptic_feedback']): ?>
            .nanimade-menu-item:active {
                transform: scale(0.95);
            }
            <?php endif; ?>
            
            <?php if ($settings['dark_mode']): ?>
            @media (prefers-color-scheme: dark) {
                .nanimade-mobile-menu {
                    background: rgba(30, 30, 30, 0.9);
                    border-top: 1px solid rgba(255, 255, 255, 0.1);
                }
                
                .nanimade-menu-item {
                    color: #ffffff;
                }
            }
            <?php endif; ?>
        </style>
        <?php
    }
    
    private function get_menu_settings() {
        return array(
            'primary_color' => get_option('nanimade_primary_color', '#4CAF50'),
            'secondary_color' => get_option('nanimade_secondary_color', '#FF9800'),
            'accent_color' => get_option('nanimade_accent_color', '#E91E63'),
            'menu_height' => get_option('nanimade_menu_height', 70),
            'blur_intensity' => get_option('nanimade_blur_intensity', 10),
            'animation_speed' => get_option('nanimade_animation_speed', 300),
            'border_radius' => get_option('nanimade_border_radius', 20),
            'haptic_feedback' => get_option('nanimade_haptic_feedback_enabled', true),
            'dark_mode' => get_option('nanimade_dark_mode_enabled', true),
            'menu_style' => get_option('nanimade_menu_style', 'floating')
        );
    }
    
    private function get_menu_background($settings) {
        switch ($settings['menu_style']) {
            case 'solid':
                return '#ffffff';
            case 'gradient':
                return 'linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.7))';
            case 'floating':
            default:
                return 'rgba(255, 255, 255, 0.8)';
        }
    }
    
    public function get_menu_items() {
        $default_items = array(
            'home' => array(
                'icon' => 'home',
                'label' => __('Home', 'nanimade-suite'),
                'url' => home_url(),
                'badge' => false
            ),
            'shop' => array(
                'icon' => 'shopping-bag',
                'label' => __('Shop', 'nanimade-suite'),
                'url' => wc_get_page_permalink('shop'),
                'badge' => false
            ),
            'cart' => array(
                'icon' => 'shopping-cart',
                'label' => __('Cart', 'nanimade-suite'),
                'url' => '#',
                'badge' => WC()->cart->get_cart_contents_count(),
                'action' => 'toggle-cart'
            ),
            'wishlist' => array(
                'icon' => 'heart',
                'label' => __('Wishlist', 'nanimade-suite'),
                'url' => '#',
                'badge' => $this->get_wishlist_count(),
                'action' => 'toggle-wishlist'
            ),
            'account' => array(
                'icon' => 'user',
                'label' => __('Account', 'nanimade-suite'),
                'url' => wc_get_page_permalink('myaccount'),
                'badge' => false
            )
        );
        
        return apply_filters('nanimade_mobile_menu_items', $default_items);
    }
    
    private function get_wishlist_count() {
        $user_id = get_current_user_id();
        if (!$user_id) return 0;
        
        $wishlist = get_user_meta($user_id, 'nanimade_wishlist', true);
        return is_array($wishlist) ? count($wishlist) : 0;
    }
}