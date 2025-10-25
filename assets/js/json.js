/**
 * JSON Processor Module
 * Handles JSON validation, formatting, and processing
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        INDENT: 4,
        DEBOUNCE_DELAY: 500,
        MAX_FILE_SIZE: 1048576, // 1MB
        COPY_SUCCESS_TIMEOUT: 2000
    };

    // Global state
    let state = {
        currentJSON: null,
        currentMode: 'pretty',
        validationDelay: null,
        passwords: []
    };

    // DOM elements
    let elements = {};

    /**
     * Initialize the JSON processor
     */
    function init() {
        cacheElements();
        setupEventListeners();
        setupTabHandlers();
    }

    /**
     * Cache DOM elements
     */
    function cacheElements() {
        elements = {
            input: document.getElementById('toolzoo-json-input'),
            output: document.getElementById('toolzoo-json-output'),
            errorDiv: document.getElementById('toolzoo-json-error'),
            statusText: document.getElementById('toolzoo-json-status-text'),
            tabs: document.querySelectorAll('.toolzoo-json-tab'),
            pasteBtn: document.getElementById('toolzoo-json-paste'),
            clearBtn: document.getElementById('toolzoo-json-clear'),
            copyInputBtn: document.getElementById('toolzoo-json-copy-input'),
            downloadBtn: document.getElementById('toolzoo-json-download'),
            copyOutputBtn: document.getElementById('toolzoo-json-copy-output'),
            downloadOutputBtn: document.getElementById('toolzoo-json-download-output'),
            clearOutputBtn: document.getElementById('toolzoo-json-clear-output')
        };
    }

    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        if (elements.input) {
            elements.input.addEventListener('input', debounce(validateAndUpdate, CONFIG.DEBOUNCE_DELAY));
        }

        if (elements.pasteBtn) {
            elements.pasteBtn.addEventListener('click', pasteFromClipboard);
        }

        if (elements.clearBtn) {
            elements.clearBtn.addEventListener('click', clearInput);
        }

        if (elements.copyInputBtn) {
            elements.copyInputBtn.addEventListener('click', () => copyToClipboard('toolzoo-json-input'));
        }

        if (elements.downloadBtn) {
            elements.downloadBtn.addEventListener('click', downloadInput);
        }

        if (elements.copyOutputBtn) {
            elements.copyOutputBtn.addEventListener('click', () => copyToClipboard('toolzoo-json-output'));
        }

        if (elements.downloadOutputBtn) {
            elements.downloadOutputBtn.addEventListener('click', downloadOutput);
        }

        if (elements.clearOutputBtn) {
            elements.clearOutputBtn.addEventListener('click', clearOutput);
        }
    }

    /**
     * Setup tab handlers
     */
    function setupTabHandlers() {
        if (elements.tabs) {
            elements.tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const mode = this.getAttribute('data-tab');
                    switchTab(mode);
                });
            });
        }
    }

    /**
     * Switch active tab
     */
    function switchTab(mode) {
        state.currentMode = mode;

        // Update tab UI
        elements.tabs.forEach(tab => {
            tab.classList.remove('active');
            if (tab.getAttribute('data-tab') === mode) {
                tab.classList.add('active');
            }
        });

        // Process and display
        validateAndUpdate();
    }

    /**
     * Validate and update output
     */
    function validateAndUpdate() {
        const inputText = elements.input.value.trim();

        if (!inputText) {
            clearError();
            clearOutput();
            updateStatus(false, { type: 'empty' });
            return;
        }

        const validation = validateJSON(inputText);

        if (validation.valid) {
            clearError();
            state.currentJSON = validation.data;
            processAndDisplay(inputText);
        } else {
            showError(validation.message);
            clearOutput();
            updateStatus(false, { type: 'invalid', error: validation.message });
        }
    }

    /**
     * Validate JSON string
     */
    function validateJSON(text) {
        try {
            const data = JSON.parse(text);
            return {
                valid: true,
                data: data,
                error: null
            };
        } catch (error) {
            return {
                valid: false,
                data: null,
                message: extractErrorMessage(error.message)
            };
        }
    }

    /**
     * Extract error message from JSON parse error
     */
    function extractErrorMessage(errorMsg) {
        // Translate common JSON error messages
        const translations = {
            'Unexpected token': 'JSON解析エラー: 予期しないトークンが見つかりました',
            'Unexpected end of JSON input': 'JSON解析エラー: JSONが途中で終わっています',
            'JSON.parse': 'JSON解析エラー'
        };

        for (const [key, value] of Object.entries(translations)) {
            if (errorMsg.includes(key)) {
                return value + ' - ' + errorMsg;
            }
        }

        return 'JSON解析エラー: ' + errorMsg;
    }

    /**
     * Process and display JSON
     */
    function processAndDisplay(inputText) {
        let output = '';

        switch (state.currentMode) {
            case 'pretty':
                output = prettyPrintJSON(inputText);
                break;
            case 'minify':
                output = minifyJSON(inputText);
                break;
            case 'tree':
                output = buildTreeView(state.currentJSON);
                break;
            case 'raw':
                output = inputText;
                break;
        }

        const outputElement = elements.output;
        if (outputElement) {
            outputElement.value = output;
        }

        updateStatus(true, {
            type: 'valid',
            inputSize: new Blob([inputText]).size,
            outputSize: new Blob([output]).size,
            lines: output.split('\n').length
        });
    }

    /**
     * Pretty print JSON
     */
    function prettyPrintJSON(text) {
        try {
            const parsed = JSON.parse(text);
            return JSON.stringify(parsed, null, CONFIG.INDENT);
        } catch (error) {
            return text;
        }
    }

    /**
     * Minify JSON
     */
    function minifyJSON(text) {
        try {
            const parsed = JSON.parse(text);
            return JSON.stringify(parsed);
        } catch (error) {
            return text;
        }
    }

    /**
     * Build Tree View representation
     */
    function buildTreeView(obj, depth = 0) {
        const indent = '  '.repeat(depth);
        let output = '';

        if (Array.isArray(obj)) {
            output += indent + '[\n';
            obj.forEach((item, index) => {
                if (typeof item === 'object' && item !== null) {
                    output += indent + '  [' + index + ']\n';
                    output += buildTreeView(item, depth + 2);
                } else {
                    output += indent + '  [' + index + '] ' + JSON.stringify(item) + '\n';
                }
            });
            output += indent + ']\n';
        } else if (typeof obj === 'object' && obj !== null) {
            output += indent + '{\n';
            const keys = Object.keys(obj);
            keys.forEach((key, index) => {
                const value = obj[key];
                const isLast = index === keys.length - 1;
                if (typeof value === 'object' && value !== null) {
                    output += indent + '  ' + key + ':\n';
                    output += buildTreeView(value, depth + 2);
                } else {
                    output += indent + '  ' + key + ': ' + JSON.stringify(value) + '\n';
                }
            });
            output += indent + '}\n';
        } else {
            output += indent + JSON.stringify(obj) + '\n';
        }

        return output.trim();
    }

    /**
     * Paste from clipboard
     */
    async function pasteFromClipboard() {
        try {
            const text = await navigator.clipboard.readText();
            elements.input.value = text;
            validateAndUpdate();
            showSuccess('クリップボードからペーストしました');
        } catch (error) {
            showError('クリップボードの読み込みに失敗しました');
        }
    }

    /**
     * Copy to clipboard
     */
    async function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        if (!element) {
            return;
        }

        const text = element.value || element.textContent;

        try {
            await navigator.clipboard.writeText(text);
            showCopySuccess(elementId);
        } catch (error) {
            fallbackCopyToClipboard(text);
        }
    }

    /**
     * Fallback copy to clipboard (for older browsers)
     */
    function fallbackCopyToClipboard(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();

        try {
            document.execCommand('copy');
            showSuccess('コピーしました');
        } catch (error) {
            showError('コピーに失敗しました');
        } finally {
            document.body.removeChild(textarea);
        }
    }

    /**
     * Show copy success feedback
     */
    function showCopySuccess(elementId) {
        showSuccess('コピーしました');
    }

    /**
     * Download JSON file
     */
    function downloadInput() {
        const text = elements.input.value;
        if (!text.trim()) {
            showError('入力がありません');
            return;
        }

        downloadFile(text, 'input.json');
    }

    /**
     * Download output file
     */
    function downloadOutput() {
        const text = elements.output.value;
        if (!text.trim()) {
            showError('出力がありません');
            return;
        }

        downloadFile(text, 'output.json');
    }

    /**
     * Download file
     */
    function downloadFile(content, filename) {
        const blob = new Blob([content], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        showSuccess(filename + ' をダウンロードしました');
    }

    /**
     * Clear input
     */
    function clearInput() {
        elements.input.value = '';
        clearOutput();
        clearError();
        updateStatus(false, { type: 'empty' });
    }

    /**
     * Clear output
     */
    function clearOutput() {
        if (elements.output) {
            elements.output.value = '';
        }
    }

    /**
     * Show error message
     */
    function showError(message) {
        if (elements.errorDiv) {
            elements.errorDiv.textContent = message;
            elements.errorDiv.style.display = 'block';
        }
    }

    /**
     * Clear error message
     */
    function clearError() {
        if (elements.errorDiv) {
            elements.errorDiv.style.display = 'none';
        }
    }

    /**
     * Show success message
     */
    function showSuccess(message) {
        // Could be extended to show toast notification
        console.log('Success:', message);
    }

    /**
     * Update status bar
     */
    function updateStatus(valid, stats) {
        if (!elements.statusText) {
            return;
        }

        let statusHtml = '';

        if (stats.type === 'empty') {
            statusHtml = '入力待機中...';
        } else if (stats.type === 'invalid') {
            statusHtml = '✗ 無効なJSON';
        } else if (stats.type === 'valid') {
            statusHtml = '✓ 有効なJSON | ';
            statusHtml += 'サイズ: ' + formatBytes(stats.inputSize);
            if (stats.outputSize) {
                statusHtml += ' → ' + formatBytes(stats.outputSize);
            }
            statusHtml += ' | 行数: ' + stats.lines;
        }

        elements.statusText.innerHTML = statusHtml;
    }

    /**
     * Format bytes to human readable format
     */
    function formatBytes(bytes) {
        if (bytes === 0) {
            return '0 B';
        }
        const k = 1024;
        const sizes = ['B', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    }

    /**
     * Debounce function
     */
    function debounce(func, delay) {
        let timeoutId = null;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
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
