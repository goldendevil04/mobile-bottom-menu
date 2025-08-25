<?php
/**
 * PWA Features Class
 * Progressive Web App functionality for mobile app-like experience
 */

if (!defined('ABSPATH')) {
    exit;
}

class NaniMade_PWA_Features {
    
    public function __construct() {
        add_action('wp_head', array($this, 'add_pwa_meta_tags'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_pwa_scripts'));
        add_action('init', array($this, 'add_pwa_endpoints'));
        add_action('wp_ajax_register_push_subscription', array($this, 'register_push_subscription'));
        add_action('wp_ajax_nopriv_register_push_subscription', array($this, 'register_push_subscription'));
        add_action('wp_ajax_send_push_notification', array($this, 'send_push_notification'));
        add_action('template_redirect', array($this, 'serve_pwa_files'));
    }
    
    public function add_pwa_meta_tags() {
        ?>
        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="<?php echo get_option('nanimade_primary_color', '#4CAF50'); ?>">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="<?php echo get_bloginfo('name'); ?>">
        <meta name="msapplication-TileColor" content="<?php echo get_option('nanimade_primary_color', '#4CAF50'); ?>">
        <meta name="msapplication-config" content="<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/browserconfig.xml">
        
        <!-- Manifest -->
        <link rel="manifest" href="<?php echo home_url('/manifest.json'); ?>">
        
        <!-- Apple Touch Icons -->
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/icons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/icons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/icons/favicon-16x16.png">
        <link rel="mask-icon" href="<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/icons/safari-pinned-tab.svg" color="<?php echo get_option('nanimade_primary_color', '#4CAF50'); ?>">
        
        <!-- Splash Screens -->
        <link rel="apple-touch-startup-image" href="<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/splash/launch-640x1136.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
        <link rel="apple-touch-startup-image" href="<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/splash/launch-750x1334.png" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
        <link rel="apple-touch-startup-image" href="<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/splash/launch-1242x2208.png" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
        <link rel="apple-touch-startup-image" href="<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/splash/launch-1125x2436.png" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
        <link rel="apple-touch-startup-image" href="<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/splash/launch-1536x2048.png" media="(min-device-width: 768px) and (max-device-width: 1024px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait)">
        <?php
    }
    
    public function enqueue_pwa_scripts() {
        wp_enqueue_script(
            'nanimade-pwa-sw-register',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/js/sw-register.js',
            array(),
            NANIMADE_SUITE_VERSION,
            true
        );
        
        wp_localize_script('nanimade-pwa-sw-register', 'nanimadePWA', array(
            'swUrl' => home_url('/sw.js'),
            'manifestUrl' => home_url('/manifest.json'),
            'vapidPublicKey' => get_option('nanimade_vapid_public_key', ''),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nanimade_pwa_nonce')
        ));
    }
    
    public function add_pwa_endpoints() {
        add_rewrite_rule('^manifest\.json$', 'index.php?nanimade_pwa=manifest', 'top');
        add_rewrite_rule('^sw\.js$', 'index.php?nanimade_pwa=sw', 'top');
        add_rewrite_rule('^offline\.html$', 'index.php?nanimade_pwa=offline', 'top');
        
        if (!get_option('nanimade_pwa_rules_flushed')) {
            flush_rewrite_rules();
            update_option('nanimade_pwa_rules_flushed', true);
        }
    }
    
    public function serve_pwa_files() {
        $pwa_request = get_query_var('nanimade_pwa');
        
        switch ($pwa_request) {
            case 'manifest':
                $this->serve_manifest();
                break;
            case 'sw':
                $this->serve_service_worker();
                break;
            case 'offline':
                $this->serve_offline_page();
                break;
        }
    }
    
    private function serve_manifest() {
        header('Content-Type: application/json');
        
        $manifest = array(
            'name' => get_bloginfo('name') . ' - ' . __('Pickle Store', 'nanimade-suite'),
            'short_name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'start_url' => home_url('/?utm_source=pwa'),
            'display' => 'standalone',
            'orientation' => 'portrait-primary',
            'theme_color' => get_option('nanimade_primary_color', '#4CAF50'),
            'background_color' => get_option('nanimade_background_color', '#ffffff'),
            'scope' => home_url('/'),
            'lang' => get_locale(),
            'categories' => array('food', 'shopping', 'business'),
            'icons' => array(
                array(
                    'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/icon-72x72.png',
                    'sizes' => '72x72',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ),
                array(
                    'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/icon-96x96.png',
                    'sizes' => '96x96',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ),
                array(
                    'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/icon-128x128.png',
                    'sizes' => '128x128',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ),
                array(
                    'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/icon-144x144.png',
                    'sizes' => '144x144',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ),
                array(
                    'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/icon-152x152.png',
                    'sizes' => '152x152',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ),
                array(
                    'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/icon-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ),
                array(
                    'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/icon-384x384.png',
                    'sizes' => '384x384',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                ),
                array(
                    'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/icon-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable any'
                )
            ),
            'shortcuts' => array(
                array(
                    'name' => __('Shop Pickles', 'nanimade-suite'),
                    'short_name' => __('Shop', 'nanimade-suite'),
                    'description' => __('Browse our pickle collection', 'nanimade-suite'),
                    'url' => wc_get_page_permalink('shop') . '?utm_source=pwa_shortcut',
                    'icons' => array(
                        array(
                            'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/shortcut-shop.png',
                            'sizes' => '96x96'
                        )
                    )
                ),
                array(
                    'name' => __('My Account', 'nanimade-suite'),
                    'short_name' => __('Account', 'nanimade-suite'),
                    'description' => __('View your orders and account', 'nanimade-suite'),
                    'url' => wc_get_page_permalink('myaccount') . '?utm_source=pwa_shortcut',
                    'icons' => array(
                        array(
                            'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/shortcut-account.png',
                            'sizes' => '96x96'
                        )
                    )
                ),
                array(
                    'name' => __('Cart', 'nanimade-suite'),
                    'short_name' => __('Cart', 'nanimade-suite'),
                    'description' => __('View your shopping cart', 'nanimade-suite'),
                    'url' => wc_get_cart_url() . '?utm_source=pwa_shortcut',
                    'icons' => array(
                        array(
                            'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/shortcut-cart.png',
                            'sizes' => '96x96'
                        )
                    )
                )
            ),
            'screenshots' => array(
                array(
                    'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/screenshots/mobile-home.png',
                    'sizes' => '375x812',
                    'type' => 'image/png',
                    'form_factor' => 'narrow'
                ),
                array(
                    'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/screenshots/mobile-shop.png',
                    'sizes' => '375x812',
                    'type' => 'image/png',
                    'form_factor' => 'narrow'
                ),
                array(
                    'src' => NANIMADE_SUITE_PLUGIN_URL . 'assets/screenshots/desktop-home.png',
                    'sizes' => '1280x720',
                    'type' => 'image/png',
                    'form_factor' => 'wide'
                )
            )
        );
        
        echo json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
    
    private function serve_service_worker() {
        header('Content-Type: application/javascript');
        header('Service-Worker-Allowed: /');
        
        $cache_version = 'nanimade-v' . NANIMADE_SUITE_VERSION;
        $cache_files = $this->get_cache_files();
        
        ?>
const CACHE_NAME = '<?php echo $cache_version; ?>';
const OFFLINE_URL = '<?php echo home_url('/offline.html'); ?>';

const CACHE_FILES = <?php echo json_encode($cache_files); ?>;

// Install event - cache essential files
self.addEventListener('install', event => {
    console.log('NaniMade SW: Installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('NaniMade SW: Caching essential files');
                return cache.addAll(CACHE_FILES);
            })
            .then(() => {
                console.log('NaniMade SW: Installation complete');
                return self.skipWaiting();
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('NaniMade SW: Activating...');
    
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('NaniMade SW: Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            console.log('NaniMade SW: Activation complete');
            return self.clients.claim();
        })
    );
});

// Fetch event - serve from cache with network fallback
self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip admin and wp-includes requests
    if (url.pathname.includes('/wp-admin/') || url.pathname.includes('/wp-includes/')) {
        return;
    }
    
    // Handle navigation requests
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then(response => {
                    // Clone and cache successful responses
                    if (response.status === 200) {
                        const responseClone = response.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, responseClone);
                        });
                    }
                    return response;
                })
                .catch(() => {
                    // Return cached version or offline page
                    return caches.match(request)
                        .then(cachedResponse => {
                            return cachedResponse || caches.match(OFFLINE_URL);
                        });
                })
        );
        return;
    }
    
    // Handle asset requests (CSS, JS, images)
    if (request.destination === 'style' || 
        request.destination === 'script' || 
        request.destination === 'image') {
        
        event.respondWith(
            caches.match(request)
                .then(cachedResponse => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    
                    return fetch(request)
                        .then(response => {
                            if (response.status === 200) {
                                const responseClone = response.clone();
                                caches.open(CACHE_NAME).then(cache => {
                                    cache.put(request, responseClone);
                                });
                            }
                            return response;
                        })
                        .catch(() => {
                            // Return placeholder for failed image requests
                            if (request.destination === 'image') {
                                return new Response(
                                    '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="#f0f0f0"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#999">Image unavailable</text></svg>',
                                    { headers: { 'Content-Type': 'image/svg+xml' } }
                                );
                            }
                        });
                })
        );
        return;
    }
    
    // Handle API requests with network-first strategy
    if (url.pathname.includes('/wp-json/') || url.pathname.includes('/wp-admin/admin-ajax.php')) {
        event.respondWith(
            fetch(request)
                .then(response => {
                    if (response.status === 200) {
                        const responseClone = response.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, responseClone);
                        });
                    }
                    return response;
                })
                .catch(() => {
                    return caches.match(request);
                })
        );
        return;
    }
});

