# JSON処理ツール 詳細設計

## 1. 機能概要

### 1.1 機能名
JSON処理ツール (JSON Processor)

### 1.2 ショートコード
```
[toolzoo_json]
```

### 1.3 機能説明
ユーザーがJSON形式のテキストを入力して、複数の操作を行えるツール。以下の機能を提供する：
- **シンタックスチェック**: 入力されたテキストが有効なJSON形式であるか検証
- **整形 (Pretty Print)**: 圧縮されたJSONをインデント付きで見やすく整形
- **圧縮 (Minify)**: 整形されたJSONから余分な空白を削除して最小化
- **シンタックスハイライト**: JSONの要素（キー、値、括弧など）を色分けして表示
- **ツリービュー**: JSONの階層構造を折りたたみ可能なツリー形式で表示

### 1.4 対応するJSONの規模
- 最小: `{}`（空オブジェクト）
- 最大: 1MB以内のテキストファイル
- 対応形式: RFC 7158 準拠のJSON

## 2. UI設計

### 2.1 全体レイアウト

```
┌─────────────────────────────────────────────────────────┐
│  JSON処理ツール                                          │
├─────────────────────────────────────────────────────────┤
│                                                          │
│ [タブ] Pretty Print | Minify | Tree View | Raw         │
│                                                          │
├─────────────────────────────────────────────────────────┤
│  入力エリア                                              │
├──────────────────┬──────────────────────────────────────┤
│ テキストを貼り   │ 整形済みJSON表示エリア              │
│ 付けまたは入力   │ (シンタックスハイライト付き)        │
│                  │                                      │
│ [貼り付け]       │ ┌──────────────────────────────────┐│
│ [クリア]         │ │ {                              ││
│ [ダウンロード]   │ │   "key": "value",             ││
│ [コピー]         │ │   "number": 123,              ││
│                  │ │   "nested": {                 ││
│                  │ │     "inner": true             ││
│                  │ │   }                           ││
│                  │ │ }                             ││
│                  │ └──────────────────────────────────┘│
├──────────────────┴──────────────────────────────────────┤
│  ステータスバー: ✓ 有効なJSON (3行, 45 bytes)          │
└─────────────────────────────────────────────────────────┘
```

### 2.2 入力エリア

#### 2.2.1 テキスト入力フィールド
- **ID**: `toolzoo-json-input`
- **タイプ**: テキストエリア（`<textarea>`）
- **属性**:
  - `placeholder`: "JSONを入力またはペーストしてください..."
  - `rows`: 15
  - `spellcheck`: false
  - `autocomplete`: off
- **機能**:
  - リアルタイムバリデーション（入力中に検証）
  - 行番号表示（オプション）
  - 自動インデント（オプション）

#### 2.2.2 操作ボタン

| ボタン | ID | 機能 |
|--------|-----|------|
| 貼り付け | `toolzoo-json-paste` | クリップボードからペースト |
| クリア | `toolzoo-json-clear` | 入力エリアをクリア |
| ダウンロード | `toolzoo-json-download` | JSON をテキストファイルでダウンロード |
| コピー | `toolzoo-json-copy-input` | 入力内容をコピー |

### 2.3 タブメニュー

```
[Pretty Print] [Minify] [Tree View] [Raw]
```

#### 2.3.1 Pretty Print タブ
- **デフォルト**: はい
- **機能**: インデント付きで見やすく整形
- **インデント**: スペース4個（設定可能）
- **表示**: シンタックスハイライト付き

#### 2.3.2 Minify タブ
- **機能**: 余分な空白を削除して1行または最小行で表示
- **用途**: データ転送量削減、API用データ
- **表示**: シンタックスハイライト付き

#### 2.3.3 Tree View タブ
- **機能**: 階層構造を折りたたみ可能なツリーで表示
- **表示**:
  - インデント付きの階層表現
  - 展開/折りたたみアイコン（▶/▼）
  - 配列の要素数表示（例: `[3 items]`）
  - オブジェクトのキー数表示（例: `{4 keys}`）

#### 2.3.4 Raw タブ
- **機能**: 元のテキストをそのまま表示
- **用途**: 元のデータを確認する場合

### 2.4 出力エリア

