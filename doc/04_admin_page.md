# 管理画面ツール一覧ページ 詳細設計

## 1. 機能概要

### 1.1 ページ名
ToolZoo ツール一覧 (ToolZoo Tools List)

### 1.2 目的
WordPress管理画面にToolZooプラグインの専用ページを追加し、利用可能なツールの一覧を表示する。各ツールの情報、ショートコード、管理画面での動作確認リンクを提供し、サイト管理者がツールを簡単に把握・利用できるようにする。

### 1.3 表示場所
WordPress管理画面 > ToolZoo

### 1.4 対象ユーザー
- サイト管理者
- 編集者（権限に応じて）

## 2. ページ設計

### 2.1 メニュー登録

#### 2.1.1 メニュー位置
- **親メニュー**: ToolZoo（トップレベルメニュー）
- **スラッグ**: `toolzoo-tools`
- **アイコン**: `dashicons-admin-tools` または `dashicons-hammer`
- **表示順**: 65（設定とコメントの間）

#### 2.1.2 必要な権限
- **capability**: `manage_options`
- 管理者のみアクセス可能

### 2.2 画面レイアウト

```
┌───────────────────────────────────────────────────────────────┐
│ ToolZoo - 便利ツール一覧                                       │
├───────────────────────────────────────────────────────────────┤
│                                                               │
│  このプラグインは便利なツール集を提供します。                   │
│  各ツールをショートコードで記事やページに埋め込むことができます。│
│                                                               │
├───────────────────────────────────────────────────────────────┤
│  【ツール一覧】                                                │
├───────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │ 1. パスワード生成ツール                                   │ │
│  ├─────────────────────────────────────────────────────────┤ │
│  │                                                           │ │
│  │ 【説明】                                                  │ │
│  │ ランダムなパスワードを20個一度に生成できるツールです。    │ │
│  │ 文字数、使用する文字種別（数字、大文字、小文字、記号）を  │ │
│  │ カスタマイズできます。生成されたパスワードは個別または    │ │
│  │ 一括でコピー可能です。                                    │ │
│  │                                                           │ │
│  │ 【ショートコード】                                        │ │
│  │ [toolzoo_password]                                        │ │
│  │ [コピー]                                                  │ │
│  │                                                           │ │
│  │ 【動作確認】                                              │ │
│  │ [管理画面で確認] [フロントエンドでプレビュー]             │ │
│  │                                                           │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │ 2. 年号一覧表示                                           │ │
│  ├─────────────────────────────────────────────────────────┤ │
│  │                                                           │ │
│  │ 【説明】                                                  │ │
│  │ 日本の元号（明治～令和）と西暦の対応表を表示します。      │ │
│  │ 年号の変換や確認に便利なツールです。検索機能により        │ │
│  │ 目的の年をすばやく見つけることができます。                │ │
│  │                                                           │ │
│  │ 【ショートコード】                                        │ │
│  │ [toolzoo_nengo]                                           │ │
│  │ [コピー]                                                  │ │
│  │                                                           │ │
│  │ 【動作確認】                                              │ │
│  │ [管理画面で確認] [フロントエンドでプレビュー]             │ │
│  │                                                           │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
└───────────────────────────────────────────────────────────────┘
```

### 2.3 UI要素詳細

#### 2.3.1 ページヘッダー
- **タイトル**: "ToolZoo - 便利ツール一覧"
- **説明文**: プラグインの概要と使い方の簡単な説明
- **クラス**: `wrap`（WordPress標準）

#### 2.3.2 ツールカード

各ツールは以下の情報を含むカードとして表示：

| 項目 | 内容 | 備考 |
|------|------|------|
| **ツール名** | ツールの日本語名 | `<h2>` タグ |
| **説明** | 機能の詳細説明 | 2-3文程度 |
| **ショートコード** | コピー可能なショートコード | コピーボタン付き |
| **管理画面で確認** | 管理画面内でツールを動作確認 | ボタンリンク |
| **フロントエンドでプレビュー** | 実際のページでの表示を確認 | ボタンリンク（オプション） |

#### 2.3.3 ショートコードコピー機能
- コピーボタンをクリックするとクリップボードにショートコードがコピーされる
- コピー成功時に視覚的フィードバック（「コピーしました」メッセージまたはボタンの色変更）

