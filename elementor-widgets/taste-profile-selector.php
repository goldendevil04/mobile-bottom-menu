<?php
if (!defined('ABSPATH')) {
    exit;
}

class NaniMade_Taste_Profile_Selector extends \Elementor\Widget_Base {

    public function get_name() {
        return 'nanimade-taste-profile';
    }

    public function get_title() {
        return __('Interactive Taste Profile', 'nanimade-suite');
    }

    public function get_icon() {
        return 'fas fa-palette';
    }

    public function get_categories() {
        return ['nanimade-suite'];
    }

    public function get_keywords() {
        return ['taste', 'flavor', 'profile', 'spice', 'interactive'];
    }

    protected function _register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Taste Profile Settings', 'nanimade-suite'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'enable_flavor_wheel',
            [
                'label' => __('Enable Interactive Flavor Wheel', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'nanimade-suite'),
                'label_off' => __('No', 'nanimade-suite'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'enable_spice_meter',
            [
                'label' => __('Enable Spice Level Meter', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'nanimade-suite'),
                'label_off' => __('No', 'nanimade-suite'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'enable_pairing_suggestions',
            [
                'label' => __('Enable Food Pairing Suggestions', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'nanimade-suite'),
                'label_off' => __('No', 'nanimade-suite'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Taste Profile Style', 'nanimade-suite'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'wheel_primary_color',
            [
                'label' => __('Wheel Primary Color', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ff6b35',
                'selectors' => [
                    '{{WRAPPER}} .nanimade-flavor-wheel' => '--wheel-primary: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'meter_color',
            [
                'label' => __('Spice Meter Color', 'nanimade-suite'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#dc3545',
                'selectors' => [
                    '{{WRAPPER}} .nanimade-spice-meter' => '--meter-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        ?>
        <div class="nanimade-taste-profile-selector">
            
            <?php if ($settings['enable_flavor_wheel'] === 'yes'): ?>
            <!-- Interactive Flavor Wheel -->
            <div class="nanimade-flavor-wheel-container">
                <h3 class="nanimade-section-title">
                    <i class="fas fa-palette"></i>
                    <?php _e('Flavor Profile', 'nanimade-suite'); ?>
                </h3>
                
                <div class="nanimade-flavor-wheel" id="nanimadeFlavorWheel">
                    <div class="nanimade-wheel-center">
                        <div class="nanimade-selected-flavor" id="nanimadeSelectedFlavor">
                            <span class="flavor-name"><?php _e('Select Flavor', 'nanimade-suite'); ?></span>
                            <span class="flavor-intensity">0%</span>
                        </div>
                    </div>
                    
                    <!-- Flavor segments -->
                    <div class="nanimade-flavor-segment" data-flavor="tangy" style="--segment-angle: 0deg;">
                        <span class="flavor-label"><?php _e('Tangy', 'nanimade-suite'); ?></span>
                        <span class="flavor-emoji">🍋</span>
                    </div>
                    <div class="nanimade-flavor-segment" data-flavor="spicy" style="--segment-angle: 60deg;">
                        <span class="flavor-label"><?php _e('Spicy', 'nanimade-suite'); ?></span>
                        <span class="flavor-emoji">🌶️</span>
                    </div>
                    <div class="nanimade-flavor-segment" data-flavor="sweet" style="--segment-angle: 120deg;">
                        <span class="flavor-label"><?php _e('Sweet', 'nanimade-suite'); ?></span>
                        <span class="flavor-emoji">🍯</span>
                    </div>
                    <div class="nanimade-flavor-segment" data-flavor="salty" style="--segment-angle: 180deg;">
                        <span class="flavor-label"><?php _e('Salty', 'nanimade-suite'); ?></span>
                        <span class="flavor-emoji">🧂</span>
                    </div>
                    <div class="nanimade-flavor-segment" data-flavor="sour" style="--segment-angle: 240deg;">
                        <span class="flavor-label"><?php _e('Sour', 'nanimade-suite'); ?></span>
                        <span class="flavor-emoji">🍋</span>
                    </div>
                    <div class="nanimade-flavor-segment" data-flavor="aromatic" style="--segment-angle: 300deg;">
                        <span class="flavor-label"><?php _e('Aromatic', 'nanimade-suite'); ?></span>
                        <span class="flavor-emoji">🌿</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($settings['enable_spice_meter'] === 'yes'): ?>
            <!-- Spice Level Meter -->
            <div class="nanimade-spice-meter-container">
                <h3 class="nanimade-section-title">
                    <i class="fas fa-thermometer-half"></i>
                    <?php _e('Heat Level', 'nanimade-suite'); ?>
                </h3>
                
                <div class="nanimade-spice-meter" id="nanimadeSpiceMeter">
                    <div class="nanimade-meter-track">
                        <div class="nanimade-meter-fill" id="nanimadeMeterFill"></div>
                        <div class="nanimade-meter-handle" id="nanimadeMeterHandle"></div>
                    </div>
                    
                    <div class="nanimade-meter-labels">
                        <span class="meter-label mild"><?php _e('Mild', 'nanimade-suite'); ?></span>
                        <span class="meter-label medium"><?php _e('Medium', 'nanimade-suite'); ?></span>
                        <span class="meter-label hot"><?php _e('Hot', 'nanimade-suite'); ?></span>
                        <span class="meter-label extreme"><?php _e('Extreme', 'nanimade-suite'); ?></span>
                    </div>
                    
                    <!-- Heat tolerance quiz -->
                    <div class="nanimade-heat-quiz">
                        <h4><?php _e('Not sure about your heat tolerance?', 'nanimade-suite'); ?></h4>
                        <button class="nanimade-quiz-btn" id="nanimadeHeatQuiz">
                            <i class="fas fa-question-circle"></i>
                            <?php _e('Take Heat Tolerance Quiz', 'nanimade-suite'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($settings['enable_pairing_suggestions'] === 'yes'): ?>
            <!-- Food Pairing Suggestions -->
            <div class="nanimade-pairing-suggestions">
                <h3 class="nanimade-section-title">
                    <i class="fas fa-utensils"></i>
                    <?php _e('Perfect Pairings', 'nanimade-suite'); ?>
                </h3>
                
                <div class="nanimade-pairing-grid">
                    <div class="nanimade-pairing-item" data-pairing="rice">
                        <div class="pairing-image">🍚</div>
                        <h4><?php _e('Steamed Rice', 'nanimade-suite'); ?></h4>
                        <p><?php _e('Classic combination for everyday meals', 'nanimade-suite'); ?></p>
                        <div class="pairing-rating">
                            <span class="stars">⭐⭐⭐⭐⭐</span>
                        </div>
                    </div>
                    
                    <div class="nanimade-pairing-item" data-pairing="roti">
                        <div class="pairing-image">🫓</div>
                        <h4><?php _e('Fresh Roti', 'nanimade-suite'); ?></h4>
                        <p><?php _e('Perfect with warm, fresh bread', 'nanimade-suite'); ?></p>
                        <div class="pairing-rating">
                            <span class="stars">⭐⭐⭐⭐⭐</span>
                        </div>
                    </div>
                    
                    <div class="nanimade-pairing-item" data-pairing="curd">
                        <div class="pairing-image">🥛</div>
                        <h4><?php _e('Fresh Curd', 'nanimade-suite'); ?></h4>
                        <p><?php _e('Cooling complement to spicy pickles', 'nanimade-suite'); ?></p>
                        <div class="pairing-rating">
                            <span class="stars">⭐⭐⭐⭐</span>
                        </div>
                    </div>
                    
                    <div class="nanimade-pairing-item" data-pairing="dal">
                        <div class="pairing-image">🍲</div>
                        <h4><?php _e('Dal & Curry', 'nanimade-suite'); ?></h4>
                        <p><?php _e('Enhances the flavors of lentil dishes', 'nanimade-suite'); ?></p>
                        <div class="pairing-rating">
                            <span class="stars">⭐⭐⭐⭐⭐</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Texture descriptions -->
            <div class="nanimade-texture-guide">
                <h3 class="nanimade-section-title">
                    <i class="fas fa-hand-paper"></i>
                    <?php _e('Texture Experience', 'nanimade-suite'); ?>
                </h3>
                
                <div class="nanimade-texture-items">
                    <div class="nanimade-texture-item">
                        <div class="texture-visual crunch"></div>
                        <h4><?php _e('Perfect Crunch', 'nanimade-suite'); ?></h4>
                        <p><?php _e('Satisfying bite with every piece', 'nanimade-suite'); ?></p>
                    </div>
                    
                    <div class="nanimade-texture-item">
                        <div class="texture-visual juicy"></div>
                        <h4><?php _e('Juicy Interior', 'nanimade-suite'); ?></h4>
                        <p><?php _e('Bursts with authentic flavors', 'nanimade-suite'); ?></p>
                    </div>
                    
                    <div class="nanimade-texture-item">
                        <div class="texture-visual tender"></div>
                        <h4><?php _e('Tender Pieces', 'nanimade-suite'); ?></h4>
                        <p><?php _e('Easy to chew, perfect consistency', 'nanimade-suite'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tasteProfile = new NaniMadeTasteProfile();
            tasteProfile.init();
        });
        </script>
        <?php
    }
}
?>