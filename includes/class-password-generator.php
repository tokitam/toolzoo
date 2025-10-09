<?php
/**
 * パスワード生成機能クラス
 *
 * @package ToolZoo
 */

// セキュリティ: 直接アクセスを防止
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_Password_Generator クラス
 */
class Toolzoo_Password_Generator {
    /**
     * HTML出力を生成
     *
     * @return string HTML出力
     */
    public function render() {
        // CSS/JSをエンキュー
        $this->enqueue_assets();

        // HTMLを生成
        ob_start();
        ?>
        <div class="toolzoo-password-container" id="toolzoo-password-generator">
            <div class="toolzoo-password-header">
                <h3>パスワード生成ツール</h3>
            </div>

            <div class="toolzoo-password-options">
                <div class="toolzoo-option-group">
                    <label for="toolzoo-password-length">
                        文字数: <span id="toolzoo-length-value">16</span>文字
                    </label>
                    <input
                        type="range"
                        id="toolzoo-password-length"
                        class="toolzoo-password-slider"
                        min="8"
                        max="64"
                        value="16"
                        step="1"
                    >
                    <div class="toolzoo-slider-labels">
                        <span>8文字</span>
                        <span>64文字</span>
                    </div>
                </div>

                <div class="toolzoo-option-group">
                    <label class="toolzoo-checkbox-label">
                        <input
                            type="checkbox"
                            id="toolzoo-use-numbers"
                            class="toolzoo-char-type"
                            checked
                        >
                        数字 (0-9)
                    </label>
                    <label class="toolzoo-checkbox-label">
                        <input
                            type="checkbox"
                            id="toolzoo-use-lowercase"
                            class="toolzoo-char-type"
                            checked
                        >
                        英字小文字 (a-z)
                    </label>
                    <label class="toolzoo-checkbox-label">
                        <input
                            type="checkbox"
                            id="toolzoo-use-uppercase"
                            class="toolzoo-char-type"
                            checked
                        >
                        英字大文字 (A-Z)
                    </label>
                    <label class="toolzoo-checkbox-label">
                        <input
                            type="checkbox"
                            id="toolzoo-use-symbols"
                            class="toolzoo-char-type"
                            checked
                        >
                        記号 (!@#$%^&amp;*()_+-=[]{}|;:,.&lt;&gt;?)
                    </label>
                    <label class="toolzoo-checkbox-label">
                        <input
                            type="checkbox"
                            id="toolzoo-exclude-ambiguous"
                            class="toolzoo-option-checkbox"
                        >
                        間違えやすい文字を除外する
                    </label>
                </div>

                <div class="toolzoo-option-group">
                    <button
                        id="toolzoo-generate-btn"
                        class="toolzoo-btn toolzoo-btn-primary"
                    >
                        パスワード生成 (20個)
                    </button>
                </div>

                <div id="toolzoo-error-message" class="toolzoo-error" style="display: none;"></div>
            </div>

            <div class="toolzoo-password-results">
                <h4>生成されたパスワード一覧</h4>
                <div id="toolzoo-password-list-container">
                    <!-- JavaScriptで動的に生成 -->
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * CSS/JSをエンキュー
     */
    private function enqueue_assets() {
        // CSS
        wp_enqueue_style(
            'toolzoo-password-css',
            TOOLZOO_PLUGIN_URL . 'assets/css/password.css',
            array(),
            TOOLZOO_VERSION
        );

        // JavaScript
        wp_enqueue_script(
            'toolzoo-password-js',
            TOOLZOO_PLUGIN_URL . 'assets/js/password.js',
            array(),
            TOOLZOO_VERSION,
            true
        );
    }
}