#### 2.3.4 管理画面で確認リンク
各ツールの「管理画面で確認」ボタンをクリックすると、管理画面内で実際のツールが動作するページに遷移。

- **パスワード生成**: `/wp-admin/admin.php?page=toolzoo-tools&tool=password`
- **年号一覧**: `/wp-admin/admin.php?page=toolzoo-tools&tool=nengo`

## 3. 機能仕様

### 3.1 ツールリスト定義

ツール情報を配列で管理：

```php
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
```

### 3.2 ページ表示モード

#### 3.2.1 一覧表示モード（デフォルト）
- クエリパラメータなし: `/wp-admin/admin.php?page=toolzoo-tools`
- 全ツールの一覧を表示

#### 3.2.2 個別ツール表示モード
- クエリパラメータあり: `/wp-admin/admin.php?page=toolzoo-tools&tool={tool_id}`
- 指定されたツールを管理画面内で実際に動作表示
- 例: `?tool=password` → パスワード生成ツールを表示

### 3.3 ショートコードコピー機能

```javascript
// ショートコードをクリップボードにコピー
function copyShortcode(shortcode, button) {
    navigator.clipboard.writeText(shortcode).then(function() {
        // 成功時の処理
        const originalText = button.textContent;
        button.textContent = 'コピーしました！';
        button.classList.add('copied');

        setTimeout(function() {
            button.textContent = originalText;
            button.classList.remove('copied');
        }, 2000);
    }).catch(function(err) {
        // フォールバック処理
        console.error('コピーに失敗しました:', err);
    });
}
```

### 3.4 個別ツール表示

個別ツール表示モードでは、対応するクラスの `render()` メソッドを呼び出して実際のツールを表示：

```php
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
    echo '<p><a href="' . admin_url('admin.php?page=toolzoo-tools') . '">&laquo; 一覧に戻る</a></p>';

    // ツールを実際に表示
    if (class_exists($tool['class'])) {
        $instance = new $tool['class']();
        echo $instance->render();
    } else {
        echo '<p>ツールクラスが見つかりません。</p>';
    }

    echo '</div>';
}
```

## 4. PHPクラス設計

### 4.1 クラス名
`Toolzoo_Admin`

### 4.2 ファイルパス
`includes/class-admin.php`（新規作成）

### 4.3 メソッド一覧

#### 4.3.1 __construct()
- **説明**: コンストラクタ
- **処理**: フックの登録

```php
public function __construct() {
    add_action('admin_menu', array($this, 'add_admin_menu'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
}
```

#### 4.3.2 add_admin_menu()
- **説明**: 管理画面にメニューを追加
- **処理**: `add_menu_page()` でトップレベルメニューを追加

```php
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
```

#### 4.3.3 render_admin_page()
- **説明**: 管理画面ページの表示
- **処理**: 一覧表示または個別ツール表示を判定して出力

```php
public function render_admin_page() {
    // 権限チェック
    if (!current_user_can('manage_options')) {
        wp_die(__('このページにアクセスする権限がありません。'));
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
```

#### 4.3.4 render_tools_list()
- **説明**: ツール一覧の表示
- **戻り値**: なし（直接出力）
- **処理**: ツールカードを生成して表示

```php
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
```

#### 4.3.5 render_tool_card($tool)
- **説明**: 個別ツールカードの表示
- **引数**: `$tool` (array) - ツール情報
- **戻り値**: なし（直接出力）

```php
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
```

#### 4.3.6 render_tool_preview($tool_id)
- **説明**: 個別ツールの動作確認表示
- **引数**: `$tool_id` (string) - ツールID
- **戻り値**: なし（直接出力）

#### 4.3.7 get_tools_list()
- **説明**: ツールリストの取得
- **戻り値**: array - ツール情報の配列

#### 4.3.8 enqueue_admin_assets($hook)
- **説明**: 管理画面用CSS/JSの読み込み
- **引数**: `$hook` (string) - 現在のページフック
- **処理**: ToolZooページでのみアセットを読み込み

```php
public function enqueue_admin_assets($hook) {
    // ToolZooページでのみ読み込み
    if ($hook !== 'toplevel_page_toolzoo-tools') {
        return;
    }

    wp_enqueue_style(
        'toolzoo-admin',
        TOOLZOO_PLUGIN_URL . 'assets/css/admin.css',
        array(),
        TOOLZOO_VERSION
    );

    wp_enqueue_script(
        'toolzoo-admin',
        TOOLZOO_PLUGIN_URL . 'assets/js/admin.js',
        array('jquery'),
        TOOLZOO_VERSION,
        true
    );
}
```

