/**
 * Admin Authentication Controller
 * Secures admin pages by verifying login status and role
 */

(function () {
    'use strict';

    checkAuth();

    function checkAuth() {
        const userJson = localStorage.getItem('user');

        // 1. Check if user exists
        if (!userJson) {
            console.warn('No user found, redirecting to login...');
            redirectToLogin();
            return;
        }

        try {
            const user = JSON.parse(userJson);

            // 2. Check if user is admin
            if (user.role !== 'admin') {
                console.warn('User is not admin, redirecting to home...');
                window.location.href = '../index.html';
                return;
            }

            // 3. User is authorized
            console.log('Admin authorized:', user.email);
            updateAdminUI(user);

        } catch (e) {
            console.error('Auth error:', e);
            redirectToLogin();
        }
    }

    function redirectToLogin() {
        // Encode current URL to redirect back after login (optional future feature)
        window.location.href = '../login.html';
    }

    function updateAdminUI(user) {
        // Update profile info in sidebar/header if elements exist
        // Note: The template might use different class names, adjusting based on standard EduChamp template

        // Example: Update user name in header
        const validSelectors = [
            '.ttr-user-avatar + span',
            '.ttr-header-right .ttr-user-avatar + span',
            '.widget-card .wc-stats' // Just as an example placeholder
        ];

        // We can add specific UI updates here if needed
    }
})();
