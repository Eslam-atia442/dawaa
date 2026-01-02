<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opening in Naseh App</title>
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}">
    <meta property="og:title" content="{{ $title ?? 'Naseh App' }}">
    <meta property="og:description" content="{{ $description ?? 'View details in the Naseh app' }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}">
    <meta property="twitter:title" content="{{ $title ?? 'Naseh App' }}">
    <meta property="twitter:description" content="{{ $description ?? 'View details in the Naseh app' }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #0A0A0F 0%, #1A1A2E 100%);
            color: #FFFFFF;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        
        .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #5453EC;
        }
        
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.2);
            border-top: 4px solid #5453EC;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .message {
            margin-top: 20px;
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 20px;
        }
        
        .button {
            display: inline-block;
            padding: 14px 28px;
            background: #5453EC;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 10px;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }
        
        .button:hover {
            background: #4342D4;
        }
        
        .button.secondary {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">Naseh</div>
        <div class="spinner" id="spinner"></div>
        <div class="message" id="message">Opening in app...</div>
        <div id="buttons" style="display: none; margin-top: 20px;">
            <a href="#" id="open-app-btn" class="button">Open in App</a>
            <a href="#" id="store-btn" class="button secondary">Download App</a>
        </div>
    </div>

    <script>
        // ============================================
        // CONFIGURATION
        // ============================================
        
        const iOS_APP_STORE_URL = '{{ config("app.ios_app_store_url", "https://apps.apple.com/app/id123456789") }}';
        const ANDROID_PLAY_STORE_URL = '{{ config("app.android_play_store_url", "https://play.google.com/store/apps/details?id=com.naseh.app") }}';
        
        // ============================================
        // PREVENT INFINITE LOOPS
        // ============================================
        
        // Check if we've already tried to open the app (prevent loops)
        const ATTEMPT_KEY = 'naseh_deeplink_attempt';
        const hasAttempted = sessionStorage.getItem(ATTEMPT_KEY);
        
        if (hasAttempted) {
            // Already attempted, show buttons instead of auto-redirecting
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('message').textContent = 'Tap the button below to open in app';
                document.getElementById('buttons').style.display = 'block';
                document.getElementById('spinner').style.display = 'none';
            });
        } else {
            // Mark that we're attempting
            sessionStorage.setItem(ATTEMPT_KEY, 'true');
        }
        
        // ============================================
        // DETECT DEVICE
        // ============================================
        
        const userAgent = navigator.userAgent || navigator.vendor || window.opera;
        const isIOS = /iPad|iPhone|iPod/.test(userAgent) && !window.MSStream;
        const isAndroid = /android/i.test(userAgent);
        const isMobile = isIOS || isAndroid;
        
        // ============================================
        // GET CURRENT ROUTE AND PARAMS
        // ============================================
        
        const currentPath = window.location.pathname;
        const urlParams = new URLSearchParams(window.location.search);
        const queryString = urlParams.toString();
        
        // Build app deep link URLs
        const customSchemeUrl = `naseh://${currentPath.replace('/', '')}${queryString ? '?' + queryString : ''}`;
        
        // ============================================
        // TRACK APP OPENING
        // ============================================
        
        let appOpened = false;
        let redirectTimer = null;
        let attemptCount = 0;
        const MAX_ATTEMPTS = 1; // Prevent multiple attempts
        
        function updateMessage(text) {
            document.getElementById('message').textContent = text;
        }
        
        function showButtons() {
            document.getElementById('buttons').style.display = 'block';
            document.getElementById('spinner').style.display = 'none';
        }
        
        function redirectToStore() {
            if (appOpened) return;
            
            // Clear the attempt flag before redirecting
            sessionStorage.removeItem(ATTEMPT_KEY);
            
            const storeUrl = isIOS ? iOS_APP_STORE_URL : ANDROID_PLAY_STORE_URL;
            updateMessage(isIOS ? 'Redirecting to App Store...' : 'Redirecting to Play Store...');
            
            // Use replace to prevent back button issues
            window.location.replace(storeUrl);
        }
        
        // ============================================
        // DETECT IF APP OPENED
        // ============================================
        
        function markAppOpened() {
            if (appOpened) return;
            appOpened = true;
            if (redirectTimer) {
                clearTimeout(redirectTimer);
            }
            updateMessage('App opened! ✅');
            document.getElementById('spinner').style.display = 'none';
            // Clear attempt flag when app opens
            sessionStorage.removeItem(ATTEMPT_KEY);
        }
        
        // Detect when page becomes hidden (app opened)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                markAppOpened();
            }
        });
        
        window.addEventListener('blur', () => {
            markAppOpened();
        });
        
        window.addEventListener('pagehide', () => {
            markAppOpened();
        });
        
        // ============================================
        // TRY TO OPEN APP (using custom scheme)
        // ============================================
        
        function tryOpenApp() {
            // Don't try if already attempted (prevents loops)
            if (hasAttempted || attemptCount >= MAX_ATTEMPTS) {
                showButtons();
                return;
            }
            
            if (!isMobile) {
                updateMessage('Please open this link on a mobile device');
                showButtons();
                return;
            }
            
            attemptCount++;
            updateMessage('Opening app...');
            
            if (isAndroid) {
                // Android: Try custom scheme first (won't reload page if app not installed)
                // Create a hidden iframe to attempt opening
                const iframe = document.createElement('iframe');
                iframe.style.border = 'none';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.display = 'none';
                iframe.src = customSchemeUrl;
                document.body.appendChild(iframe);
                
                // Also try Universal Link as fallback
                setTimeout(() => {
                    if (!appOpened) {
                        // Try Universal Link (but use window.open to avoid page reload)
                        const link = document.createElement('a');
                        link.href = customSchemeUrl;
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                }, 500);
                
                // If app doesn't open within 2 seconds, redirect to Play Store
                redirectTimer = setTimeout(() => {
                    if (!appOpened && !document.hidden) {
                        document.body.removeChild(iframe);
                        redirectToStore();
                    }
                }, 2000);
                
            } else if (isIOS) {
                // iOS: Try custom scheme first
                const link = document.createElement('a');
                link.href = customSchemeUrl;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Fallback: Try Universal Link after 1 second
                setTimeout(() => {
                    if (!appOpened && !document.hidden) {
                        // Try Universal Link
                        const universalLink = document.createElement('a');
                        universalLink.href = window.location.href;
                        universalLink.style.display = 'none';
                        document.body.appendChild(universalLink);
                        universalLink.click();
                        document.body.removeChild(universalLink);
                        
                        // Final fallback: Redirect to App Store after another 2 seconds
                        redirectTimer = setTimeout(() => {
                            if (!appOpened && !document.hidden) {
                                redirectToStore();
                            }
                        }, 2000);
                    }
                }, 1000);
            }
        }
        
        // ============================================
        // MANUAL BUTTONS
        // ============================================
        
        document.getElementById('open-app-btn').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Clear attempt flag to allow manual retry
            sessionStorage.removeItem(ATTEMPT_KEY);
            
            appOpened = false;
            updateMessage('Opening app...');
            document.getElementById('spinner').style.display = 'block';
            document.getElementById('buttons').style.display = 'none';
            
            if (isAndroid) {
                const iframe = document.createElement('iframe');
                iframe.style.border = 'none';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.display = 'none';
                iframe.src = customSchemeUrl;
                document.body.appendChild(iframe);
                
                setTimeout(() => {
                    const link = document.createElement('a');
                    link.href = customSchemeUrl;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }, 500);
                
                redirectTimer = setTimeout(() => {
                    if (!appOpened && !document.hidden) {
                        document.body.removeChild(iframe);
                        redirectToStore();
                    }
                }, 2000);
            } else if (isIOS) {
                const link = document.createElement('a');
                link.href = customSchemeUrl;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                setTimeout(() => {
                    if (!appOpened && !document.hidden) {
                        const universalLink = document.createElement('a');
                        universalLink.href = window.location.href;
                        universalLink.style.display = 'none';
                        document.body.appendChild(universalLink);
                        universalLink.click();
                        document.body.removeChild(universalLink);
                        
                        redirectTimer = setTimeout(() => {
                            if (!appOpened && !document.hidden) {
                                redirectToStore();
                            }
                        }, 2000);
                    }
                }, 1000);
            }
        });
        
        document.getElementById('store-btn').addEventListener('click', function(e) {
            e.preventDefault();
            redirectToStore();
        });
        
        // ============================================
        // PAGE FOCUS - USER CAME BACK (app didn't open)
        // ============================================
        
        window.addEventListener('focus', () => {
            setTimeout(() => {
                if (!appOpened && !document.hidden && isMobile) {
                    updateMessage('Tap the button below to open in app');
                    showButtons();
                }
            }, 500);
        });
        
        // ============================================
        // START AUTOMATICALLY (only if not already attempted)
        // ============================================
        
        if (!hasAttempted) {
            // Start immediately when page loads
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(tryOpenApp, 100);
                });
            } else {
                setTimeout(tryOpenApp, 100);
            }
        }
    </script>
</body>
</html>