#### 2.4.1 出力表示エリア
- **ID**: `toolzoo-json-output`
- **機能**: 各タブに応じた結果を表示
- **属性**:
  - 読み取り専用（`readonly`）
  - スクロール対応
  - シンタックスハイライト

#### 2.4.2 出力操作ボタン

| ボタン | ID | 機能 |
|--------|-----|------|
| コピー | `toolzoo-json-copy-output` | 出力内容をコピー |
| ダウンロード | `toolzoo-json-download-output` | 出力内容をダウンロード |
| クリア | `toolzoo-json-clear-output` | 出力をクリア |

### 2.5 ステータスバー

```
✓ 有効なJSON | 行数: 5 | サイズ: 245 bytes | 圧縮時: 189 bytes
```

- **バリデーション状態**:
  - ✓ 有効なJSON（緑色）
  - ✗ 無効なJSON（赤色）
  - 入力待機中（グレー）

- **統計情報**:
  - 入力側: 行数、バイト数
  - 出力側: 行数、バイト数、圧縮率

## 3. 機能仕様

### 3.1 JSON バリデーション

#### 3.1.1 バリデーション処理
```javascript
function validateJSON(text) {
  try {
    JSON.parse(text);
    return {
      valid: true,
      error: null,
      message: '有効なJSONです'
    };
  } catch (error) {
    return {
      valid: false,
      error: error,
      message: `JSON解析エラー: ${error.message}`,
      position: extractErrorPosition(error.message)
    };
  }
}
```

#### 3.1.2 エラーメッセージ
- 構文エラーの位置を表示
- エラー内容を日本語で説明

### 3.2 Pretty Print (整形)

#### 3.2.1 処理フロー
```
1. 入力テキストをバリデーション
2. JSON.parse() でオブジェクトに変換
3. JSON.stringify(obj, null, indent) で整形
4. シンタックスハイライト処理
5. 結果を出力
```

#### 3.2.2 インデント設定
- **デフォルト**: 4スペース
- **設定メニュー**: インデント数を選択可能（2/4/8スペース）

#### 3.2.3 実装例
```javascript
function prettyPrintJSON(text) {
  const parsed = JSON.parse(text);
  const indent = getIndentSetting(); // 4
  return JSON.stringify(parsed, null, indent);
}
```

### 3.3 Minify (圧縮)

#### 3.3.1 処理フロー
```
1. 入力テキストをバリデーション
2. JSON.parse() でオブジェクトに変換
3. JSON.stringify(obj) で圧縮（インデントなし）
4. シンタックスハイライト処理
5. 結果を出力
```

#### 3.3.2 実装例
```javascript
function minifyJSON(text) {
  const parsed = JSON.parse(text);
  return JSON.stringify(parsed);
}
```

#### 3.3.3 圧縮率計算
```javascript
function calculateCompressionRate(original, minified) {
  const originalSize = new Blob([original]).size;
  const minifiedSize = new Blob([minified]).size;
  const rate = ((1 - minifiedSize / originalSize) * 100).toFixed(2);
  return rate; // パーセンテージ
}
```

### 3.4 Tree View (ツリービュー)

#### 3.4.1 ツリー構造表示
```
▼ root
  ▶ name: "John Doe"
  ▶ age: 30
  ▼ address
    - street: "123 Main St"
    - city: "Springfield"
    - zip: "12345"
  ▼ hobbies [3 items]
    - [0] "reading"
    - [1] "gaming"
    - [2] "coding"
```

#### 3.4.2 実装戦略
```javascript
function buildTreeView(obj, depth = 0) {
  const indent = '  '.repeat(depth);
  let html = '';

  if (Array.isArray(obj)) {
    html += `<div class="tree-node">${indent}[${obj.length} items]</div>`;
    obj.forEach((item, index) => {
      if (typeof item === 'object' && item !== null) {
        html += buildTreeView(item, depth + 1);
      } else {
        html += `<div class="tree-item">${indent}  [${index}] ${JSON.stringify(item)}</div>`;
      }
    });
  } else if (typeof obj === 'object' && obj !== null) {
    const keys = Object.keys(obj);
    html += `<div class="tree-node">${indent}{${keys.length} keys}</div>`;
    keys.forEach(key => {
      const value = obj[key];
      if (typeof value === 'object' && value !== null) {
        html += `<div class="tree-key">${indent}  ▼ ${key}</div>`;
        html += buildTreeView(value, depth + 2);
      } else {
        html += `<div class="tree-item">${indent}  - ${key}: ${JSON.stringify(value)}</div>`;
      }
    });
  }

  return html;
}
```