// Background sync for cart updates
self.addEventListener('sync', event => {
    if (event.tag === 'cart-sync') {
        event.waitUntil(syncCart());
    }
});

// Push notification handling
self.addEventListener('push', event => {
    console.log('NaniMade SW: Push received');
    
    const options = {
        body: 'You have new updates!',
        icon: '<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/icons/icon-192x192.png',
        badge: '<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/icons/badge-72x72.png',
        vibrate: [200, 100, 200],
        data: {
            url: '<?php echo home_url(); ?>'
        },
        actions: [
            {
                action: 'view',
                title: 'View',
                icon: '<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/icons/action-view.png'
            },
            {
                action: 'dismiss',
                title: 'Dismiss',
                icon: '<?php echo NANIMADE_SUITE_PLUGIN_URL; ?>assets/icons/action-dismiss.png'
            }
        ]
    };
    
    if (event.data) {
        const data = event.data.json();
        options.title = data.title || 'NaniMade Pickles';
        options.body = data.body || options.body;
        options.icon = data.icon || options.icon;
        options.data.url = data.url || options.data.url;
    }
    
    event.waitUntil(
        self.registration.showNotification(options.title || 'NaniMade Pickles', options)
    );
});

// Notification click handling
self.addEventListener('notificationclick', event => {
    console.log('NaniMade SW: Notification clicked');
    
    event.notification.close();
    
    if (event.action === 'view') {
        event.waitUntil(
            clients.openWindow(event.notification.data.url)
        );
    }
});

