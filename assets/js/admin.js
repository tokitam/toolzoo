/**
 * ToolZoo 管理画面JavaScript
 */

(function($) {
    'use strict';

    /**
     * ドキュメント準備完了時の処理
     */
    $(document).ready(function() {
        initCopyButtons();
    });

    /**
     * コピーボタンの初期化
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
     * クリップボードにコピー
     *
     * @param {string} text コピーするテキスト
     * @param {jQuery} button ボタン要素
     */
    function copyToClipboard(text, button) {
        // モダンブラウザのClipboard API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text)
                .then(function() {
                    showCopySuccess(button);
                })
                .catch(function(err) {
                    console.error('コピーに失敗しました:', err);
                    fallbackCopy(text, button);
                });
        } else {
            // フォールバック処理
            fallbackCopy(text, button);
        }
    }

    /**
     * フォールバックコピー処理（古いブラウザ対応）
     *
     * @param {string} text コピーするテキスト
     * @param {jQuery} button ボタン要素
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
        textarea.setSelectionRange(0, 99999); // モバイル対応

        try {
            const success = document.execCommand('copy');
            if (success) {
                showCopySuccess(button);
            } else {
                console.error('execCommand("copy")が失敗しました');
            }
        } catch (err) {
            console.error('フォールバックコピーに失敗しました:', err);
        } finally {
            document.body.removeChild(textarea);
        }
    }

    /**
     * コピー成功の視覚的フィードバック
     *
     * @param {jQuery} button ボタン要素
     */
    function showCopySuccess(button) {
        const originalText = button.text();

        // ボタンのテキストとスタイルを変更
        button.text('コピーしました！');
        button.addClass('copied');

        // ボタンを一時的に無効化
        button.prop('disabled', true);

        // 2秒後に元に戻す
        setTimeout(function() {
            button.text(originalText);
            button.removeClass('copied');
            button.prop('disabled', false);
        }, 2000);
    }

})(jQuery);
