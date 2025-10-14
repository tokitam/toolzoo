/**
 * パスワード生成ツール JavaScript
 *
 * @package ToolZoo
 */

(function() {
    'use strict';

    // 現在表示中のパスワードリスト
    let currentPasswords = [];

    /**
     * 初期化
     */
    document.addEventListener('DOMContentLoaded', function() {
        initPasswordGenerator();
    });

    /**
     * パスワードジェネレーターの初期化
     */
    function initPasswordGenerator() {
        // イベントリスナーを設定
        setupEventListeners();

        // デフォルト設定で自動生成
        const defaultOptions = getOptions();
        generateAndDisplay(defaultOptions);
    }

    /**
     * イベントリスナーの設定
     */
    function setupEventListeners() {
        // 生成ボタン
        const generateBtn = document.getElementById('toolzoo-generate-btn');
        if (generateBtn) {
            generateBtn.addEventListener('click', function() {
                const options = getOptions();
                if (validateOptions(options)) {
                    generateAndDisplay(options);
                }
            });
        }

        // スライダー
        const slider = document.getElementById('toolzoo-password-length');
        if (slider) {
            slider.addEventListener('input', function() {
                const valueDisplay = document.getElementById('toolzoo-length-value');
                if (valueDisplay) {
                    valueDisplay.textContent = this.value;
                }
            });
        }

        // チェックボックス
        const checkboxes = document.querySelectorAll('.toolzoo-char-type');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                validateOptions(getOptions());
            });
        });
    }

    /**
     * ユーザー設定を取得
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
     * オプションのバリデーション
     */
    function validateOptions(options) {
        const errorMsg = document.getElementById('toolzoo-error-message');

        if (!options.numbers && !options.lowercase && !options.uppercase && !options.symbols) {
            showError('最低1つの文字種別を選択してください');
            return false;
        }

        hideError();
        return true;
    }

    /**
     * エラーメッセージを表示
     */
    function showError(message) {
        const errorMsg = document.getElementById('toolzoo-error-message');
        if (errorMsg) {
            errorMsg.textContent = message;
            errorMsg.style.display = 'block';
        }
    }

    /**
     * エラーメッセージを非表示
     */
    function hideError() {
        const errorMsg = document.getElementById('toolzoo-error-message');
        if (errorMsg) {
            errorMsg.style.display = 'none';
        }
    }

    /**
     * パスワードを生成して表示
     */
    function generateAndDisplay(options) {
        const passwords = generatePasswordList(options.length, options, 20);
        currentPasswords = passwords;
        displayPasswordList(passwords);
    }

    /**
     * パスワードリストを生成
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
     * 1個のパスワードを生成
     */
    function generatePassword(length, options) {
        let charset = '';
        if (options.numbers) charset += '0123456789';
        if (options.lowercase) charset += 'abcdefghijklmnopqrstuvwxyz';
        if (options.uppercase) charset += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if (options.symbols) charset += '!@#$%^&*()_+-=[]{}|;:,.<>?';

        // 間違えやすい文字を除外
        if (options.excludeAmbiguous) {
            const ambiguous = '0OoQiIl1!\'"`.,:;/\\|[]~-';
            charset = charset.split('').filter(function(char) {
                return !ambiguous.includes(char);
            }).join('');
        }

        let password = '';

        // crypto.getRandomValuesを使用（暗号学的に安全）
        if (window.crypto && window.crypto.getRandomValues) {
            const values = new Uint32Array(length);
            window.crypto.getRandomValues(values);

            for (let i = 0; i < length; i++) {
                password += charset[values[i] % charset.length];
            }
        } else {
            // フォールバック: Math.random()
            for (let i = 0; i < length; i++) {
                password += charset[Math.floor(Math.random() * charset.length)];
            }
        }

        // 各文字種別が含まれるか確認
        if (!validatePassword(password, options)) {
            return generatePassword(length, options); // 再帰的に再生成
        }

        return password;
    }

    /**
     * パスワードのバリデーション
     */
    function validatePassword(password, options) {
        if (options.numbers && !/[0-9]/.test(password)) return false;
        if (options.lowercase && !/[a-z]/.test(password)) return false;
        if (options.uppercase && !/[A-Z]/.test(password)) return false;
        if (options.symbols && !/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(password)) return false;
        return true;
    }

    /**
     * パスワードリストを表示
     */
    function displayPasswordList(passwords) {
        const container = document.getElementById('toolzoo-password-list-container');
        if (!container) return;

        container.innerHTML = renderPasswordList(passwords);
        attachCopyEventListeners(passwords);
    }

    /**
     * パスワードリストのHTMLを生成
     */
    function renderPasswordList(passwords) {
        let html = '<ol class="toolzoo-password-list">';

        passwords.forEach(function(password, index) {
            html += '<li class="toolzoo-password-item">';
            html += '<span class="toolzoo-password-text">' + escapeHtml(password) + '</span>';
            html += '<button class="toolzoo-copy-single-btn" data-password-index="' + index + '">コピー</button>';
            html += '</li>';
        });

        html += '</ol>';
        html += '<div class="toolzoo-copy-all-container">';
        html += '<button id="toolzoo-copy-all-btn" class="toolzoo-btn toolzoo-btn-primary">すべてコピー</button>';
        html += '</div>';

        return html;
    }

    /**
     * HTMLエスケープ
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
     * コピーイベントリスナーを設定
     */
    function attachCopyEventListeners(passwords) {
        // 個別コピーボタン
        const singleBtns = document.querySelectorAll('.toolzoo-copy-single-btn');
        singleBtns.forEach(function(button) {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-password-index'));
                const password = passwords[index];
                copySinglePassword(password, this);
            });
        });

        // すべてコピーボタン
        const copyAllBtn = document.getElementById('toolzoo-copy-all-btn');
        if (copyAllBtn) {
            copyAllBtn.addEventListener('click', function() {
                copyAllPasswords(passwords);
            });
        }
    }

    /**
     * 個別パスワードをコピー
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
     * すべてのパスワードをコピー
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
     * コピー成功のフィードバック表示
     */
    function showCopySuccess(button) {
        const originalText = button.textContent;
        button.textContent = 'コピー済み';
        button.classList.add('copied');

        setTimeout(function() {
            button.textContent = originalText;
            button.classList.remove('copied');
        }, 2000);
    }

    /**
     * すべてコピー成功のフィードバック表示
     */
    function showCopyAllSuccess() {
        const button = document.getElementById('toolzoo-copy-all-btn');
        if (!button) return;

        const originalText = button.textContent;
        button.textContent = 'コピー完了';
        button.classList.add('copied');

        setTimeout(function() {
            button.textContent = originalText;
            button.classList.remove('copied');
        }, 2000);
    }

    /**
     * フォールバック: 古いブラウザ対応
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
            console.error('コピーに失敗しました', err);
        } finally {
            document.body.removeChild(textarea);
        }
    }

})();
