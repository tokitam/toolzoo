<?php
/**
 * 年号一覧表示機能クラス
 *
 * @package ToolZoo
 */

// セキュリティ: 直接アクセスを防止
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_Nengo_List クラス
 */
class Toolzoo_Nengo_List {
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
        <div class="toolzoo-nengo-container" id="toolzoo-nengo-list">
            <div class="toolzoo-nengo-header">
                <h3>年号一覧表</h3>
                <p class="toolzoo-nengo-description">
                    1868年（明治元年）から<?php echo date('Y'); ?>年までの西暦と日本の年号を一覧表示しています。
                </p>
            </div>

            <div class="toolzoo-nengo-jump-buttons">
                <button class="toolzoo-nengo-jump-btn" data-era="meiji">明治</button>
                <button class="toolzoo-nengo-jump-btn" data-era="taisho">大正</button>
                <button class="toolzoo-nengo-jump-btn" data-era="showa">昭和</button>
                <button class="toolzoo-nengo-jump-btn" data-era="heisei">平成</button>
                <button class="toolzoo-nengo-jump-btn" data-era="reiwa">令和</button>
            </div>

            <div class="toolzoo-nengo-table-wrapper">
                <div id="toolzoo-nengo-table-container">
                    <!-- JavaScriptで動的に生成 -->
                    <div class="toolzoo-loading">データを読み込んでいます...</div>
                </div>
            </div>

            <div class="toolzoo-nengo-footer">
                <button id="toolzoo-scroll-top-btn" class="toolzoo-btn-secondary">
                    ページトップへ
                </button>
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
            'toolzoo-nengo-css',
            TOOLZOO_PLUGIN_URL . 'assets/css/nengo.css',
            array(),
            TOOLZOO_VERSION
        );

        // JavaScript
        wp_enqueue_script(
            'toolzoo-nengo-js',
            TOOLZOO_PLUGIN_URL . 'assets/js/nengo.js',
            array(),
            TOOLZOO_VERSION,
            true
        );
    }
}