## 5. CSS設計

### 5.1 ファイル名
`assets/css/admin.css`（新規作成）

### 5.2 主要クラス

```css
/* ページ全体 */
.toolzoo-admin-page {
    max-width: 1200px;
}

/* イントロ */
.toolzoo-admin-intro {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-left: 4px solid #2271b1;
    padding: 15px 20px;
    margin: 20px 0;
}

.toolzoo-admin-intro p {
    margin: 0;
    font-size: 14px;
}

/* ツールグリッド */
.toolzoo-tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

/* ツールカード */
.toolzoo-tool-card {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
    transition: box-shadow 0.2s;
}

.toolzoo-tool-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* カードヘッダー */
.toolzoo-tool-header {
    background: #f0f0f1;
    padding: 15px 20px;
    border-bottom: 1px solid #c3c4c7;
    display: flex;
    align-items: center;
    gap: 10px;
}

.toolzoo-tool-header .dashicons {
    font-size: 24px;
    width: 24px;
    height: 24px;
    color: #2271b1;
}

.toolzoo-tool-header h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

/* カードボディ */
.toolzoo-tool-body {
    padding: 20px;
}

.toolzoo-tool-section {
    margin-bottom: 20px;
}

.toolzoo-tool-section:last-child {
    margin-bottom: 0;
}

.toolzoo-tool-section h3 {
    margin: 0 0 10px 0;
    font-size: 14px;
    font-weight: 600;
    color: #1d2327;
}

.toolzoo-tool-section p {
    margin: 0;
    font-size: 13px;
    line-height: 1.6;
    color: #3c434a;
}

/* ショートコード表示 */
.toolzoo-shortcode-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f6f7f7;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #dcdcde;
}

.toolzoo-shortcode {
    flex: 1;
    font-family: 'Courier New', Courier, monospace;
    font-size: 13px;
    color: #d63638;
    background: transparent;
    padding: 0;
}

.toolzoo-copy-btn {
    flex-shrink: 0;
    padding: 4px 12px;
    font-size: 12px;
    height: auto;
    line-height: 1.4;
}

.toolzoo-copy-btn.copied {
    background: #00a32a;
    border-color: #00a32a;
    color: #fff;
}

/* 個別ツールプレビュー */
.toolzoo-admin-tool-preview {
    background: #fff;
    padding: 20px;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    margin-top: 20px;
}

.toolzoo-admin-tool-preview h2 {
    margin-top: 0;
}

/* レスポンシブ対応 */
@media screen and (max-width: 782px) {
    .toolzoo-tools-grid {
        grid-template-columns: 1fr;
    }

    .toolzoo-shortcode-wrapper {
        flex-direction: column;
        align-items: stretch;
    }

    .toolzoo-copy-btn {
        width: 100%;
    }
}
```

## 6. JavaScript設計

### 6.1 ファイル名
`assets/js/admin.js`（新規作成）

### 6.2 主要機能

```javascript
(function($) {
    'use strict';

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
     */
    function copyToClipboard(text, button) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            // モダンブラウザ
            navigator.clipboard.writeText(text)
                .then(function() {
                    showCopySuccess(button);
                })
                .catch(function(err) {
                    console.error('コピーに失敗しました:', err);
                    fallbackCopy(text, button);
                });
        } else {
            // フォールバック
            fallbackCopy(text, button);
        }
    }

    /**
     * フォールバックコピー処理
     */
    function fallbackCopy(text, button) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();

        try {
            const success = document.execCommand('copy');
            if (success) {
                showCopySuccess(button);
            }
        } catch (err) {
            console.error('フォールバックコピーに失敗しました:', err);
        } finally {
            document.body.removeChild(textarea);
        }
    }

    /**
     * コピー成功の視覚的フィードバック
     */
    function showCopySuccess(button) {
        const originalText = button.text();
        button.text('コピーしました！');
        button.addClass('copied');

        setTimeout(function() {
            button.text(originalText);
            button.removeClass('copied');
        }, 2000);
    }

})(jQuery);
```

## 7. 実装手順

