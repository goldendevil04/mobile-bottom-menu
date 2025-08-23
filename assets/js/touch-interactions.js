/**
 * Advanced Touch Interactions for NaniMade Suite
 */

class NaniMadeTouchInteractions {
    constructor() {
        this.isTouch = 'ontouchstart' in window;
        this.gestures = new Map();
        this.init();
    }
    
    init() {
        if (!this.isTouch) return;
        
        this.initTouchEvents();
        this.initGestureRecognition();
        this.initHapticFeedback();
        this.optimizeForTouch();
    }
    
    initTouchEvents() {
        // Enhanced touch event handling
        document.addEventListener('touchstart', (e) => this.handleTouchStart(e), { passive: false });
        document.addEventListener('touchmove', (e) => this.handleTouchMove(e), { passive: false });
        document.addEventListener('touchend', (e) => this.handleTouchEnd(e), { passive: false });
        document.addEventListener('touchcancel', (e) => this.handleTouchCancel(e));
    }
    
    initGestureRecognition() {
        this.gestureRecognizer = new NaniMadeGestureRecognizer();
        
        // Register common gestures
        this.gestureRecognizer.register('swipeLeft', this.handleSwipeLeft.bind(this));
        this.gestureRecognizer.register('swipeRight', this.handleSwipeRight.bind(this));
        this.gestureRecognizer.register('swipeUp', this.handleSwipeUp.bind(this));
        this.gestureRecognizer.register('swipeDown', this.handleSwipeDown.bind(this));
        this.gestureRecognizer.register('pinch', this.handlePinch.bind(this));
        this.gestureRecognizer.register('doubleTap', this.handleDoubleTap.bind(this));
        this.gestureRecognizer.register('longPress', this.handleLongPress.bind(this));
    }
    
    initHapticFeedback() {
        // Simulate haptic feedback with visual and audio cues
        this.hapticEnabled = localStorage.getItem('nanimade_haptic') !== 'false';
        
        if (this.hapticEnabled) {
            this.setupHapticTriggers();
        }
    }
    
    optimizeForTouch() {
        // Add touch-friendly classes and optimizations
        document.body.classList.add('nanimade-touch-device');
        
        // Increase touch targets
        this.enhanceTouchTargets();
        
        // Improve scroll performance
        this.optimizeScrolling();
        
        // Add touch indicators
        this.addTouchIndicators();
    }
    
    handleTouchStart(e) {
        const touch = e.touches[0];
        const target = e.target.closest('.nanimade-touchable, .nanimade-menu-item, .nanimade-btn');
        
        if (target) {
            // Add touch feedback
            target.classList.add('nanimade-touching');
            
            // Store touch data
            this.gestures.set('start', {
                x: touch.clientX,
                y: touch.clientY,
                time: Date.now(),
                target: target
            });
            
            // Haptic feedback
            this.triggerHapticFeedback('light');
        }
    }
    
    handleTouchMove(e) {
        const touch = e.touches[0];
        const startData = this.gestures.get('start');
        
        if (!startData) return;
        
        const deltaX = touch.clientX - startData.x;
        const deltaY = touch.clientY - startData.y;
        const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
        
        // Update gesture data
        this.gestures.set('current', {
            x: touch.clientX,
            y: touch.clientY,
            deltaX: deltaX,
            deltaY: deltaY,
            distance: distance,
            time: Date.now()
        });
        
        // Handle specific touch interactions
        this.handleDragInteraction(e, startData, deltaX, deltaY);
    }
    
    handleTouchEnd(e) {
        const startData = this.gestures.get('start');
        const currentData = this.gestures.get('current');
        
        if (!startData) return;
        
        // Remove touch feedback
        const target = startData.target;
        if (target) {
            target.classList.remove('nanimade-touching');
        }
        
        // Analyze gesture
        if (currentData) {
            this.analyzeGesture(startData, currentData);
        }
        
        // Clear gesture data
        this.gestures.clear();
    }
    