#### 3.4.3 折りたたみ機能
- クリック可能な展開/折りたたみアイコン（▶/▼）
- JavaScriptでDOM要素の表示/非表示を制御
- クリック時に状態を切り替え

### 3.5 シンタックスハイライト

#### 3.5.1 色分けルール

| 要素 | 色 | 例 |
|------|-----|-----|
| キー | 青（#0066cc） | `"name"` |
| 文字列値 | 緑（#228822） | `"John Doe"` |
| 数値 | 紫（#990099） | `30`, `3.14` |
| ブール値 | オレンジ（#ff6600） | `true`, `false` |
| null | グレー（#666666） | `null` |
| 括弧 | 黒（#000000） | `{}`, `[]` |

#### 3.5.2 実装方法
```javascript
function highlightJSON(text) {
  // 正規表現でパターンマッチング
  const highlighted = text
    .replace(/"([^"\\]|\\.)*"/g, (match) => {
      // キーと値を区別
      if (match.includes(':')) {
        return `<span class="json-key">${match}</span>`;
      } else {
        return `<span class="json-string">${match}</span>`;
      }
    })
    .replace(/:\s*/g, '<span class="json-colon">: </span>')
    .replace(/\b(true|false|null)\b/g, '<span class="json-keyword">$1</span>')
    .replace(/:\s*(-?\d+\.?\d*)/g, (match, num) => {
      return `: <span class="json-number">${num}</span>`;
    });

  return highlighted;
}
```

#### 3.5.3 CSS クラス
- `.json-key`: キーのスタイル
- `.json-string`: 文字列値のスタイル
- `.json-number`: 数値のスタイル
- `.json-keyword`: ブール値/null のスタイル
- `.json-colon`: コロン区切りのスタイル
- `.json-bracket`: 括弧のスタイル

### 3.6 入出力機能

#### 3.6.1 クリップボード操作
```javascript
// ペースト
async function pasteFromClipboard() {
  try {
    const text = await navigator.clipboard.readText();
    document.getElementById('toolzoo-json-input').value = text;
    validateAndUpdate();
    return true;
  } catch (error) {
    showError('クリップボードの読み込みに失敗しました');
    return false;
  }
}

// コピー
async function copyToClipboard(elementId) {
  const element = document.getElementById(elementId);
  const text = element.value || element.textContent;
  try {
    await navigator.clipboard.writeText(text);
    showCopySuccess();
    return true;
  } catch (error) {
    return fallbackCopy(text);
  }
}
```

#### 3.6.2 ダウンロード機能
```javascript
function downloadJSON(content, filename = 'data.json') {
  const blob = new Blob([content], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = filename;
  link.click();
  URL.revokeObjectURL(url);
}
```

#### 3.6.3 リアルタイム処理
- 入力エリアへの入力中にリアルタイムでバリデーション
- タブ切り替え時に対応する処理を実行
- デバウンス処理で重い処理の実行頻度を制限（500ms）

## 4. PHPクラス設計

### 4.1 クラス名
`Toolzoo_JSON_Processor`

### 4.2 ファイルパス
`includes/class-json-processor.php`

### 4.3 メソッド一覧

#### 4.3.1 __construct()
- **説明**: コンストラクタ
- **処理**: 初期化処理

#### 4.3.2 render()
- **説明**: HTMLを生成して返却
- **戻り値**: string (HTML)
- **処理**:
  - HTMLテンプレートの生成
  - CSS/JSのエンキュー
  - ローカライズ対応

#### 4.3.3 enqueue_assets()
- **説明**: CSS/JSを読み込み
- **処理**:
  - `wp_enqueue_style('toolzoo-json-css')`
  - `wp_enqueue_script('toolzoo-json-js')`

### 4.4 ショートコードハンドラー
```php
function toolzoo_json_shortcode($atts) {
    $processor = new Toolzoo_JSON_Processor();
    return $processor->render();
}
add_shortcode('toolzoo_json', 'toolzoo_json_shortcode');
```

### 4.5 セキュリティ処理
- 出力時のエスケープ処理（`esc_html()`, `esc_attr()`）
- XSS対策
- CSRF対策（必要に応じて）

