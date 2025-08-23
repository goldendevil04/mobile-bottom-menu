<?php
if (!defined('ABSPATH')) {
    exit;
}

class NaniMade_Pickle_Jar_Customizer extends \Elementor\Widget_Base {

    public function get_name() {
        return 'nanimade-pickle-jar-customizer';
    }

    public function get_title() {
        return __('Pickle Jar Customizer', 'nanimade-suite');
    }

    public function get_icon() {
        return 'fas fa-jar';
    }

    public function get_categories() {
        return ['nanimade-suite'];
    }

    public function get_keywords() {
        return ['pickle', 'jar', 'customizer', 'product', 'interactive'];
    }

    protected function _register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Jar Customizer Settings', 'nanimade-suite'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'jar_style',
            [
                'label' => __('Jar Style', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'mason',
                'options' => [
                    'mason' => __('Classic Mason Jar', 'nanimade-suite'),
                    'vintage' => __('Vintage Glass Jar', 'nanimade-suite'),
                    'modern' => __('Modern Container', 'nanimade-suite'),
                    'traditional' => __('Traditional Indian Jar', 'nanimade-suite'),
                ],
            ]
        );

        $this->add_control(
            'enable_3d_rotation',
            [
                'label' => __('Enable 3D Rotation', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'nanimade-suite'),
                'label_off' => __('No', 'nanimade-suite'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'spice_levels',
            [
                'label' => __('Available Spice Levels', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['mild', 'medium', 'hot', 'extra-hot'],
                'options' => [
                    'mild' => __('Mild 🌶️', 'nanimade-suite'),
                    'medium' => __('Medium 🌶️🌶️', 'nanimade-suite'),
                    'hot' => __('Hot 🌶️🌶️🌶️', 'nanimade-suite'),
                    'extra-hot' => __('Extra Hot 🌶️🌶️🌶️🌶️', 'nanimade-suite'),
                    'nuclear' => __('Nuclear 🌶️🌶️🌶️🌶️🌶️', 'nanimade-suite'),
                ],
            ]
        );

        $this->add_control(
            'jar_sizes',
            [
                'label' => __('Available Jar Sizes', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['small', 'medium', 'large'],
                'options' => [
                    'small' => __('Small (250g)', 'nanimade-suite'),
                    'medium' => __('Medium (500g)', 'nanimade-suite'),
                    'large' => __('Large (1kg)', 'nanimade-suite'),
                    'family' => __('Family Pack (2kg)', 'nanimade-suite'),
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Jar Appearance
        $this->start_controls_section(
            'style_jar_section',
            [
                'label' => __('Jar Appearance', 'nanimade-suite'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'jar_primary_color',
            [
                'label' => __('Jar Primary Color', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#8B4513',
                'selectors' => [
                    '{{WRAPPER}} .nanimade-jar-container' => '--jar-primary: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'jar_accent_color',
            [
                'label' => __('Jar Accent Color', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D2691E',
                'selectors' => [
                    '{{WRAPPER}} .nanimade-jar-container' => '--jar-accent: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'jar_size',
            [
                'label' => __('Jar Display Size', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 500,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 300,
                ],
                'selectors' => [
                    '{{WRAPPER}} .nanimade-jar-display' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Spice Indicator
        $this->start_controls_section(
            'style_spice_section',
            [
                'label' => __('Spice Level Indicator', 'nanimade-suite'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'spice_mild_color',
            [
                'label' => __('Mild Spice Color', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#28a745',
                'selectors' => [
                    '{{WRAPPER}} .nanimade-spice-level.mild' => 'background: linear-gradient(135deg, {{VALUE}} 0%, #20c997 100%)',
                ],
            ]
        );

        $this->add_control(
            'spice_hot_color',
            [
                'label' => __('Hot Spice Color', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#dc3545',
                'selectors' => [
                    '{{WRAPPER}} .nanimade-spice-level.hot' => 'background: linear-gradient(135deg, {{VALUE}} 0%, #c82333 100%)',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $jar_style = $settings['jar_style'];
        $enable_3d = $settings['enable_3d_rotation'] === 'yes' ? 'nanimade-3d-enabled' : '';
        
        ?>
        <div class="nanimade-pickle-jar-customizer <?php echo esc_attr($enable_3d); ?>">
            <div class="nanimade-customizer-container">
                
                <!-- Jar Display -->
                <div class="nanimade-jar-container nanimade-jar-<?php echo esc_attr($jar_style); ?>">
                    <div class="nanimade-jar-display" id="nanimadeJarDisplay">
                        <div class="nanimade-jar-base">
                            <div class="nanimade-jar-contents" id="nanimadeJarContents">
                                <!-- Animated pickle pieces -->
                                <div class="nanimade-pickle-piece piece-1"></div>
                                <div class="nanimade-pickle-piece piece-2"></div>
                                <div class="nanimade-pickle-piece piece-3"></div>
                                <div class="nanimade-pickle-piece piece-4"></div>
                                <div class="nanimade-pickle-piece piece-5"></div>
                            </div>
                            <div class="nanimade-jar-liquid" id="nanimadeJarLiquid"></div>
                        </div>
                        <div class="nanimade-jar-lid">
                            <div class="nanimade-lid-pattern"></div>
                            <div class="nanimade-lid-label">
                                <span class="nanimade-brand">Nani Made</span>
                                <span class="nanimade-product-name" id="nanimadeProductName">Custom Pickle</span>
                            </div>
                        </div>
                        
                        <!-- Floating spice particles -->
                        <div class="nanimade-spice-particles">
                            <span class="particle particle-1">🌶️</span>
                            <span class="particle particle-2">🧄</span>
                            <span class="particle particle-3">🧅</span>
                            <span class="particle particle-4">🌿</span>
                        </div>
                    </div>
                    
                    <!-- Freshness indicator -->
                    <div class="nanimade-freshness-indicator">
                        <div class="nanimade-fresh-badge">
                            <i class="fas fa-leaf"></i>
                            <span><?php _e('Made Fresh Today', 'nanimade-suite'); ?></span>
                        </div>
                        <div class="nanimade-batch-info">
                            <span><?php _e('Batch #', 'nanimade-suite'); ?><?php echo date('Ymd'); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Customization Controls -->
                <div class="nanimade-customizer-controls">
                    
                    <!-- Spice Level Selector -->
                    <div class="nanimade-control-group">
                        <h4 class="nanimade-control-title">
                            <i class="fas fa-fire"></i>
                            <?php _e('Spice Level', 'nanimade-suite'); ?>
                        </h4>
                        <div class="nanimade-spice-selector">
                            <?php foreach ($settings['spice_levels'] as $level): ?>
                            <button class="nanimade-spice-level <?php echo esc_attr($level); ?>" data-spice="<?php echo esc_attr($level); ?>">
                                <span class="nanimade-spice-icon">
                                    <?php
                                    $chili_count = array(
                                        'mild' => 1,
                                        'medium' => 2,
                                        'hot' => 3,
                                        'extra-hot' => 4,
                                        'nuclear' => 5
                                    );
                                    for ($i = 0; $i < $chili_count[$level]; $i++) {
                                        echo '🌶️';
                                    }
                                    ?>
                                </span>
                                <span class="nanimade-spice-label"><?php echo ucfirst($level); ?></span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Jar Size Selector -->
                    <div class="nanimade-control-group">
                        <h4 class="nanimade-control-title">
                            <i class="fas fa-weight-hanging"></i>
                            <?php _e('Jar Size', 'nanimade-suite'); ?>
                        </h4>
                        <div class="nanimade-size-selector">
                            <?php foreach ($settings['jar_sizes'] as $size): ?>
                            <button class="nanimade-jar-size" data-size="<?php echo esc_attr($size); ?>">
                                <div class="nanimade-size-visual size-<?php echo esc_attr($size); ?>"></div>
                                <span class="nanimade-size-label">
                                    <?php
                                    $size_labels = array(
                                        'small' => __('Small (250g)', 'nanimade-suite'),
                                        'medium' => __('Medium (500g)', 'nanimade-suite'),
                                        'large' => __('Large (1kg)', 'nanimade-suite'),
                                        'family' => __('Family (2kg)', 'nanimade-suite'),
                                    );
                                    echo $size_labels[$size];
                                    ?>
                                </span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Ingredient Customization -->
                    <div class="nanimade-control-group">
                        <h4 class="nanimade-control-title">
                            <i class="fas fa-seedling"></i>
                            <?php _e('Extra Ingredients', 'nanimade-suite'); ?>
                        </h4>
                        <div class="nanimade-ingredient-selector">
                            <label class="nanimade-ingredient-option">
                                <input type="checkbox" name="extra_garlic" value="1">
                                <span class="nanimade-ingredient-visual">🧄</span>
                                <span class="nanimade-ingredient-name"><?php _e('Extra Garlic', 'nanimade-suite'); ?></span>
                                <span class="nanimade-ingredient-price">+₹10</span>
                            </label>
                            
                            <label class="nanimade-ingredient-option">
                                <input type="checkbox" name="extra_chili" value="1">
                                <span class="nanimade-ingredient-visual">🌶️</span>
                                <span class="nanimade-ingredient-name"><?php _e('Extra Chili', 'nanimade-suite'); ?></span>
                                <span class="nanimade-ingredient-price">+₹15</span>
                            </label>
                            
                            <label class="nanimade-ingredient-option">
                                <input type="checkbox" name="curry_leaves" value="1">
                                <span class="nanimade-ingredient-visual">🌿</span>
                                <span class="nanimade-ingredient-name"><?php _e('Fresh Curry Leaves', 'nanimade-suite'); ?></span>
                                <span class="nanimade-ingredient-price">+₹8</span>
                            </label>
                            
                            <label class="nanimade-ingredient-option">
                                <input type="checkbox" name="mustard_seeds" value="1">
                                <span class="nanimade-ingredient-visual">🟡</span>
                                <span class="nanimade-ingredient-name"><?php _e('Mustard Seeds', 'nanimade-suite'); ?></span>
                                <span class="nanimade-ingredient-price">+₹5</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Custom Label Designer -->
                    <div class="nanimade-control-group">
                        <h4 class="nanimade-control-title">
                            <i class="fas fa-tag"></i>
                            <?php _e('Custom Label (Gift Orders)', 'nanimade-suite'); ?>
                        </h4>
                        <div class="nanimade-label-designer">
                            <input type="text" class="nanimade-custom-message" placeholder="<?php _e('Enter custom message for gift...', 'nanimade-suite'); ?>" maxlength="50">
                            <div class="nanimade-label-preview">
                                <div class="nanimade-label-template">
                                    <span class="nanimade-label-brand">Made with ❤️ by Nani</span>
                                    <span class="nanimade-label-custom" id="nanimadeLabelCustom"><?php _e('Special Gift', 'nanimade-suite'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Price Calculator -->
                    <div class="nanimade-price-calculator">
                        <div class="nanimade-price-breakdown">
                            <div class="nanimade-price-item">
                                <span><?php _e('Base Price:', 'nanimade-suite'); ?></span>
                                <span class="nanimade-base-price">₹120</span>
                            </div>
                            <div class="nanimade-price-item">
                                <span><?php _e('Extras:', 'nanimade-suite'); ?></span>
                                <span class="nanimade-extras-price" id="nanimadeExtrasPrice">₹0</span>
                            </div>
                            <div class="nanimade-price-total">
                                <span><?php _e('Total:', 'nanimade-suite'); ?></span>
                                <span class="nanimade-total-price" id="nanimadeTotalPrice">₹120</span>
                            </div>
                        </div>
                        
                        <button class="nanimade-add-custom-jar" id="nanimadeAddCustomJar">
                            <i class="fas fa-plus-circle"></i>
                            <span><?php _e('Add Custom Jar to Cart', 'nanimade-suite'); ?></span>
                            <div class="nanimade-button-ripple"></div>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Floating ingredients for animation -->
            <div class="nanimade-floating-ingredients">
                <div class="floating-ingredient ingredient-1">🥒</div>
                <div class="floating-ingredient ingredient-2">🌶️</div>
                <div class="floating-ingredient ingredient-3">🧄</div>
                <div class="floating-ingredient ingredient-4">🧅</div>
                <div class="floating-ingredient ingredient-5">🌿</div>
            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jarCustomizer = new NaniMadeJarCustomizer();
            jarCustomizer.init();
        });
        </script>
        <?php
    }
}
?>