<?php
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['submit']) && wp_verify_nonce($_POST['nanimade_nonce'], 'nanimade_settings')) {
    $settings = array(
        'mobile_menu_enabled' => isset($_POST['mobile_menu_enabled']),
        'sidebar_cart_enabled' => isset($_POST['sidebar_cart_enabled']),
        'pwa_enabled' => isset($_POST['pwa_enabled']),
        'animations_enabled' => isset($_POST['animations_enabled']),
        'design_style' => sanitize_text_field($_POST['design_style']),
        'primary_color' => sanitize_hex_color($_POST['primary_color']),
        'secondary_color' => sanitize_hex_color($_POST['secondary_color']),
        'accent_color' => sanitize_hex_color($_POST['accent_color']),
        'enable_analytics' => isset($_POST['enable_analytics']),
        'enable_push_notifications' => isset($_POST['enable_push_notifications']),
        'enable_voice_search' => isset($_POST['enable_voice_search']),
        'enable_haptic_feedback' => isset($_POST['enable_haptic_feedback']),
    );
    
    update_option('nanimade_suite_settings', $settings);
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'nanimade-suite') . '</p></div>';
}

$settings = get_option('nanimade_suite_settings', array());
?>

<div class="wrap nanimade-admin-wrap">
    <div class="nanimade-admin-header">
        <h1>
            <i class="fas fa-pepper-hot"></i>
            <?php _e('NaniMade Complete Pickle Commerce Suite', 'nanimade-suite'); ?>
        </h1>
        <p class="nanimade-admin-subtitle">
            <?php _e('Complete mobile commerce solution with advanced Elementor Pro integration', 'nanimade-suite'); ?>
        </p>
    </div>
    
    <div class="nanimade-admin-container">
        <div class="nanimade-admin-main">
            <form method="post" action="">
                <?php wp_nonce_field('nanimade_settings', 'nanimade_nonce'); ?>
                
                <!-- General Settings -->
                <div class="nanimade-settings-section">
                    <h2>
                        <i class="fas fa-cog"></i>
                        <?php _e('General Settings', 'nanimade-suite'); ?>
                    </h2>
                    
                    <div class="nanimade-settings-grid">
                        <div class="nanimade-setting-item">
                            <label class="nanimade-toggle">
                                <input type="checkbox" name="mobile_menu_enabled" <?php checked(isset($settings['mobile_menu_enabled']) ? $settings['mobile_menu_enabled'] : true); ?>>
                                <span class="nanimade-toggle-slider"></span>
                                <span class="nanimade-toggle-label"><?php _e('Enable Mobile Bottom Menu', 'nanimade-suite'); ?></span>
                            </label>
                            <p class="nanimade-setting-description">
                                <?php _e('Show the mobile app-style bottom navigation menu', 'nanimade-suite'); ?>
                            </p>
                        </div>
                        
                        <div class="nanimade-setting-item">
                            <label class="nanimade-toggle">
                                <input type="checkbox" name="sidebar_cart_enabled" <?php checked(isset($settings['sidebar_cart_enabled']) ? $settings['sidebar_cart_enabled'] : true); ?>>
                                <span class="nanimade-toggle-slider"></span>
                                <span class="nanimade-toggle-label"><?php _e('Enable Sidebar Cart', 'nanimade-suite'); ?></span>
                            </label>
                            <p class="nanimade-setting-description">
                                <?php _e('Advanced sliding cart with live updates and recommendations', 'nanimade-suite'); ?>
                            </p>
                        </div>
                        
                        <div class="nanimade-setting-item">
                            <label class="nanimade-toggle">
                                <input type="checkbox" name="pwa_enabled" <?php checked(isset($settings['pwa_enabled']) ? $settings['pwa_enabled'] : true); ?>>
                                <span class="nanimade-toggle-slider"></span>
                                <span class="nanimade-toggle-label"><?php _e('Enable PWA Features', 'nanimade-suite'); ?></span>
                            </label>
                            <p class="nanimade-setting-description">
                                <?php _e('Offline support, push notifications, and app installation', 'nanimade-suite'); ?>
                            </p>
                        </div>
                        
                        <div class="nanimade-setting-item">
                            <label class="nanimade-toggle">
                                <input type="checkbox" name="animations_enabled" <?php checked(isset($settings['animations_enabled']) ? $settings['animations_enabled'] : true); ?>>
                                <span class="nanimade-toggle-slider"></span>
                                <span class="nanimade-toggle-label"><?php _e('Enable Animations', 'nanimade-suite'); ?></span>
                            </label>
                            <p class="nanimade-setting-description">
                                <?php _e('Smooth animations and micro-interactions for better UX', 'nanimade-suite'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Design Settings -->
                <div class="nanimade-settings-section">
                    <h2>
                        <i class="fas fa-palette"></i>
                        <?php _e('Design & Appearance', 'nanimade-suite'); ?>
                    </h2>
                    
                    <div class="nanimade-settings-grid">
                        <div class="nanimade-setting-item full-width">
                            <label for="design_style"><?php _e('Design Style', 'nanimade-suite'); ?></label>
                            <select name="design_style" id="design_style" class="nanimade-select">
                                <option value="pickle-modern" <?php selected(isset($settings['design_style']) ? $settings['design_style'] : 'pickle-modern', 'pickle-modern'); ?>>
                                    <?php _e('Pickle Modern - Glassmorphism with pickle themes', 'nanimade-suite'); ?>
                                </option>
                                <option value="pickle-minimal" <?php selected(isset($settings['design_style']) ? $settings['design_style'] : '', 'pickle-minimal'); ?>>
                                    <?php _e('Pickle Minimal - Clean and simple', 'nanimade-suite'); ?>
                                </option>
                                <option value="pickle-classic" <?php selected(isset($settings['design_style']) ? $settings['design_style'] : '', 'pickle-classic'); ?>>
                                    <?php _e('Pickle Classic - Traditional Indian style', 'nanimade-suite'); ?>
                                </option>
                                <option value="pickle-gradient" <?php selected(isset($settings['design_style']) ? $settings['design_style'] : '', 'pickle-gradient'); ?>>
                                    <?php _e('Pickle Gradient - Vibrant and colorful', 'nanimade-suite'); ?>
                                </option>
                            </select>
                        </div>
                        
                        <div class="nanimade-setting-item">
                            <label for="primary_color"><?php _e('Primary Color', 'nanimade-suite'); ?></label>
                            <input type="text" name="primary_color" id="primary_color" value="<?php echo esc_attr(isset($settings['primary_color']) ? $settings['primary_color'] : '#ff6b35'); ?>" class="nanimade-color-picker">
                            <p class="nanimade-setting-description"><?php _e('Main brand color for buttons and accents', 'nanimade-suite'); ?></p>
                        </div>
                        
                        <div class="nanimade-setting-item">
                            <label for="secondary_color"><?php _e('Secondary Color', 'nanimade-suite'); ?></label>
                            <input type="text" name="secondary_color" id="secondary_color" value="<?php echo esc_attr(isset($settings['secondary_color']) ? $settings['secondary_color'] : '#28a745'); ?>" class="nanimade-color-picker">
                            <p class="nanimade-setting-description"><?php _e('Secondary color for success states and highlights', 'nanimade-suite'); ?></p>
                        </div>
                        
                        <div class="nanimade-setting-item">
                            <label for="accent_color"><?php _e('Accent Color', 'nanimade-suite'); ?></label>
                            <input type="text" name="accent_color" id="accent_color" value="<?php echo esc_attr(isset($settings['accent_color']) ? $settings['accent_color'] : '#ffc107'); ?>" class="nanimade-color-picker">
                            <p class="nanimade-setting-description"><?php _e('Accent color for special offers and notifications', 'nanimade-suite'); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Features -->
                <div class="nanimade-settings-section">
                    <h2>
                        <i class="fas fa-rocket"></i>
                        <?php _e('Advanced Features', 'nanimade-suite'); ?>
                    </h2>
                    
                    <div class="nanimade-settings-grid">
                        <div class="nanimade-setting-item">
                            <label class="nanimade-toggle">
                                <input type="checkbox" name="enable_analytics" <?php checked(isset($settings['enable_analytics']) ? $settings['enable_analytics'] : true); ?>>
                                <span class="nanimade-toggle-slider"></span>
                                <span class="nanimade-toggle-label"><?php _e('Enable Analytics', 'nanimade-suite'); ?></span>
                            </label>
                            <p class="nanimade-setting-description">
                                <?php _e('Track user behavior and conversion metrics', 'nanimade-suite'); ?>
                            </p>
                        </div>
                        
                        <div class="nanimade-setting-item">
                            <label class="nanimade-toggle">
                                <input type="checkbox" name="enable_push_notifications" <?php checked(isset($settings['enable_push_notifications']) ? $settings['enable_push_notifications'] : true); ?>>
                                <span class="nanimade-toggle-slider"></span>
                                <span class="nanimade-toggle-label"><?php _e('Enable Push Notifications', 'nanimade-suite'); ?></span>
                            </label>
                            <p class="nanimade-setting-description">
                                <?php _e('Send order updates and promotional notifications', 'nanimade-suite'); ?>
                            </p>
                        </div>
                        
                        <div class="nanimade-setting-item">
                            <label class="nanimade-toggle">
                                <input type="checkbox" name="enable_voice_search" <?php checked(isset($settings['enable_voice_search']) ? $settings['enable_voice_search'] : true); ?>>
                                <span class="nanimade-toggle-slider"></span>
                                <span class="nanimade-toggle-label"><?php _e('Enable Voice Search', 'nanimade-suite'); ?></span>
                            </label>
                            <p class="nanimade-setting-description">
                                <?php _e('Allow customers to search using voice commands', 'nanimade-suite'); ?>
                            </p>
                        </div>
                        
                        <div class="nanimade-setting-item">
                            <label class="nanimade-toggle">
                                <input type="checkbox" name="enable_haptic_feedback" <?php checked(isset($settings['enable_haptic_feedback']) ? $settings['enable_haptic_feedback'] : true); ?>>
                                <span class="nanimade-toggle-slider"></span>
                                <span class="nanimade-toggle-label"><?php _e('Enable Haptic Feedback', 'nanimade-suite'); ?></span>
                            </label>
                            <p class="nanimade-setting-description">
                                <?php _e('Vibration feedback for touch interactions on mobile', 'nanimade-suite'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Pickle Business Features -->
                <div class="nanimade-settings-section">
                    <h2>
                        <i class="fas fa-jar"></i>
                        <?php _e('Pickle Business Features', 'nanimade-suite'); ?>
                    </h2>
                    
                    <div class="nanimade-feature-showcase">
                        <div class="nanimade-feature-card">
                            <div class="nanimade-feature-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                            <h3><?php _e('Interactive Jar Customizer', 'nanimade-suite'); ?></h3>
                            <p><?php _e('3D jar visualization with real-time spice level adjustment and custom combinations', 'nanimade-suite'); ?></p>
                            <span class="nanimade-feature-status enabled"><?php _e('Enabled', 'nanimade-suite'); ?></span>
                        </div>
                        
                        <div class="nanimade-feature-card">
                            <div class="nanimade-feature-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h3><?php _e('Recipe Story Timeline', 'nanimade-suite'); ?></h3>
                            <p><?php _e('Interactive cooking process with family stories and ingredient timelines', 'nanimade-suite'); ?></p>
                            <span class="nanimade-feature-status enabled"><?php _e('Enabled', 'nanimade-suite'); ?></span>
                        </div>
                        
                        <div class="nanimade-feature-card">
                            <div class="nanimade-feature-icon">
                                <i class="fas fa-palette"></i>
                            </div>
                            <h3><?php _e('Taste Profile Selector', 'nanimade-suite'); ?></h3>
                            <p><?php _e('Interactive flavor wheel with spice meters and food pairing suggestions', 'nanimade-suite'); ?></p>
                            <span class="nanimade-feature-status enabled"><?php _e('Enabled', 'nanimade-suite'); ?></span>
                        </div>
                        
                        <div class="nanimade-feature-card">
                            <div class="nanimade-feature-icon">
                                <i class="fas fa-images"></i>
                            </div>
                            <h3><?php _e('Smart Product Gallery', 'nanimade-suite'); ?></h3>
                            <p><?php _e('Touch-friendly gallery with zoom, 360° view, and process videos', 'nanimade-suite'); ?></p>
                            <span class="nanimade-feature-status enabled"><?php _e('Enabled', 'nanimade-suite'); ?></span>
                        </div>
                        
                        <div class="nanimade-feature-card">
                            <div class="nanimade-feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3><?php _e('Trust Signals', 'nanimade-suite'); ?></h3>
                            <p><?php _e('Quality badges, freshness indicators, and authenticity verification', 'nanimade-suite'); ?></p>
                            <span class="nanimade-feature-status enabled"><?php _e('Enabled', 'nanimade-suite'); ?></span>
                        </div>
                        
                        <div class="nanimade-feature-card">
                            <div class="nanimade-feature-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h3><?php _e('PWA Capabilities', 'nanimade-suite'); ?></h3>
                            <p><?php _e('Offline browsing, push notifications, and app-like installation', 'nanimade-suite'); ?></p>
                            <span class="nanimade-feature-status enabled"><?php _e('Enabled', 'nanimade-suite'); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Analytics Dashboard -->
                <div class="nanimade-settings-section">
                    <h2>
                        <i class="fas fa-chart-line"></i>
                        <?php _e('Analytics Dashboard', 'nanimade-suite'); ?>
                    </h2>
                    
                    <div class="nanimade-analytics-grid">
                        <div class="nanimade-analytics-card">
                            <div class="nanimade-analytics-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="nanimade-analytics-content">
                                <h3><?php echo $this->get_total_users(); ?></h3>
                                <p><?php _e('Total Users', 'nanimade-suite'); ?></p>
                            </div>
                        </div>
                        
                        <div class="nanimade-analytics-card">
                            <div class="nanimade-analytics-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="nanimade-analytics-content">
                                <h3><?php echo $this->get_cart_interactions(); ?></h3>
                                <p><?php _e('Cart Interactions', 'nanimade-suite'); ?></p>
                            </div>
                        </div>
                        
                        <div class="nanimade-analytics-card">
                            <div class="nanimade-analytics-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="nanimade-analytics-content">
                                <h3><?php echo $this->get_mobile_usage(); ?>%</h3>
                                <p><?php _e('Mobile Usage', 'nanimade-suite'); ?></p>
                            </div>
                        </div>
                        
                        <div class="nanimade-analytics-card">
                            <div class="nanimade-analytics-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="nanimade-analytics-content">
                                <h3><?php echo $this->get_app_installs(); ?></h3>
                                <p><?php _e('App Installs', 'nanimade-suite'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Elementor Integration -->
                <div class="nanimade-settings-section">
                    <h2>
                        <i class="fab fa-elementor"></i>
                        <?php _e('Elementor Pro Integration', 'nanimade-suite'); ?>
                    </h2>
                    
                    <div class="nanimade-elementor-widgets">
                        <p class="nanimade-integration-description">
                            <?php _e('All widgets are automatically available in Elementor under the "NaniMade Pickle Suite" category. No additional setup required!', 'nanimade-suite'); ?>
                        </p>
                        
                        <div class="nanimade-widget-list">
                            <div class="nanimade-widget-item">
                                <i class="fas fa-jar"></i>
                                <span><?php _e('Pickle Jar Customizer', 'nanimade-suite'); ?></span>
                                <span class="nanimade-widget-status"><?php _e('Active', 'nanimade-suite'); ?></span>
                            </div>
                            
                            <div class="nanimade-widget-item">
                                <i class="fas fa-book-open"></i>
                                <span><?php _e('Recipe Story Timeline', 'nanimade-suite'); ?></span>
                                <span class="nanimade-widget-status"><?php _e('Active', 'nanimade-suite'); ?></span>
                            </div>
                            
                            <div class="nanimade-widget-item">
                                <i class="fas fa-palette"></i>
                                <span><?php _e('Taste Profile Selector', 'nanimade-suite'); ?></span>
                                <span class="nanimade-widget-status"><?php _e('Active', 'nanimade-suite'); ?></span>
                            </div>
                            
                            <div class="nanimade-widget-item">
                                <i class="fas fa-images"></i>
                                <span><?php _e('Smart Product Gallery', 'nanimade-suite'); ?></span>
                                <span class="nanimade-widget-status"><?php _e('Active', 'nanimade-suite'); ?></span>
                            </div>
                            
                            <div class="nanimade-widget-item">
                                <i class="fas fa-mobile-alt"></i>
                                <span><?php _e('Mobile Menu Pro', 'nanimade-suite'); ?></span>
                                <span class="nanimade-widget-status"><?php _e('Active', 'nanimade-suite'); ?></span>
                            </div>
                            
                            <div class="nanimade-widget-item">
                                <i class="fas fa-shopping-basket"></i>
                                <span><?php _e('Sidebar Cart Widget', 'nanimade-suite'); ?></span>
                                <span class="nanimade-widget-status"><?php _e('Active', 'nanimade-suite'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="nanimade-settings-footer">
                    <button type="submit" name="submit" class="nanimade-btn nanimade-btn-primary">
                        <i class="fas fa-save"></i>
                        <?php _e('Save Settings', 'nanimade-suite'); ?>
                    </button>
                    
                    <button type="button" class="nanimade-btn nanimade-btn-secondary" id="nanimadePreview">
                        <i class="fas fa-eye"></i>
                        <?php _e('Preview Changes', 'nanimade-suite'); ?>
                    </button>
                    
                    <button type="button" class="nanimade-btn nanimade-btn-outline" id="nanimadeReset">
                        <i class="fas fa-undo"></i>
                        <?php _e('Reset to Defaults', 'nanimade-suite'); ?>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Sidebar -->
        <div class="nanimade-admin-sidebar">
            <div class="nanimade-sidebar-card">
                <h3>
                    <i class="fas fa-question-circle"></i>
                    <?php _e('Quick Start Guide', 'nanimade-suite'); ?>
                </h3>
                <ol class="nanimade-quick-steps">
                    <li><?php _e('Configure your design style and colors above', 'nanimade-suite'); ?></li>
                    <li><?php _e('Edit any page with Elementor', 'nanimade-suite'); ?></li>
                    <li><?php _e('Add NaniMade widgets from the widget panel', 'nanimade-suite'); ?></li>
                    <li><?php _e('Customize each widget to match your brand', 'nanimade-suite'); ?></li>
                    <li><?php _e('Test on mobile devices for best experience', 'nanimade-suite'); ?></li>
                </ol>
            </div>
            
            <div class="nanimade-sidebar-card">
                <h3>
                    <i class="fas fa-mobile-alt"></i>
                    <?php _e('Mobile Preview', 'nanimade-suite'); ?>
                </h3>
                <div class="nanimade-mobile-preview">
                    <div class="nanimade-phone-frame">
                        <div class="nanimade-phone-screen">
                            <div class="nanimade-preview-menu">
                                <div class="preview-item">
                                    <i class="fas fa-home"></i>
                                    <span>Home</span>
                                </div>
                                <div class="preview-item">
                                    <i class="fas fa-pepper-hot"></i>
                                    <span>Pickles</span>
                                </div>
                                <div class="preview-item active">
                                    <i class="fas fa-shopping-basket"></i>
                                    <span>Cart</span>
                                    <span class="preview-badge">3</span>
                                </div>
                                <div class="preview-item">
                                    <i class="fas fa-user-circle"></i>
                                    <span>Account</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="nanimade-preview-note">
                    <?php _e('Live preview updates as you change settings', 'nanimade-suite'); ?>
                </p>
            </div>
            
            <div class="nanimade-sidebar-card">
                <h3>
                    <i class="fas fa-heart"></i>
                    <?php _e('Support NaniMade', 'nanimade-suite'); ?>
                </h3>
                <p><?php _e('This plugin is completely free! If you find it helpful, please consider:', 'nanimade-suite'); ?></p>
                <ul class="nanimade-support-list">
                    <li><i class="fas fa-star"></i> <?php _e('Rating it 5 stars', 'nanimade-suite'); ?></li>
                    <li><i class="fas fa-share"></i> <?php _e('Sharing with friends', 'nanimade-suite'); ?></li>
                    <li><i class="fas fa-bug"></i> <?php _e('Reporting bugs', 'nanimade-suite'); ?></li>
                    <li><i class="fas fa-lightbulb"></i> <?php _e('Suggesting features', 'nanimade-suite'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize color pickers
    $('.nanimade-color-picker').wpColorPicker({
        change: function(event, ui) {
            updatePreview();
        }
    });
    
    // Live preview updates
    function updatePreview() {
        const primaryColor = $('#primary_color').val();
        const secondaryColor = $('#secondary_color').val();
        const accentColor = $('#accent_color').val();
        
        // Update CSS variables for preview
        document.documentElement.style.setProperty('--nanimade-primary', primaryColor);
        document.documentElement.style.setProperty('--nanimade-secondary', secondaryColor);
        document.documentElement.style.setProperty('--nanimade-accent', accentColor);
        
        // Update mobile preview
        updateMobilePreview();
    }
    
    function updateMobilePreview() {
        const style = $('#design_style').val();
        const preview = $('.nanimade-preview-menu');
        
        preview.removeClass('style-pickle-modern style-pickle-minimal style-pickle-classic style-pickle-gradient');
        preview.addClass('style-' + style);
    }
    
    // Settings change handlers
    $('input, select').on('change', updatePreview);
    
    // Preview button
    $('#nanimadePreview').on('click', function() {
        // Open preview in new tab
        window.open('/?nanimade_preview=1', '_blank');
    });
    
    // Reset button
    $('#nanimadeReset').on('click', function() {
        if (confirm('<?php _e('Are you sure you want to reset all settings to defaults?', 'nanimade-suite'); ?>')) {
            // Reset form to defaults
            $('input[type="checkbox"]').prop('checked', true);
            $('#design_style').val('pickle-modern');
            $('#primary_color').val('#ff6b35').trigger('change');
            $('#secondary_color').val('#28a745').trigger('change');
            $('#accent_color').val('#ffc107').trigger('change');
            
            updatePreview();
        }
    });
    
    // Initialize preview
    updatePreview();
});
</script>

<?php
// Helper methods for analytics
function get_total_users() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nanimade_analytics';
    $count = $wpdb->get_var("SELECT COUNT(DISTINCT session_id) FROM $table_name WHERE event_type = 'page_view' AND timestamp > DATE_SUB(NOW(), INTERVAL 30 DAY)");
    return $count ?: 0;
}

function get_cart_interactions() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nanimade_analytics';
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE event_type IN ('cart_opened', 'add_to_cart_attempt') AND timestamp > DATE_SUB(NOW(), INTERVAL 30 DAY)");
    return $count ?: 0;
}

function get_mobile_usage() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nanimade_analytics';
    
    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE event_type = 'page_view' AND timestamp > DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $mobile = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE event_type = 'page_view' AND JSON_EXTRACT(event_data, '$.is_mobile') = true AND timestamp > DATE_SUB(NOW(), INTERVAL 30 DAY)");
    
    return $total > 0 ? round(($mobile / $total) * 100) : 0;
}

function get_app_installs() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nanimade_analytics';
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE event_type = 'pwa_installed'");
    return $count ?: 0;
}
?>