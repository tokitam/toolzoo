/**
 * ToolZoo Links Module
 * Handles active state detection and link interactions
 */

(function() {
    'use strict';

    /**
     * Initialize ToolZoo Links
     */
    function init() {
        detectAndMarkActiveLink();
    }

    /**
     * Detect current tool and mark as active
     */
    function detectAndMarkActiveLink() {
        const currentTool = detectCurrentTool();

        if (currentTool) {
            const activeLink = document.querySelector(
                '.toolzoo-link[data-tool="' + escapeSelector(currentTool) + '"]'
            );

            if (activeLink) {
                activeLink.classList.add('active');
                activeLink.setAttribute('aria-current', 'page');
            }
        }
    }

    /**
     * Detect current tool from URL or page context
     *
     * @return {string|null} Tool ID or null
     */
    function detectCurrentTool() {
        // Check URL query parameter
        const params = new URLSearchParams(window.location.search);
        const toolParam = params.get('tool');

        if (toolParam) {
            return toolParam;
        }

        // Check page slug
        const pathParts = window.location.pathname.split('/').filter(Boolean);

        if (pathParts.length >= 2 && pathParts[0] === 'tools') {
            return pathParts[1];
        }

        // Check if we're on toolzoo or tools page
        if (pathParts.includes('toolzoo') || pathParts.includes('tools')) {
            // Could add more specific detection here
            return null;
        }

        return null;
    }

    /**
     * Escape CSS selector special characters
     *
     * @param {string} str String to escape
     * @return {string} Escaped string
     */
    function escapeSelector(str) {
        return str.replace(/([!"#$%&'()*+,.\/:;?@[\\\]^`{|}~])/g, '\\$1');
    }

    /**
     * Initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
