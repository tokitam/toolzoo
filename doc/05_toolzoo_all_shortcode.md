# toolzoo_all ショートコード 詳細設計書

## 1. 機能概要

### 1.1 ショートコード名
`[toolzoo_all]`

### 1.2 目的
ユーザー側画面（フロントエンド）で、ToolZooプラグインの全ツールをカード形式で一覧表示し、各ツールへのリンクを提供する。訪問者はカードをクリックすることで、個別のツールページに遷移してツールを利用できる。

### 1.3 利用シーン
- ツール集トップページの作成
- ユーティリティページでのツール一覧
- サイトマップやナビゲーションページ
- ランディングページでのツール紹介

### 1.4 主な特徴
- **カード形式の一覧表示**: 見やすいカードレイアウト
- **リンクによるページ遷移**: 各ツールの個別ページへ移動
- **レスポンシブデザイン**: モバイル・タブレット対応
- **アクセシビリティ**: キーボード操作・スクリーンリーダー対応
- **シンプルな構造**: JavaScriptに依存しないHTML中心の設計

## 2. UI/UX設計

### 2.1 レイアウト構成

```
┌─────────────────────────────────────────────────────────────┐
│                     ツール一覧                                │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────────────┐  ┌─────────────────────────┐ │
│  │ 🔒 パスワード生成ツール    │  │ 📅 年号一覧表示           │ │
│  ├─────────────────────────┤  ├─────────────────────────┤ │
│  │                         │  │                         │ │
│  │ ランダムなパスワードを    │  │ 日本の元号と西暦の       │ │
│  │ 20個一度に生成できる      │  │ 対応表を表示します。     │ │
│  │ ツールです。              │  │                         │ │
│  │                         │  │                         │ │
│  │ [ツールを使う →]          │  │ [ツールを使う →]          │ │
│  └─────────────────────────┘  └─────────────────────────┘ │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 カード構成

各カードには以下の要素を含む：

```
┌─────────────────────────────────┐
│ 🔒 パスワード生成ツール            │  ← アイコン + タイトル
├─────────────────────────────────┤
│                                 │
│ ランダムなパスワードを20個一度に │  ← 説明文
│ 生成できるツールです。文字数、   │
│ 使用する文字種別をカスタマイズ   │
│ できます。                       │
│                                 │
│ [ツールを使う →]                  │  ← リンクボタン
└─────────────────────────────────┘
```

### 2.3 インタラクション

#### 2.3.1 カードクリック
- **操作**: カード全体またはボタンをクリック
- **動作**: 個別ツールページへ遷移
- **URL形式**:
  - 固定ページ: `/tools/password/`（パーマリンク設定による）
  - クエリパラメータ: `/?tool=password`（フォールバック）

#### 2.3.2 ホバー効果
- カード全体が微妙に浮き上がる
- ボタンの色が変化
- カーソルがポインターに変化

### 2.4 レスポンシブデザイン

#### 2.4.1 デスクトップ (1024px以上)
- 2カラムグリッド
- カード幅: 最大600px

#### 2.4.2 タブレット (768px - 1023px)
- 2カラムグリッド
- カード幅: 可変

#### 2.4.3 スマートフォン (767px以下)
- 1カラム
- カード幅: 100%
- ボタンを全幅表示

## 3. ページ構成

### 3.1 ページ構造

ToolZooプラグインは以下のページ構造を持つ：

```
/toolzoo/                  ← ツール一覧ページ（[toolzoo_all]を配置）
├── /toolzoo/password/     ← パスワード生成ツール（[toolzoo_password]を配置）
└── /toolzoo/nengo/        ← 年号一覧ツール（[toolzoo_nengo]を配置）
```

### 3.2 URL設計

#### 3.2.1 オプション1: 固定ページを使用（推奨）
ユーザーが手動で以下の固定ページを作成：

| ページタイトル | スラッグ | ショートコード | URL |
|--------------|---------|--------------|-----|
| ツール一覧 | `toolzoo` | `[toolzoo_all]` | `/toolzoo/` |
| パスワード生成 | `password` | `[toolzoo_password]` | `/toolzoo/password/` |
| 年号一覧 | `nengo` | `[toolzoo_nengo]` | `/toolzoo/nengo/` |

**メリット:**
- WordPressの標準機能を使用
- SEOに有利
- 柔軟なカスタマイズが可能
- パーマリンク設定を尊重

#### 3.2.2 オプション2: カスタムリライトルール（自動生成）
プラグインが自動的にページを生成：

```php
// リライトルールの追加
add_action('init', function() {
    add_rewrite_rule('^toolzoo/?$', 'index.php?toolzoo_page=list', 'top');
    add_rewrite_rule('^toolzoo/([^/]+)/?$', 'index.php?toolzoo_page=$matches[1]', 'top');
});
```

**メリット:**
- 自動セットアップ
- ユーザーの手間が不要

**デメリット:**
- 他のプラグインとの競合可能性
- より複雑な実装

**推奨**: オプション1（固定ページ使用）をメインとし、設定画面で自動セットアップ機能を提供

### 3.3 リンク生成ロジック

#### 3.3.1 リンク先の決定方法

```php
/**
 * ツールページのURLを取得
 *
 * @param string $tool_id ツールID
 * @return string URL
 */