## 5. CSS設計

### 5.1 ファイル名
`assets/css/json.css`

### 5.2 主要クラス
- `.toolzoo-json-container`: 全体コンテナ
- `.toolzoo-json-tabs`: タブメニュー
- `.toolzoo-json-tab`: タブアイテム
- `.toolzoo-json-tab-content`: タブコンテンツ
- `.toolzoo-json-input-area`: 入力エリア
- `.toolzoo-json-output-area`: 出力エリア
- `.toolzoo-json-input`: 入力テキストエリア
- `.toolzoo-json-output`: 出力テキストエリア
- `.toolzoo-json-button`: ボタン共通
- `.toolzoo-json-status`: ステータスバー
- `.json-key`, `.json-string`, `.json-number`, `.json-keyword`: ハイライト

### 5.3 レイアウトCSS例
```css
.toolzoo-json-container {
  display: flex;
  flex-direction: column;
  gap: 15px;
  padding: 20px;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.toolzoo-json-main {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 15px;
  min-height: 400px;
}

.toolzoo-json-input-area,
.toolzoo-json-output-area {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.toolzoo-json-input,
.toolzoo-json-output {
  font-family: 'Courier New', Courier, monospace;
  font-size: 13px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: #f9f9f9;
  overflow-y: auto;
  flex: 1;
  resize: vertical;
}

.toolzoo-json-tabs {
  display: flex;
  gap: 0;
  border-bottom: 2px solid #ddd;
  list-style: none;
  padding: 0;
  margin: 0;
}

.toolzoo-json-tab {
  padding: 10px 20px;
  cursor: pointer;
  background: #f5f5f5;
  border: 1px solid #ddd;
  border-bottom: none;
  margin-right: 2px;
  transition: background 0.2s;
}

.toolzoo-json-tab.active {
  background: #fff;
  border-bottom: 2px solid #0073aa;
  position: relative;
  bottom: -2px;
}

.toolzoo-json-tab:hover {
  background: #eee;
}

.toolzoo-json-status {
  padding: 10px 15px;
  background: #f0f0f0;
  border-radius: 4px;
  font-size: 13px;
  color: #666;
}

.toolzoo-json-status.valid {
  background: #e8f5e9;
  color: #2e7d32;
}

.toolzoo-json-status.invalid {
  background: #ffebee;
  color: #c62828;
}
```

### 5.4 シンタックスハイライトCSS
```css
.json-key {
  color: #0066cc;
  font-weight: bold;
}

.json-string {
  color: #228822;
}

.json-number {
  color: #990099;
}

.json-keyword {
  color: #ff6600;
  font-weight: bold;
}

.json-null {
  color: #666666;
  font-style: italic;
}

.json-bracket {
  color: #000000;
  font-weight: bold;
}
```

### 5.5 Tree View CSS
```css
.tree-node {
  padding: 5px 0;
  cursor: pointer;
  user-select: none;
}

.tree-node::before {
  content: '▼ ';
  color: #666;
  margin-right: 5px;
}

.tree-node.collapsed::before {
  content: '▶ ';
}

.tree-item {
  padding: 3px 0;
  color: #333;
}

.tree-key {
  color: #0066cc;
  font-weight: bold;
}
```

### 5.6 レスポンシブ対応
```css
@media screen and (max-width: 768px) {
  .toolzoo-json-main {
    grid-template-columns: 1fr;
  }

  .toolzoo-json-tabs {
    flex-wrap: wrap;
  }

  .toolzoo-json-input,
  .toolzoo-json-output {
    min-height: 300px;
  }
}
```

## 6. JavaScript設計

### 6.1 ファイル名
`assets/js/json.js`

### 6.2 主要関数

#### 6.2.1 初期化
```javascript
document.addEventListener('DOMContentLoaded', function() {
  initJSONProcessor();
});

function initJSONProcessor() {
  setupEventListeners();
  setupTabHandlers();
}
```

