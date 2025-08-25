<?php
/**
 * Sidebar Cart Template
 * Renders the sliding sidebar cart with advanced features
 */

if (!defined('ABSPATH')) {
    exit;
}

$sidebar_cart = new NaniMade_Sidebar_Cart();
?>

<!-- Cart Overlay -->
<div class="nanimade-cart-overlay" aria-hidden="true"></div>

<!-- Sidebar Cart -->
<aside class="nanimade-sidebar-cart" role="dialog" aria-labelledby="cart-title" aria-modal="true">
    <!-- Cart Header -->
    <header class="nanimade-cart-header">
        <h2 id="cart-title" class="nanimade-cart-title">
            <?php _e('Shopping Cart', 'nanimade-suite'); ?>
            <span class="nanimade-cart-count-header">(<?php echo WC()->cart->get_cart_contents_count(); ?>)</span>
        </h2>
        
        <button type="button" class="nanimade-cart-close" aria-label="<?php _e('Close cart', 'nanimade-suite'); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </header>
    
    <!-- Cart Content -->
    <main class="nanimade-sidebar-cart-content">
        <?php echo $sidebar_cart->get_cart_html(); ?>
    </main>
    
    <!-- Cart Recommendations -->
    <section class="nanimade-cart-recommendations" aria-labelledby="recommendations-title">
        <h3 id="recommendations-title" class="nanimade-recommendations-title">
            <?php _e('You might also like', 'nanimade-suite'); ?>
        </h3>
        <div class="nanimade-recommendations-grid" id="cart-recommendations">
            <!-- Recommendations loaded via AJAX -->
        </div>
    </section>
</aside>

<style>
/* Enhanced Sidebar Cart Styles */
.nanimade-sidebar-cart {
    /* Enhanced glassmorphism */
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-left: 1px solid rgba(255, 255, 255, 0.2);
    
    /* Enhanced shadow */
    box-shadow: 
        -10px 0 50px rgba(0, 0, 0, 0.1),
        -5px 0 20px rgba(0, 0, 0, 0.05);
}

/* Cart Header Enhancements */
.nanimade-cart-header {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 0;
    z-index: 10;
}

.nanimade-cart-title {
    display: flex;
    align-items: center;
    gap: var(--nanimade-space-2);
    font-size: var(--nanimade-text-xl);
    font-weight: 700;
    color: var(--nanimade-neutral-900);
    margin: 0;
}

.nanimade-cart-count-header {
    font-size: var(--nanimade-text-sm);
    font-weight: 500;
    color: var(--nanimade-neutral-600);
    background: var(--nanimade-neutral-100);
    padding: var(--nanimade-space-1) var(--nanimade-space-2);
    border-radius: var(--nanimade-radius-full);
}

.nanimade-cart-close {
    background: none;
    border: none;
    padding: var(--nanimade-space-2);
    cursor: pointer;
    border-radius: var(--nanimade-radius-md);
    color: var(--nanimade-neutral-600);
    transition: all var(--nanimade-transition-fast);
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    position: relative;
    overflow: hidden;
}

.nanimade-cart-close::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--nanimade-neutral-100);
    border-radius: inherit;
    opacity: 0;
    transition: opacity var(--nanimade-transition-fast);
}

.nanimade-cart-close:hover::before {
    opacity: 1;
}

.nanimade-cart-close:hover {
    color: var(--nanimade-neutral-900);
    transform: scale(1.05);
}

.nanimade-cart-close:active {
    transform: scale(0.95);
}

/* Cart Content Scrolling */
.nanimade-sidebar-cart-content {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: var(--nanimade-space-6);
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: var(--nanimade-neutral-300) transparent;
}

.nanimade-sidebar-cart-content::-webkit-scrollbar {
    width: 6px;
}

.nanimade-sidebar-cart-content::-webkit-scrollbar-track {
    background: transparent;
}

.nanimade-sidebar-cart-content::-webkit-scrollbar-thumb {
    background: var(--nanimade-neutral-300);
    border-radius: 3px;
}

.nanimade-sidebar-cart-content::-webkit-scrollbar-thumb:hover {
    background: var(--nanimade-neutral-400);
}

