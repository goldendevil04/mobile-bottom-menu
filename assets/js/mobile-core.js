/**
 * NaniMade Mobile Core JavaScript
 * Core functionality for mobile commerce features
 */

(function($) {
    'use strict';
    
    // Global NaniMade object
    window.NaniMade = window.NaniMade || {};
    
    // Core mobile functionality
    NaniMade.Mobile = {
        
        // Configuration
        config: {
            breakpoints: {
                mobile: 768,
                tablet: 1024,
                desktop: 1200
            },
            animations: {
                duration: 300,
                easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
            },
            touch: {
                threshold: 50,
                timeout: 300
            }
        },
        
        // Initialize mobile features
        init: function() {
            this.setupEventListeners();
            this.initMobileMenu();
            this.initSidebarCart();
            this.initTouchGestures();
            this.initScrollEffects();
            this.initLazyLoading();
            this.initPerformanceOptimizations();
            
            // Initialize after DOM is ready
            $(document).ready(() => {
                this.onDOMReady();
            });
        },
        
        // Setup global event listeners
        setupEventListeners: function() {
            // Window resize handler
            let resizeTimeout;
            $(window).on('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    this.handleResize();
                }, 250);
            });
            
            // Orientation change handler
            $(window).on('orientationchange', () => {
                setTimeout(() => {
                    this.handleOrientationChange();
                }, 100);
            });
            
            // Scroll handler with throttling
            let scrollTimeout;
            $(window).on('scroll', () => {
                if (!scrollTimeout) {
                    scrollTimeout = setTimeout(() => {
                        this.handleScroll();
                        scrollTimeout = null;
                    }, 16); // ~60fps
                }
            });
            
            // Visibility change handler
            document.addEventListener('visibilitychange', () => {
                this.handleVisibilityChange();
            });
        },
        
        // Initialize mobile menu
        initMobileMenu: function() {
            const $menu = $('.nanimade-mobile-menu');
            const $menuItems = $('.nanimade-menu-item');
            
            if (!$menu.length) return;
            
            // Menu item click handlers
            $menuItems.on('click', (e) => {
                const $item = $(e.currentTarget);
                const action = $item.data('action');
                
                if (action) {
                    e.preventDefault();
                    this.handleMenuAction(action, $item);
                }
                
                // Add active state
                $menuItems.removeClass('active');
                $item.addClass('active');
                
                // Haptic feedback
                this.triggerHapticFeedback('light');
            });
            
            // Auto-hide menu on scroll
            let lastScrollTop = 0;
            $(window).on('scroll', () => {
                const scrollTop = $(window).scrollTop();
                const scrollDelta = Math.abs(scrollTop - lastScrollTop);
                
                if (scrollDelta > 5) {
                    if (scrollTop > lastScrollTop && scrollTop > 100) {
                        $menu.addClass('hidden');
                    } else {
                        $menu.removeClass('hidden');
                    }
                    lastScrollTop = scrollTop;
                }
            });
            
            // Update cart count
            this.updateCartCount();
        },
        
        // Initialize sidebar cart
        initSidebarCart: function() {
            const $cart = $('.nanimade-sidebar-cart');
            const $overlay = $('.nanimade-cart-overlay');
            const $closeBtn = $('.nanimade-cart-close');
            
            if (!$cart.length) return;
            
            // Close cart handlers
            $closeBtn.add($overlay).on('click', () => {
                this.closeSidebarCart();
            });
            
            // Prevent cart close when clicking inside cart
            $cart.on('click', (e) => {
                e.stopPropagation();
            });
            
            // Swipe to close
            this.initSwipeToClose($cart);
            
            // Quantity controls
            this.initQuantityControls();
            
            // Coupon functionality
            this.initCouponFunctionality();
            
            // Save for later
            this.initSaveForLater();
        },
        
        // Initialize touch gestures
        initTouchGestures: function() {
            if (!('ontouchstart' in window)) return;
            
            let touchStartX = 0;
            let touchStartY = 0;
            let touchStartTime = 0;
            
            $(document).on('touchstart', (e) => {
                const touch = e.originalEvent.touches[0];
                touchStartX = touch.clientX;
                touchStartY = touch.clientY;
                touchStartTime = Date.now();
            });
            
            $(document).on('touchend', (e) => {
                const touch = e.originalEvent.changedTouches[0];
                const touchEndX = touch.clientX;
                const touchEndY = touch.clientY;
                const touchEndTime = Date.now();
                
                const deltaX = touchEndX - touchStartX;
                const deltaY = touchEndY - touchStartY;
                const deltaTime = touchEndTime - touchStartTime;
                
                // Detect swipe gestures
                if (Math.abs(deltaX) > this.config.touch.threshold && 
                    deltaTime < this.config.touch.timeout) {
                    
                    if (deltaX > 0) {
                        this.handleSwipeRight(e);
                    } else {
                        this.handleSwipeLeft(e);
                    }
                }
                
                if (Math.abs(deltaY) > this.config.touch.threshold && 
                    deltaTime < this.config.touch.timeout) {
                    
                    if (deltaY > 0) {
                        this.handleSwipeDown(e);
                    } else {
                        this.handleSwipeUp(e);
                    }
                }
            });
        },
        
        // Initialize scroll effects
        initScrollEffects: function() {
            const $revealElements = $('.nanimade-scroll-reveal, .nanimade-scroll-reveal-left, .nanimade-scroll-reveal-right, .nanimade-scroll-reveal-scale');
            
            if (!$revealElements.length) return;
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        $(entry.target).addClass('revealed');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            $revealElements.each((index, element) => {
                observer.observe(element);
            });
        },
        
        // Initialize lazy loading
        initLazyLoading: function() {
            const $lazyImages = $('img[data-src]');
            
            if (!$lazyImages.length) return;
            
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const $img = $(entry.target);
                        const src = $img.data('src');
                        
                        $img.attr('src', src).removeAttr('data-src');
                        $img.addClass('nanimade-animate-fade-in');
                        
                        imageObserver.unobserve(entry.target);
                    }
                });
            });
            
            $lazyImages.each((index, img) => {
                imageObserver.observe(img);
            });
        },
        
        // Initialize performance optimizations
        initPerformanceOptimizations: function() {
            // Add will-change to animated elements
            $('.nanimade-animate-float, .nanimade-animate-bubble, .nanimade-hover-lift')
                .addClass('nanimade-will-change');
            
            // GPU acceleration for transforms
            $('.nanimade-mobile-menu, .nanimade-sidebar-cart')
                .addClass('nanimade-gpu-accelerated');
            
            // Preload critical resources
            this.preloadCriticalResources();
        },
        
        // Handle menu actions
        handleMenuAction: function(action, $item) {
            switch (action) {
                case 'toggle-cart':
                    this.toggleSidebarCart();
                    break;
                case 'toggle-wishlist':
                    this.toggleWishlistPanel();
                    break;
                case 'toggle-search':
                    this.toggleSearchPanel();
                    break;
                default:
                    console.log('Unknown menu action:', action);
            }
        },
        
        // Toggle sidebar cart
        toggleSidebarCart: function() {
            const $cart = $('.nanimade-sidebar-cart');
            const $overlay = $('.nanimade-cart-overlay');
            
            if ($cart.hasClass('open')) {
                this.closeSidebarCart();
            } else {
                this.openSidebarCart();
            }
        },
        
        // Open sidebar cart
        openSidebarCart: function() {
            const $cart = $('.nanimade-sidebar-cart');
            const $overlay = $('.nanimade-cart-overlay');
            const $body = $('body');
            
            $cart.addClass('open');
            $overlay.addClass('active');
            $body.addClass('nanimade-cart-open');
            
            // Prevent body scroll
            $body.css('overflow', 'hidden');
            
            // Focus management
            $cart.find('.nanimade-cart-close').focus();
            
            // Track analytics
            this.trackEvent('cart_opened');
        },
        
        // Close sidebar cart
        closeSidebarCart: function() {
            const $cart = $('.nanimade-sidebar-cart');
            const $overlay = $('.nanimade-cart-overlay');
            const $body = $('body');
            
            $cart.removeClass('open');
            $overlay.removeClass('active');
            $body.removeClass('nanimade-cart-open');
            
            // Restore body scroll
            $body.css('overflow', '');
            
            // Track analytics
            this.trackEvent('cart_closed');
        },
        
        // Initialize quantity controls
        initQuantityControls: function() {
            $(document).on('click', '.nanimade-qty-btn', (e) => {
                e.preventDefault();
                
                const $btn = $(e.currentTarget);
                const $input = $btn.siblings('.nanimade-qty-input');
                const action = $btn.data('action');
                const currentQty = parseInt($input.val()) || 1;
                const cartItemKey = $btn.closest('.nanimade-cart-item').data('cart-item-key');
                
                let newQty = currentQty;
                
                if (action === 'increase') {
                    newQty = currentQty + 1;
                } else if (action === 'decrease' && currentQty > 1) {
                    newQty = currentQty - 1;
                }
                
                if (newQty !== currentQty) {
                    $input.val(newQty);
                    this.updateCartQuantity(cartItemKey, newQty);
                    this.triggerHapticFeedback('light');
                }
            });
            
            // Direct input changes
            $(document).on('change', '.nanimade-qty-input', (e) => {
                const $input = $(e.currentTarget);
                const newQty = parseInt($input.val()) || 1;
                const cartItemKey = $input.closest('.nanimade-cart-item').data('cart-item-key');
                
                this.updateCartQuantity(cartItemKey, newQty);
            });
        },
        
        // Initialize coupon functionality
        initCouponFunctionality: function() {
            $(document).on('click', '.nanimade-apply-coupon', (e) => {
                e.preventDefault();
                
                const $btn = $(e.currentTarget);
                const $input = $('.nanimade-coupon-input');
                const couponCode = $input.val().trim();
                
                if (!couponCode) {
                    this.showNotification('Please enter a coupon code', 'warning');
                    return;
                }
                
                this.applyCoupon(couponCode, $btn);
            });
            
            // Enter key support
            $(document).on('keypress', '.nanimade-coupon-input', (e) => {
                if (e.which === 13) {
                    $('.nanimade-apply-coupon').click();
                }
            });
        },
        
        // Initialize save for later
        initSaveForLater: function() {
            $(document).on('click', '.nanimade-save-later', (e) => {
                e.preventDefault();
                
                const $btn = $(e.currentTarget);
                const cartItemKey = $btn.closest('.nanimade-cart-item').data('cart-item-key');
                
                this.saveForLater(cartItemKey, $btn);
            });
            
            $(document).on('click', '.nanimade-remove-item', (e) => {
                e.preventDefault();
                
                const $btn = $(e.currentTarget);
                const cartItemKey = $btn.closest('.nanimade-cart-item').data('cart-item-key');
                
                this.removeFromCart(cartItemKey, $btn);
            });
        },
        
        // Initialize swipe to close
        initSwipeToClose: function($element) {
            let startX = 0;
            let currentX = 0;
            let isDragging = false;
            
            $element.on('touchstart', (e) => {
                startX = e.originalEvent.touches[0].clientX;
                isDragging = true;
                $element.css('transition', 'none');
            });
            
            $element.on('touchmove', (e) => {
                if (!isDragging) return;
                
                currentX = e.originalEvent.touches[0].clientX;
                const deltaX = currentX - startX;
                
                if (deltaX > 0) {
                    $element.css('transform', `translateX(${deltaX}px)`);
                }
            });
            
            $element.on('touchend', () => {
                if (!isDragging) return;
                
                isDragging = false;
                $element.css('transition', '');
                
                const deltaX = currentX - startX;
                
                if (deltaX > 100) {
                    this.closeSidebarCart();
                } else {
                    $element.css('transform', '');
                }
            });
        },
        
        // Update cart quantity via AJAX
        updateCartQuantity: function(cartItemKey, quantity) {
            const $cartItem = $(`.nanimade-cart-item[data-cart-item-key="${cartItemKey}"]`);
            
            // Show loading state
            $cartItem.addClass('nanimade-loading');
            
            $.ajax({
                url: nanimade_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_cart_quantity',
                    nonce: nanimade_ajax.nonce,
                    cart_item_key: cartItemKey,
                    quantity: quantity
                },
                success: (response) => {
                    if (response.success) {
                        this.updateCartDisplay(response.data);
                        this.showNotification('Cart updated', 'success');
                    } else {
                        this.showNotification(response.data.message || 'Update failed', 'error');
                    }
                },
                error: () => {
                    this.showNotification('Network error', 'error');
                },
                complete: () => {
                    $cartItem.removeClass('nanimade-loading');
                }
            });
        },
        
        // Apply coupon via AJAX
        applyCoupon: function(couponCode, $btn) {
            const originalText = $btn.text();
            
            $btn.text('Applying...').prop('disabled', true);
            
            $.ajax({
                url: nanimade_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'apply_coupon_sidebar',
                    nonce: nanimade_ajax.nonce,
                    coupon_code: couponCode
                },
                success: (response) => {
                    if (response.success) {
                        this.updateCartDisplay(response.data);
                        this.showNotification(response.data.message, 'success');
                        $('.nanimade-coupon-input').val('');
                    } else {
                        this.showNotification(response.data.message || 'Coupon failed', 'error');
                    }
                },
                error: () => {
                    this.showNotification('Network error', 'error');
                },
                complete: () => {
                    $btn.text(originalText).prop('disabled', false);
                }
            });
        },
        
        // Save item for later
        saveForLater: function(cartItemKey, $btn) {
            const originalHtml = $btn.html();
            
            $btn.html('<div class="nanimade-spinner"></div>').prop('disabled', true);
            
            $.ajax({
                url: nanimade_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'save_for_later',
                    nonce: nanimade_ajax.nonce,
                    cart_item_key: cartItemKey
                },
                success: (response) => {
                    if (response.success) {
                        this.updateCartDisplay(response.data);
                        this.showNotification(response.data.message, 'success');
                    } else {
                        this.showNotification(response.data.message || 'Save failed', 'error');
                    }
                },
                error: () => {
                    this.showNotification('Network error', 'error');
                },
                complete: () => {
                    $btn.html(originalHtml).prop('disabled', false);
                }
            });
        },
        
        // Remove item from cart
        removeFromCart: function(cartItemKey, $btn) {
            const $cartItem = $btn.closest('.nanimade-cart-item');
            
            // Animate removal
            $cartItem.addClass('nanimade-animate-slide-out-right');
            
            setTimeout(() => {
                $.ajax({
                    url: nanimade_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'remove_from_cart_sidebar',
                        nonce: nanimade_ajax.nonce,
                        cart_item_key: cartItemKey
                    },
                    success: (response) => {
                        if (response.success) {
                            this.updateCartDisplay(response.data);
                            this.showNotification(response.data.message, 'success');
                        } else {
                            this.showNotification(response.data.message || 'Remove failed', 'error');
                            $cartItem.removeClass('nanimade-animate-slide-out-right');
                        }
                    },
                    error: () => {
                        this.showNotification('Network error', 'error');
                        $cartItem.removeClass('nanimade-animate-slide-out-right');
                    }
                });
            }, 300);
        },
        
        // Update cart display
        updateCartDisplay: function(data) {
            if (data.cart_html) {
                $('.nanimade-sidebar-cart-content').html(data.cart_html);
            }
            
            if (data.cart_count !== undefined) {
                this.updateCartCount(data.cart_count);
            }
        },
        
        // Update cart count in menu
        updateCartCount: function(count) {
            if (count === undefined) {
                // Fetch current count
                $.ajax({
                    url: nanimade_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_cart_count',
                        nonce: nanimade_ajax.nonce
                    },
                    success: (response) => {
                        if (response.success) {
                            this.updateCartBadge(response.data.count);
                        }
                    }
                });
            } else {
                this.updateCartBadge(count);
            }
        },
        
        // Update cart badge
        updateCartBadge: function(count) {
            const $badge = $('.nanimade-menu-item[data-action="toggle-cart"] .nanimade-menu-badge');
            
            if (count > 0) {
                $badge.text(count).show().addClass('nanimade-animate-bounce-in');
                setTimeout(() => {
                    $badge.removeClass('nanimade-animate-bounce-in');
                }, 600);
            } else {
                $badge.hide();
            }
        },
        
        // Show notification
        showNotification: function(message, type = 'info') {
            const $notification = $(`
                <div class="nanimade-notification nanimade-notification-${type}">
                    <span class="nanimade-notification-message">${message}</span>
                    <button class="nanimade-notification-close">&times;</button>
                </div>
            `);
            
            $('body').append($notification);
            
            // Animate in
            setTimeout(() => {
                $notification.addClass('show');
            }, 10);
            
            // Auto hide
            setTimeout(() => {
                this.hideNotification($notification);
            }, 3000);
            
            // Manual close
            $notification.find('.nanimade-notification-close').on('click', () => {
                this.hideNotification($notification);
            });
        },
        
        // Hide notification
        hideNotification: function($notification) {
            $notification.removeClass('show');
            setTimeout(() => {
                $notification.remove();
            }, 300);
        },
        
        // Trigger haptic feedback
        triggerHapticFeedback: function(type = 'light') {
            if ('vibrate' in navigator && nanimade_ajax.settings.haptic_feedback_enabled) {
                const patterns = {
                    light: [10],
                    medium: [20],
                    heavy: [30],
                    success: [10, 50, 10],
                    error: [50, 50, 50]
                };
                
                navigator.vibrate(patterns[type] || patterns.light);
            }
        },
        
        // Track analytics event
        trackEvent: function(eventName, data = {}) {
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, {
                    event_category: 'NaniMade Mobile',
                    ...data
                });
            }
            
            // Custom analytics tracking
            $.ajax({
                url: nanimade_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'track_user_interaction',
                    nonce: nanimade_ajax.nonce,
                    event_name: eventName,
                    event_data: JSON.stringify(data),
                    page_url: window.location.href
                }
            });
        },
        
        // Handle window resize
        handleResize: function() {
            const width = $(window).width();
            
            // Update mobile state
            if (width < this.config.breakpoints.mobile) {
                $('body').addClass('nanimade-is-mobile').removeClass('nanimade-is-tablet nanimade-is-desktop');
            } else if (width < this.config.breakpoints.tablet) {
                $('body').addClass('nanimade-is-tablet').removeClass('nanimade-is-mobile nanimade-is-desktop');
            } else {
                $('body').addClass('nanimade-is-desktop').removeClass('nanimade-is-mobile nanimade-is-tablet');
            }
            
            // Close cart on desktop
            if (width >= this.config.breakpoints.tablet) {
                this.closeSidebarCart();
            }
        },
        
        // Handle orientation change
        handleOrientationChange: function() {
            // Force layout recalculation
            const $menu = $('.nanimade-mobile-menu');
            $menu.hide().show();
            
            // Update viewport height for iOS
            if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
                const vh = window.innerHeight * 0.01;
                document.documentElement.style.setProperty('--vh', `${vh}px`);
            }
        },
        
        // Handle scroll
        handleScroll: function() {
            const scrollTop = $(window).scrollTop();
            
            // Update scroll-based animations
            this.updateScrollAnimations(scrollTop);
            
            // Track scroll depth
            this.trackScrollDepth(scrollTop);
        },
        
        // Handle visibility change
        handleVisibilityChange: function() {
            if (document.hidden) {
                this.trackEvent('page_hidden');
            } else {
                this.trackEvent('page_visible');
            }
        },
        
        // Handle swipe gestures
        handleSwipeRight: function(e) {
            // Open cart on swipe right from edge
            if (e.originalEvent.changedTouches[0].clientX < 50) {
                this.openSidebarCart();
            }
        },
        
        handleSwipeLeft: function(e) {
            // Close cart on swipe left
            if ($('.nanimade-sidebar-cart').hasClass('open')) {
                this.closeSidebarCart();
            }
        },
        
        handleSwipeUp: function(e) {
            // Show menu on swipe up
            $('.nanimade-mobile-menu').removeClass('hidden');
        },
        
        handleSwipeDown: function(e) {
            // Hide menu on swipe down
            $('.nanimade-mobile-menu').addClass('hidden');
        },
        
        // Update scroll animations
        updateScrollAnimations: function(scrollTop) {
            // Parallax effects
            $('.nanimade-parallax').each(function() {
                const $element = $(this);
                const speed = $element.data('speed') || 0.5;
                const yPos = -(scrollTop * speed);
                $element.css('transform', `translateY(${yPos}px)`);
            });
        },
        
        // Track scroll depth
        trackScrollDepth: function(scrollTop) {
            const windowHeight = $(window).height();
            const documentHeight = $(document).height();
            const scrollPercent = Math.round((scrollTop / (documentHeight - windowHeight)) * 100);
            
            // Track milestones
            const milestones = [25, 50, 75, 100];
            milestones.forEach(milestone => {
                if (scrollPercent >= milestone && !this.scrollMilestones?.[milestone]) {
                    this.scrollMilestones = this.scrollMilestones || {};
                    this.scrollMilestones[milestone] = true;
                    this.trackEvent('scroll_depth', { percent: milestone });
                }
            });
        },
        
        // Preload critical resources
        preloadCriticalResources: function() {
            const resources = [
                nanimade_ajax.ajax_url,
                // Add other critical resources
            ];
            
            resources.forEach(url => {
                const link = document.createElement('link');
                link.rel = 'prefetch';
                link.href = url;
                document.head.appendChild(link);
            });
        },
        
        // DOM ready handler
        onDOMReady: function() {
            // Initialize stagger animations
            $('.nanimade-stagger-container').each(function() {
                const $container = $(this);
                setTimeout(() => {
                    $container.addClass('animate');
                }, 100);
            });
            
            // Initialize touch ripple effects
            $('.nanimade-touch-ripple').on('touchstart click', function(e) {
                const $element = $(this);
                const rect = this.getBoundingClientRect();
                const x = (e.originalEvent.touches?.[0]?.clientX || e.clientX) - rect.left;
                const y = (e.originalEvent.touches?.[0]?.clientY || e.clientY) - rect.top;
                
                const $ripple = $('<span class="nanimade-ripple-effect"></span>');
                $ripple.css({
                    left: x,
                    top: y
                });
                
                $element.append($ripple);
                
                setTimeout(() => {
                    $ripple.remove();
                }, 600);
            });
            
            // Initialize performance monitoring
            this.initPerformanceMonitoring();
        },
        
        // Initialize performance monitoring
        initPerformanceMonitoring: function() {
            if ('performance' in window) {
                // Monitor page load time
                window.addEventListener('load', () => {
                    setTimeout(() => {
                        const perfData = performance.getEntriesByType('navigation')[0];
                        this.trackEvent('page_performance', {
                            load_time: Math.round(perfData.loadEventEnd - perfData.fetchStart),
                            dom_ready: Math.round(perfData.domContentLoadedEventEnd - perfData.fetchStart)
                        });
                    }, 0);
                });
            }
        }
    };
    
    // Initialize when DOM is ready
    $(document).ready(() => {
        NaniMade.Mobile.init();
    });
    
})(jQuery);