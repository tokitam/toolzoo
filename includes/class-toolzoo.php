<?php
/**
 * ToolZoo メインクラス
 *
 * @package ToolZoo
 */

// セキュリティ: 直接アクセスを防止
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo メインクラス
 */
class Toolzoo {
    /**
     * 初期化
     */
    public function init() {
        // 機能クラスの読み込み
        $this->load_classes();

        // 管理画面クラスの読み込み
        $this->load_admin_class();

        // ショートコードの登録
        $this->register_shortcodes();

        // 国際化の設定
        add_action('init', array($this, 'load_textdomain'));
    }

    /**
     * 機能クラスの読み込み
     */
    private function load_classes() {
        require_once TOOLZOO_PLUGIN_DIR . 'includes/class-password-generator.php';
        require_once TOOLZOO_PLUGIN_DIR . 'includes/class-nengo-list.php';
    }

    /**
     * 管理画面クラスの読み込み
     */
    private function load_admin_class() {
        if (is_admin()) {
            require_once TOOLZOO_PLUGIN_DIR . 'includes/class-admin.php';
            new Toolzoo_Admin();
        }
    }

    /**
     * ショートコードの登録
     */
    private function register_shortcodes() {
        add_shortcode('toolzoo_password', array($this, 'password_shortcode'));
        add_shortcode('toolzoo_nengo', array($this, 'nengo_shortcode'));
    }

    /**
     * パスワード生成ショートコード
     *
     * @param array $atts ショートコード属性
     * @return string HTML出力
     */
    public function password_shortcode($atts) {
        $generator = new Toolzoo_Password_Generator();
        return $generator->render();
    }

    /**
     * 年号一覧ショートコード
     *
     * @param array $atts ショートコード属性
     * @return string HTML出力
     */
    public function nengo_shortcode($atts) {
        $list = new Toolzoo_Nengo_List();
        return $list->render();
    }

    /**
     * 翻訳ファイルの読み込み
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'toolzoo',
            false,
            dirname(TOOLZOO_PLUGIN_BASENAME) . '/languages'
        );
    }
}