/* Enhanced Cart Items */
.nanimade-cart-item {
    display: flex;
    gap: var(--nanimade-space-4);
    padding: var(--nanimade-space-4);
    background: var(--nanimade-neutral-50);
    border-radius: var(--nanimade-radius-xl);
    transition: all var(--nanimade-transition-normal);
    border: 1px solid transparent;
    position: relative;
    overflow: hidden;
}

.nanimade-cart-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.05), rgba(255, 152, 0, 0.05));
    opacity: 0;
    transition: opacity var(--nanimade-transition-fast);
}

.nanimade-cart-item:hover {
    background: white;
    border-color: var(--nanimade-neutral-200);
    transform: translateY(-2px);
    box-shadow: var(--nanimade-shadow-lg);
}

.nanimade-cart-item:hover::before {
    opacity: 1;
}

/* Enhanced Cart Item Image */
.nanimade-cart-item-image {
    flex-shrink: 0;
    width: 70px;
    height: 70px;
    border-radius: var(--nanimade-radius-lg);
    overflow: hidden;
    background: white;
    box-shadow: var(--nanimade-shadow-sm);
    position: relative;
}

.nanimade-cart-item-image::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.nanimade-cart-item:hover .nanimade-cart-item-image::after {
    transform: translateX(100%);
}

.nanimade-cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--nanimade-transition-normal);
}

.nanimade-cart-item:hover .nanimade-cart-item-image img {
    transform: scale(1.05);
}

/* Enhanced Quantity Controls */
.nanimade-quantity-controls {
    display: flex;
    align-items: center;
    background: white;
    border: 2px solid var(--nanimade-neutral-200);
    border-radius: var(--nanimade-radius-lg);
    overflow: hidden;
    box-shadow: var(--nanimade-shadow-sm);
    transition: all var(--nanimade-transition-fast);
}

.nanimade-quantity-controls:focus-within {
    border-color: var(--nanimade-primary);
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

.nanimade-qty-btn {
    background: none;
    border: none;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: var(--nanimade-text-base);
    font-weight: 700;
    color: var(--nanimade-neutral-600);
    transition: all var(--nanimade-transition-fast);
    position: relative;
    overflow: hidden;
}

.nanimade-qty-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--nanimade-primary);
    opacity: 0;
    transition: opacity var(--nanimade-transition-fast);
}

.nanimade-qty-btn:hover::before {
    opacity: 0.1;
}

.nanimade-qty-btn:hover {
    color: var(--nanimade-primary);
    transform: scale(1.1);
}

.nanimade-qty-btn:active {
    transform: scale(0.9);
}

.nanimade-qty-input {
    border: none;
    width: 50px;
    height: 36px;
    text-align: center;
    font-size: var(--nanimade-text-sm);
    font-weight: 600;
    color: var(--nanimade-neutral-900);
    background: transparent;
    outline: none;
}

/* Enhanced Cart Totals */
.nanimade-cart-totals {
    margin-top: var(--nanimade-space-8);
    padding: var(--nanimade-space-6);
    background: linear-gradient(135deg, var(--nanimade-neutral-50), white);
    border-radius: var(--nanimade-radius-xl);
    border: 1px solid var(--nanimade-neutral-200);
    box-shadow: var(--nanimade-shadow-sm);
    position: relative;
    overflow: hidden;
}

.nanimade-cart-totals::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--nanimade-primary), var(--nanimade-secondary), var(--nanimade-accent));
}

.nanimade-cart-totals > div {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--nanimade-space-3) 0;
    font-size: var(--nanimade-text-sm);
    transition: all var(--nanimade-transition-fast);
}

.nanimade-cart-totals > div:not(:last-child) {
    border-bottom: 1px solid var(--nanimade-neutral-200);
}

.nanimade-cart-totals > div:hover {
    background: rgba(76, 175, 80, 0.02);
    margin: 0 calc(-1 * var(--nanimade-space-3));
    padding-left: var(--nanimade-space-3);
    padding-right: var(--nanimade-space-3);
    border-radius: var(--nanimade-radius-md);
}

