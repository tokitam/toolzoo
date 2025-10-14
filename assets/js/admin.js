/**
 * ToolZoo Admin JavaScript
 */

(function($) {
    'use strict';

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        initCopyButtons();
    });

    /**
     * Initialize copy buttons
     */
    function initCopyButtons() {
        $('.toolzoo-copy-btn').on('click', function(e) {
            e.preventDefault();

            const button = $(this);
            const shortcode = button.data('shortcode');

            copyToClipboard(shortcode, button);
        });
    }

    /**
     * Copy to clipboard
     *
     * @param {string} text Text to copy
     * @param {jQuery} button Button element
     */
    function copyToClipboard(text, button) {
        // Modern browser Clipboard API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text)
                .then(function() {
                    showCopySuccess(button);
                })
                .catch(function(err) {
                    console.error('Copy failed:', err);
                    fallbackCopy(text, button);
                });
        } else {
            // Fallback method
            fallbackCopy(text, button);
        }
    }

    /**
     * Fallback copy method (for older browsers)
     *
     * @param {string} text Text to copy
     * @param {jQuery} button Button element
     */
    function fallbackCopy(text, button) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        textarea.style.top = '0';
        textarea.style.left = '0';
        document.body.appendChild(textarea);
        textarea.select();
        textarea.setSelectionRange(0, 99999); // Mobile support

        try {
            const success = document.execCommand('copy');
            if (success) {
                showCopySuccess(button);
            } else {
                console.error('execCommand("copy") failed');
            }
        } catch (err) {
            console.error('Fallback copy failed:', err);
        } finally {
            document.body.removeChild(textarea);
        }
    }

    /**
     * Show visual feedback for successful copy
     *
     * @param {jQuery} button Button element
     */
    function showCopySuccess(button) {
        const originalText = button.text();

        // Change button text and style
        button.text('Copied!');
        button.addClass('copied');

        // Temporarily disable button
        button.prop('disabled', true);

        // Restore after 2 seconds
        setTimeout(function() {
            button.text(originalText);
            button.removeClass('copied');
            button.prop('disabled', false);
        }, 2000);
    }

})(jQuery);