    handleTouchCancel(e) {
        // Clean up on touch cancel
        const startData = this.gestures.get('start');
        if (startData && startData.target) {
            startData.target.classList.remove('nanimade-touching');
        }
        this.gestures.clear();
    }
    
    handleDragInteraction(e, startData, deltaX, deltaY) {
        const target = startData.target;
        
        // Handle cart item swipe to remove
        if (target.closest('.nanimade-cart-item')) {
            if (Math.abs(deltaX) > 20 && Math.abs(deltaX) > Math.abs(deltaY)) {
                e.preventDefault();
                const cartItem = target.closest('.nanimade-cart-item');
                
                if (deltaX < 0) { // Swipe left
                    cartItem.style.transform = `translateX(${Math.max(deltaX, -80)}px)`;
                    cartItem.classList.add('swiping');
                }
            }
        }
        
        // Handle sidebar cart swipe to close
        if (target.closest('.nanimade-sidebar-cart')) {
            if (deltaX > 20) {
                e.preventDefault();
                const cart = target.closest('.nanimade-sidebar-cart');
                cart.style.transform = `translateX(${Math.min(deltaX, 100)}px)`;
            }
        }
    }
    
    analyzeGesture(startData, currentData) {
        const deltaX = currentData.deltaX;
        const deltaY = currentData.deltaY;
        const distance = currentData.distance;
        const duration = currentData.time - startData.time;
        const velocity = distance / duration;
        
        // Determine gesture type
        if (distance < 10 && duration < 300) {
            this.gestureRecognizer.trigger('tap', startData.target);
        } else if (distance < 10 && duration > 500) {
            this.gestureRecognizer.trigger('longPress', startData.target);
        } else if (velocity > 0.5) {
            if (Math.abs(deltaX) > Math.abs(deltaY)) {
                if (deltaX > 0) {
                    this.gestureRecognizer.trigger('swipeRight', startData.target);
                } else {
                    this.gestureRecognizer.trigger('swipeLeft', startData.target);
                }
            } else {
                if (deltaY > 0) {
                    this.gestureRecognizer.trigger('swipeDown', startData.target);
                } else {
                    this.gestureRecognizer.trigger('swipeUp', startData.target);
                }
            }
        }
    }
    
    handleSwipeLeft(target) {
        // Handle swipe left gestures
        if (target.closest('.nanimade-cart-item')) {
            this.showRemoveOption(target.closest('.nanimade-cart-item'));
        } else if (target.closest('.nanimade-product-gallery')) {
            this.nextImage(target.closest('.nanimade-product-gallery'));
        }
    }
    
    handleSwipeRight(target) {
        // Handle swipe right gestures
        if (target.closest('.nanimade-sidebar-cart')) {
            window.nanimadeSuite.closeSidebarCart();
        } else if (target.closest('.nanimade-product-gallery')) {
            this.prevImage(target.closest('.nanimade-product-gallery'));
        }
    }
    
    handleSwipeUp(target) {
        // Handle swipe up gestures
        if (target.closest('.nanimade-mobile-menu')) {
            this.showQuickActions();
        }
    }
    
    handleSwipeDown(target) {
        // Handle swipe down gestures
        if (window.scrollY === 0) {
            this.triggerPullToRefresh();
        }
    }
    
    handlePinch(target) {
        // Handle pinch gestures for zoom
        if (target.closest('.nanimade-product-image')) {
            this.toggleImageZoom(target.closest('.nanimade-product-image'));
        }
    }
    
    handleDoubleTap(target) {
        // Handle double tap gestures
        if (target.closest('.nanimade-menu-item[data-item="cart"]')) {
            this.quickAddLastItem();
        }
    }
    
    handleLongPress(target) {
        // Handle long press gestures
        if (target.closest('.nanimade-menu-item')) {
            this.showMenuOptions(target.closest('.nanimade-menu-item'));
        }
    }
    