private function get_tool_url($tool_id) {
    // 設定からベースURLを取得（将来の拡張用）
    $base_url = get_option('toolzoo_base_url', '');

    if ($base_url) {
        // 設定されたベースURLを使用
        return trailingslashit($base_url) . $tool_id . '/';
    }

    // デフォルト: スラッグでページを検索
    $slug_map = array(
        'password' => 'password',
        'nengo'    => 'nengo',
    );

    if (isset($slug_map[$tool_id])) {
        $page = get_page_by_path('toolzoo/' . $slug_map[$tool_id]);
        if ($page) {
            return get_permalink($page->ID);
        }
    }

    // フォールバック: クエリパラメータ方式
    return add_query_arg('tool', $tool_id, home_url('/toolzoo/'));
}
```

## 4. 技術仕様

### 4.1 PHPクラス設計

#### 4.1.1 クラス名
`Toolzoo_All_Shortcode`

#### 4.1.2 ファイルパス
`includes/class-all-shortcode.php`（新規作成）

#### 4.1.3 主要メソッド

```php
<?php
/**
 * toolzoo_all ショートコード クラス
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_All_Shortcode クラス
 */
class Toolzoo_All_Shortcode {
    /**
     * ショートコード出力
     *
     * @param array $atts ショートコード属性
     * @return string HTML出力
     */
    public function render($atts) {
        // アセットの読み込み
        $this->enqueue_assets();

        // ツールリストの取得
        $tools = $this->get_tools_list();

        // HTML生成
        return $this->generate_html($tools);
    }

    /**
     * ツールリスト取得
     *
     * @return array ツール情報の配列
     */
    private function get_tools_list() {
        return array(
            array(
                'id'          => 'password',
                'name'        => __('Password Generator', 'toolzoo'),
                'description' => __('A tool that generates 20 random passwords at once. You can customize the length and character types (numbers, uppercase, lowercase, symbols). Generated passwords can be copied individually or all at once.', 'toolzoo'),
                'icon'        => '🔒',
                'slug'        => 'password',
            ),
            array(
                'id'          => 'nengo',
                'name'        => __('Japanese Era List', 'toolzoo'),
                'description' => __('Displays a correspondence table between Japanese era names (Meiji to Reiwa) and Western calendar years. A convenient tool for converting and checking era years. The search function allows you to quickly find the desired year.', 'toolzoo'),
                'icon'        => '📅',
                'slug'        => 'nengo',
            ),
        );
    }

