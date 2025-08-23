/**
 * Progressive Web App Features for NaniMade Suite
 */

class NaniMadePWAFeatures {
    constructor() {
        this.isOnline = navigator.onLine;
        this.offlineData = new Map();
        this.syncQueue = [];
        
        this.init();
    }
    
    init() {
        this.registerServiceWorker();
        this.setupOfflineHandling();
        this.initCaching();
        this.setupBackgroundSync();
        this.initPushNotifications();
        this.addInstallPrompt();
        this.setupOfflineIndicator();
    }
    
    registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/nanimade-sw.js', {
                scope: '/'
            })
            .then(registration => {
                console.log('Service Worker registered:', registration.scope);
                
                // Handle updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.showUpdateAvailable();
                        }
                    });
                });
            })
            .catch(error => {
                console.log('Service Worker registration failed:', error);
            });
            
            // Listen for messages from service worker
            navigator.serviceWorker.addEventListener('message', (event) => {
                this.handleServiceWorkerMessage(event.data);
            });
        }
    }
    
    setupOfflineHandling() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.handleOnline();
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.handleOffline();
        });
        
        // Check initial state
        if (!this.isOnline) {
            this.handleOffline();
        }
    }
    
    handleOnline() {
        this.hideOfflineIndicator();
        this.syncOfflineData();
        window.nanimadeSuite?.showNotification('Back online! Syncing your data...', 'success');
    }
    
    handleOffline() {
        this.showOfflineIndicator();
        window.nanimadeSuite?.showNotification('You\'re offline. Your cart is saved locally.', 'warning');
    }
    
    initCaching() {
        // Cache important resources
        this.cacheEssentialResources();
        
        // Setup dynamic caching for product images
        this.setupImageCaching();
        
        // Cache cart data locally
        this.setupCartCaching();
    }
    
    cacheEssentialResources() {
        const essentialResources = [
            '/',
            '/shop/',
            '/cart/',
            '/my-account/',
            nanimade_ajax.ajax_url,
            // Add more essential URLs
        ];
        
        if ('caches' in window) {
            caches.open('nanimade-essential-v1').then(cache => {
                cache.addAll(essentialResources);
            });
        }
    }
    
    setupImageCaching() {
        // Intercept image requests and cache them
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({
                type: 'CACHE_IMAGES',
                urls: this.getProductImageUrls()
            });
        }
    }
    
    getProductImageUrls() {
        const images = document.querySelectorAll('img[src*="wp-content/uploads"]');
        return Array.from(images).map(img => img.src);
    }
    
    setupCartCaching() {
        // Save cart state to localStorage for offline access
        this.saveCartState();
        
        // Listen for cart changes
        document.addEventListener('nanimade_cart_updated', () => {
            this.saveCartState();
        });
    }
    
    saveCartState() {
        if (!this.isOnline) {
            const cartData = {
                items: this.getCartItems(),
                total: this.getCartTotal(),
                timestamp: Date.now()
            };
            
            localStorage.setItem('nanimade_offline_cart', JSON.stringify(cartData));
        }
    }
    
    getCartItems() {
        const items = [];
        const cartItems = document.querySelectorAll('.nanimade-cart-item');
        
        cartItems.forEach(item => {
            items.push({
                key: item.dataset.cartItemKey,
                name: item.querySelector('.nanimade-item-name').textContent,
                quantity: item.querySelector('.nanimade-quantity').textContent,
                price: item.querySelector('.nanimade-item-price').textContent
            });
        });
        
        return items;
    }
    
    getCartTotal() {
        const totalElement = document.getElementById('nanimadeCartTotal');
        return totalElement ? totalElement.textContent : '₹0';
    }
    
    setupBackgroundSync() {
        // Queue actions for background sync when online
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            navigator.serviceWorker.ready.then(registration => {
                // Register for background sync
                return registration.sync.register('nanimade-background-sync');
            });
        }
    }
    
    queueForSync(action, data) {
        this.syncQueue.push({
            action: action,
            data: data,
            timestamp: Date.now()
        });
        
        localStorage.setItem('nanimade_sync_queue', JSON.stringify(this.syncQueue));
        
        // Try to sync immediately if online
        if (this.isOnline) {
            this.processSyncQueue();
        }
    }
    
    processSyncQueue() {
        if (this.syncQueue.length === 0) return;
        
        const queue = [...this.syncQueue];
        this.syncQueue = [];
        
        queue.forEach(item => {
            this.syncAction(item.action, item.data);
        });
        
        localStorage.removeItem('nanimade_sync_queue');
    }
    
    syncAction(action, data) {
        const syncData = {
            action: `nanimade_sync_${action}`,
            data: data,
            nonce: nanimade_ajax.nonce
        };
        
        fetch(nanimade_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(syncData)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                console.log(`Synced ${action} successfully`);
            }
        })
        .catch(error => {
            console.error(`Failed to sync ${action}:`, error);
            // Re-queue for later
            this.queueForSync(action, data);
        });
    }
    
    syncOfflineData() {
        // Load and sync offline data
        const offlineCart = localStorage.getItem('nanimade_offline_cart');
        const syncQueue = localStorage.getItem('nanimade_sync_queue');
        
        if (offlineCart) {
            const cartData = JSON.parse(offlineCart);
            this.syncOfflineCart(cartData);
        }
        
        if (syncQueue) {
            this.syncQueue = JSON.parse(syncQueue);
            this.processSyncQueue();
        }
    }
    
    syncOfflineCart(cartData) {
        // Sync offline cart changes with server
        const data = {
            action: 'nanimade_sync_offline_cart',
            cart_data: JSON.stringify(cartData),
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
        .then(result => {
            if (result.success) {
                localStorage.removeItem('nanimade_offline_cart');
                window.nanimadeSuite?.updateCartDisplay(result.data);
            }
        });
    }
    
    initPushNotifications() {
        if (!('Notification' in window) || !('serviceWorker' in navigator)) {
            return;
        }
        
        // Check if notifications are already granted
        if (Notification.permission === 'granted') {
            this.subscribeToNotifications();
        } else if (Notification.permission !== 'denied') {
            // Show notification permission prompt after user interaction
            this.setupNotificationPrompt();
        }
    }
    
    setupNotificationPrompt() {
        // Show notification permission request after user has interacted with the site
        let userInteracted = false;
        
        const interactionEvents = ['click', 'touchstart', 'keydown'];
        
        const handleInteraction = () => {
            if (!userInteracted) {
                userInteracted = true;
                setTimeout(() => {
                    this.requestNotificationPermission();
                }, 2000);
                
                // Remove event listeners
                interactionEvents.forEach(event => {
                    document.removeEventListener(event, handleInteraction);
                });
            }
        };
        
        interactionEvents.forEach(event => {
            document.addEventListener(event, handleInteraction);
        });
    }
    
    requestNotificationPermission() {
        const prompt = document.createElement('div');
        prompt.className = 'nanimade-notification-prompt';
        prompt.innerHTML = `
            <div class="nanimade-prompt-content">
                <div class="nanimade-prompt-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="nanimade-prompt-text">
                    <h4>Stay updated with your orders</h4>
                    <p>Get notifications about order status, special offers, and new pickle varieties.</p>
                </div>
                <div class="nanimade-prompt-actions">
                    <button class="nanimade-prompt-btn secondary" id="nanimadeNotifyLater">Not now</button>
                    <button class="nanimade-prompt-btn primary" id="nanimadeNotifyAllow">Allow</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(prompt);
        setTimeout(() => prompt.classList.add('show'), 100);
        
        // Handle allow
        document.getElementById('nanimadeNotifyAllow').addEventListener('click', () => {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    this.subscribeToNotifications();
                    window.nanimadeSuite?.showNotification('Notifications enabled!', 'success');
                }
                prompt.remove();
            });
        });
        
        // Handle not now
        document.getElementById('nanimadeNotifyLater').addEventListener('click', () => {
            prompt.remove();
            // Don't ask again for this session
            sessionStorage.setItem('nanimade_notification_declined', 'true');
        });
    }
    
    subscribeToNotifications() {
        navigator.serviceWorker.ready.then(registration => {
            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array('BEl62iUYgUivxIkv69yViEuiBIa6iMjp3gqC4ahgQA8_QHSPHps6AcXwdrdHar-6-adHd4n3b1ZJJpV6uQvSWWs')
            });
        }).then(subscription => {
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
    
    addInstallPrompt() {
        let deferredPrompt;
        
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Show custom install prompt
            this.showInstallPrompt(deferredPrompt);
        });
        
        // Handle successful installation
        window.addEventListener('appinstalled', () => {
            console.log('PWA installed successfully');
            window.nanimadeSuite?.showNotification('NaniMade app installed! Welcome to the full experience.', 'success');
            
            // Track installation
            if (window.nanimadeSuite?.analytics) {
                window.nanimadeSuite.analytics.trackEvent('pwa_installed', {
                    timestamp: Date.now()
                });
            }
        });
    }
    
    showInstallPrompt(deferredPrompt) {
        // Don't show if already declined this session
        if (sessionStorage.getItem('nanimade_install_declined')) {
            return;
        }
        
        const prompt = document.createElement('div');
        prompt.className = 'nanimade-install-prompt';
        prompt.innerHTML = `
            <div class="nanimade-install-content">
                <div class="nanimade-install-icon">
                    <img src="${nanimade_ajax.plugin_url}assets/images/app-icon.png" alt="NaniMade App" width="60" height="60">
                </div>
                <div class="nanimade-install-text">
                    <h4>Install NaniMade App</h4>
                    <p>Get faster access, offline browsing, and exclusive app-only features!</p>
                    <div class="nanimade-install-features">
                        <span><i class="fas fa-wifi-slash"></i> Offline browsing</span>
                        <span><i class="fas fa-bell"></i> Order notifications</span>
                        <span><i class="fas fa-rocket"></i> Faster loading</span>
                    </div>
                </div>
            </div>
            <div class="nanimade-install-actions">
                <button class="nanimade-install-btn secondary" id="nanimadeInstallLater">Maybe later</button>
                <button class="nanimade-install-btn primary" id="nanimadeInstallNow">Install App</button>
            </div>
        `;
        
        document.body.appendChild(prompt);
        setTimeout(() => prompt.classList.add('show'), 500);
        
        // Handle install
        document.getElementById('nanimadeInstallNow').addEventListener('click', () => {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted the install prompt');
                } else {
                    console.log('User dismissed the install prompt');
                }
                deferredPrompt = null;
                prompt.remove();
            });
        });
        
        // Handle later
        document.getElementById('nanimadeInstallLater').addEventListener('click', () => {
            prompt.remove();
            sessionStorage.setItem('nanimade_install_declined', 'true');
        });
        
        // Auto hide after 10 seconds
        setTimeout(() => {
            if (document.body.contains(prompt)) {
                prompt.classList.remove('show');
                setTimeout(() => prompt.remove(), 300);
            }
        }, 10000);
    }
    
    setupOfflineIndicator() {
        const indicator = document.createElement('div');
        indicator.id = 'nanimadeOfflineIndicator';
        indicator.className = 'nanimade-offline-indicator';
        indicator.innerHTML = `
            <i class="fas fa-wifi-slash"></i>
            <span>Offline Mode</span>
        `;
        document.body.appendChild(indicator);
    }
    
    showOfflineIndicator() {
        const indicator = document.getElementById('nanimadeOfflineIndicator');
        if (indicator) {
            indicator.classList.add('show');
        }
    }
    
    hideOfflineIndicator() {
        const indicator = document.getElementById('nanimadeOfflineIndicator');
        if (indicator) {
            indicator.classList.remove('show');
        }
    }
    
    setupBackgroundSync() {
        // Setup background sync for cart updates and analytics
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            navigator.serviceWorker.ready.then(registration => {
                // Register sync events
                registration.sync.register('nanimade-cart-sync');
                registration.sync.register('nanimade-analytics-sync');
            });
        }
    }
    
    handleServiceWorkerMessage(message) {
        switch (message.type) {
            case 'CACHE_UPDATED':
                window.nanimadeSuite?.showNotification('App updated! Refresh to see new features.', 'info');
                break;
                
            case 'OFFLINE_FALLBACK':
                this.showOfflinePage();
                break;
                
            case 'SYNC_COMPLETE':
                window.nanimadeSuite?.showNotification('Data synced successfully!', 'success');
                break;
        }
    }
    
    showOfflinePage() {
        // Show offline page with cached content
        const offlinePage = document.createElement('div');
        offlinePage.className = 'nanimade-offline-page';
        offlinePage.innerHTML = `
            <div class="nanimade-offline-content">
                <div class="nanimade-offline-icon">
                    <i class="fas fa-wifi-slash"></i>
                </div>
                <h2>You're offline</h2>
                <p>Don't worry! You can still browse your cart and saved items.</p>
                <div class="nanimade-offline-actions">
                    <button class="nanimade-btn primary" onclick="location.reload()">Try again</button>
                    <button class="nanimade-btn secondary" onclick="this.parentElement.parentElement.parentElement.remove()">Continue offline</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(offlinePage);
        setTimeout(() => offlinePage.classList.add('show'), 100);
    }
    
    showUpdateAvailable() {
        const updatePrompt = document.createElement('div');
        updatePrompt.className = 'nanimade-update-prompt';
        updatePrompt.innerHTML = `
            <div class="nanimade-update-content">
                <i class="fas fa-download"></i>
                <span>New version available!</span>
                <button class="nanimade-update-btn" onclick="location.reload()">Update</button>
            </div>
        `;
        
        document.body.appendChild(updatePrompt);
        setTimeout(() => updatePrompt.classList.add('show'), 100);
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            updatePrompt.classList.remove('show');
            setTimeout(() => updatePrompt.remove(), 300);
        }, 5000);
    }
    
    // Offline cart functionality
    addToOfflineCart(productId, quantity, variations = {}) {
        const offlineCart = JSON.parse(localStorage.getItem('nanimade_offline_cart') || '{"items": []}');
        
        const existingItem = offlineCart.items.find(item => 
            item.productId === productId && 
            JSON.stringify(item.variations) === JSON.stringify(variations)
        );
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            offlineCart.items.push({
                productId: productId,
                quantity: quantity,
                variations: variations,
                timestamp: Date.now()
            });
        }
        
        localStorage.setItem('nanimade_offline_cart', JSON.stringify(offlineCart));
        
        // Queue for sync when online
        this.queueForSync('add_to_cart', {
            product_id: productId,
            quantity: quantity,
            variations: variations
        });
        
        return true;
    }
    
    getOfflineCart() {
        return JSON.parse(localStorage.getItem('nanimade_offline_cart') || '{"items": []}');
    }
    
    clearOfflineCart() {
        localStorage.removeItem('nanimade_offline_cart');
    }
    
    // Performance monitoring
    trackPerformance() {
        if ('performance' in window) {
            window.addEventListener('load', () => {
                const perfData = performance.getEntriesByType('navigation')[0];
                
                const metrics = {
                    loadTime: perfData.loadEventEnd - perfData.loadEventStart,
                    domContentLoaded: perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart,
                    firstPaint: this.getFirstPaint(),
                    firstContentfulPaint: this.getFirstContentfulPaint(),
                    largestContentfulPaint: this.getLargestContentfulPaint()
                };
                
                // Send metrics to analytics
                if (window.nanimadeSuite?.analytics) {
                    window.nanimadeSuite.analytics.trackEvent('performance_metrics', metrics);
                }
            });
        }
    }
    
    getFirstPaint() {
        const paintEntries = performance.getEntriesByType('paint');
        const firstPaint = paintEntries.find(entry => entry.name === 'first-paint');
        return firstPaint ? firstPaint.startTime : 0;
    }
    
    getFirstContentfulPaint() {
        const paintEntries = performance.getEntriesByType('paint');
        const fcp = paintEntries.find(entry => entry.name === 'first-contentful-paint');
        return fcp ? fcp.startTime : 0;
    }
    
    getLargestContentfulPaint() {
        return new Promise((resolve) => {
            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const lastEntry = entries[entries.length - 1];
                resolve(lastEntry.startTime);
                observer.disconnect();
            });
            
            observer.observe({ entryTypes: ['largest-contentful-paint'] });
            
            // Fallback timeout
            setTimeout(() => resolve(0), 5000);
        });
    }
}