.nanimade-cart-total {
    font-weight: 700;
    font-size: var(--nanimade-text-lg);
    color: var(--nanimade-neutral-900);
    background: linear-gradient(135deg, var(--nanimade-primary), var(--nanimade-secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Enhanced Buttons */
.nanimade-btn {
    position: relative;
    overflow: hidden;
    font-weight: 600;
    letter-spacing: 0.025em;
    text-transform: uppercase;
    font-size: var(--nanimade-text-xs);
}

.nanimade-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.nanimade-btn:hover::before {
    left: 100%;
}

.nanimade-express-checkout {
    background: linear-gradient(135deg, var(--nanimade-accent), var(--nanimade-accent-dark));
    box-shadow: 
        var(--nanimade-shadow-lg),
        0 0 20px rgba(233, 30, 99, 0.3);
    animation: nanimade-checkout-glow 2s ease-in-out infinite alternate;
}

@keyframes nanimade-checkout-glow {
    0% {
        box-shadow: 
            var(--nanimade-shadow-lg),
            0 0 20px rgba(233, 30, 99, 0.3);
    }
    100% {
        box-shadow: 
            var(--nanimade-shadow-xl),
            0 0 30px rgba(233, 30, 99, 0.5);
    }
}

/* Cart Recommendations */
.nanimade-cart-recommendations {
    padding: var(--nanimade-space-6);
    border-top: 1px solid var(--nanimade-neutral-200);
    background: var(--nanimade-neutral-50);
}

.nanimade-recommendations-title {
    font-size: var(--nanimade-text-base);
    font-weight: 600;
    color: var(--nanimade-neutral-900);
    margin: 0 0 var(--nanimade-space-4);
    text-align: center;
}

.nanimade-recommendations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: var(--nanimade-space-4);
}

.nanimade-recommendation-item {
    background: white;
    border-radius: var(--nanimade-radius-lg);
    padding: var(--nanimade-space-3);
    text-align: center;
    transition: all var(--nanimade-transition-normal);
    border: 1px solid var(--nanimade-neutral-200);
    cursor: pointer;
}

.nanimade-recommendation-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--nanimade-shadow-lg);
    border-color: var(--nanimade-primary);
}

.nanimade-recommendation-image {
    width: 60px;
    height: 60px;
    border-radius: var(--nanimade-radius-md);
    margin: 0 auto var(--nanimade-space-2);
    overflow: hidden;
}

.nanimade-recommendation-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.nanimade-recommendation-name {
    font-size: var(--nanimade-text-xs);
    font-weight: 500;
    color: var(--nanimade-neutral-900);
    margin: 0 0 var(--nanimade-space-1);
    line-height: var(--nanimade-leading-tight);
}

.nanimade-recommendation-price {
    font-size: var(--nanimade-text-xs);
    font-weight: 600;
    color: var(--nanimade-primary);
}

/* Loading States */
.nanimade-cart-item.nanimade-loading {
    opacity: 0.7;
    pointer-events: none;
}

.nanimade-cart-item.nanimade-loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
    animation: nanimade-loading-shimmer 1.5s infinite;
}

/* Slide out animation for removed items */
.nanimade-animate-slide-out-right {
    animation: nanimade-slide-out-right 0.3s ease-in forwards;
}

@keyframes nanimade-slide-out-right {
    0% {
        transform: translateX(0);
        opacity: 1;
    }
    100% {
        transform: translateX(100%);
        opacity: 0;
    }
}

