# ToolZoo Links ショートコード 詳細設計書

## 1. 機能概要

### 1.1 ショートコード名
`[toolzoo_links]`

### 1.2 目的
ToolZooプラグインのすべてのツールへのリンクを、横並びで表示するナビゲーション用ショートコード。訪問者は一目でどのようなツールが利用できるかを確認でき、各ツールへ素早くアクセスできる。

### 1.3 利用シーン
- ツール集トップページのナビゲーション
- サイドバーのウィジェット
- ページのヘッダーやフッター
- ツール一覧ページの補足リンク集
- ブログ記事のツール紹介セクション

### 1.4 主な特徴
- **水平レイアウト**: ツールへのリンクを横並びで表示
- **コンパクト設計**: スペースを最小限に使用
- **アイコン付き**: 絵文字またはダッシュアイコンで視覚的に識別
- **レスポンシブ**: モバイルでは自動的に折り返し
- **シンプル実装**: JavaScriptに依存しない設計
- **カスタマイズ可能**: 属性で表示スタイルを変更

## 2. UI/UX設計

### 2.1 デフォルト表示

```
┌────────────────────────────────────────────────────────┐
│  🔒 パスワード生成  │  📅 年号一覧  │  🌍 世界時計  │  {} JSON処理  │
└────────────────────────────────────────────────────────┘
```

### 2.2 レイアウトバリエーション

#### 2.2.1 アイコン + テキスト（デフォルト）
```
┌────────────────────────────────────────────────────────┐
│  🔒 パスワード生成ツール  │  📅 年号一覧表  │  🌍 世界時計  │  {} JSON処理  │
└────────────────────────────────────────────────────────┘
```

#### 2.2.2 アイコンのみ
```
┌────────────────────────────────────┐
│  🔒  │  📅  │  🌍  │  {}  │
└────────────────────────────────────┘
```

#### 2.2.3 テキストのみ
```
┌────────────────────────────────────────────────────────┐
│  パスワード生成  │  年号一覧  │  世界時計  │  JSON処理  │
└────────────────────────────────────────────────────────┘
```

### 2.3 ショートコード属性

```
[toolzoo_links style="icon-text" size="medium" separator="bullet"]
```

| 属性 | 値 | デフォルト | 説明 |
|------|-----|-----------|------|
| `style` | `icon-text` / `icon-only` / `text-only` | `icon-text` | 表示スタイル |
| `size` | `small` / `medium` / `large` | `medium` | リンクサイズ |
| `separator` | `dot` / `bullet` / `pipe` / `slash` | `pipe` | リンク区切り文字 |
| `columns` | `1-6` | 自動 | モバイル時のカラム数 |
| `class` | カスタムCSSクラス | (なし) | 追加CSSクラス |

### 2.4 デバイス別レイアウト

#### 2.4.1 デスクトップ (1024px以上)
- 1行で全ツール表示
- 区切り文字で分離

#### 2.4.2 タブレット (768px - 1023px)
- 2行で表示
- 区切り文字で分離

#### 2.4.3 スマートフォン (480px - 767px)
- 自動折り返し
- リスト形式またはスタック表示

#### 2.4.4 小型スマートフォン (< 480px)
- 1列表示
- 区切り文字なし（改行区切り）

### 2.5 インタラクション

#### 2.5.1 リンク動作
- **操作**: リンクをクリック
- **動作**: 対応するツールページへ遷移
- **URL**: 各ツールのページまたはクエリパラメータ

#### 2.5.2 ホバー効果
- リンクテキストの色変化
- 下線表示
- 背景色の微かな変化（オプション）

## 3. 技術仕様

### 3.1 HTMLマークアップ