    /**
     * ツールページのURLを取得
     *
     * @param string $tool_id ツールID
     * @return string URL
     */
    private function get_tool_url($tool_id) {
        // ツールページを探す（親ページ: toolzoo）
        $parent_page = get_page_by_path('toolzoo');

        if ($parent_page) {
            // 子ページを探す
            $tool_page = get_page_by_path('toolzoo/' . $tool_id);
            if ($tool_page) {
                return get_permalink($tool_page->ID);
            }
        }

        // フォールバック: 現在のページにクエリパラメータを追加
        return add_query_arg('tool', $tool_id, get_permalink());
    }

    /**
     * HTML生成
     *
     * @param array $tools ツールリスト
     * @return string HTML
     */
    private function generate_html($tools) {
        ob_start();
        ?>
        <div class="toolzoo-all-container">
            <div class="toolzoo-all-grid">
                <?php foreach ($tools as $tool) : ?>
                    <?php $this->render_tool_card($tool); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * ツールカード表示
     *
     * @param array $tool ツール情報
     */
    private function render_tool_card($tool) {
        $tool_url = $this->get_tool_url($tool['id']);
        ?>
        <div class="toolzoo-all-card">
            <a href="<?php echo esc_url($tool_url); ?>" class="toolzoo-all-card-link">
                <div class="toolzoo-all-card-header">
                    <span class="toolzoo-all-icon" aria-hidden="true"><?php echo esc_html($tool['icon']); ?></span>
                    <h3 class="toolzoo-all-title"><?php echo esc_html($tool['name']); ?></h3>
                </div>

                <div class="toolzoo-all-card-body">
                    <p class="toolzoo-all-description">
                        <?php echo esc_html($tool['description']); ?>
                    </p>

                    <span class="toolzoo-all-btn">
                        <?php esc_html_e('Use Tool', 'toolzoo'); ?>
                        <span class="toolzoo-all-arrow" aria-hidden="true">→</span>
                    </span>
                </div>
            </a>
        </div>
        <?php
    }

    /**
     * アセット読み込み
     */
    private function enqueue_assets() {
        // CSS
        wp_enqueue_style(
            'toolzoo-all-css',
            TOOLZOO_PLUGIN_URL . 'assets/css/all.css',
            array(),
            TOOLZOO_VERSION
        );
    }
}
```

### 4.2 ショートコード登録

`includes/class-toolzoo.php` に追加：

```php
/**
 * ショートコードの登録
 */
private function register_shortcodes() {
    add_shortcode('toolzoo_password', array($this, 'password_shortcode'));
    add_shortcode('toolzoo_nengo', array($this, 'nengo_shortcode'));
    add_shortcode('toolzoo_all', array($this, 'all_shortcode')); // 追加
}

/**
 * 全ツール一覧ショートコード
 *
 * @param array $atts ショートコード属性
 * @return string HTML出力
 */
public function all_shortcode($atts) {
    $shortcode = new Toolzoo_All_Shortcode();
    return $shortcode->render($atts);
}
```

### 4.3 クラス読み込み

`includes/class-toolzoo.php` の `load_classes()` メソッドに追加：

```php
private function load_classes() {
    require_once TOOLZOO_PLUGIN_DIR . 'includes/class-password-generator.php';
    require_once TOOLZOO_PLUGIN_DIR . 'includes/class-nengo-list.php';
    require_once TOOLZOO_PLUGIN_DIR . 'includes/class-all-shortcode.php'; // 追加
}
```

## 5. フロントエンド設計

### 5.1 CSS設計

#### 5.1.1 ファイル名
`assets/css/all.css`（新規作成）

#### 5.1.2 主要スタイル

```css
/**
 * toolzoo_all ショートコード用スタイル
 */

/* ===================================
   コンテナ
   =================================== */
.toolzoo-all-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px 0;
}

/* ===================================
   グリッドレイアウト
   =================================== */
.toolzoo-all-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    margin: 0;
}

/* ===================================
   ツールカード
   =================================== */