#### 6.2.2 イベントハンドラー設定
```javascript
function setupEventListeners() {
  // 入力イベント
  const input = document.getElementById('toolzoo-json-input');
  input.addEventListener('input', debounce(validateAndUpdate, 500));

  // ボタンイベント
  document.getElementById('toolzoo-json-paste').addEventListener('click', pasteFromClipboard);
  document.getElementById('toolzoo-json-clear').addEventListener('click', clearInput);
  document.getElementById('toolzoo-json-copy-input').addEventListener('click', () => copyToClipboard('toolzoo-json-input'));
  document.getElementById('toolzoo-json-download').addEventListener('click', downloadInput);
  document.getElementById('toolzoo-json-copy-output').addEventListener('click', () => copyToClipboard('toolzoo-json-output'));
  document.getElementById('toolzoo-json-download-output').addEventListener('click', downloadOutput);
}

function setupTabHandlers() {
  const tabs = document.querySelectorAll('.toolzoo-json-tab');
  tabs.forEach(tab => {
    tab.addEventListener('click', switchTab);
  });
}
```

#### 6.2.3 主要関数リスト
- `initJSONProcessor()`: 初期化
- `setupEventListeners()`: イベントリスナー設定
- `setupTabHandlers()`: タブハンドラー設定
- `validateJSON(text)`: JSON検証
- `validateAndUpdate()`: 検証と更新
- `prettyPrintJSON(text)`: 整形
- `minifyJSON(text)`: 圧縮
- `buildTreeView(obj)`: ツリービュー生成
- `highlightJSON(text)`: シンタックスハイライト
- `switchTab(event)`: タブ切り替え
- `pasteFromClipboard()`: ペースト
- `copyToClipboard(elementId)`: コピー
- `downloadJSON(content, filename)`: ダウンロード
- `clearInput()`: 入力クリア
- `debounce(func, delay)`: デバウンス処理
- `showError(message)`: エラー表示
- `showCopySuccess()`: コピー成功表示
- `updateStatus(valid, stats)`: ステータス更新

### 6.3 グローバル変数
```javascript
let currentJSON = null;        // 現在のJSONオブジェクト
let currentMode = 'pretty';    // 現在のモード
let validationDelay = 500;     // デバウンス遅延（ms）
```

## 7. エラーハンドリング

### 7.1 バリデーションエラー

```javascript
const errors = {
  INVALID_JSON: 'JSON解析エラー: 無効なJSON形式です',
  UNEXPECTED_TOKEN: 'JSON解析エラー: 予期しないトークンが見つかりました',
  EMPTY_INPUT: '入力がありません。JSONを入力してください',
  SIZE_EXCEEDED: 'ファイルサイズが1MBを超えています'
};
```

### 7.2 エラー表示UI
- 入力エリアの上部に赤色エラーバナーを表示
- エラーの詳細を含むメッセージを表示
- 問題の位置を示すインジケーター（行番号など）

### 7.3 ユーザーフィードバック
```javascript
function showError(message, details = '') {
  const errorElement = document.getElementById('toolzoo-json-error');
  errorElement.textContent = message;
  if (details) {
    errorElement.title = details;
  }
  errorElement.style.display = 'block';
}

function clearError() {
  const errorElement = document.getElementById('toolzoo-json-error');
  errorElement.style.display = 'none';
}
```

## 8. セキュリティ考慮事項

### 8.1 入力検証
- JSON.parse()による厳密な構文チェック
- 最大サイズ制限（1MB）の実装
- 深さ制限（ネストの深さ）の検討

### 8.2 XSS対策
- シンタックスハイライト時のエスケープ処理
- textContent 使用を推奨
- ユーザー入力の直接的な HTML 挿入を避ける

### 8.3 データの扱い
- JSONデータはブラウザのローカルストレージに保存しない
- セッション中のメモリ上でのみ保持
- ダウンロード時のファイル名サニタイズ

## 9. パフォーマンス考慮事項

### 9.1 大規模データ対応
- 入力時のデバウンス処理（500ms）
- 処理が重い場合はローディング表示
- 1MB超のファイルは警告表示

### 9.2 メモリ管理
- 不要なDOM要素の削除
- イベントリスナーの適切な削除
- メモリリークの防止

### 9.3 最適化
- CSS/JSのミニファイ
- 冗長なDOM操作の削減
- アルゴリズムの最適化（特にツリービュー生成）

## 10. テスト項目

### 10.1 機能テスト

#### 10.1.1 バリデーション
- [ ] 有効なJSONが正しく認識される
- [ ] 無効なJSONがエラーで検出される
- [ ] エラーメッセージが明確
- [ ] 空入力のハンドリング
- [ ] 最大サイズ制限の動作