#### 3.1.1 基本構造
```html
<div class="toolzoo-links toolzoo-links-icon-text toolzoo-links-medium">
    <a href="/tools/password/" class="toolzoo-link" title="パスワード生成ツール">
        <span class="toolzoo-link-icon">🔒</span>
        <span class="toolzoo-link-text">パスワード生成</span>
    </a>
    <span class="toolzoo-links-separator">|</span>
    <a href="/tools/nengo/" class="toolzoo-link" title="年号一覧表">
        <span class="toolzoo-link-icon">📅</span>
        <span class="toolzoo-link-text">年号一覧</span>
    </a>
    <!-- ... 他のツール ... -->
</div>
```

#### 3.1.2 クラス名
- `.toolzoo-links`: コンテナ
- `.toolzoo-links-[style]`: スタイル (icon-text, icon-only, text-only)
- `.toolzoo-links-[size]`: サイズ (small, medium, large)
- `.toolzoo-link`: 個別リンク
- `.toolzoo-link-icon`: アイコン要素
- `.toolzoo-link-text`: テキスト要素
- `.toolzoo-links-separator`: 区切り文字

### 3.2 PHPクラス設計

#### 3.2.1 クラス名
`Toolzoo_Links`

#### 3.2.2 ファイルパス
`includes/class-toolzoo-links.php`

#### 3.2.3 メソッド一覧

**`render($atts)`**
- **説明**: ショートコード出力をレンダリング
- **パラメータ**: `$atts` ショートコード属性
- **戻り値**: string (HTML)
- **処理**:
  - 属性をパース
  - ツール情報をConstants から取得
  - HTMLを生成して返却
  - CSSをエンキュー

**`get_link_url($tool_id)`**
- **説明**: ツールリンク先URLを取得
- **パラメータ**: `$tool_id` ツールID
- **戻り値**: string (URL)
- **処理**:
  - 固定ページまたはクエリパラメータでURL構築
  - フォールバック対応

**`normalize_atts($atts)`**
- **説明**: ショートコード属性を標準化
- **パラメータ**: `$atts` 元の属性
- **戻り値**: array (標準化された属性)
- **処理**:
  - デフォルト値を設定
  - 値の妥当性チェック

### 3.3 ショートコード登録

```php
add_shortcode('toolzoo_links', array($this, 'links_shortcode'));
```

## 4. CSS設計

### 4.1 ファイル名
`assets/css/links.css`

### 4.2 主要スタイル

```css
/* コンテナ */
.toolzoo-links {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

/* 個別リンク */
.toolzoo-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    color: #0073aa;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.2s;
    white-space: nowrap;
}

.toolzoo-link:hover {
    color: #005a87;
    background-color: #f0f0f0;
}

/* アイコン */
.toolzoo-link-icon {
    font-size: 1.2em;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* テキスト */
.toolzoo-link-text {
    font-size: 0.95em;
    font-weight: 500;
}

/* 区切り文字 */
.toolzoo-links-separator {
    color: #ccc;
    font-weight: 300;
}

/* スタイルバリエーション */
.toolzoo-links-icon-only .toolzoo-link-text {
    display: none;
}

.toolzoo-links-text-only .toolzoo-link-icon {
    display: none;
}

/* サイズバリエーション */
.toolzoo-links-small .toolzoo-link {
    padding: 6px 10px;
    font-size: 0.9em;
}

.toolzoo-links-large .toolzoo-link {
    padding: 10px 14px;
    font-size: 1.05em;
}

/* レスポンシブ */
@media screen and (max-width: 768px) {
    .toolzoo-links {
        gap: 4px;
    }

    .toolzoo-link {
        padding: 6px 10px;
    }
}

@media screen and (max-width: 480px) {
    .toolzoo-links {
        flex-direction: column;
        width: 100%;
    }

    .toolzoo-link {
        width: 100%;
        justify-content: flex-start;
    }

    .toolzoo-links-separator {
        display: none;
    }
}
```

### 4.3 レスポンシブ対応

- デスクトップ: フレックスレイアウト（行並び）
- タブレット: ギャップを削減
- モバイル: 折り返し対応、または列並び

## 5. JavaScript設計

### 5.1 ファイル名
`assets/js/links.js`

### 5.2 機能

