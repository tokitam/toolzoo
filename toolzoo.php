<?php
/**
 * Plugin Name: ToolZoo
 * Plugin URI: https://github.com/yourusername/toolzoo
 * Description: 便利なツール集を提供するWordPressプラグイン。パスワード生成、年号一覧表示などの機能を含みます。
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: toolzoo
 * Domain Path: /languages
 */

// セキュリティ: 直接アクセスを防止
if (!defined('ABSPATH')) {
    exit;
}

// プラグインの定数定義
define('TOOLZOO_VERSION', '1.0.0');
define('TOOLZOO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TOOLZOO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TOOLZOO_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * メインクラスの読み込み
 */
require_once TOOLZOO_PLUGIN_DIR . 'includes/class-toolzoo.php';

/**
 * プラグインの初期化
 */
function toolzoo_init() {
    $toolzoo = new Toolzoo();
    $toolzoo->init();
}
add_action('plugins_loaded', 'toolzoo_init');

/**
 * プラグイン有効化時の処理
 */
function toolzoo_activate() {
    // 将来的な処理のためのプレースホルダー
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'toolzoo_activate');

/**
 * プラグイン無効化時の処理
 */
function toolzoo_deactivate() {
    // 将来的な処理のためのプレースホルダー
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'toolzoo_deactivate');
