<?php
if (!defined('ABSPATH')) {
    exit;
}

class NaniMade_Recipe_Story_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'nanimade-recipe-story';
    }

    public function get_title() {
        return __('Recipe Story Timeline', 'nanimade-suite');
    }

    public function get_icon() {
        return 'fas fa-book-open';
    }

    public function get_categories() {
        return ['nanimade-suite'];
    }

    public function get_keywords() {
        return ['recipe', 'story', 'timeline', 'cooking', 'process'];
    }

    protected function _register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Recipe Story Settings', 'nanimade-suite'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'recipe_title',
            [
                'label' => __('Recipe Title', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Nani\'s Traditional Mango Pickle', 'nanimade-suite'),
                'placeholder' => __('Enter recipe title', 'nanimade-suite'),
            ]
        );

        $this->add_control(
            'recipe_description',
            [
                'label' => __('Recipe Description', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('A time-honored recipe passed down through generations, made with love and the finest ingredients.', 'nanimade-suite'),
                'placeholder' => __('Enter recipe description', 'nanimade-suite'),
            ]
        );

        $this->add_control(
            'family_story',
            [
                'label' => __('Family Story', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('This recipe has been in our family for over 50 years, originally created by my grandmother who learned it from her mother in rural Karnataka.', 'nanimade-suite'),
            ]
        );

        // Recipe Steps Repeater
        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'step_title',
            [
                'label' => __('Step Title', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Preparation Step', 'nanimade-suite'),
            ]
        );

        $repeater->add_control(
            'step_description',
            [
                'label' => __('Step Description', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Describe this cooking step...', 'nanimade-suite'),
            ]
        );

        $repeater->add_control(
            'step_time',
            [
                'label' => __('Time Required', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('10 minutes', 'nanimade-suite'),
            ]
        );

        $repeater->add_control(
            'step_icon',
            [
                'label' => __('Step Icon', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-utensils',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $repeater->add_control(
            'step_image',
            [
                'label' => __('Step Image', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg',
                ],
            ]
        );

        $this->add_control(
            'recipe_steps',
            [
                'label' => __('Recipe Steps', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'step_title' => __('Select Fresh Mangoes', 'nanimade-suite'),
                        'step_description' => __('Choose firm, unripe mangoes for the perfect texture and taste.', 'nanimade-suite'),
                        'step_time' => __('5 minutes', 'nanimade-suite'),
                        'step_icon' => ['value' => 'fas fa-apple-alt', 'library' => 'fa-solid'],
                    ],
                    [
                        'step_title' => __('Prepare Spice Mix', 'nanimade-suite'),
                        'step_description' => __('Grind fresh spices including red chili, turmeric, and fenugreek.', 'nanimade-suite'),
                        'step_time' => __('15 minutes', 'nanimade-suite'),
                        'step_icon' => ['value' => 'fas fa-mortar-pestle', 'library' => 'fa-solid'],
                    ],
                    [
                        'step_title' => __('Mix and Marinate', 'nanimade-suite'),
                        'step_description' => __('Combine mangoes with spices and let the flavors develop.', 'nanimade-suite'),
                        'step_time' => __('30 minutes', 'nanimade-suite'),
                        'step_icon' => ['value' => 'fas fa-hand-sparkles', 'library' => 'fa-solid'],
                    ],
                    [
                        'step_title' => __('Traditional Fermentation', 'nanimade-suite'),
                        'step_description' => __('Allow natural fermentation for authentic taste and preservation.', 'nanimade-suite'),
                        'step_time' => __('2-3 days', 'nanimade-suite'),
                        'step_icon' => ['value' => 'fas fa-clock', 'library' => 'fa-solid'],
                    ],
                ],
                'title_field' => '{{{ step_title }}}',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Timeline Style', 'nanimade-suite'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'timeline_color',
            [
                'label' => __('Timeline Color', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ff6b35',
                'selectors' => [
                    '{{WRAPPER}} .nanimade-timeline-line' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .nanimade-step-marker' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'step_background',
            [
                'label' => __('Step Background', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .nanimade-recipe-step' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        ?>
        <div class="nanimade-recipe-story-widget">
            <div class="nanimade-recipe-header">
                <h2 class="nanimade-recipe-title"><?php echo esc_html($settings['recipe_title']); ?></h2>
                <p class="nanimade-recipe-description"><?php echo esc_html($settings['recipe_description']); ?></p>
                
                <!-- Authenticity badge -->
                <div class="nanimade-authenticity-badge">
                    <i class="fas fa-certificate"></i>
                    <span><?php _e('Traditional Recipe', 'nanimade-suite'); ?></span>
                </div>
            </div>
            
            <!-- Family Story Section -->
            <div class="nanimade-family-story">
                <div class="nanimade-story-content">
                    <div class="nanimade-story-text">
                        <?php echo wp_kses_post($settings['family_story']); ?>
                    </div>
                    <div class="nanimade-story-gallery">
                        <div class="nanimade-vintage-photo">
                            <img src="https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg" alt="<?php _e('Family cooking tradition', 'nanimade-suite'); ?>">
                            <div class="nanimade-photo-caption"><?php _e('Nani in her kitchen, 1970s', 'nanimade-suite'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Interactive Recipe Timeline -->
            <div class="nanimade-recipe-timeline">
                <div class="nanimade-timeline-line"></div>
                
                <?php foreach ($settings['recipe_steps'] as $index => $step): ?>
                <div class="nanimade-recipe-step" data-step="<?php echo $index + 1; ?>">
                    <div class="nanimade-step-marker">
                        <div class="nanimade-step-number"><?php echo $index + 1; ?></div>
                        <div class="nanimade-step-icon">
                            <?php \Elementor\Icons_Manager::render_icon($step['step_icon'], ['aria-hidden' => 'true']); ?>
                        </div>
                    </div>
                    
                    <div class="nanimade-step-content">
                        <div class="nanimade-step-header">
                            <h4 class="nanimade-step-title"><?php echo esc_html($step['step_title']); ?></h4>
                            <span class="nanimade-step-time">
                                <i class="fas fa-clock"></i>
                                <?php echo esc_html($step['step_time']); ?>
                            </span>
                        </div>
                        
                        <p class="nanimade-step-description"><?php echo esc_html($step['step_description']); ?></p>
                        
                        <?php if (!empty($step['step_image']['url'])): ?>
                        <div class="nanimade-step-image">
                            <img src="<?php echo esc_url($step['step_image']['url']); ?>" alt="<?php echo esc_attr($step['step_title']); ?>">
                            <div class="nanimade-image-overlay">
                                <i class="fas fa-play-circle"></i>
                                <span><?php _e('View Process', 'nanimade-suite'); ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Interactive elements -->
                        <div class="nanimade-step-interactions">
                            <button class="nanimade-step-action" data-action="like">
                                <i class="fas fa-heart"></i>
                                <span><?php _e('Love this step', 'nanimade-suite'); ?></span>
                            </button>
                            <button class="nanimade-step-action" data-action="question">
                                <i class="fas fa-question-circle"></i>
                                <span><?php _e('Ask about this', 'nanimade-suite'); ?></span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Cooking animation elements -->
                    <div class="nanimade-cooking-animation">
                        <div class="steam-particle steam-1"></div>
                        <div class="steam-particle steam-2"></div>
                        <div class="steam-particle steam-3"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Seasonal availability -->
            <div class="nanimade-seasonal-info">
                <h4><?php _e('Seasonal Availability', 'nanimade-suite'); ?></h4>
                <div class="nanimade-season-calendar">
                    <div class="nanimade-season active" data-season="summer">
                        <i class="fas fa-sun"></i>
                        <span><?php _e('Summer', 'nanimade-suite'); ?></span>
                        <div class="nanimade-season-note"><?php _e('Peak mango season', 'nanimade-suite'); ?></div>
                    </div>
                    <div class="nanimade-season" data-season="monsoon">
                        <i class="fas fa-cloud-rain"></i>
                        <span><?php _e('Monsoon', 'nanimade-suite'); ?></span>
                        <div class="nanimade-season-note"><?php _e('Limited availability', 'nanimade-suite'); ?></div>
                    </div>
                    <div class="nanimade-season" data-season="winter">
                        <i class="fas fa-snowflake"></i>
                        <span><?php _e('Winter', 'nanimade-suite'); ?></span>
                        <div class="nanimade-season-note"><?php _e('Preserved varieties', 'nanimade-suite'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>