/* Dark mode enhancements */
@media (prefers-color-scheme: dark) {
    .nanimade-sidebar-cart {
        background: rgba(26, 26, 26, 0.95);
        border-left-color: rgba(255, 255, 255, 0.1);
    }
    
    .nanimade-cart-header {
        background: rgba(26, 26, 26, 0.9);
        border-bottom-color: rgba(255, 255, 255, 0.1);
    }
    
    .nanimade-cart-item {
        background: var(--nanimade-neutral-100);
    }
    
    .nanimade-cart-item:hover {
        background: var(--nanimade-neutral-200);
    }
    
    .nanimade-cart-totals {
        background: linear-gradient(135deg, var(--nanimade-neutral-100), var(--nanimade-neutral-200));
    }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
    .nanimade-cart-item,
    .nanimade-qty-btn,
    .nanimade-recommendation-item,
    .nanimade-express-checkout {
        transition: none;
        animation: none;
    }
    
    .nanimade-cart-item:hover,
    .nanimade-recommendation-item:hover {
        transform: none;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .nanimade-sidebar-cart {
        background: white;
        border-left: 3px solid black;
    }
    
    .nanimade-cart-item {
        border: 2px solid #ccc;
    }
    
    .nanimade-cart-item:hover {
        border-color: black;
    }
    
    .nanimade-btn {
        border: 2px solid currentColor;
    }
}
</style>

<script>
// Enhanced sidebar cart functionality
document.addEventListener('DOMContentLoaded', function() {
    // Load cart recommendations
    loadCartRecommendations();
    
    // Enhanced touch interactions
    const cartItems = document.querySelectorAll('.nanimade-cart-item');
    cartItems.forEach(item => {
        // Add swipe to remove functionality
        let startX = 0;
        let currentX = 0;
        let isDragging = false;
        
        item.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
            isDragging = true;
            this.style.transition = 'none';
        });
        
        item.addEventListener('touchmove', function(e) {
            if (!isDragging) return;
            
            currentX = e.touches[0].clientX;
            const deltaX = currentX - startX;
            
            if (deltaX < 0) {
                this.style.transform = `translateX(${Math.max(deltaX, -100)}px)`;
                this.style.opacity = Math.max(1 + deltaX / 200, 0.3);
            }
        });
        
        item.addEventListener('touchend', function() {
            if (!isDragging) return;
            
            isDragging = false;
            this.style.transition = '';
            
            const deltaX = currentX - startX;
            
            if (deltaX < -80) {
                // Remove item
                const removeBtn = this.querySelector('.nanimade-remove-item');
                if (removeBtn) {
                    removeBtn.click();
                }
            } else {
                // Reset position
                this.style.transform = '';
                this.style.opacity = '';
            }
        });
    });
    
    // Enhanced keyboard navigation
    const cart = document.querySelector('.nanimade-sidebar-cart');
    cart.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelector('.nanimade-cart-close').click();
        }
    });
    
    // Auto-save cart state
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                saveCartState();
            }
        });
    });
    
    observer.observe(document.querySelector('.nanimade-sidebar-cart-content'), {
        childList: true,
        subtree: true
    });
});

function loadCartRecommendations() {
    const recommendationsContainer = document.getElementById('cart-recommendations');
    if (!recommendationsContainer) return;
    
    // Show loading state
    recommendationsContainer.innerHTML = '<div class="nanimade-loading-recommendations">Loading recommendations...</div>';
    
    fetch(nanimade_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'get_cart_recommendations',
            nonce: nanimade_ajax.nonce
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.recommendations) {
            displayRecommendations(data.data.recommendations);
        } else {
            recommendationsContainer.innerHTML = '<p>No recommendations available</p>';
        }
    })
    .catch(error => {
        console.error('Error loading recommendations:', error);
        recommendationsContainer.innerHTML = '<p>Failed to load recommendations</p>';
    });
}

function displayRecommendations(recommendations) {
    const container = document.getElementById('cart-recommendations');
    
    container.innerHTML = recommendations.map(item => `
        <div class="nanimade-recommendation-item" onclick="addRecommendationToCart(${item.id})">
            <div class="nanimade-recommendation-image">
                <img src="${item.image}" alt="${item.name}" loading="lazy">
            </div>
            <div class="nanimade-recommendation-name">${item.name}</div>
            <div class="nanimade-recommendation-price">${item.price}</div>
        </div>
    `).join('');
}

function addRecommendationToCart(productId) {
    // Add haptic feedback
    if ('vibrate' in navigator) {
        navigator.vibrate(50);
    }
    
    // Add to cart logic here
    console.log('Adding product to cart:', productId);
}

function saveCartState() {
    // Save cart state to localStorage for offline functionality
    const cartData = {
        timestamp: Date.now(),
        items: Array.from(document.querySelectorAll('.nanimade-cart-item')).map(item => ({
            key: item.dataset.cartItemKey,
            quantity: item.querySelector('.nanimade-qty-input')?.value || 1
        }))
    };
    
    localStorage.setItem('nanimade_cart_state', JSON.stringify(cartData));
}
</script>