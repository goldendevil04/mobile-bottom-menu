<?php
if (!defined('ABSPATH')) {
    exit;
}

class NaniMade_Elementor_Integration {
    
    public function __construct() {
        add_action('elementor/widgets/widgets_registered', array($this, 'register_widgets'));
        add_action('elementor/elements/categories_registered', array($this, 'register_categories'));
        add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_elementor_styles'));
    }
    
    public function register_categories($elements_manager) {
        $elements_manager->add_category(
            'nanimade-suite',
            array(
                'title' => __('NaniMade Pickle Suite', 'nanimade-suite'),
                'icon' => 'fa fa-pepper-hot',
            )
        );
    }
    
    public function register_widgets() {
        // Load widget files
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'elementor-widgets/pickle-jar-customizer.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'elementor-widgets/recipe-story-widget.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'elementor-widgets/taste-profile-selector.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'elementor-widgets/smart-product-gallery.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'elementor-widgets/mobile-menu-pro.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'elementor-widgets/sidebar-cart-widget.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'elementor-widgets/trust-signals-widget.php';
        
        // Register widgets
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new NaniMade_Pickle_Jar_Customizer());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new NaniMade_Recipe_Story_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new NaniMade_Taste_Profile_Selector());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new NaniMade_Smart_Product_Gallery());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new NaniMade_Mobile_Menu_Pro());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new NaniMade_Sidebar_Cart_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new NaniMade_Trust_Signals_Widget());
    }
    
    public function enqueue_elementor_styles() {
        wp_enqueue_style(
            'nanimade-elementor-widgets',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/elementor-widgets.css',
            array(),
            NANIMADE_SUITE_VERSION
        );
    }
}
?>