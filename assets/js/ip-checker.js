/**
 * IP-CHECKER JavaScript
 *
 * @package ToolZoo
 */

document.addEventListener('DOMContentLoaded', function() {
    initIPChecker();
});

/**
 * Initialize IP Checker
 */
function initIPChecker() {
    setupCopyButtons();
}

/**
 * Setup copy button event listeners
 */
function setupCopyButtons() {
    const buttons = document.querySelectorAll('.toolzoo-ip-checker-copy-btn');

    buttons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const target = this.dataset.target;
            const container = this.closest('.toolzoo-ip-checker-item');
            const valueElement = container.querySelector('.toolzoo-ip-checker-value');
            const textToCopy = valueElement.dataset.value || valueElement.textContent;

            copyToClipboard(textToCopy, this);
        });
    });
}

/**
 * Copy text to clipboard
 *
 * @param {string} text Text to copy
 * @param {HTMLElement} button Button element for feedback
 */
async function copyToClipboard(text, button) {
    try {
        await navigator.clipboard.writeText(text);
        showCopySuccess(button);
    } catch (err) {
        fallbackCopyToClipboard(text, button);
    }
}

/**
 * Show copy success feedback
 *
 * @param {HTMLElement} button Button element
 */
function showCopySuccess(button) {
    const originalText = button.textContent;
    const copiedText = (typeof toolzooIpL10n !== 'undefined' && toolzooIpL10n.copied)
        ? toolzooIpL10n.copied
        : 'Copied!';

    button.textContent = copiedText;
    button.classList.add('copied');

    setTimeout(function() {
        button.textContent = originalText;
        button.classList.remove('copied');
    }, 2000);
}

/**
 * Fallback copy to clipboard (for older browsers)
 *
 * @param {string} text Text to copy
 * @param {HTMLElement} button Button element for feedback
 * @return {boolean} Success or failure
 */
function fallbackCopyToClipboard(text, button) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    textarea.style.top = '0';
    textarea.style.left = '0';
    document.body.appendChild(textarea);

    try {
        textarea.select();
        const success = document.execCommand('copy');

        if (success && button) {
            showCopySuccess(button);
        }

        document.body.removeChild(textarea);
        return success;
    } catch (err) {
        console.error('Fallback copy failed:', err);
        document.body.removeChild(textarea);
        return false;
    }
}
