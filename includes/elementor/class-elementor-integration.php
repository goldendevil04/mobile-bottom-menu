<?php
/**
 * Elementor Integration Class
 * Main class for integrating with Elementor Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

class NaniMade_Elementor_Integration {
    
    public function __construct() {
        add_action('elementor/widgets/widgets_registered', array($this, 'register_widgets'));
        add_action('elementor/controls/controls_registered', array($this, 'register_controls'));
        add_action('elementor/elements/categories_registered', array($this, 'add_widget_categories'));
        add_action('elementor/editor/before_enqueue_scripts', array($this, 'enqueue_editor_scripts'));
        add_action('elementor/preview/enqueue_styles', array($this, 'enqueue_preview_styles'));
        add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_frontend_styles'));
    }
    
    public function register_widgets() {
        if (!class_exists('Elementor\Widget_Base')) {
            return;
        }
        
        // Register pickle-specific widgets
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new NaniMade_Pickle_Customizer_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new NaniMade_Recipe_Story_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new NaniMade_Taste_Profile_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new NaniMade_Smart_Gallery_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new NaniMade_Trust_Signals_Widget());
    }
    
    public function register_controls() {
        // Register custom controls
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/elementor/controls/class-spice-level-control.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/elementor/controls/class-jar-size-control.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/elementor/controls/class-flavor-wheel-control.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/elementor/controls/class-ingredient-selector-control.php';
        require_once NANIMADE_SUITE_PLUGIN_PATH . 'includes/elementor/controls/class-seasonal-calendar-control.php';
        
        $controls_manager = \Elementor\Plugin::$instance->controls_manager;
        
        $controls_manager->register_control('nanimade_spice_level', new NaniMade_Spice_Level_Control());
        $controls_manager->register_control('nanimade_jar_size', new NaniMade_Jar_Size_Control());
        $controls_manager->register_control('nanimade_flavor_wheel', new NaniMade_Flavor_Wheel_Control());
        $controls_manager->register_control('nanimade_ingredient_selector', new NaniMade_Ingredient_Selector_Control());
        $controls_manager->register_control('nanimade_seasonal_calendar', new NaniMade_Seasonal_Calendar_Control());
    }
    
    public function add_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'nanimade-pickle-widgets',
            array(
                'title' => __('NaniMade Pickle Widgets', 'nanimade-suite'),
                'icon' => 'fa fa-leaf'
            )
        );
        
        $elements_manager->add_category(
            'nanimade-commerce-widgets',
            array(
                'title' => __('NaniMade Commerce', 'nanimade-suite'),
                'icon' => 'fa fa-shopping-cart'
            )
        );
        
        $elements_manager->add_category(
            'nanimade-mobile-widgets',
            array(
                'title' => __('NaniMade Mobile', 'nanimade-suite'),
                'icon' => 'fa fa-mobile'
            )
        );
    }
    
    public function enqueue_editor_scripts() {
        wp_enqueue_script(
            'nanimade-elementor-editor',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/js/elementor-editor.js',
            array('jquery', 'elementor-editor'),
            NANIMADE_SUITE_VERSION,
            true
        );
        
        wp_localize_script('nanimade-elementor-editor', 'nanimadeElementor', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nanimade_elementor_nonce'),
            'iconApiUrl' => 'https://api.iconify.design',
            'unsplashApiUrl' => 'https://api.unsplash.com',
            'googleFontsApiUrl' => 'https://fonts.googleapis.com/css2',
            'pluginUrl' => NANIMADE_SUITE_PLUGIN_URL
        ));
    }
    
    public function enqueue_preview_styles() {
        wp_enqueue_style(
            'nanimade-elementor-preview',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/elementor-preview.css',
            array(),
            NANIMADE_SUITE_VERSION
        );
    }
    
    public function enqueue_frontend_styles() {
        wp_enqueue_style(
            'nanimade-elementor-frontend',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/elementor-frontend.css',
            array(),
            NANIMADE_SUITE_VERSION
        );
        
        // Enqueue animation libraries
        wp_enqueue_style(
            'animate-css',
            'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
            array(),
            '4.1.1'
        );
        
        wp_enqueue_script(
            'aos-js',
            'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js',
            array(),
            '2.3.4',
            true
        );
        
        wp_enqueue_style(
            'aos-css',
            'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css',
            array(),
            '2.3.4'
        );
    }
    
    public static function get_elementor_settings() {
        return array(
            'animation_duration' => get_option('nanimade_animation_duration', 300),
            'mobile_breakpoint' => get_option('nanimade_mobile_breakpoint', 768),
            'tablet_breakpoint' => get_option('nanimade_tablet_breakpoint', 1024),
            'enable_lazy_loading' => get_option('nanimade_enable_lazy_loading', true),
            'enable_touch_gestures' => get_option('nanimade_enable_touch_gestures', true),
            'primary_color' => get_option('nanimade_primary_color', '#4CAF50'),
            'secondary_color' => get_option('nanimade_secondary_color', '#FF9800'),
            'accent_color' => get_option('nanimade_accent_color', '#E91E63'),
            'success_color' => get_option('nanimade_success_color', '#4CAF50'),
            'warning_color' => get_option('nanimade_warning_color', '#FF9800'),
            'error_color' => get_option('nanimade_error_color', '#F44336'),
            'neutral_colors' => array(
                'light' => '#F5F5F5',
                'medium' => '#9E9E9E',
                'dark' => '#424242'
            )
        );
    }
    
    public static function get_icon_libraries() {
        return array(
            'feather' => array(
                'name' => 'Feather Icons',
                'url' => 'https://api.iconify.design/feather',
                'prefix' => 'feather:'
            ),
            'heroicons' => array(
                'name' => 'Heroicons',
                'url' => 'https://api.iconify.design/heroicons',
                'prefix' => 'heroicons:'
            ),
            'lucide' => array(
                'name' => 'Lucide',
                'url' => 'https://api.iconify.design/lucide',
                'prefix' => 'lucide:'
            ),
            'tabler' => array(
                'name' => 'Tabler Icons',
                'url' => 'https://api.iconify.design/tabler',
                'prefix' => 'tabler:'
            ),
            'material-symbols' => array(
                'name' => 'Material Symbols',
                'url' => 'https://api.iconify.design/material-symbols',
                'prefix' => 'material-symbols:'
            )
        );
    }
    
    public static function get_google_fonts() {
        return array(
            'Inter' => 'Inter:wght@300;400;500;600;700',
            'Poppins' => 'Poppins:wght@300;400;500;600;700',
            'Roboto' => 'Roboto:wght@300;400;500;700',
            'Open Sans' => 'Open+Sans:wght@300;400;600;700',
            'Lato' => 'Lato:wght@300;400;700',
            'Montserrat' => 'Montserrat:wght@300;400;500;600;700',
            'Nunito' => 'Nunito:wght@300;400;600;700',
            'Source Sans Pro' => 'Source+Sans+Pro:wght@300;400;600;700',
            'Raleway' => 'Raleway:wght@300;400;500;600;700',
            'Playfair Display' => 'Playfair+Display:wght@400;500;600;700'
        );
    }
    
    public static function get_animation_presets() {
        return array(
            'fade' => array(
                'name' => __('Fade', 'nanimade-suite'),
                'class' => 'animate__fadeIn',
                'duration' => 600
            ),
            'slide-up' => array(
                'name' => __('Slide Up', 'nanimade-suite'),
                'class' => 'animate__slideInUp',
                'duration' => 800
            ),
            'slide-down' => array(
                'name' => __('Slide Down', 'nanimade-suite'),
                'class' => 'animate__slideInDown',
                'duration' => 800
            ),
            'slide-left' => array(
                'name' => __('Slide Left', 'nanimade-suite'),
                'class' => 'animate__slideInLeft',
                'duration' => 800
            ),
            'slide-right' => array(
                'name' => __('Slide Right', 'nanimade-suite'),
                'class' => 'animate__slideInRight',
                'duration' => 800
            ),
            'zoom-in' => array(
                'name' => __('Zoom In', 'nanimade-suite'),
                'class' => 'animate__zoomIn',
                'duration' => 600
            ),
            'zoom-out' => array(
                'name' => __('Zoom Out', 'nanimade-suite'),
                'class' => 'animate__zoomOut',
                'duration' => 600
            ),
            'bounce' => array(
                'name' => __('Bounce', 'nanimade-suite'),
                'class' => 'animate__bounceIn',
                'duration' => 1000
            ),
            'flip' => array(
                'name' => __('Flip', 'nanimade-suite'),
                'class' => 'animate__flipInX',
                'duration' => 800
            ),
            'rotate' => array(
                'name' => __('Rotate', 'nanimade-suite'),
                'class' => 'animate__rotateIn',
                'duration' => 800
            )
        );
    }
    
    public static function get_responsive_breakpoints() {
        return array(
            'mobile' => array(
                'label' => __('Mobile', 'nanimade-suite'),
                'max_width' => 767,
                'icon' => 'eicon-device-mobile'
            ),
            'tablet' => array(
                'label' => __('Tablet', 'nanimade-suite'),
                'min_width' => 768,
                'max_width' => 1023,
                'icon' => 'eicon-device-tablet'
            ),
            'desktop' => array(
                'label' => __('Desktop', 'nanimade-suite'),
                'min_width' => 1024,
                'icon' => 'eicon-device-desktop'
            ),
            'widescreen' => array(
                'label' => __('Widescreen', 'nanimade-suite'),
                'min_width' => 1200,
                'icon' => 'eicon-device-desktop'
            )
        );
    }
}