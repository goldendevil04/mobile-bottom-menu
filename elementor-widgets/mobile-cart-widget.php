<?php
if (!defined('ABSPATH')) {
    exit;
}

class MBM_Mobile_Cart_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'mbm-mobile-cart';
    }

    public function get_title() {
        return __('Mobile Cart Pro', 'mobile-bottom-menu');
    }

    public function get_icon() {
        return 'eicon-cart';
    }

    public function get_categories() {
        return ['mobile-bottom-menu'];
    }

    public function get_keywords() {
        return ['mobile', 'cart', 'woocommerce', 'product', 'sticky'];
    }

    protected function _register_controls() {
        
        // Content Section - General
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Cart Settings', 'mobile-bottom-menu'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_on_product_only',
            [
                'label' => __('Show Only on Product Pages', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'mobile-bottom-menu'),
                'label_off' => __('No', 'mobile-bottom-menu'),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => __('Display cart widget only on single product pages', 'mobile-bottom-menu'),
            ]
        );

        $this->add_control(
            'enable_variations',
            [
                'label' => __('Enable Product Variations', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'mobile-bottom-menu'),
                'label_off' => __('No', 'mobile-bottom-menu'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'enable_quantity_selector',
            [
                'label' => __('Enable Quantity Selector', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'mobile-bottom-menu'),
                'label_off' => __('No', 'mobile-bottom-menu'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Style Section - Price Box
        $this->start_controls_section(
            'style_price_section',
            [
                'label' => __('Price Box', 'mobile-bottom-menu'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'price_box_background',
            [
                'label' => __('Regular Price Background', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f8f9fa',
                'selectors' => [
                    '{{WRAPPER}} .mbm-price-box.mbm-regular-box' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'sale_box_background',
            [
                'label' => __('Sale Price Background', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ff6b35',
                'selectors' => [
                    '{{WRAPPER}} .mbm-price-box.mbm-sale-box' => 'background: linear-gradient(135deg, {{VALUE}} 0%, #f7931e 100%)',
                ],
            ]
        );

        $this->add_control(
            'price_text_color',
            [
                'label' => __('Price Text Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .mbm-current-price' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'price_font_size',
            [
                'label' => __('Price Font Size', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 14,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-current-price' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'price_box_padding',
            [
                'label' => __('Price Box Padding', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '16',
                    'right' => '16',
                    'bottom' => '16',
                    'left' => '16',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-price-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'price_box_border_radius',
            [
                'label' => __('Price Box Border Radius', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 25,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-price-box' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Discount Badge
        $this->start_controls_section(
            'style_badge_section',
            [
                'label' => __('Discount Badge', 'mobile-bottom-menu'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'enable_discount_badge',
            [
                'label' => __('Enable Discount Badge', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'mobile-bottom-menu'),
                'label_off' => __('No', 'mobile-bottom-menu'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'badge_background_color',
            [
                'label' => __('Badge Background', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#dc3545',
                'selectors' => [
                    '{{WRAPPER}} .mbm-discount-badge' => 'background: linear-gradient(135deg, {{VALUE}} 0%, #c82333 100%)',
                ],
                'condition' => [
                    'enable_discount_badge' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'badge_text_color',
            [
                'label' => __('Badge Text Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .mbm-discount-badge' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'enable_discount_badge' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_font_size',
            [
                'label' => __('Badge Font Size', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 16,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 11,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-discount-badge' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_discount_badge' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Quantity Selector
        $this->start_controls_section(
            'style_quantity_section',
            [
                'label' => __('Quantity Selector', 'mobile-bottom-menu'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'quantity_background',
            [
                'label' => __('Background Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .mbm-quantity-selector' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'quantity_border_color',
            [
                'label' => __('Border Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#dee2e6',
                'selectors' => [
                    '{{WRAPPER}} .mbm-quantity-selector' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'quantity_button_color',
            [
                'label' => __('Button Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f8f9fa',
                'selectors' => [
                    '{{WRAPPER}} .mbm-qty-btn' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'quantity_button_hover_color',
            [
                'label' => __('Button Hover Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ff6b35',
                'selectors' => [
                    '{{WRAPPER}} .mbm-qty-btn:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'quantity_border_radius',
            [
                'label' => __('Border Radius', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 12,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-quantity-selector' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Add to Cart Button
        $this->start_controls_section(
            'style_button_section',
            [
                'label' => __('Add to Cart Button', 'mobile-bottom-menu'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('button_style_tabs');

        $this->start_controls_tab(
            'button_normal_tab',
            [
                'label' => __('Normal', 'mobile-bottom-menu'),
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => __('Background Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#28a745',
                'selectors' => [
                    '{{WRAPPER}} .mbm-add-to-cart-btn' => 'background: linear-gradient(135deg, {{VALUE}} 0%, #218838 100%)',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Text Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .mbm-add-to-cart-btn' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover_tab',
            [
                'label' => __('Hover', 'mobile-bottom-menu'),
            ]
        );

        $this->add_control(
            'button_hover_background',
            [
                'label' => __('Background Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#218838',
                'selectors' => [
                    '{{WRAPPER}} .mbm-add-to-cart-btn:hover' => 'background: linear-gradient(135deg, {{VALUE}} 0%, #1e7e34 100%)',
                ],
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => __('Text Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .mbm-add-to-cart-btn:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'button_font_size',
            [
                'label' => __('Font Size', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 12,
                        'max' => 18,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 14,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-add-to-cart-btn' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '12',
                    'right' => '20',
                    'bottom' => '12',
                    'left' => '20',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-add-to-cart-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 12,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-add-to-cart-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Advanced Section
        $this->start_controls_section(
            'advanced_section',
            [
                'label' => __('Advanced', 'mobile-bottom-menu'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'sticky_position',
            [
                'label' => __('Sticky Position', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'mobile-bottom-menu'),
                'label_off' => __('No', 'mobile-bottom-menu'),
                'return_value' => 'yes',
                'default' => '',
                'description' => __('Make the cart stick to the bottom of the screen', 'mobile-bottom-menu'),
            ]
        );

        $this->add_responsive_control(
            'z_index',
            [
                'label' => __('Z-Index', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 9999,
                'step' => 1,
                'default' => 9999,
                'selectors' => [
                    '{{WRAPPER}} .mbm-mobile-cart' => 'z-index: {{VALUE}};',
                ],
                'condition' => [
                    'sticky_position' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Check if we should show only on product pages
        if ($settings['show_on_product_only'] === 'yes' && !is_product()) {
            return;
        }
        
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            echo '<div class="mbm-error">WooCommerce is required for this widget.</div>';
            return;
        }
        
        global $product;
        if (!$product && is_product()) {
            global $post;
            $product = wc_get_product($post->ID);
        }
        
        if (!$product) {
            echo '<div class="mbm-error">No product found.</div>';
            return;
        }
        
        $sticky_class = $settings['sticky_position'] === 'yes' ? 'mbm-sticky' : '';
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        $current_price = $product->get_price();
        $discount_percentage = 0;
        
        if ($sale_price && $regular_price && $regular_price > $sale_price) {
            $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
        }
        
        $currency_symbol = get_woocommerce_currency_symbol();
        
        ?>
        <div class="mbm-mobile-cart <?php echo esc_attr($sticky_class); ?> mbm-elementor-widget" data-product-id="<?php echo $product->get_id(); ?>">
            
            <?php if ($settings['enable_variations'] === 'yes' && $product->is_type('variable')): ?>
            <div class="mbm-variations-container">
                <div class="mbm-variations-header">
                    <h4><?php _e('Select Options', 'mobile-bottom-menu'); ?></h4>
                </div>
                <?php
                $attributes = $product->get_variation_attributes();
                foreach ($attributes as $attribute_name => $options): ?>
                    <div class="mbm-variation-group">
                        <label><?php echo wc_attribute_label($attribute_name); ?>:</label>
                        <select name="<?php echo esc_attr($attribute_name); ?>" class="mbm-variation-select" data-attribute="<?php echo esc_attr($attribute_name); ?>">
                            <option value=""><?php printf(__('Choose %s', 'mobile-bottom-menu'), wc_attribute_label($attribute_name)); ?></option>
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
                    <?php if ($discount_percentage > 0 && $settings['enable_discount_badge'] === 'yes'): ?>
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
                    <?php if ($settings['enable_quantity_selector'] === 'yes'): ?>
                    <div class="mbm-quantity-wrapper">
                        <label class="mbm-qty-label"><?php _e('Qty:', 'mobile-bottom-menu'); ?></label>
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
                    <?php endif; ?>
                    
                    <button class="mbm-add-to-cart-btn" data-product-id="<?php echo $product->get_id(); ?>">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="mbm-cart-text"><?php _e('Add to Cart', 'mobile-bottom-menu'); ?></span>
                        <div class="mbm-loading-spinner"></div>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
}
?>