    setupHapticTriggers() {
        // Add haptic feedback to interactive elements
        const triggers = document.querySelectorAll('.nanimade-menu-item, .nanimade-btn, .nanimade-qty-btn');
        
        triggers.forEach(trigger => {
            trigger.addEventListener('touchstart', () => {
                this.triggerHapticFeedback('light');
            });
        });
        
        // Special haptic feedback for important actions
        const importantTriggers = document.querySelectorAll('.nanimade-add-to-cart-btn, .nanimade-remove-item');
        
        importantTriggers.forEach(trigger => {
            trigger.addEventListener('click', () => {
                this.triggerHapticFeedback('medium');
            });
        });
    }
    
    triggerHapticFeedback(intensity = 'light') {
        // Use Vibration API if available
        if ('vibrate' in navigator) {
            const patterns = {
                light: [10],
                medium: [20],
                heavy: [30],
                success: [10, 50, 10],
                error: [50, 100, 50]
            };
            
            navigator.vibrate(patterns[intensity] || patterns.light);
        }
        
        // Visual feedback as fallback
        this.addVisualHapticFeedback(intensity);
    }
    
    addVisualHapticFeedback(intensity) {
        const feedback = document.createElement('div');
        feedback.className = `nanimade-haptic-feedback nanimade-haptic-${intensity}`;
        document.body.appendChild(feedback);
        
        setTimeout(() => feedback.remove(), 200);
    }
    
    enhanceTouchTargets() {
        // Ensure all interactive elements meet minimum touch target size (44px)
        const elements = document.querySelectorAll('.nanimade-menu-item, .nanimade-btn, button');
        
        elements.forEach(element => {
            const rect = element.getBoundingClientRect();
            if (rect.width < 44 || rect.height < 44) {
                element.style.minWidth = '44px';
                element.style.minHeight = '44px';
                element.classList.add('nanimade-enhanced-touch-target');
            }
        });
    }
    
    optimizeScrolling() {
        // Add momentum scrolling for iOS
        const scrollContainers = document.querySelectorAll('.nanimade-cart-content, .nanimade-product-gallery');
        
        scrollContainers.forEach(container => {
            container.style.webkitOverflowScrolling = 'touch';
            container.style.overflowScrolling = 'touch';
        });
    }
    
    addTouchIndicators() {
        // Add visual indicators for swipeable elements
        const swipeableElements = document.querySelectorAll('.nanimade-cart-item, .nanimade-product-gallery');
        
        swipeableElements.forEach(element => {
            if (!element.querySelector('.nanimade-swipe-indicator')) {
                const indicator = document.createElement('div');
                indicator.className = 'nanimade-swipe-indicator';
                indicator.innerHTML = '<i class="fas fa-chevron-left"></i>';
                element.appendChild(indicator);
            }
        });
    }
    
    showRemoveOption(cartItem) {
        cartItem.classList.add('show-remove');
        setTimeout(() => cartItem.classList.remove('show-remove'), 3000);
    }
    
    nextImage(gallery) {
        const currentImage = gallery.querySelector('.active');
        const nextImage = currentImage.nextElementSibling || gallery.querySelector('img:first-child');
        
        if (nextImage) {
            currentImage.classList.remove('active');
            nextImage.classList.add('active');
            this.triggerHapticFeedback('light');
        }
    }
    
    prevImage(gallery) {
        const currentImage = gallery.querySelector('.active');
        const prevImage = currentImage.previousElementSibling || gallery.querySelector('img:last-child');
        
        if (prevImage) {
            currentImage.classList.remove('active');
            prevImage.classList.add('active');
            this.triggerHapticFeedback('light');
        }
    }
    