.toolzoo-all-card {
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.toolzoo-all-card:hover {
    border-color: #667eea;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
    transform: translateY(-4px);
}

.toolzoo-all-card:focus-within {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

/* ===================================
   カードリンク
   =================================== */
.toolzoo-all-card-link {
    display: block;
    text-decoration: none;
    color: inherit;
    height: 100%;
}

.toolzoo-all-card-link:hover,
.toolzoo-all-card-link:focus {
    outline: none;
}

/* ===================================
   カードヘッダー
   =================================== */
.toolzoo-all-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 24px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.toolzoo-all-icon {
    font-size: 32px;
    flex-shrink: 0;
    line-height: 1;
}

.toolzoo-all-title {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #fff;
    line-height: 1.3;
}

/* ===================================
   カードボディ
   =================================== */
.toolzoo-all-card-body {
    padding: 24px 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.toolzoo-all-description {
    margin: 0;
    font-size: 14px;
    line-height: 1.6;
    color: #555;
    flex: 1;
}

/* ===================================
   ボタン
   =================================== */
.toolzoo-all-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    background: #667eea;
    color: #fff;
    border-radius: 6px;
    font-size: 15px;
    font-weight: 500;
    transition: background-color 0.2s ease, transform 0.1s ease;
    text-align: center;
}

.toolzoo-all-card:hover .toolzoo-all-btn {
    background: #5568d3;
    transform: translateX(4px);
}

.toolzoo-all-arrow {
    font-size: 16px;
    transition: transform 0.2s ease;
}

.toolzoo-all-card:hover .toolzoo-all-arrow {
    transform: translateX(4px);
}

/* ===================================
   レスポンシブ対応
   =================================== */

/* タブレット */
@media screen and (max-width: 768px) {
    .toolzoo-all-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .toolzoo-all-card-header {
        padding: 20px 16px;
    }

    .toolzoo-all-icon {
        font-size: 28px;
    }

    .toolzoo-all-title {
        font-size: 18px;
    }

    .toolzoo-all-card-body {
        padding: 20px 16px;
    }
}

/* スマートフォン */
@media screen and (max-width: 480px) {
    .toolzoo-all-container {
        padding: 16px 0;
    }

    .toolzoo-all-grid {
        gap: 16px;
    }

    .toolzoo-all-card-header {
        padding: 16px;
    }

    .toolzoo-all-icon {
        font-size: 24px;
    }

    .toolzoo-all-title {
        font-size: 16px;
    }

    .toolzoo-all-card-body {
        padding: 16px;
        gap: 16px;
    }

    .toolzoo-all-description {
        font-size: 13px;
    }

    .toolzoo-all-btn {
        padding: 10px 20px;
        font-size: 14px;
    }
}

/* ===================================
   アクセシビリティ
   =================================== */

/* ハイコントラストモード */
@media (prefers-contrast: high) {
    .toolzoo-all-card {
        border: 3px solid #000;
    }

    .toolzoo-all-card-header {
        background: #000;
        color: #fff;
    }

    .toolzoo-all-btn {
        background: #000;
        color: #fff;
    }
}

/* リデュースモーション */
@media (prefers-reduced-motion: reduce) {
    .toolzoo-all-card,
    .toolzoo-all-btn,
    .toolzoo-all-arrow {
        transition: none;
    }

    .toolzoo-all-card:hover {
        transform: none;
    }

    .toolzoo-all-card:hover .toolzoo-all-btn,
    .toolzoo-all-card:hover .toolzoo-all-arrow {
        transform: none;
    }
}

/* フォーカス表示の強化 */
.toolzoo-all-card-link:focus-visible {
    outline: 3px solid #667eea;
    outline-offset: 2px;
}

/* ===================================
   印刷時のスタイル
   =================================== */
@media print {
    .toolzoo-all-card {
        break-inside: avoid;
        page-break-inside: avoid;
        box-shadow: none;
        border: 2px solid #000;
    }

    .toolzoo-all-card-header {
        background: #000 !important;
        color: #fff !important;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    .toolzoo-all-btn {
        display: none;
    }
}
```

## 6. セットアップガイド

### 6.1 固定ページの作成方法

プラグインをインストール後、以下の固定ページを作成：

#### 6.1.1 親ページ: ツール一覧

| 項目 | 値 |
|------|-----|
| **タイトル** | ToolZoo |
| **スラッグ** | `toolzoo` |
| **本文** | `[toolzoo_all]` |
| **公開状態** | 公開 |

#### 6.1.2 子ページ1: パスワード生成

| 項目 | 値 |
|------|-----|
| **タイトル** | パスワード生成 |
| **スラッグ** | `password` |
| **親ページ** | ToolZoo |
| **本文** | `[toolzoo_password]` |
| **公開状態** | 公開 |

#### 6.1.3 子ページ2: 年号一覧

| 項目 | 値 |
|------|-----|
| **タイトル** | 年号一覧 |
| **スラッグ** | `nengo` |
| **親ページ** | ToolZoo |
| **本文** | `[toolzoo_nengo]` |
| **公開状態** | 公開 |

### 6.2 自動セットアップ機能（オプション）

将来的に管理画面から自動でページを作成する機能を追加可能：

```php
/**
 * ツールページの自動作成
 */
public function create_tool_pages() {
    // 親ページ: ツール一覧
    $parent_page = array(
        'post_title'   => __('ToolZoo', 'toolzoo'),
        'post_content' => '[toolzoo_all]',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_name'    => 'toolzoo',
    );
    $parent_id = wp_insert_post($parent_page);

    // 子ページ: パスワード生成
    $password_page = array(
        'post_title'   => __('Password Generator', 'toolzoo'),
        'post_content' => '[toolzoo_password]',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_name'    => 'password',
        'post_parent'  => $parent_id,
    );
    wp_insert_post($password_page);

    // 子ページ: 年号一覧
    $nengo_page = array(
        'post_title'   => __('Japanese Era List', 'toolzoo'),
        'post_content' => '[toolzoo_nengo]',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_name'    => 'nengo',
        'post_parent'  => $parent_id,
    );
    wp_insert_post($nengo_page);
}
```

## 7. セキュリティ考慮事項

### 7.1 出力のエスケープ
- すべてのテキスト出力: `esc_html()`
- すべての属性値: `esc_attr()`
- すべてのURL: `esc_url()`

### 7.2 入力のサニタイゼーション
- ショートコード属性: `sanitize_text_field()`

### 7.3 XSS対策
- ユーザー入力を直接出力しない
- 翻訳関数を通して出力

## 8. アクセシビリティ

### 8.1 セマンティックHTML
- `<a>` タグによる適切なリンク構造
- `<h3>` による見出し階層
- `aria-hidden` で装飾的要素をマーク

### 8.2 キーボード操作
- **Tab**: カード間のフォーカス移動
- **Enter**: ツールページへ遷移
- すべてのカードがキーボードでアクセス可能

### 8.3 スクリーンリーダー対応
- 適切な見出しレベル
- リンクに明確なラベル
- ARIA属性の適切な使用

### 8.4 視覚的フィードバック
- フォーカス時の明確なアウトライン
- ホバー時の視覚的変化
- 十分なカラーコントラスト

## 9. パフォーマンス最適化

### 9.1 軽量設計
- JavaScriptを使用しない（CSS中心）
- 最小限のDOM要素
- シンプルなCSS

### 9.2 アセット最適化
- CSSのミニファイ
- 重複読み込みの防止

## 10. 実装手順

### Phase 1: 基本構造の作成
1. `includes/class-all-shortcode.php` ファイル作成
2. 基本的なクラス構造とメソッド実装
3. ショートコード登録

### Phase 2: HTML/CSS実装
1. `assets/css/all.css` ファイル作成
2. カードレイアウトのスタイリング
3. レスポンシブ対応

### Phase 3: リンク機能実装
1. URL取得ロジックの実装
2. ページ検索機能
3. フォールバック処理

### Phase 4: テストと調整
1. 各ブラウザでの動作確認
2. レスポンシブ表示の確認
3. アクセシビリティチェック
4. リンク動作の確認

## 11. テスト項目

### 11.1 機能テスト
- [ ] ショートコード `[toolzoo_all]` が正しく動作する
- [ ] すべてのツールカードが表示される
- [ ] 各カードのリンクが正しく機能する
- [ ] ツールページへ正しく遷移する
- [ ] アセット（CSS）が正しく読み込まれる

### 11.2 表示テスト
- [ ] デスクトップ（1920px, 1440px, 1024px）
- [ ] タブレット（768px, 834px）
- [ ] スマートフォン（375px, 414px）
- [ ] カードレイアウトが崩れない
- [ ] アイコンと文字が正しく表示される

### 11.3 ブラウザ互換性
- [ ] Chrome（最新版）
- [ ] Firefox（最新版）
- [ ] Safari（最新版）
- [ ] Edge（最新版）
- [ ] モバイルブラウザ（iOS Safari, Chrome Mobile）

### 11.4 アクセシビリティテスト
- [ ] キーボードのみで操作可能
- [ ] スクリーンリーダーで読み上げ可能
- [ ] フォーカスインジケーターが表示される
- [ ] カラーコントラストが適切

### 11.5 リンクテスト
- [ ] 固定ページが存在する場合、正しくリンクされる
- [ ] 固定ページが存在しない場合、フォールバックが機能する
- [ ] パーマリンク設定に関わらず動作する

## 12. 将来の拡張案

### 12.1 機能追加
- **カテゴリー分類**: ツールが増えた際のカテゴリー別表示
- **検索機能**: ツール検索
- **並び替え**: 名前順、人気順など
- **お気に入り機能**: LocalStorageでお気に入りツールを保存

### 12.2 UI改善
- **テーマカスタマイズ**: カラースキームの変更
- **アイコンカスタマイズ**: Dashiconsや画像の使用
- **レイアウトオプション**: リスト表示/グリッド表示の切り替え

### 12.3 セットアップ改善
- **自動ページ作成**: 管理画面からワンクリックでページ作成
- **ページテンプレート**: カスタムテンプレートの提供
- **設定画面**: ベースURL、スラッグのカスタマイズ

## 13. 使用例

### 13.1 基本的な使用

固定ページ「ToolZoo」（スラッグ: `toolzoo`）に以下を追加：

```
[toolzoo_all]
```

### 13.2 カスタマイズ例

ページ本文に説明文を追加：

```
<h2>便利ツール集</h2>
<p>このページでは、便利なツールを提供しています。ぜひご活用ください。</p>

[toolzoo_all]

<p>各ツールの詳細は、カードをクリックしてご確認ください。</p>
```

## 14. 関連ドキュメント

- [01_overview.md](./01_overview.md) - プラグイン全体の概要
- [02_password.md](./02_password.md) - パスワード生成機能の詳細
- [03_nengo.md](./03_nengo.md) - 年号一覧機能の詳細
- [04_admin_page.md](./04_admin_page.md) - 管理画面ページの詳細

## 15. まとめ

`[toolzoo_all]` ショートコードは、ToolZooプラグインの全ツールをユーザー側で一覧表示し、各ツールページへのナビゲーションを提供します。

**主な特徴:**
- シンプルなカード形式の一覧表示
- リンクによる各ツールページへの遷移
- 完全なレスポンシブデザイン
- アクセシビリティ対応
- JavaScriptに依存しない軽量設計

**使用例:**
```
[toolzoo_all]
```

このショートコードにより、ユーザーは1つのページで利用可能なツールを確認し、必要なツールページへ簡単にアクセスできるようになります。
