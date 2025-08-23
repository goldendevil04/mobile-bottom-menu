/**
 * NaniMade Complete Pickle Commerce Suite - Main JavaScript
 */

class NaniMadeSuite {
    constructor() {
        this.cart = null;
        this.menu = null;
        this.pwa = null;
        this.analytics = null;
        this.touchHandler = null;
        
        this.init();
    }
    
    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initComponents());
        } else {
            this.initComponents();
        }
    }
    
    initComponents() {
        // Initialize core components
        this.initMobileMenu();
        this.initSidebarCart();
        this.initTouchGestures();
        this.initPWAFeatures();
        this.initAnalytics();
        this.initNotifications();
        
        // Initialize pickle-specific features
        this.initPickleAnimations();
        this.initSeasonalThemes();
        this.initVoiceSearch();
        
        console.log('NaniMade Suite initialized successfully');
    }
    
    initMobileMenu() {
        const menu = document.getElementById('nanimadeMobileMenu');
        if (!menu) return;
        
        this.menu = new NaniMadeMobileMenu(menu);
        
        // Add click handlers for menu items
        const menuItems = menu.querySelectorAll('.nanimade-menu-item');
        menuItems.forEach(item => {
            item.addEventListener('click', (e) => this.handleMenuClick(e));
            
            // Add haptic feedback simulation
            item.addEventListener('touchstart', () => {
                item.classList.add('haptic-feedback');
                setTimeout(() => item.classList.remove('haptic-feedback'), 100);
            });
        });
        
        // Handle cart item click
        const cartItem = menu.querySelector('[data-item="cart"]');
        if (cartItem) {
            cartItem.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSidebarCart();
            });
        }
        
        // Add magnetic snap effect
        this.initMagneticEffect(menuItems);
    }
    
    initSidebarCart() {
        const cart = document.getElementById('nanimadeSidebarCart');
        const overlay = document.getElementById('nanimadeCartOverlay');
        const closeBtn = document.getElementById('nanimadeCartClose');
        
        if (!cart) return;
        
        this.cart = new NaniMadeSidebarCart(cart);
        
        // Close cart handlers
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeSidebarCart());
        }
        
        if (overlay) {
            overlay.addEventListener('click', () => this.closeSidebarCart());
        }
        
        // Quantity controls
        this.initQuantityControls();
        
        // Swipe to remove
        this.initSwipeToRemove();
    }
    
    initTouchGestures() {
        this.touchHandler = new NaniMadeTouchHandler();
        
        // Pull to refresh
        this.initPullToRefresh();
        
        // Swipe navigation
        this.initSwipeNavigation();
        
        // Pinch to zoom (for product images)
        this.initPinchZoom();
    }
    
    initPWAFeatures() {
        this.pwa = new NaniMadePWA();
        
        // Check if app can be installed
        this.checkPWAInstallability();
        
        // Handle offline functionality
        this.initOfflineSupport();
        
        // Push notifications
        this.initPushNotifications();
    }
    
    initAnalytics() {
        this.analytics = new NaniMadeAnalytics();
        
        // Track user interactions
        this.trackMenuInteractions();
        this.trackCartActions();
        this.trackProductViews();
    }
    
    initNotifications() {
        // Create notification container
        if (!document.getElementById('nanimadeNotifications')) {
            const container = document.createElement('div');
            container.id = 'nanimadeNotifications';
            container.className = 'nanimade-notifications-container';
            document.body.appendChild(container);
        }
    }
    
    initPickleAnimations() {
        // Floating pickle bubbles
        this.initPickleBubbles();
        
        // Spice level animations
        this.initSpiceAnimations();
        
        // Kitchen sound effects (optional)
        this.initKitchenSounds();
    }
    
    initSeasonalThemes() {
        const currentMonth = new Date().getMonth();
        let season = 'summer'; // default
        
        if (currentMonth >= 2 && currentMonth <= 4) season = 'spring';
        else if (currentMonth >= 5 && currentMonth <= 7) season = 'summer';
        else if (currentMonth >= 8 && currentMonth <= 10) season = 'autumn';
        else season = 'winter';
        
        document.body.classList.add(`nanimade-season-${season}`);
    }
    
    initVoiceSearch() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            return;
        }
        
        const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-IN';
        
        // Add voice search button to menu
        this.addVoiceSearchButton(recognition);
    }
    
    handleMenuClick(e) {
        const item = e.currentTarget;
        const itemType = item.dataset.item;
        
        // Add click animation
        item.classList.add('clicked');
        setTimeout(() => item.classList.remove('clicked'), 200);
        
        // Track analytics
        if (this.analytics) {
            this.analytics.trackEvent('menu_click', {
                item: itemType,
                timestamp: Date.now()
            });
        }
        
        // Handle special items
        if (itemType === 'cart') {
            e.preventDefault();
            this.toggleSidebarCart();
        }
    }
    
    toggleSidebarCart() {
        const cart = document.getElementById('nanimadeSidebarCart');
        const overlay = document.getElementById('nanimadeCartOverlay');
        
        if (!cart) return;
        
        const isActive = cart.classList.contains('active');
        
        if (isActive) {
            this.closeSidebarCart();
        } else {
            this.openSidebarCart();
        }
    }
    
    openSidebarCart() {
        const cart = document.getElementById('nanimadeSidebarCart');
        const overlay = document.getElementById('nanimadeCartOverlay');
        
        if (cart) cart.classList.add('active');
        if (overlay) overlay.classList.add('active');
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Update cart content
        this.updateCartContent();
        
        // Track analytics
        if (this.analytics) {
            this.analytics.trackEvent('cart_opened', {
                timestamp: Date.now()
            });
        }
    }
    
    closeSidebarCart() {
        const cart = document.getElementById('nanimadeSidebarCart');
        const overlay = document.getElementById('nanimadeCartOverlay');
        
        if (cart) cart.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        
        // Restore body scroll
        document.body.style.overflow = '';
    }
    
    initQuantityControls() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.nanimade-qty-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.nanimade-qty-btn');
                const cartItemKey = btn.dataset.cartItemKey;
                const isPlus = btn.classList.contains('nanimade-qty-plus');
                const quantitySpan = btn.parentElement.querySelector('.nanimade-quantity');
                let currentQty = parseInt(quantitySpan.textContent);
                
                if (isPlus) {
                    currentQty++;
                } else {
                    currentQty = Math.max(0, currentQty - 1);
                }
                
                this.updateCartQuantity(cartItemKey, currentQty);
            }
        });
    }
    
    initSwipeToRemove() {
        let startX = 0;
        let currentX = 0;
        let isDragging = false;
        
        document.addEventListener('touchstart', (e) => {
            const cartItem = e.target.closest('.nanimade-cart-item');
            if (!cartItem) return;
            
            startX = e.touches[0].clientX;
            isDragging = true;
        });
        
        document.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            
            const cartItem = e.target.closest('.nanimade-cart-item');
            if (!cartItem) return;
            
            currentX = e.touches[0].clientX;
            const diffX = startX - currentX;
            
            if (diffX > 0) {
                cartItem.style.transform = `translateX(-${Math.min(diffX, 60)}px)`;
            }
        });
        
        document.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            
            const cartItem = e.target.closest('.nanimade-cart-item');
            if (!cartItem) return;
            
            const diffX = startX - currentX;
            
            if (diffX > 60) {
                // Remove item
                const cartItemKey = cartItem.dataset.cartItemKey;
                this.removeCartItem(cartItemKey);
            } else {
                // Snap back
                cartItem.style.transform = '';
            }
            
            isDragging = false;
        });
    }
    
    initMagneticEffect(menuItems) {
        menuItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                // Add magnetic effect to nearby items
                const rect = item.getBoundingClientRect();
                const centerX = rect.left + rect.width / 2;
                
                menuItems.forEach(otherItem => {
                    if (otherItem === item) return;
                    
                    const otherRect = otherItem.getBoundingClientRect();
                    const otherCenterX = otherRect.left + otherRect.width / 2;
                    const distance = Math.abs(centerX - otherCenterX);
                    
                    if (distance < 100) {
                        otherItem.classList.add('magnetic');
                    }
                });
            });
            
            item.addEventListener('mouseleave', () => {
                menuItems.forEach(otherItem => {
                    otherItem.classList.remove('magnetic');
                });
            });
        });
    }
    
    initPickleBubbles() {
        const menuItems = document.querySelectorAll('.nanimade-menu-item');
        
        menuItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                this.createPickleBubbles(item);
            });
        });
    }
    
    createPickleBubbles(element) {
        const bubbleContainer = element.querySelector('.nanimade-pickle-bubbles');
        if (!bubbleContainer) return;
        
        // Clear existing bubbles
        bubbleContainer.innerHTML = '';
        
        // Create new bubbles
        for (let i = 0; i < 5; i++) {
            const bubble = document.createElement('span');
            bubble.className = 'bubble';
            bubble.style.left = Math.random() * 80 + 10 + '%';
            bubble.style.animationDelay = Math.random() * 2 + 's';
            bubbleContainer.appendChild(bubble);
        }
    }
    
    initSpiceAnimations() {
        const spiceIndicators = document.querySelectorAll('.nanimade-spice-indicator');
        
        spiceIndicators.forEach(indicator => {
            const chilis = indicator.querySelectorAll('.chili');
            
            indicator.parentElement.addEventListener('mouseenter', () => {
                chilis.forEach((chili, index) => {
                    setTimeout(() => {
                        chili.style.animation = 'chiliGlow 0.5s ease-out';
                    }, index * 100);
                });
            });
        });
    }
    
    initKitchenSounds() {
        // Optional: Add subtle kitchen sounds
        // This would require audio files and user permission
        if (localStorage.getItem('nanimade_sounds_enabled') === 'true') {
            // Initialize audio context and load sounds
            this.initAudioContext();
        }
    }
    
    initPullToRefresh() {
        let startY = 0;
        let currentY = 0;
        let isPulling = false;
        
        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0) {
                startY = e.touches[0].clientY;
                isPulling = true;
            }
        });
        
        document.addEventListener('touchmove', (e) => {
            if (!isPulling) return;
            
            currentY = e.touches[0].clientY;
            const pullDistance = currentY - startY;
            
            if (pullDistance > 0 && pullDistance < 100) {
                e.preventDefault();
                this.showPullToRefreshIndicator(pullDistance);
            }
        });
        
        document.addEventListener('touchend', () => {
            if (!isPulling) return;
            
            const pullDistance = currentY - startY;
            
            if (pullDistance > 60) {
                this.triggerRefresh();
            }
            
            this.hidePullToRefreshIndicator();
            isPulling = false;
        });
    }
    
    initSwipeNavigation() {
        let startX = 0;
        let startY = 0;
        
        document.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });
        
        document.addEventListener('touchend', (e) => {
            const endX = e.changedTouches[0].clientX;
            const endY = e.changedTouches[0].clientY;
            
            const diffX = startX - endX;
            const diffY = startY - endY;
            
            // Check if it's a horizontal swipe
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                if (diffX > 0) {
                    // Swipe left - next page/product
                    this.handleSwipeLeft();
                } else {
                    // Swipe right - previous page/product
                    this.handleSwipeRight();
                }
            }
        });
    }
    
    initPinchZoom() {
        let initialDistance = 0;
        let currentScale = 1;
        
        document.addEventListener('touchstart', (e) => {
            if (e.touches.length === 2) {
                const touch1 = e.touches[0];
                const touch2 = e.touches[1];
                initialDistance = this.getDistance(touch1, touch2);
            }
        });
        
        document.addEventListener('touchmove', (e) => {
            if (e.touches.length === 2) {
                e.preventDefault();
                
                const touch1 = e.touches[0];
                const touch2 = e.touches[1];
                const currentDistance = this.getDistance(touch1, touch2);
                
                const scale = currentDistance / initialDistance;
                currentScale = Math.min(Math.max(scale, 0.5), 3);
                
                const target = e.target.closest('.nanimade-zoomable');
                if (target) {
                    target.style.transform = `scale(${currentScale})`;
                }
            }
        });
    }
    
    getDistance(touch1, touch2) {
        const dx = touch1.clientX - touch2.clientX;
        const dy = touch1.clientY - touch2.clientY;
        return Math.sqrt(dx * dx + dy * dy);
    }
    
    updateCartQuantity(cartItemKey, quantity) {
        const data = {
            action: 'nanimade_update_cart',
            cart_item_key: cartItemKey,
            quantity: quantity,
            nonce: nanimade_ajax.nonce
        };
        
        // Show loading state
        this.showCartLoading();
        
        fetch(nanimade_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateCartDisplay(data.data);
                this.showNotification('Cart updated successfully!', 'success');
            } else {
                this.showNotification('Failed to update cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Something went wrong', 'error');
        })
        .finally(() => {
            this.hideCartLoading();
        });
    }
    
    removeCartItem(cartItemKey) {
        this.updateCartQuantity(cartItemKey, 0);
    }
    
    updateCartDisplay(data) {
        // Update cart count badge
        const badge = document.getElementById('nanimadeCartBadge');
        if (badge) {
            badge.textContent = data.cart_count;
            badge.classList.add('bounce');
            setTimeout(() => badge.classList.remove('bounce'), 600);
        }
        
        // Update cart total
        const total = document.getElementById('nanimadeCartTotal');
        if (total) {
            total.innerHTML = data.cart_total;
        }
        
        // Update cart content
        const content = document.getElementById('nanimadeCartContent');
        if (content && data.cart_html) {
            content.innerHTML = data.cart_html;
        }
    }
    
    showCartLoading() {
        const cart = document.getElementById('nanimadeSidebarCart');
        if (cart) {
            cart.classList.add('nanimade-cart-loading');
        }
    }
    
    hideCartLoading() {
        const cart = document.getElementById('nanimadeSidebarCart');
        if (cart) {
            cart.classList.remove('nanimade-cart-loading');
        }
    }
    
    showNotification(message, type = 'info') {
        const container = document.getElementById('nanimadeNotifications');
        if (!container) return;
        
        const notification = document.createElement('div');
        notification.className = `nanimade-notification ${type}`;
        notification.innerHTML = `
            <div class="nanimade-notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;
        
        container.appendChild(notification);
        
        // Show notification
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Auto remove
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || icons.info;
    }
    
    checkPWAInstallability() {
        let deferredPrompt;
        
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            this.showPWAInstallPrompt(deferredPrompt);
        });
    }
    
    showPWAInstallPrompt(deferredPrompt) {
        const prompt = document.createElement('div');
        prompt.className = 'nanimade-pwa-prompt';
        prompt.innerHTML = `
            <div class="nanimade-pwa-content">
                <div class="nanimade-pwa-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="nanimade-pwa-text">
                    <h4>Install NaniMade App</h4>
                    <p>Get the full app experience with offline access and notifications.</p>
                </div>
            </div>
            <div class="nanimade-pwa-actions">
                <button class="nanimade-pwa-btn secondary" id="nanimadePwaLater">Later</button>
                <button class="nanimade-pwa-btn primary" id="nanimadePwaInstall">Install</button>
            </div>
        `;
        
        document.body.appendChild(prompt);
        
        setTimeout(() => prompt.classList.add('show'), 100);
        
        // Handle install
        document.getElementById('nanimadePwaInstall').addEventListener('click', () => {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('PWA installed');
                }
                deferredPrompt = null;
                prompt.remove();
            });
        });
        
        // Handle later
        document.getElementById('nanimadePwaLater').addEventListener('click', () => {
            prompt.classList.remove('show');
            setTimeout(() => prompt.remove(), 300);
        });
    }
    
    trackMenuInteractions() {
        const menuItems = document.querySelectorAll('.nanimade-menu-item');
        
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                if (this.analytics) {
                    this.analytics.trackEvent('menu_interaction', {
                        item: item.dataset.item,
                        timestamp: Date.now()
                    });
                }
            });
        });
    }
    
    trackCartActions() {
        // Track cart opens, closes, and modifications
        document.addEventListener('click', (e) => {
            if (e.target.closest('.nanimade-add-to-cart-btn')) {
                if (this.analytics) {
                    this.analytics.trackEvent('add_to_cart_attempt', {
                        timestamp: Date.now()
                    });
                }
            }
        });
    }
    
    trackProductViews() {
        // Track product page views and interactions
        if (document.body.classList.contains('single-product')) {
            if (this.analytics) {
                this.analytics.trackEvent('product_view', {
                    product_id: this.getCurrentProductId(),
                    timestamp: Date.now()
                });
            }
        }
    }
    
    getCurrentProductId() {
        const productElement = document.querySelector('[data-product-id]');
        return productElement ? productElement.dataset.productId : null;
    }
    
    addVoiceSearchButton(recognition) {
        const fab = document.getElementById('nanimadeFab');
        if (!fab) return;
        
        fab.addEventListener('click', () => {
            this.startVoiceSearch(recognition);
        });
    }
    
    startVoiceSearch(recognition) {
        const overlay = document.createElement('div');
        overlay.className = 'nanimade-voice-search';
        overlay.innerHTML = '<i class="fas fa-microphone"></i>';
        document.body.appendChild(overlay);
        
        setTimeout(() => overlay.classList.add('active'), 100);
        
        recognition.start();
        
        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            this.handleVoiceSearchResult(transcript);
            overlay.remove();
        };
        
        recognition.onerror = () => {
            overlay.remove();
            this.showNotification('Voice search failed', 'error');
        };
        
        recognition.onend = () => {
            overlay.remove();
        };
    }
    
    handleVoiceSearchResult(transcript) {
        // Simple voice search handling
        const searchTerm = transcript.toLowerCase();
        
        if (searchTerm.includes('mango') || searchTerm.includes('pickle')) {
            window.location.href = '/shop/?s=mango+pickle';
        } else if (searchTerm.includes('cart')) {
            this.openSidebarCart();
        } else if (searchTerm.includes('home')) {
            window.location.href = '/';
        } else {
            window.location.href = `/shop/?s=${encodeURIComponent(transcript)}`;
        }
    }
    
    showPullToRefreshIndicator(distance) {
        let indicator = document.getElementById('nanimadePullRefresh');
        
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'nanimadePullRefresh';
            indicator.className = 'nanimade-pull-refresh';
            indicator.innerHTML = '<i class="fas fa-sync-alt"></i>';
            document.body.appendChild(indicator);
        }
        
        const progress = Math.min(distance / 60, 1);
        indicator.style.transform = `translateX(-50%) translateY(${-60 + (distance * 0.5)}px) rotate(${progress * 360}deg)`;
        indicator.style.opacity = progress;
    }
    
    hidePullToRefreshIndicator() {
        const indicator = document.getElementById('nanimadePullRefresh');
        if (indicator) {
            indicator.style.transform = 'translateX(-50%) translateY(-60px)';
            indicator.style.opacity = '0';
        }
    }
    
    triggerRefresh() {
        // Refresh cart content and product information
        this.updateCartContent();
        this.showNotification('Content refreshed!', 'success');
    }
    
    updateCartContent() {
        // Fetch updated cart content via AJAX
        const data = {
            action: 'nanimade_get_cart_content',
            nonce: nanimade_ajax.nonce
        };
        
        fetch(nanimade_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const content = document.getElementById('nanimadeCartContent');
                if (content) {
                    content.innerHTML = data.data.html;
                }
                
                // Update cart count
                const badge = document.getElementById('nanimadeCartBadge');
                if (badge) {
                    badge.textContent = data.data.cart_count;
                }
            }
        })
        .catch(error => {
            console.error('Error updating cart:', error);
        });
    }
    
    handleSwipeLeft() {
        // Navigate to next product or page
        const nextLink = document.querySelector('.next-product, .nav-next a');
        if (nextLink) {
            nextLink.click();
        }
    }
    
    handleSwipeRight() {
        // Navigate to previous product or page
        const prevLink = document.querySelector('.prev-product, .nav-previous a');
        if (prevLink) {
            prevLink.click();
        }
    }
    
    initOfflineSupport() {
        // Register service worker for offline functionality
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/nanimade-sw.js')
                .then(registration => {
                    console.log('Service Worker registered:', registration);
                })
                .catch(error => {
                    console.log('Service Worker registration failed:', error);
                });
        }
        
        // Handle online/offline status
        window.addEventListener('online', () => {
            this.showNotification('Back online! Syncing data...', 'success');
            this.syncOfflineData();
        });
        
        window.addEventListener('offline', () => {
            this.showNotification('You\'re offline. Some features may be limited.', 'warning');
        });
    }
    
    initPushNotifications() {
        if ('Notification' in window && 'serviceWorker' in navigator) {
            // Request permission for notifications
            if (Notification.permission === 'default') {
                setTimeout(() => {
                    this.requestNotificationPermission();
                }, 5000); // Wait 5 seconds before asking
            }
        }
    }
    
    requestNotificationPermission() {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                this.showNotification('Notifications enabled! You\'ll get updates about your orders.', 'success');
                this.subscribeToNotifications();
            }
        });
    }
    
    subscribeToNotifications() {
        // Subscribe to push notifications
        navigator.serviceWorker.ready.then(registration => {
            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array('YOUR_VAPID_PUBLIC_KEY')
            });
        }).then(subscription => {
            // Send subscription to server
            this.sendSubscriptionToServer(subscription);
        });
    }
    
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');
        
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
    
    sendSubscriptionToServer(subscription) {
        // Send subscription to WordPress backend
        const data = {
            action: 'nanimade_save_push_subscription',
            subscription: JSON.stringify(subscription),
            nonce: nanimade_ajax.nonce
        };
        
        fetch(nanimade_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        });
    }
    
    syncOfflineData() {
        // Sync any offline cart changes or analytics data
        const offlineData = localStorage.getItem('nanimade_offline_data');
        if (offlineData) {
            const data = JSON.parse(offlineData);
            // Send offline data to server
            this.sendOfflineDataToServer(data);
            localStorage.removeItem('nanimade_offline_data');
        }
    }
    
    sendOfflineDataToServer(data) {
        fetch(nanimade_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'nanimade_sync_offline_data',
                data: data,
                nonce: nanimade_ajax.nonce
            })
        });
    }
}

// Supporting Classes
class NaniMadeMobileMenu {
    constructor(element) {
        this.element = element;
        this.init();
    }
    
    init() {
        // Add entrance animation
        setTimeout(() => {
            this.element.classList.add('nanimade-menu-visible');
        }, 100);
    }
}

class NaniMadeSidebarCart {
    constructor(element) {
        this.element = element;
        this.init();
    }
    
    init() {
        // Initialize cart-specific functionality
        this.setupSwipeGestures();
    }
    
    setupSwipeGestures() {
        // Implement swipe to close cart
        let startX = 0;
        
        this.element.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });
        
        this.element.addEventListener('touchmove', (e) => {
            const currentX = e.touches[0].clientX;
            const diffX = currentX - startX;
            
            if (diffX > 0) {
                this.element.style.transform = `translateX(${Math.min(diffX, 100)}px)`;
            }
        });
        
        this.element.addEventListener('touchend', (e) => {
            const endX = e.changedTouches[0].clientX;
            const diffX = endX - startX;
            
            if (diffX > 100) {
                // Close cart
                window.nanimadeSuite.closeSidebarCart();
            } else {
                // Snap back
                this.element.style.transform = '';
            }
        });
    }
}

class NaniMadeTouchHandler {
    constructor() {
        this.init();
    }
    
    init() {
        // Add touch-friendly enhancements
        this.addTouchFeedback();
        this.optimizeScrolling();
    }
    
    addTouchFeedback() {
        document.addEventListener('touchstart', (e) => {
            const target = e.target.closest('.nanimade-menu-item, .nanimade-btn, button');
            if (target) {
                target.classList.add('touching');
            }
        });
        
        document.addEventListener('touchend', (e) => {
            const target = e.target.closest('.nanimade-menu-item, .nanimade-btn, button');
            if (target) {
                setTimeout(() => target.classList.remove('touching'), 150);
            }
        });
    }
    
    optimizeScrolling() {
        // Improve scroll performance on mobile
        const scrollElements = document.querySelectorAll('.nanimade-cart-content');
        scrollElements.forEach(element => {
            element.style.webkitOverflowScrolling = 'touch';
        });
    }
}

class NaniMadePWA {
    constructor() {
        this.init();
    }
    
    init() {
        // PWA initialization
        this.registerServiceWorker();
        this.handleInstallPrompt();
    }
    
    registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/nanimade-sw.js');
        }
    }
    
    handleInstallPrompt() {
        // Handle PWA install prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            // Store the event for later use
            window.deferredPrompt = e;
        });
    }
}

class NaniMadeAnalytics {
    constructor() {
        this.sessionId = this.generateSessionId();
        this.events = [];
        this.init();
    }
    
    init() {
        // Initialize analytics tracking
        this.trackPageView();
        this.setupPerformanceTracking();
    }
    
    generateSessionId() {
        return 'nanimade_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    trackEvent(eventType, eventData) {
        const event = {
            type: eventType,
            data: eventData,
            sessionId: this.sessionId,
            timestamp: Date.now(),
            url: window.location.href
        };
        
        this.events.push(event);
        
        // Send to server periodically
        if (this.events.length >= 10) {
            this.sendEvents();
        }
    }
    
    trackPageView() {
        this.trackEvent('page_view', {
            page: window.location.pathname,
            referrer: document.referrer
        });
    }
    
    setupPerformanceTracking() {
        // Track performance metrics
        window.addEventListener('load', () => {
            const perfData = performance.getEntriesByType('navigation')[0];
            this.trackEvent('performance', {
                loadTime: perfData.loadEventEnd - perfData.loadEventStart,
                domContentLoaded: perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart
            });
        });
    }
    
    sendEvents() {
        if (this.events.length === 0) return;
        
        const data = {
            action: 'nanimade_track_events',
            events: JSON.stringify(this.events),
            nonce: nanimade_ajax.nonce
        };
        
        fetch(nanimade_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        }).then(() => {
            this.events = [];
        });
    }
}

// Initialize the suite when DOM is ready
window.nanimadeSuite = new NaniMadeSuite();

// Export for use in other scripts
window.NaniMadeSuite = NaniMadeSuite;