### 7.1 Phase 1: 基本構造の作成
1. `includes/class-admin.php` の作成
2. `Toolzoo` メインクラスに管理画面クラスの読み込みを追加
3. メニュー登録とページ表示の基本実装

### 7.2 Phase 2: ツール一覧の実装
1. ツールリスト定義
2. ツールカード表示機能
3. レイアウト調整

### 7.3 Phase 3: インタラクション実装
1. ショートコードコピー機能
2. CSS/JSアセットの作成
3. 視覚的フィードバックの実装

### 7.4 Phase 4: 個別ツール表示
1. 個別ツールプレビューページの実装
2. ツールクラスのインスタンス化と表示
3. 一覧に戻るリンクの追加

### 7.5 Phase 5: テストと調整
1. 各機能の動作確認
2. レスポンシブ対応の確認
3. アクセシビリティチェック

## 8. メインクラスへの統合

`includes/class-toolzoo.php` に以下を追加：

```php
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
 * 初期化（修正版）
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
```

## 9. セキュリティ考慮事項

### 9.1 権限チェック
- 管理画面アクセスには `manage_options` 権限が必要
- ページ表示前に `current_user_can()` で権限確認

### 9.2 入力サニタイゼーション
- `$_GET['tool']` パラメータは `sanitize_text_field()` で処理
- すべての出力に適切なエスケープ関数を使用
  - `esc_html()`: テキスト出力
  - `esc_attr()`: 属性値
  - `esc_url()`: URL

### 9.3 Nonce検証
- 現時点では表示のみのため不要
- 将来的に設定保存機能を追加する場合は nonce を実装

## 10. アクセシビリティ

### 10.1 キーボード操作
- すべてのボタンとリンクがキーボードでアクセス可能
- Tabキーでフォーカス移動
- Enterキーで実行

### 10.2 スクリーンリーダー対応
- 適切な見出し階層（h1 > h2 > h3）
- ボタンとリンクに明確なラベル
- ARIA属性の追加を検討

### 10.3 視覚的フィードバック
- フォーカス時のアウトライン表示
- ホバー時の視覚的変化
- コピー成功時の明確なフィードバック

## 11. テスト項目

### 11.1 機能テスト
- [ ] 管理メニューにToolZooが表示される
- [ ] ツール一覧ページが正しく表示される
- [ ] 各ツールカードが正しく表示される
- [ ] ショートコードコピーボタンが動作する
- [ ] コピー成功の視覚的フィードバックが表示される
- [ ] 「管理画面で確認」リンクが動作する
- [ ] 個別ツールページでツールが実際に動作する
- [ ] 「一覧に戻る」リンクが動作する
- [ ] 権限のないユーザーはアクセスできない

### 11.2 表示テスト
- [ ] デスクトップでレイアウトが正しい
- [ ] タブレットでレイアウトが正しい
- [ ] スマートフォンでレイアウトが正しい
- [ ] WordPress標準テーマでの表示確認
- [ ] 人気のある他のプラグインとの競合がない

### 11.3 ブラウザ互換性
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge

### 11.4 WordPress互換性
- [ ] WordPress 5.0+
- [ ] WordPress 6.0+
- [ ] Gutenbergエディタとの共存
- [ ] クラシックエディタとの共存

## 12. 将来の拡張案

### 12.1 機能追加
- **設定ページ**: ツールごとの設定や有効/無効切り替え
- **使用統計**: 各ツールの利用状況の表示
- **お気に入り機能**: よく使うツールをお気に入り登録
- **カテゴリー分類**: ツールが増えた際のカテゴリー分け
- **検索機能**: ツール検索
- **並び替え**: ドラッグ&ドロップでツールの表示順変更

### 12.2 UI改善
- **ダークモード**: WordPress のカラースキームに対応
- **グリッドビュー/リストビュー切り替え**
- **アニメーション**: カード表示時のフェードイン効果
- **ツールプレビュー**: カード上でホバーすると簡易プレビュー表示

### 12.3 新規ツール追加時の対応
- `get_tools_list()` メソッドに配列要素を追加するだけで自動的に一覧に表示される
- スケーラブルな設計

## 13. 関連ドキュメント

- [01_overview.md](./01_overview.md) - プラグイン全体の概要
- [02_password.md](./02_password.md) - パスワード生成機能の詳細
- [03_nengo.md](./03_nengo.md) - 年号一覧機能の詳細
