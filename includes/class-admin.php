<?php
/**
 * ToolZoo 管理画面クラス
 *
 * @package ToolZoo
 */

// セキュリティ: 直接アクセスを防止
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_Admin クラス
 *
 * 管理画面のメニュー追加とツール一覧ページの表示を担当
 */
class Toolzoo_Admin {
    /**
     * コンストラクタ
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * 管理画面にメニューを追加
     */
    public function add_admin_menu() {
        add_menu_page(
            'ToolZoo',                          // ページタイトル
            'ToolZoo',                          // メニュータイトル
            'manage_options',                   // 必要な権限
            'toolzoo-tools',                    // メニュースラッグ
            array($this, 'render_admin_page'),  // コールバック関数
            'dashicons-admin-tools',            // アイコン
            65                                  // 表示順
        );
    }

    /**
     * 管理画面ページの表示
     */
    public function render_admin_page() {
        // 権限チェック
        if (!current_user_can('manage_options')) {
            wp_die(__('このページにアクセスする権限がありません。', 'toolzoo'));
        }

        // ツール個別表示かどうかを判定
        $tool_id = isset($_GET['tool']) ? sanitize_text_field($_GET['tool']) : '';

        echo '<div class="wrap toolzoo-admin-page">';

        if ($tool_id) {
            $this->render_tool_preview($tool_id);
        } else {
            $this->render_tools_list();
        }

        echo '</div>';
    }

    /**
     * ツール一覧の表示
     */
    private function render_tools_list() {
        ?>
        <h1><?php echo esc_html(get_admin_page_title()); ?> - 便利ツール一覧</h1>

        <div class="toolzoo-admin-intro">
            <p>このプラグインは便利なツール集を提供します。各ツールをショートコードで記事やページに埋め込むことができます。</p>
        </div>

        <div class="toolzoo-tools-grid">
            <?php
            $tools = $this->get_tools_list();
            foreach ($tools as $tool) {
                $this->render_tool_card($tool);
            }
            ?>
        </div>
        <?php
    }

    /**
     * 個別ツールカードの表示
     *
     * @param array $tool ツール情報
     */
    private function render_tool_card($tool) {
        $admin_url = admin_url('admin.php?page=toolzoo-tools&tool=' . $tool['id']);
        ?>
        <div class="toolzoo-tool-card">
            <div class="toolzoo-tool-header">
                <span class="dashicons <?php echo esc_attr($tool['icon']); ?>"></span>
                <h2><?php echo esc_html($tool['name']); ?></h2>
            </div>

            <div class="toolzoo-tool-body">
                <div class="toolzoo-tool-section">
                    <h3>説明</h3>
                    <p><?php echo esc_html($tool['description']); ?></p>
                </div>

                <div class="toolzoo-tool-section">
                    <h3>ショートコード</h3>
                    <div class="toolzoo-shortcode-wrapper">
                        <code class="toolzoo-shortcode"><?php echo esc_html($tool['shortcode']); ?></code>
                        <button
                            class="button button-secondary toolzoo-copy-btn"
                            data-shortcode="<?php echo esc_attr($tool['shortcode']); ?>">
                            コピー
                        </button>
                    </div>
                </div>

                <div class="toolzoo-tool-section">
                    <h3>動作確認</h3>
                    <a href="<?php echo esc_url($admin_url); ?>" class="button button-primary">
                        管理画面で確認
                    </a>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * 個別ツールの動作確認表示
     *
     * @param string $tool_id ツールID
     */
    private function render_tool_preview($tool_id) {
        $tools = $this->get_tools_list();
        $tool = null;

        foreach ($tools as $t) {
            if ($t['id'] === $tool_id) {
                $tool = $t;
                break;
            }
        }

        if (!$tool) {
            echo '<div class="notice notice-error"><p>ツールが見つかりません。</p></div>';
            return;
        }

        echo '<div class="toolzoo-admin-tool-preview">';
        echo '<h2>' . esc_html($tool['name']) . '</h2>';
        echo '<p><a href="' . esc_url(admin_url('admin.php?page=toolzoo-tools')) . '" class="button">&laquo; 一覧に戻る</a></p>';
        echo '<hr style="margin: 20px 0;">';

        // ツールを実際に表示
        if (class_exists($tool['class'])) {
            $instance = new $tool['class']();
            echo $instance->render();
        } else {
            echo '<p>ツールクラスが見つかりません: ' . esc_html($tool['class']) . '</p>';
        }

        echo '</div>';
    }

    /**
     * ツールリストの取得
     *
     * @return array ツール情報の配列
     */
    private function get_tools_list() {
        return array(
            array(
                'id'          => 'password',
                'name'        => 'パスワード生成ツール',
                'description' => 'ランダムなパスワードを20個一度に生成できるツールです。文字数、使用する文字種別（数字、大文字、小文字、記号）をカスタマイズできます。生成されたパスワードは個別または一括でコピー可能です。',
                'shortcode'   => '[toolzoo_password]',
                'class'       => 'Toolzoo_Password_Generator',
                'icon'        => 'dashicons-lock',
            ),
            array(
                'id'          => 'nengo',
                'name'        => '年号一覧表示',
                'description' => '日本の元号（明治～令和）と西暦の対応表を表示します。年号の変換や確認に便利なツールです。検索機能により目的の年をすばやく見つけることができます。',
                'shortcode'   => '[toolzoo_nengo]',
                'class'       => 'Toolzoo_Nengo_List',
                'icon'        => 'dashicons-calendar-alt',
            ),
        );
    }

    /**
     * 管理画面用CSS/JSの読み込み
     *
     * @param string $hook 現在のページフック
     */
    public function enqueue_admin_assets($hook) {
        // ToolZooページでのみ読み込み
        if ($hook !== 'toplevel_page_toolzoo-tools') {
            return;
        }

        // CSS
        wp_enqueue_style(
            'toolzoo-admin',
            TOOLZOO_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            TOOLZOO_VERSION
        );

        // JavaScript
        wp_enqueue_script(
            'toolzoo-admin',
            TOOLZOO_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            TOOLZOO_VERSION,
            true
        );

        // 個別ツールの表示時は、そのツールのアセットも読み込む
        $tool_id = isset($_GET['tool']) ? sanitize_text_field($_GET['tool']) : '';
        if ($tool_id) {
            $this->enqueue_tool_assets($tool_id);
        }
    }

    /**
     * 個別ツールのアセットを読み込み
     *
     * @param string $tool_id ツールID
     */
    private function enqueue_tool_assets($tool_id) {
        // パスワード生成ツール
        if ($tool_id === 'password') {
            wp_enqueue_style(
                'toolzoo-password',
                TOOLZOO_PLUGIN_URL . 'assets/css/password.css',
                array(),
                TOOLZOO_VERSION
            );
            wp_enqueue_script(
                'toolzoo-password',
                TOOLZOO_PLUGIN_URL . 'assets/js/password.js',
                array(),
                TOOLZOO_VERSION,
                true
            );
        }

        // 年号一覧ツール
        if ($tool_id === 'nengo') {
            wp_enqueue_style(
                'toolzoo-nengo',
                TOOLZOO_PLUGIN_URL . 'assets/css/nengo.css',
                array(),
                TOOLZOO_VERSION
            );
            wp_enqueue_script(
                'toolzoo-nengo',
                TOOLZOO_PLUGIN_URL . 'assets/js/nengo.js',
                array(),
                TOOLZOO_VERSION,
                true
            );
        }
    }
}