// Helper function to sync cart data
async function syncCart() {
    try {
        const cartData = await getStoredCartData();
        if (cartData) {
            const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'sync_cart_data',
                    nonce: '<?php echo wp_create_nonce('nanimade_sync_nonce'); ?>',
                    cart_data: JSON.stringify(cartData)
                })
            });
            
            if (response.ok) {
                console.log('NaniMade SW: Cart synced successfully');
                clearStoredCartData();
            }
        }
    } catch (error) {
        console.error('NaniMade SW: Cart sync failed:', error);
    }
}

// Helper functions for cart data management
function getStoredCartData() {
    return new Promise((resolve) => {
        // Implementation would depend on IndexedDB or other storage
        resolve(null);
    });
}

function clearStoredCartData() {
    // Clear stored cart data after successful sync
}
        <?php
        exit;
    }
    
    private function serve_offline_page() {
        header('Content-Type: text/html');
        ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php _e('Offline', 'nanimade-suite'); ?> - <?php bloginfo('name'); ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }
        
        .offline-container {
            max-width: 400px;
            padding: 2rem;
        }
        
        .offline-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
            opacity: 0.8;
        }
        
        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            font-weight: 300;
        }
        
        p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .retry-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .retry-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }
        
        .pickle-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-icon pickle-animation">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="50" cy="50" rx="20" ry="35" fill="currentColor" opacity="0.8"/>
                <ellipse cx="45" cy="35" rx="3" ry="4" fill="rgba(255,255,255,0.6)"/>
                <ellipse cx="55" cy="45" rx="2" ry="3" fill="rgba(255,255,255,0.6)"/>
                <ellipse cx="48" cy="55" rx="2.5" ry="3.5" fill="rgba(255,255,255,0.6)"/>
                <ellipse cx="52" cy="65" rx="2" ry="3" fill="rgba(255,255,255,0.6)"/>
            </svg>
        </div>
        
        <h1><?php _e('You\'re Offline', 'nanimade-suite'); ?></h1>
        <p><?php _e('Don\'t worry! You can still browse our cached pickle collection. Check your connection and try again when you\'re back online.', 'nanimade-suite'); ?></p>
        
        <button class="retry-btn" onclick="window.location.reload()">
            <?php _e('Try Again', 'nanimade-suite'); ?>
        </button>
    </div>
    
    <script>
        // Auto-retry when connection is restored
        window.addEventListener('online', function() {
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    </script>
</body>
</html>
        <?php
        exit;
    }
    
    private function get_cache_files() {
        return array(
            home_url('/'),
            home_url('/offline.html'),
            wc_get_page_permalink('shop'),
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/mobile-styles.css',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/css/animations.css',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/js/mobile-core.js',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/js/touch-interactions.js',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/icon-192x192.png',
            NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/icon-512x512.png'
        );
    }
    
    public function register_push_subscription() {
        check_ajax_referer('nanimade_pwa_nonce', 'nonce');
        
        $subscription = json_decode(stripslashes($_POST['subscription']), true);
        $user_id = get_current_user_id();
        
        if ($subscription && isset($subscription['endpoint'])) {
            $subscriptions = get_option('nanimade_push_subscriptions', array());
            
            $subscription_data = array(
                'endpoint' => $subscription['endpoint'],
                'keys' => $subscription['keys'],
                'user_id' => $user_id,
                'created' => current_time('mysql')
            );
            
            $subscriptions[] = $subscription_data;
            update_option('nanimade_push_subscriptions', $subscriptions);
            
            wp_send_json_success(array(
                'message' => __('Push notifications enabled!', 'nanimade-suite')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Invalid subscription data.', 'nanimade-suite')
            ));
        }
    }
    
    public function send_push_notification() {
        check_ajax_referer('nanimade_pwa_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'nanimade-suite')));
        }
        
        $title = sanitize_text_field($_POST['title']);
        $body = sanitize_textarea_field($_POST['body']);
        $url = esc_url_raw($_POST['url']);
        
        $subscriptions = get_option('nanimade_push_subscriptions', array());
        $sent_count = 0;
        
        foreach ($subscriptions as $subscription) {
            if ($this->send_push_to_subscription($subscription, $title, $body, $url)) {
                $sent_count++;
            }
        }
        
        wp_send_json_success(array(
            'message' => sprintf(__('Notification sent to %d subscribers.', 'nanimade-suite'), $sent_count)
        ));
    }
    
    private function send_push_to_subscription($subscription, $title, $body, $url) {
        $vapid_private_key = get_option('nanimade_vapid_private_key');
        $vapid_public_key = get_option('nanimade_vapid_public_key');
        
        if (!$vapid_private_key || !$vapid_public_key) {
            return false;
        }
        
        $payload = json_encode(array(
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'icon' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/icon-192x192.png',
            'badge' => NANIMADE_SUITE_PLUGIN_URL . 'assets/icons/badge-72x72.png'
        ));
        
        // This would require a proper Web Push library implementation
        // For now, we'll return true as a placeholder
        return true;
    }
}

// Add query var for PWA endpoints
function nanimade_add_pwa_query_vars($vars) {
    $vars[] = 'nanimade_pwa';
    return $vars;
}
add_filter('query_vars', 'nanimade_add_pwa_query_vars');