    showQuickActions() {
        // Show quick action menu
        const quickActions = document.createElement('div');
        quickActions.className = 'nanimade-quick-actions';
        quickActions.innerHTML = `
            <div class="nanimade-quick-action" data-action="search">
                <i class="fas fa-search"></i>
                <span>Search</span>
            </div>
            <div class="nanimade-quick-action" data-action="favorites">
                <i class="fas fa-heart"></i>
                <span>Favorites</span>
            </div>
            <div class="nanimade-quick-action" data-action="orders">
                <i class="fas fa-receipt"></i>
                <span>Orders</span>
            </div>
        `;
        
        document.body.appendChild(quickActions);
        setTimeout(() => quickActions.classList.add('show'), 100);
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            quickActions.classList.remove('show');
            setTimeout(() => quickActions.remove(), 300);
        }, 3000);
    }
    
    triggerPullToRefresh() {
        // Trigger pull to refresh functionality
        window.nanimadeSuite.triggerRefresh();
    }
    
    toggleImageZoom(image) {
        image.classList.toggle('zoomed');
        this.triggerHapticFeedback('medium');
    }
    
    quickAddLastItem() {
        // Quick add last purchased item
        const lastItem = localStorage.getItem('nanimade_last_item');
        if (lastItem) {
            const productData = JSON.parse(lastItem);
            this.addToCart(productData.id, 1);
            this.triggerHapticFeedback('success');
        }
    }
    
    showMenuOptions(menuItem) {
        // Show context menu for menu item
        const options = document.createElement('div');
        options.className = 'nanimade-menu-options';
        options.innerHTML = `
            <div class="nanimade-option" data-action="customize">
                <i class="fas fa-cog"></i>
                <span>Customize</span>
            </div>
            <div class="nanimade-option" data-action="hide">
                <i class="fas fa-eye-slash"></i>
                <span>Hide</span>
            </div>
        `;
        
        document.body.appendChild(options);
        
        // Position near the menu item
        const rect = menuItem.getBoundingClientRect();
        options.style.left = rect.left + 'px';
        options.style.top = (rect.top - options.offsetHeight - 10) + 'px';
        
        setTimeout(() => options.classList.add('show'), 100);
        
        // Auto hide
        setTimeout(() => {
            options.classList.remove('show');
            setTimeout(() => options.remove(), 300);
        }, 3000);
        
        this.triggerHapticFeedback('medium');
    }
    
    addToCart(productId, quantity = 1, variations = {}) {
        const data = {
            action: 'nanimade_add_to_cart',
            product_id: productId,
            quantity: quantity,
            variations: variations,
            nonce: nanimade_ajax.nonce
        };
        
        return fetch(nanimade_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.triggerHapticFeedback('success');
                window.nanimadeSuite.showNotification(data.data.message, 'success');
                window.nanimadeSuite.updateCartDisplay(data.data);
            } else {
                this.triggerHapticFeedback('error');
                window.nanimadeSuite.showNotification(data.data.message, 'error');
            }
            return data;
        });
    }
}

class NaniMadeGestureRecognizer {
    constructor() {
        this.handlers = new Map();
        this.thresholds = {
            swipeDistance: 50,
            swipeVelocity: 0.3,
            longPressTime: 500,
            doubleTapTime: 300
        };
        this.lastTap = 0;
    }
    
    register(gesture, handler) {
        this.handlers.set(gesture, handler);
    }
    
    trigger(gesture, target, data = {}) {
        const handler = this.handlers.get(gesture);
        if (handler) {
            handler(target, data);
        }
    }
    
    recognizeSwipe(startData, endData) {
        const deltaX = endData.x - startData.x;
        const deltaY = endData.y - startData.y;
        const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
        const duration = endData.time - startData.time;
        const velocity = distance / duration;
        
        if (distance > this.thresholds.swipeDistance && velocity > this.thresholds.swipeVelocity) {
            if (Math.abs(deltaX) > Math.abs(deltaY)) {
                return deltaX > 0 ? 'swipeRight' : 'swipeLeft';
            } else {
                return deltaY > 0 ? 'swipeDown' : 'swipeUp';
            }
        }
        
        return null;
    }
    
    recognizeDoubleTap(currentTime) {
        const timeDiff = currentTime - this.lastTap;
        this.lastTap = currentTime;
        
        return timeDiff < this.thresholds.doubleTapTime;
    }
    
    recognizeLongPress(duration) {
        return duration > this.thresholds.longPressTime;
    }
}

// Pickle-specific touch interactions
class NaniMadePickleTouchFeatures {
    constructor() {
        this.init();
    }
    
    init() {
        this.initJarRotation();
        this.initSpiceLevelTouch();
        this.initIngredientDragDrop();
    }
    