基本的にはJavaScriptは不要だが、以下の機能は検討：

**オプション機能:**
- **アクティブ状態の表示**: 現在のページに該当するリンクをハイライト
- **ツールチップ**: リンク説明の表示
- **キーボードナビゲーション**: Tab キーでのフォーカス管理

### 5.3 実装

```javascript
document.addEventListener('DOMContentLoaded', function() {
    // 現在のページに対応するリンクをハイライト
    const currentTool = detectCurrentTool();
    if (currentTool) {
        document.querySelector('[data-tool="' + currentTool + '"]')
            ?.classList.add('active');
    }
});

function detectCurrentTool() {
    // URLまたはページデータから現在のツールを検出
    // ...
}
```

## 6. ローカライズ対応

### 6.1 翻訳対象文字列

- `All Tools List` (既存)
- ツール名（既存）
- ツール説明（既存）

## 7. テスト項目

### 7.1 機能テスト
- [ ] ショートコード基本動作
- [ ] 属性パース（style, size, separator）
- [ ] リンク生成（全ツール）
- [ ] URL生成（各ツール）
- [ ] 属性値の妥当性チェック

### 7.2 UI/UXテスト
- [ ] デスクトップ表示
- [ ] タブレット表示
- [ ] スマートフォン表示
- [ ] ホバー効果
- [ ] リンク動作

### 7.3 ブラウザ互換性
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge

### 7.4 アクセシビリティ
- [ ] キーボード操作（Tab）
- [ ] スクリーンリーダー対応
- [ ] カラーコントラスト
- [ ] リンクのタイトル属性

## 8. 使用例

### 8.1 基本的な使い方
```
[toolzoo_links]
```
→ デフォルト表示（アイコン + テキスト、パイプ区切り）

### 8.2 アイコンのみ表示
```
[toolzoo_links style="icon-only" size="large"]
```
→ 大きなアイコンのみ表示

### 8.3 テキストのみ表示
```
[toolzoo_links style="text-only" separator="dot"]
```
→ テキストのみ、ドット区切り表示

### 8.4 小型表示
```
[toolzoo_links size="small" separator="slash"]
```
→ 小型で、スラッシュ区切り表示

## 9. 将来の拡張案

### 9.1 機能拡張
- **除外機能**: 特定のツールを除外して表示
- **グループ化**: ツールをカテゴリーでグループ化
- **カウント表示**: 各ツールの使用回数など
- **検索機能**: ツール検索UI
- **ドロップダウン**: 各ツールの説明をポップアップで表示

### 9.2 スタイル拡張
- **テーマ対応**: 異なるカラースキーム
- **カスタムカラー**: 属性で色指定
- **バッジ表示**: NEW / HOT など
- **アニメーション**: ホバーアニメーション追加

### 9.3 統計機能
- **クリック数追跡**: 各ツールへのクリック数
- **人気度表示**: 最も使用されているツール
- **推奨表示**: アクセス者に最適なツール

## 10. コンテンツ管理

### 10.1 Constants ファイルとの連携
- ツール情報は `Toolzoo_Constants::get_tools_list()` から取得
- ツール追加時は Constants ファイルのみ更新

### 10.2 URL生成ロジック
- 固定ページベース: 子ページとして `/tools/password/` など
- クエリパラメータベース: `/?tool=password` など
- フォールバック: `[toolzoo_all tool="password"]` など

## 11. パフォーマンス

### 11.1 最適化
- CSSはインライン化を検討（小規模なため）
- JavaScriptは必要な場合のみロード
- キャッシュ対応：通常は変更されないため

### 11.2 セキュリティ
- ショートコード属性のサニタイズ
- URL生成時のエスケープ
- XSS対策

## 12. 管理画面統合

### 12.1 ツール一覧での表示
- Constants に「ツールリンク」として登録
- 説明: 「ToolZooのすべてのツールへのリンクを横並びで表示します」
- ショートコード: `[toolzoo_links]`
- プレビュー: 非表示（class が空）