#### 10.1.2 Pretty Print
- [ ] 圧縮されたJSONが整形される
- [ ] インデント設定が反映される
- [ ] ネストされたオブジェクトが正しく整形される
- [ ] 配列のインデント

#### 10.1.3 Minify
- [ ] 余分な空白が削除される
- [ ] 機能性は変わらない
- [ ] 圧縮率が正しく計算される

#### 10.1.4 Tree View
- [ ] 階層構造が正しく表示される
- [ ] 展開/折りたたみが動作する
- [ ] 配列の要素数が表示される
- [ ] オブジェクトのキー数が表示される
- [ ] 深くネストされた構造の表示

#### 10.1.5 シンタックスハイライト
- [ ] キーが正しく色分けされる
- [ ] 値が正しく色分けされる
- [ ] 各要素の色が適切

#### 10.1.6 入出力機能
- [ ] クリップボードペーストが動作
- [ ] テキストコピーが動作
- [ ] JSONファイルダウンロードが動作
- [ ] ファイル名が適切に設定される

### 10.2 UI/UXテスト
- [ ] タブ切り替えが滑らか
- [ ] レスポンシブデザイン（モバイル/タブレット/デスクトップ）
- [ ] キーボード操作（Tab、Enter）
- [ ] マウス操作

### 10.3 ブラウザ互換性テスト
- [ ] Chrome（最新版）
- [ ] Firefox（最新版）
- [ ] Safari（最新版）
- [ ] Edge（最新版）
- [ ] クリップボード API の動作確認
- [ ] フォールバック実装の確認

### 10.4 アクセシビリティテスト
- [ ] キーボード全操作可能
- [ ] スクリーンリーダー対応
- [ ] フォーカス表示
- [ ] コントラスト確認

### 10.5 パフォーマンステスト
- [ ] 小規模JSON（< 10KB）の処理速度
- [ ] 中規模JSON（100KB）の処理速度
- [ ] 大規模JSON（1MB）の処理時間
- [ ] メモリリークがない
- [ ] CPU使用率が適切

### 10.6 エッジケーステスト
- [ ] 空オブジェクト `{}`
- [ ] 空配列 `[]`
- [ ] Unicode文字を含むJSON
- [ ] 非常に深くネストされたJSON
- [ ] 特殊文字を含むキー
- [ ] 改行を含むテキスト値

## 11. 将来の拡張案

### 11.1 機能拡張
- **JSONバリデータ**: より詳細なスキーマ検証
- **差分比較**: 2つのJSONの差を表示
- **マージ機能**: 複数のJSONをマージ
- **フィルタリング**: 特定のキーで抽出
- **変換機能**: JSON ↔ CSV / XML などの形式変換
- **検索機能**: キーと値から検索
- **JSONパス**: 特定の要素をセレクタで指定
- **スキーマ生成**: JSONからスキーマを自動生成

### 11.2 UI改善
- **ダークモード**: 暗いテーマ
- **フォントサイズ調整**: ユーザー設定可能
- **カラーテーマ**: ハイライトカラーのカスタマイズ
- **レイアウト切り替え**: 垂直/水平分割
- **フルスクリーンモード**: 大画面表示

### 11.3 高度な機能
- **JSON editor**: 直接編集可能なエディタ
- **バージョン管理**: 編集履歴の保存
- **プリセット**: よく使うスニペット保存
- **正規化**: キーの並び替えなど

## 12. 使用例

### 12.1 基本的な使い方
1. JSONテキストを入力エリアに貼り付け
2. バリデーション結果を確認
3. 必要なタブ（Pretty Print/Minify/Tree View）を選択
4. 結果を確認またはコピー

### 12.2 圧縮データの確認
1. 圧縮されたAPIレスポンスを貼り付け
2. Pretty Print タブで整形済み状態を確認
3. 必要に応じてコピーまたはダウンロード

### 12.3 構造確認
1. JSONを貼り付け
2. Tree View タブで階層構造を確認
3. 必要なデータを特定

## 13. ローカライズ対応

### 13.1 翻訳対象文字列
- ボタンラベル
- タブ名
- エラーメッセージ
- ステータスメッセージ
- プレースホルダーテキスト

### 13.2 実装方法
WordPress の `__()`, `_e()`, `esc_html__()` などを使用
