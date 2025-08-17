<?php
if (!defined('ABSPATH')) {
    exit;
}

class MBM_Mobile_Menu_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'mbm-mobile-menu';
    }

    public function get_title() {
        return __('Mobile Menu Pro', 'mobile-bottom-menu');
    }

    public function get_icon() {
        return 'eicon-nav-menu';
    }

    public function get_categories() {
        return ['mobile-bottom-menu'];
    }

    public function get_keywords() {
        return ['mobile', 'menu', 'navigation', 'bottom', 'sticky'];
    }

    protected function _register_controls() {
        
        // Content Section - Menu Items
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Menu Items', 'mobile-bottom-menu'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'menu_label',
            [
                'label' => __('Label', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Menu Item', 'mobile-bottom-menu'),
                'placeholder' => __('Enter menu label', 'mobile-bottom-menu'),
            ]
        );

        $repeater->add_control(
            'menu_icon',
            [
                'label' => __('Icon', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-home',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $repeater->add_control(
            'menu_link',
            [
                'label' => __('Link', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'mobile-bottom-menu'),
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $repeater->add_control(
            'show_badge',
            [
                'label' => __('Show Badge', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'mobile-bottom-menu'),
                'label_off' => __('No', 'mobile-bottom-menu'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $repeater->add_control(
            'badge_text',
            [
                'label' => __('Badge Text', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '0',
                'condition' => [
                    'show_badge' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'badge_color',
            [
                'label' => __('Badge Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#dc3545',
                'condition' => [
                    'show_badge' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'menu_items',
            [
                'label' => __('Menu Items', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'menu_label' => __('Home', 'mobile-bottom-menu'),
                        'menu_icon' => ['value' => 'fas fa-home', 'library' => 'fa-solid'],
                        'menu_link' => ['url' => home_url()],
                    ],
                    [
                        'menu_label' => __('Shop', 'mobile-bottom-menu'),
                        'menu_icon' => ['value' => 'fas fa-shopping-bag', 'library' => 'fa-solid'],
                        'menu_link' => ['url' => '#'],
                    ],
                    [
                        'menu_label' => __('Cart', 'mobile-bottom-menu'),
                        'menu_icon' => ['value' => 'fas fa-shopping-cart', 'library' => 'fa-solid'],
                        'menu_link' => ['url' => '#'],
                        'show_badge' => 'yes',
                        'badge_text' => '0',
                    ],
                    [
                        'menu_label' => __('Account', 'mobile-bottom-menu'),
                        'menu_icon' => ['value' => 'fas fa-user', 'library' => 'fa-solid'],
                        'menu_link' => ['url' => '#'],
                    ],
                ],
                'title_field' => '{{{ menu_label }}}',
            ]
        );

        $this->end_controls_section();

        // Style Section - General
        $this->start_controls_section(
            'style_general_section',
            [
                'label' => __('General Style', 'mobile-bottom-menu'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'design_style',
            [
                'label' => __('Design Style', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'modern',
                'options' => [
                    'modern' => __('Modern', 'mobile-bottom-menu'),
                    'minimal' => __('Minimal', 'mobile-bottom-menu'),
                    'classic' => __('Classic', 'mobile-bottom-menu'),
                    'gradient' => __('Gradient', 'mobile-bottom-menu'),
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => __('Background Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .mbm-mobile-menu' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'menu_shadow',
                'label' => __('Box Shadow', 'mobile-bottom-menu'),
                'selector' => '{{WRAPPER}} .mbm-mobile-menu',
                'fields_options' => [
                    'box_shadow_type' => [
                        'default' => 'yes',
                    ],
                    'box_shadow' => [
                        'default' => [
                            'horizontal' => 0,
                            'vertical' => -4,
                            'blur' => 25,
                            'spread' => 0,
                            'color' => 'rgba(0,0,0,0.15)',
                        ],
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_padding',
            [
                'label' => __('Padding', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '16',
                    'right' => '12',
                    'bottom' => '12',
                    'left' => '12',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-menu-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_border_radius',
            [
                'label' => __('Border Radius', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => '20',
                    'right' => '20',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-mobile-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Menu Items
        $this->start_controls_section(
            'style_items_section',
            [
                'label' => __('Menu Items', 'mobile-bottom-menu'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('menu_items_style_tabs');

        $this->start_controls_tab(
            'menu_items_normal_tab',
            [
                'label' => __('Normal', 'mobile-bottom-menu'),
            ]
        );

        $this->add_control(
            'item_text_color',
            [
                'label' => __('Text Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .mbm-menu-item' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'item_background_color',
            [
                'label' => __('Background Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .mbm-menu-item' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'menu_items_hover_tab',
            [
                'label' => __('Hover', 'mobile-bottom-menu'),
            ]
        );

        $this->add_control(
            'item_hover_color',
            [
                'label' => __('Text Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ff6b35',
                'selectors' => [
                    '{{WRAPPER}} .mbm-menu-item:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .mbm-menu-item:hover i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'item_hover_background',
            [
                'label' => __('Background Color', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(255, 107, 53, 0.1)',
                'selectors' => [
                    '{{WRAPPER}} .mbm-menu-item:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __('Icon Size', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 16,
                        'max' => 40,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 24,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-menu-item i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .mbm-menu-item svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'text_size',
            [
                'label' => __('Text Size', 'mobile-bottom-menu'),
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
                    '{{WRAPPER}} .mbm-label' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'text_weight',
            [
                'label' => __('Text Weight', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '600',
                'options' => [
                    '400' => __('Normal', 'mobile-bottom-menu'),
                    '500' => __('Medium', 'mobile-bottom-menu'),
                    '600' => __('Semi Bold', 'mobile-bottom-menu'),
                    '700' => __('Bold', 'mobile-bottom-menu'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-label' => 'font-weight: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => __('Item Padding', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '12',
                    'right' => '16',
                    'bottom' => '12',
                    'left' => '16',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-menu-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label' => __('Item Border Radius', 'mobile-bottom-menu'),
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
                    '{{WRAPPER}} .mbm-menu-item' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Badge
        $this->start_controls_section(
            'style_badge_section',
            [
                'label' => __('Badge Style', 'mobile-bottom-menu'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'badge_size',
            [
                'label' => __('Badge Size', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 16,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-cart-badge' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
                        'max' => 14,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mbm-cart-badge' => 'font-size: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .mbm-cart-badge' => 'color: {{VALUE}}',
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
            'enable_animations',
            [
                'label' => __('Enable Animations', 'mobile-bottom-menu'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'mobile-bottom-menu'),
                'label_off' => __('No', 'mobile-bottom-menu'),
                'return_value' => 'yes',
                'default' => 'yes',
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
                'description' => __('Make the menu stick to the bottom of the screen', 'mobile-bottom-menu'),
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
                    '{{WRAPPER}} .mbm-mobile-menu' => 'z-index: {{VALUE}};',
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
        $design_style = $settings['design_style'];
        $enable_animations = $settings['enable_animations'] === 'yes' ? 'mbm-animated' : '';
        $sticky_class = $settings['sticky_position'] === 'yes' ? 'mbm-sticky' : '';
        
        if (!empty($settings['menu_items'])) {
            ?>
            <div class="mbm-mobile-menu mbm-style-<?php echo esc_attr($design_style); ?> <?php echo esc_attr($enable_animations); ?> <?php echo esc_attr($sticky_class); ?> mbm-elementor-widget">
                <div class="mbm-menu-container">
                    <?php foreach ($settings['menu_items'] as $item): ?>
                        <?php
                        $link_key = 'link_' . $item['_id'];
                        $this->add_render_attribute($link_key, 'href', $item['menu_link']['url']);
                        $this->add_render_attribute($link_key, 'class', 'mbm-menu-item');
                        
                        if ($item['menu_link']['is_external']) {
                            $this->add_render_attribute($link_key, 'target', '_blank');
                        }
                        if ($item['menu_link']['nofollow']) {
                            $this->add_render_attribute($link_key, 'rel', 'nofollow');
                        }
                        ?>
                        <a <?php echo $this->get_render_attribute_string($link_key); ?>>
                            <div class="mbm-icon-wrapper">
                                <?php \Elementor\Icons_Manager::render_icon($item['menu_icon'], ['aria-hidden' => 'true']); ?>
                                <?php if ($item['show_badge'] === 'yes'): ?>
                                <span class="mbm-cart-badge" style="background-color: <?php echo esc_attr($item['badge_color']); ?>">
                                    <?php echo esc_html($item['badge_text']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <span class="mbm-label"><?php echo esc_html($item['menu_label']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
        }
    }
}
?>