    initJarRotation() {
        const jars = document.querySelectorAll('.nanimade-jar-display');
        
        jars.forEach(jar => {
            let startX = 0;
            let currentRotation = 0;
            
            jar.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                jar.style.transition = 'none';
            });
            
            jar.addEventListener('touchmove', (e) => {
                const currentX = e.touches[0].clientX;
                const deltaX = currentX - startX;
                const rotation = currentRotation + (deltaX * 0.5);
                
                jar.style.transform = `rotateY(${rotation}deg)`;
            });
            
            jar.addEventListener('touchend', () => {
                jar.style.transition = 'transform 0.3s ease-out';
                currentRotation = parseFloat(jar.style.transform.match(/rotateY\(([^)]+)deg\)/)?.[1] || 0);
            });
        });
    }
    
    initSpiceLevelTouch() {
        const spiceMeters = document.querySelectorAll('.nanimade-spice-meter');
        
        spiceMeters.forEach(meter => {
            const handle = meter.querySelector('.nanimade-meter-handle');
            const fill = meter.querySelector('.nanimade-meter-fill');
            const track = meter.querySelector('.nanimade-meter-track');
            
            if (!handle || !fill || !track) return;
            
            let isDragging = false;
            
            handle.addEventListener('touchstart', (e) => {
                isDragging = true;
                e.preventDefault();
            });
            
            document.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                
                const rect = track.getBoundingClientRect();
                const x = e.touches[0].clientX - rect.left;
                const percentage = Math.max(0, Math.min(100, (x / rect.width) * 100));
                
                handle.style.left = percentage + '%';
                fill.style.width = percentage + '%';
                
                // Update spice level indicator
                this.updateSpiceLevel(meter, percentage);
                
                e.preventDefault();
            });
            
            document.addEventListener('touchend', () => {
                isDragging = false;
            });
        });
    }
    
    updateSpiceLevel(meter, percentage) {
        const level = Math.floor(percentage / 25); // 0-3 levels
        const levels = ['mild', 'medium', 'hot', 'extreme'];
        const currentLevel = levels[level] || 'mild';
        
        meter.dataset.spiceLevel = currentLevel;
        
        // Update visual indicators
        const indicators = meter.querySelectorAll('.nanimade-spice-visual');
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index <= level);
        });
        
        // Trigger haptic feedback based on spice level
        if (level >= 2) {
            window.nanimadeTouchInteractions?.triggerHapticFeedback('medium');
        }
    }
    
    initIngredientDragDrop() {
        const ingredients = document.querySelectorAll('.nanimade-ingredient-item');
        const dropZone = document.querySelector('.nanimade-jar-contents');
        
        if (!dropZone) return;
        
        ingredients.forEach(ingredient => {
            ingredient.addEventListener('touchstart', (e) => {
                ingredient.classList.add('dragging');
                this.createDragGhost(ingredient, e.touches[0]);
            });
            
            ingredient.addEventListener('touchend', (e) => {
                ingredient.classList.remove('dragging');
                this.removeDragGhost();
                
                // Check if dropped on jar
                const touch = e.changedTouches[0];
                const elementBelow = document.elementFromPoint(touch.clientX, touch.clientY);
                
                if (elementBelow && elementBelow.closest('.nanimade-jar-contents')) {
                    this.addIngredientToJar(ingredient, dropZone);
                }
            });
        });
    }
    
    createDragGhost(ingredient, touch) {
        const ghost = ingredient.cloneNode(true);
        ghost.className = 'nanimade-drag-ghost';
        ghost.style.position = 'fixed';
        ghost.style.left = touch.clientX - 25 + 'px';
        ghost.style.top = touch.clientY - 25 + 'px';
        ghost.style.zIndex = '10000';
        ghost.style.pointerEvents = 'none';
        ghost.style.transform = 'scale(1.2)';
        ghost.style.opacity = '0.8';
        
        document.body.appendChild(ghost);
        
        // Follow touch movement
        document.addEventListener('touchmove', this.updateDragGhost);
    }
    
    updateDragGhost(e) {
        const ghost = document.querySelector('.nanimade-drag-ghost');
        if (ghost && e.touches[0]) {
            ghost.style.left = e.touches[0].clientX - 25 + 'px';
            ghost.style.top = e.touches[0].clientY - 25 + 'px';
        }
    }
    
    removeDragGhost() {
        const ghost = document.querySelector('.nanimade-drag-ghost');
        if (ghost) {
            ghost.remove();
        }
        document.removeEventListener('touchmove', this.updateDragGhost);
    }
    
    addIngredientToJar(ingredient, jar) {
        // Add visual feedback for ingredient addition
        const ingredientClone = ingredient.cloneNode(true);
        ingredientClone.className = 'nanimade-jar-ingredient';
        ingredientClone.style.position = 'absolute';
        ingredientClone.style.left = Math.random() * 80 + 10 + '%';
        ingredientClone.style.top = Math.random() * 80 + 10 + '%';
        ingredientClone.style.transform = 'scale(0.5)';
        
        jar.appendChild(ingredientClone);
        
        // Animate ingredient falling into jar
        setTimeout(() => {
            ingredientClone.style.animation = 'ingredientDrop 0.8s ease-out forwards';
        }, 100);
        
        // Trigger haptic feedback
        window.nanimadeTouchInteractions?.triggerHapticFeedback('success');
        
        // Show success message
        window.nanimadeSuite?.showNotification('Ingredient added to your custom pickle!', 'success');
    }
}