// Initialize PWA features
window.nanimadePWAFeatures = new NaniMadePWAFeatures();

// Add PWA-specific styles
const pwaStyles = `
    .nanimade-install-prompt,
    .nanimade-notification-prompt {
        position: fixed;
        bottom: 20px;
        left: 20px;
        right: 20px;
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        transform: translateY(100%);
        transition: transform 0.3s ease-out;
    }
    
    .nanimade-install-prompt.show,
    .nanimade-notification-prompt.show {
        transform: translateY(0);
    }
    
    .nanimade-install-content,
    .nanimade-prompt-content {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }
    
    .nanimade-install-icon,
    .nanimade-prompt-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
        color: white;
        font-size: 24px;
        flex-shrink: 0;
    }
    
    .nanimade-install-text h4,
    .nanimade-prompt-text h4 {
        margin: 0 0 8px;
        font-size: 16px;
        color: #333;
    }
    
    .nanimade-install-text p,
    .nanimade-prompt-text p {
        margin: 0 0 12px;
        font-size: 14px;
        color: #666;
        line-height: 1.4;
    }
    
    .nanimade-install-features {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .nanimade-install-features span {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        color: #28a745;
        font-weight: 600;
    }
    
    .nanimade-install-actions,
    .nanimade-prompt-actions {
        display: flex;
        gap: 12px;
    }
    
    .nanimade-install-btn,
    .nanimade-prompt-btn {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease-out;
    }
    
    .nanimade-install-btn.primary,
    .nanimade-prompt-btn.primary {
        background: #ff6b35;
        color: white;
    }
    
    .nanimade-install-btn.secondary,
    .nanimade-prompt-btn.secondary {
        background: transparent;
        color: #666;
        border: 1px solid #ddd;
    }
    
    .nanimade-install-btn:hover,
    .nanimade-prompt-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .nanimade-offline-indicator {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(-100%);
        background: #6c757d;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        z-index: 9999;
        transition: transform 0.3s ease-out;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .nanimade-offline-indicator.show {
        transform: translateX(-50%) translateY(0);
    }
    
    .nanimade-offline-page {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: white;
        z-index: 10001;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease-out;
    }
    
    .nanimade-offline-page.show {
        opacity: 1;
        visibility: visible;
    }
    
    .nanimade-offline-content {
        text-align: center;
        padding: 40px;
        max-width: 400px;
    }
    
    .nanimade-offline-icon {
        width: 80px;
        height: 80px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        color: #6c757d;
        font-size: 32px;
    }
    
    .nanimade-offline-content h2 {
        margin: 0 0 16px;
        color: #333;
    }
    
    .nanimade-offline-content p {
        margin: 0 0 24px;
        color: #666;
        line-height: 1.5;
    }
    
    .nanimade-offline-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    
    .nanimade-update-prompt {
        position: fixed;
        top: 20px;
        left: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 16px;
        border-radius: 12px;
        z-index: 10000;
        transform: translateY(-100%);
        transition: transform 0.3s ease-out;
    }
    
    .nanimade-update-prompt.show {
        transform: translateY(0);
    }
    
    .nanimade-update-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    
    .nanimade-update-btn {
        
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease-out;
    }
    
    .nanimade-update-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    
    /* Touch device optimizations */
    .nanimade-touch-device .nanimade-menu-item {
        min-height: 44px;
        min-width: 44px;
    }
    
    .nanimade-touch-device .nanimade-btn {
        min-height: 44px;
        padding: 12px 20px;
    }
    
    .nanimade-touching {
        transform: scale(0.95);
        opacity: 0.8;
        transition: all 0.1s ease-out;
    }
    
    @media (max-width: 480px) {
        .nanimade-install-prompt,
        .nanimade-notification-prompt {
            left: 12px;
            right: 12px;
            bottom: 12px;
        }
        
        .nanimade-install-content,
        .nanimade-prompt-content {
            flex-direction: column;
            text-align: center;
        }
        
        .nanimade-install-features {
            justify-content: center;
        }
    }
`;

// Inject PWA styles
const pwaStyleSheet = document.createElement('style');
pwaStyleSheet.textContent = pwaStyles;
document.head.appendChild(pwaStyleSheet);