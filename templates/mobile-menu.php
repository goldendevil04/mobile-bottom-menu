<?php
/**
 * Mobile Menu Template
 * Renders the mobile bottom navigation menu
 */

if (!defined('ABSPATH')) {
    exit;
}

$mobile_menu = new NaniMade_Mobile_Menu();
$menu_items = $mobile_menu->get_menu_items();
$settings = get_option('nanimade_mobile_menu_settings', array());
?>

<nav class="nanimade-mobile-menu" role="navigation" aria-label="<?php _e('Mobile Navigation', 'nanimade-suite'); ?>">
    <?php foreach ($menu_items as $key => $item): ?>
        <a href="<?php echo esc_url($item['url']); ?>" 
           class="nanimade-menu-item nanimade-touch-feedback" 
           data-action="<?php echo esc_attr($item['action'] ?? ''); ?>"
           aria-label="<?php echo esc_attr($item['label']); ?>"
           <?php if ($item['action']): ?>onclick="return false;"<?php endif; ?>>
            
            <div class="nanimade-menu-icon">
                <?php echo $this->get_menu_icon($item['icon']); ?>
            </div>
            
            <span class="nanimade-menu-label"><?php echo esc_html($item['label']); ?></span>
            
            <?php if ($item['badge'] && $item['badge'] > 0): ?>
                <span class="nanimade-menu-badge" aria-label="<?php echo esc_attr(sprintf(__('%d items', 'nanimade-suite'), $item['badge'])); ?>">
                    <?php echo esc_html($item['badge']); ?>
                </span>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</nav>

<?php
// Helper function to get menu icons
function get_menu_icon($icon_name) {
    $icons = array(
        'home' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9,22 9,12 15,12 15,22"></polyline></svg>',
        
        'shopping-bag' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><circle cx="9" cy="8" r="2"></circle><path d="m20.2 8-2 6H5.8l-2-6"></path></svg>',
        
        'shopping-cart' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="m1 1 4 4 2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>',
        
        'heart' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7Z"></path></svg>',
        
        'user' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
        
        'search' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>',
        
        'menu' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="6" y2="6"></line><line x1="4" x2="20" y1="12" y2="12"></line><line x1="4" x2="20" y1="18" y2="18"></line></svg>'
    );
    
    return $icons[$icon_name] ?? $icons['menu'];
}
?>

<style>
/* Additional mobile menu styles for specific themes */
.nanimade-mobile-menu {
    /* Glassmorphism effect */
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.1);
}

/* Dark mode glassmorphism */
@media (prefers-color-scheme: dark) {
    .nanimade-mobile-menu {
        background: rgba(26, 26, 26, 0.8);
        border-top-color: rgba(255, 255, 255, 0.1);
        box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.3);
    }
}

/* Floating menu style */
.nanimade-menu-floating {
    margin: 16px;
    border-radius: 24px;
    border: none;
    left: 16px;
    right: 16px;
    width: auto;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
}

/* Menu item hover effects */
.nanimade-menu-item {
    position: relative;
    overflow: hidden;
}

.nanimade-menu-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at center, rgba(76, 175, 80, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.nanimade-menu-item:hover::before,
.nanimade-menu-item.active::before {
    opacity: 1;
}

/* Badge animations */
.nanimade-menu-badge {
    transform-origin: center;
    animation: nanimade-badge-appear 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes nanimade-badge-appear {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Accessibility improvements */
.nanimade-menu-item:focus {
    outline: 2px solid var(--nanimade-primary);
    outline-offset: 2px;
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .nanimade-mobile-menu {
        background: white;
        border-top: 2px solid black;
    }
    
    .nanimade-menu-item {
        border: 1px solid transparent;
    }
    
    .nanimade-menu-item:hover,
    .nanimade-menu-item:focus,
    .nanimade-menu-item.active {
        border-color: black;
        background: #f0f0f0;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .nanimade-menu-item,
    .nanimade-menu-icon,
    .nanimade-menu-badge {
        transition: none;
        animation: none;
    }
}
</style>

<script>
// Enhanced mobile menu functionality
document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.nanimade-menu-item');
    
    // Add touch feedback
    menuItems.forEach(item => {
        item.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.95)';
        });
        
        item.addEventListener('touchend', function() {
            this.style.transform = '';
        });
        
        item.addEventListener('touchcancel', function() {
            this.style.transform = '';
        });
    });
    
    // Update active states based on current page
    const currentUrl = window.location.pathname;
    menuItems.forEach(item => {
        const itemUrl = new URL(item.href).pathname;
        if (itemUrl === currentUrl) {
            item.classList.add('active');
        }
    });
    
    // Keyboard navigation
    let currentIndex = -1;
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab' && e.target.closest('.nanimade-mobile-menu')) {
            // Custom tab navigation for better UX
            e.preventDefault();
            
            if (e.shiftKey) {
                currentIndex = currentIndex <= 0 ? menuItems.length - 1 : currentIndex - 1;
            } else {
                currentIndex = currentIndex >= menuItems.length - 1 ? 0 : currentIndex + 1;
            }
            
            menuItems[currentIndex].focus();
        }
    });
});
</script>