// Initialize touch interactions
window.nanimadeTouchInteractions = new NaniMadeTouchInteractions();
window.nanimadePickleTouchFeatures = new NaniMadePickleTouchFeatures();

// CSS animations for touch interactions
const touchStyles = `
    .nanimade-touching {
        transform: scale(0.95);
        opacity: 0.8;
        transition: all 0.1s ease-out;
    }
    
    .nanimade-haptic-feedback {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        pointer-events: none;
        z-index: 10001;
    }
    
    .nanimade-haptic-light {
        background: rgba(255, 107, 53, 0.3);
        animation: hapticPulse 0.2s ease-out;
    }
    
    .nanimade-haptic-medium {
        background: rgba(255, 107, 53, 0.5);
        animation: hapticPulse 0.3s ease-out;
    }
    
    .nanimade-haptic-heavy {
        background: rgba(255, 107, 53, 0.7);
        animation: hapticPulse 0.4s ease-out;
    }
    
    @keyframes hapticPulse {
        0% { transform: translate(-50%, -50%) scale(0); opacity: 1; }
        100% { transform: translate(-50%, -50%) scale(3); opacity: 0; }
    }
    
    .nanimade-quick-actions {
        position: fixed;
        bottom: 100px;
        left: 50%;
        transform: translateX(-50%) translateY(100%);
        display: flex;
        gap: 16px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        padding: 16px;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        z-index: 9997;
        transition: transform 0.3s ease-out;
    }
    
    .nanimade-quick-actions.show {
        transform: translateX(-50%) translateY(0);
    }
    
    .nanimade-quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 12px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease-out;
        min-width: 60px;
    }
    
    .nanimade-quick-action:hover {
        background: rgba(255, 107, 53, 0.1);
        transform: translateY(-2px);
    }
    
    .nanimade-quick-action i {
        font-size: 20px;
        color: #ff6b35;
        margin-bottom: 4px;
    }
    
    .nanimade-quick-action span {
        font-size: 10px;
        font-weight: 600;
        color: #333;
    }
    
    @keyframes ingredientDrop {
        0% { 
            transform: scale(0.5) translateY(-50px); 
            opacity: 1; 
        }
        50% { 
            transform: scale(0.7) translateY(0); 
            opacity: 0.8; 
        }
        100% { 
            transform: scale(0.3) translateY(20px); 
            opacity: 0.6; 
        }
    }
`;

// Inject touch styles
const touchStyleSheet = document.createElement('style');
touchStyleSheet.textContent = touchStyles;
document.head.appendChild(touchStyleSheet);