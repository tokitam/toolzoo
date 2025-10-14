/**
 * Password Generator Tool JavaScript
 *
 * @package ToolZoo
 */

(function() {
    'use strict';

    // Currently displayed password list
    let currentPasswords = [];

    /**
     * Initialize
     */
    document.addEventListener('DOMContentLoaded', function() {
        initPasswordGenerator();
    });

    /**
     * Initialize password generator
     */
    function initPasswordGenerator() {
        // Setup event listeners
        setupEventListeners();

        // Auto-generate with default settings
        const defaultOptions = getOptions();
        generateAndDisplay(defaultOptions);
    }

    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        // Generate button
        const generateBtn = document.getElementById('toolzoo-generate-btn');
        if (generateBtn) {
            generateBtn.addEventListener('click', function() {
                const options = getOptions();
                if (validateOptions(options)) {
                    generateAndDisplay(options);
                }
            });
        }

        // Slider
        const slider = document.getElementById('toolzoo-password-length');
        if (slider) {
            slider.addEventListener('input', function() {
                const valueDisplay = document.getElementById('toolzoo-length-value');
                if (valueDisplay) {
                    valueDisplay.textContent = this.value;
                }
            });
        }

        // Checkboxes
        const checkboxes = document.querySelectorAll('.toolzoo-char-type');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                validateOptions(getOptions());
            });
        });
    }

    /**
     * Get user settings
     */
    function getOptions() {
        const excludeAmbiguousEl = document.getElementById('toolzoo-exclude-ambiguous');
        return {
            length: parseInt(document.getElementById('toolzoo-password-length').value),
            numbers: document.getElementById('toolzoo-use-numbers').checked,
            lowercase: document.getElementById('toolzoo-use-lowercase').checked,
            uppercase: document.getElementById('toolzoo-use-uppercase').checked,
            symbols: document.getElementById('toolzoo-use-symbols').checked,
            excludeAmbiguous: excludeAmbiguousEl ? excludeAmbiguousEl.checked : false
        };
    }

    /**
     * Validate options
     */
    function validateOptions(options) {
        const errorMsg = document.getElementById('toolzoo-error-message');

        if (!options.numbers && !options.lowercase && !options.uppercase && !options.symbols) {
            showError('Please select at least one character type');
            return false;
        }

        hideError();
        return true;
    }

    /**
     * Show error message
     */
    function showError(message) {
        const errorMsg = document.getElementById('toolzoo-error-message');
        if (errorMsg) {
            errorMsg.textContent = message;
            errorMsg.style.display = 'block';
        }
    }

    /**
     * Hide error message
     */
    function hideError() {
        const errorMsg = document.getElementById('toolzoo-error-message');
        if (errorMsg) {
            errorMsg.style.display = 'none';
        }
    }

    /**
     * Generate and display passwords
     */
    function generateAndDisplay(options) {
        const passwords = generatePasswordList(options.length, options, 20);
        currentPasswords = passwords;
        displayPasswordList(passwords);
    }

    /**
     * Generate password list
     */
    function generatePasswordList(length, options, count) {
        const passwords = [];

        for (let i = 0; i < count; i++) {
            const password = generatePassword(length, options);
            passwords.push(password);
        }

        return passwords;
    }

    /**
     * Generate a single password
     */
    function generatePassword(length, options) {
        let charset = '';
        if (options.numbers) charset += '0123456789';
        if (options.lowercase) charset += 'abcdefghijklmnopqrstuvwxyz';
        if (options.uppercase) charset += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if (options.symbols) charset += '!@#$%^&*()_+-=[]{}|;:,.<>?';

        // Exclude ambiguous characters
        if (options.excludeAmbiguous) {
            const ambiguous = '0OoQiIl1!\'"`.,:;/\\|[]~-';
            charset = charset.split('').filter(function(char) {
                return !ambiguous.includes(char);
            }).join('');
        }

        let password = '';

        // Use crypto.getRandomValues (cryptographically secure)
        if (window.crypto && window.crypto.getRandomValues) {
            const values = new Uint32Array(length);
            window.crypto.getRandomValues(values);

            for (let i = 0; i < length; i++) {
                password += charset[values[i] % charset.length];
            }
        } else {
            // Fallback: Math.random()
            for (let i = 0; i < length; i++) {
                password += charset[Math.floor(Math.random() * charset.length)];
            }
        }

        // Check if each character type is included
        if (!validatePassword(password, options)) {
            return generatePassword(length, options); // Recursively regenerate
        }

        return password;
    }

    /**
     * Validate password
     */
    function validatePassword(password, options) {
        if (options.numbers && !/[0-9]/.test(password)) return false;
        if (options.lowercase && !/[a-z]/.test(password)) return false;
        if (options.uppercase && !/[A-Z]/.test(password)) return false;
        if (options.symbols && !/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(password)) return false;
        return true;
    }

    /**
     * Display password list
     */
    function displayPasswordList(passwords) {
        const container = document.getElementById('toolzoo-password-list-container');
        if (!container) return;

        container.innerHTML = renderPasswordList(passwords);
        attachCopyEventListeners(passwords);
    }

    /**
     * Generate password list HTML
     */
    function renderPasswordList(passwords) {
        let html = '<ol class="toolzoo-password-list">';

        passwords.forEach(function(password, index) {
            html += '<li class="toolzoo-password-item">';
            html += '<span class="toolzoo-password-text">' + escapeHtml(password) + '</span>';
            html += '<button class="toolzoo-copy-single-btn" data-password-index="' + index + '">Copy</button>';
            html += '</li>';
        });

        html += '</ol>';
        html += '<div class="toolzoo-copy-all-container">';
        html += '<button id="toolzoo-copy-all-btn" class="toolzoo-btn toolzoo-btn-primary">Copy All</button>';
        html += '</div>';

        return html;
    }

    /**
     * HTML escape
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /**
     * Attach copy event listeners
     */
    function attachCopyEventListeners(passwords) {
        // Individual copy buttons
        const singleBtns = document.querySelectorAll('.toolzoo-copy-single-btn');
        singleBtns.forEach(function(button) {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-password-index'));
                const password = passwords[index];
                copySinglePassword(password, this);
            });
        });

        // Copy all button
        const copyAllBtn = document.getElementById('toolzoo-copy-all-btn');
        if (copyAllBtn) {
            copyAllBtn.addEventListener('click', function() {
                copyAllPasswords(passwords);
            });
        }
    }

    /**
     * Copy single password
     */
    async function copySinglePassword(password, button) {
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(password);
                showCopySuccess(button);
            } else {
                fallbackCopyToClipboard(password, button);
            }
        } catch (err) {
            fallbackCopyToClipboard(password, button);
        }
    }

    /**
     * Copy all passwords
     */
    async function copyAllPasswords(passwords) {
        const text = passwords.join('\n');

        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(text);
                showCopyAllSuccess();
            } else {
                fallbackCopyToClipboard(text, null);
            }
        } catch (err) {
            fallbackCopyToClipboard(text, null);
        }
    }

    /**
     * Show copy success feedback
     */
    function showCopySuccess(button) {
        const originalText = button.textContent;
        button.textContent = 'Copied';
        button.classList.add('copied');

        setTimeout(function() {
            button.textContent = originalText;
            button.classList.remove('copied');
        }, 2000);
    }

    /**
     * Show copy all success feedback
     */
    function showCopyAllSuccess() {
        const button = document.getElementById('toolzoo-copy-all-btn');
        if (!button) return;

        const originalText = button.textContent;
        button.textContent = 'Copied';
        button.classList.add('copied');

        setTimeout(function() {
            button.textContent = originalText;
            button.classList.remove('copied');
        }, 2000);
    }

    /**
     * Fallback: for older browsers
     */
    function fallbackCopyToClipboard(text, button) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();

        try {
            const success = document.execCommand('copy');
            if (success) {
                if (button) {
                    showCopySuccess(button);
                } else {
                    showCopyAllSuccess();
                }
            }
        } catch (err) {
            console.error('Copy failed', err);
        } finally {
            document.body.removeChild(textarea);
        }
    }

})();
