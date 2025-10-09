# パスワード生成機能 詳細設計

## 1. 機能概要

### 1.1 機能名
パスワード生成ツール (Password Generator)

### 1.2 ショートコード
```
[toolzoo_password]
```

### 1.3 機能説明
ユーザーが指定した条件に基づいて、ランダムなパスワードを20個一度に生成して表示するツール。
数字、英字（大文字・小文字）、記号の使用有無と文字数を選択でき、安全なパスワードを簡単に生成できる。

### 1.4 表示内容
- 生成数: 20個（固定）
- 各パスワードにコピーボタンを配置
- リスト形式で見やすく表示

## 2. UI設計

### 2.1 画面レイアウト
```
┌─────────────────────────────────────────────┐
│   パスワード生成ツール                       │
├─────────────────────────────────────────────┤
│                                             │
│ 文字数: [16] ──────────── (スライダー)      │
│         8文字 ~ 64文字                      │
│                                             │
│ ☑ 数字 (0-9)                                │
│ ☑ 英字小文字 (a-z)                          │
│ ☑ 英字大文字 (A-Z)                          │
│ ☑ 記号 (!@#$%^&*()_+-=[]{}|;:,.<>?)         │
│                                             │
│ [ パスワード生成 (20個) ]                   │
│                                             │
├─────────────────────────────────────────────┤
│ 生成されたパスワード一覧                     │
├─────────────────────────────────────────────┤
│                                             │
│  1. aB3!xY9@mN7#pQr2           [コピー]     │
│  2. T5$zW8&kL4%mNp9            [コピー]     │
│  3. R9^hG2*vB6#xT1             [コピー]     │
│  4. M3@jK7!nQ5$pF8             [コピー]     │
│  5. P6&wH9#bV3*cX2             [コピー]     │
│  6. S4$rJ8@gN1!mY5             [コピー]     │
│  7. L7*fD2&qK9#hP6             [コピー]     │
│  8. Z3!nM5$wG8@vT1             [コピー]     │
│  9. B9#xR4&pJ7*kW2             [コピー]     │
│ 10. Q6@hL3!fS8$nM5             [コピー]     │
│ 11. Y2*tK9&bD4#vR7             [コピー]     │
│ 12. H8!pW5$xG1@mN3             [コピー]     │
│ 13. V4#jT7*nK2&qF9             [コピー]     │
│ 14. C1$rM6@wP8!hL3             [コピー]     │
│ 15. N9&bG3#xJ5*kS7             [コピー]     │
│ 16. F2!vR8$pT4@mW6             [コピー]     │
│ 17. X5*hK1&nD9#qL3             [コピー]     │
│ 18. W7@jM4$bG2!vP8             [コピー]     │
│ 19. K3#pS6*xR1&fT9             [コピー]     │
│ 20. G8!nW2$hM5@kL7             [コピー]     │
│                                             │
│                      [ すべてコピー ]        │
│                                             │
└─────────────────────────────────────────────┘
```

### 2.2 UI要素詳細

#### 2.2.1 文字数選択
- **タイプ**: レンジスライダー + 数値表示
- **範囲**: 8文字 ~ 64文字
- **デフォルト値**: 16文字
- **ID**: `toolzoo-password-length`

#### 2.2.2 文字種別選択
各文字種別をチェックボックスで選択可能

| 項目 | ID | デフォルト | 使用文字 |
|------|-----|-----------|----------|
| 数字 | `toolzoo-use-numbers` | チェック | 0123456789 |
| 英字小文字 | `toolzoo-use-lowercase` | チェック | abcdefghijklmnopqrstuvwxyz |
| 英字大文字 | `toolzoo-use-uppercase` | チェック | ABCDEFGHIJKLMNOPQRSTUVWXYZ |
| 記号 | `toolzoo-use-symbols` | チェック | !@#$%^&*()_+-=[]{}|;:,.<>? |

**バリデーション**: 最低1つの文字種別を選択必須

#### 2.2.3 生成ボタン
- **ラベル**: "パスワード生成 (20個)"
- **ID**: `toolzoo-generate-btn`
- **動作**: クリック時に20個のパスワードを生成

#### 2.2.4 パスワードリスト表示エリア
- **ID**: `toolzoo-password-list`
- **構造**: 順序付きリスト（`<ol>`）
- **各行**: 番号 + パスワード + コピーボタン
- **フォント**: 等幅フォント（monospace）
- **初期状態**: 非表示または説明文

#### 2.2.5 個別コピーボタン
- **ラベル**: "コピー" / "コピー済み"
- **クラス**: `toolzoo-copy-single-btn`
- **data属性**: `data-password-index` (パスワードのインデックス)
- **動作**:
  - クリップボードに該当パスワードをコピー
  - コピー成功時にボタンテキストを"コピー済み"に変更
  - 2秒後に元の状態に戻る

#### 2.2.6 すべてコピーボタン
- **ラベル**: "すべてコピー"
- **ID**: `toolzoo-copy-all-btn`
- **動作**:
  - 20個すべてのパスワードをテキスト形式でクリップボードにコピー
  - 各パスワードは改行で区切る
  - コピー成功時に視覚的フィードバック

## 3. 機能仕様

### 3.1 パスワード生成アルゴリズム

#### 3.1.1 処理フロー
```
1. ユーザー設定を取得
   ↓
2. 使用文字セットの構築
   ↓
3. 20個のパスワードを生成
   ↓
4. 各パスワードについて:
   a. 暗号学的に安全な乱数生成器を使用
   b. 指定文字数分のランダム文字を選択
   c. 各文字種別が最低1文字含まれるか確認
   d. 含まれない場合は再生成
   ↓
5. パスワードリストを返却
   ↓
6. HTML生成して表示
```

#### 3.1.2 実装方法
```javascript
// 20個のパスワードを生成
function generatePasswordList(length, options, count = 20) {
  const passwords = [];

  for (let i = 0; i < count; i++) {
    const password = generatePassword(length, options);
    passwords.push(password);
  }

  return passwords;
}

// 1個のパスワード生成
function generatePassword(length, options) {
  let charset = '';
  if (options.numbers) charset += '0123456789';
  if (options.lowercase) charset += 'abcdefghijklmnopqrstuvwxyz';
  if (options.uppercase) charset += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  if (options.symbols) charset += '!@#$%^&*()_+-=[]{}|;:,.<>?';

  let password = '';
  const values = new Uint32Array(length);
  crypto.getRandomValues(values);

  for (let i = 0; i < length; i++) {
    password += charset[values[i] % charset.length];
  }

  // 各文字種別が含まれるか確認
  if (!validatePassword(password, options)) {
    return generatePassword(length, options); // 再帰的に再生成
  }

  return password;
}

// パスワードのバリデーション
function validatePassword(password, options) {
  if (options.numbers && !/[0-9]/.test(password)) return false;
  if (options.lowercase && !/[a-z]/.test(password)) return false;
  if (options.uppercase && !/[A-Z]/.test(password)) return false;
  if (options.symbols && !/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(password)) return false;
  return true;
}
```

### 3.2 リスト表示

#### 3.2.1 HTML生成
```javascript
function renderPasswordList(passwords) {
  let html = '<ol class="toolzoo-password-list">';

  passwords.forEach((password, index) => {
    html += '<li class="toolzoo-password-item">';
    html += `<span class="toolzoo-password-text">${escapeHtml(password)}</span>`;
    html += `<button class="toolzoo-copy-single-btn" data-password-index="${index}">コピー</button>`;
    html += '</li>';
  });

  html += '</ol>';
  html += '<div class="toolzoo-copy-all-container">';
  html += '<button id="toolzoo-copy-all-btn" class="toolzoo-btn toolzoo-btn-primary">すべてコピー</button>';
  html += '</div>';

  return html;
}
```

#### 3.2.2 DOM更新
```javascript
function displayPasswordList(passwords) {
  const container = document.getElementById('toolzoo-password-list-container');
  container.innerHTML = renderPasswordList(passwords);

  // イベントリスナーを設定
  attachCopyEventListeners(passwords);
}
```

### 3.3 クリップボードコピー機能

#### 3.3.1 個別コピー
```javascript
async function copySinglePassword(password, button) {
  try {
    await navigator.clipboard.writeText(password);
    showCopySuccess(button);
    return true;
  } catch (err) {
    // フォールバック
    return fallbackCopyToClipboard(password, button);
  }
}

function showCopySuccess(button) {
  const originalText = button.textContent;
  button.textContent = 'コピー済み';
  button.classList.add('copied');

  setTimeout(() => {
    button.textContent = originalText;
    button.classList.remove('copied');
  }, 2000);
}
```

#### 3.3.2 すべてコピー
```javascript
async function copyAllPasswords(passwords) {
  const text = passwords.join('\n');

  try {
    await navigator.clipboard.writeText(text);
    showCopyAllSuccess();
    return true;
  } catch (err) {
    return fallbackCopyToClipboard(text, null);
  }
}

function showCopyAllSuccess() {
  const button = document.getElementById('toolzoo-copy-all-btn');
  const originalText = button.textContent;
  button.textContent = 'コピー完了';
  button.classList.add('copied');

  setTimeout(() => {
    button.textContent = originalText;
    button.classList.remove('copied');
  }, 2000);
}
```

#### 3.3.3 フォールバック実装
```javascript
function fallbackCopyToClipboard(text, button) {
  const textarea = document.createElement('textarea');
  textarea.value = text;
  textarea.style.position = 'fixed';
  textarea.style.opacity = '0';
  document.body.appendChild(textarea);
  textarea.select();

  try {
    const success = document.execCommand('copy');
    if (success && button) {
      showCopySuccess(button);
    } else if (success) {
      showCopyAllSuccess();
    }
    return success;
  } catch (err) {
    return false;
  } finally {
    document.body.removeChild(textarea);
  }
}
```

### 3.4 イベントハンドラー

#### 3.4.1 イベントリスナー設定
```javascript
function attachCopyEventListeners(passwords) {
  // 個別コピーボタン
  document.querySelectorAll('.toolzoo-copy-single-btn').forEach(button => {
    button.addEventListener('click', function() {
      const index = parseInt(this.dataset.passwordIndex);
      const password = passwords[index];
      copySinglePassword(password, this);
    });
  });

  // すべてコピーボタン
  const copyAllBtn = document.getElementById('toolzoo-copy-all-btn');
  if (copyAllBtn) {
    copyAllBtn.addEventListener('click', function() {
      copyAllPasswords(passwords);
    });
  }
}
```

### 3.5 初期表示

#### 3.5.1 デフォルト動作
**オプション1: 初期状態では非表示**
- ページ読み込み時は説明文のみ表示
- 「パスワード生成」ボタンを押して初めて生成

**オプション2: 自動生成（推奨）**
- ページ読み込み時にデフォルト設定で20個自動生成
- ユーザーはすぐに使用可能

```javascript
document.addEventListener('DOMContentLoaded', function() {
  initPasswordGenerator();

  // デフォルト設定で自動生成
  const defaultOptions = {
    length: 16,
    numbers: true,
    lowercase: true,
    uppercase: true,
    symbols: true
  };

  generateAndDisplay(defaultOptions);
});
```

## 4. PHPクラス設計

### 4.1 クラス名
`Toolzoo_Password_Generator`

### 4.2 ファイルパス
`includes/class-password-generator.php`

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

#### 4.3.3 enqueue_assets()
- **説明**: CSS/JSを読み込み
- **処理**:
  - `wp_enqueue_style('toolzoo-password-css')`
  - `wp_enqueue_script('toolzoo-password-js')`

### 4.4 ショートコードハンドラー
```php
function toolzoo_password_shortcode($atts) {
    $generator = new Toolzoo_Password_Generator();
    return $generator->render();
}
add_shortcode('toolzoo_password', 'toolzoo_password_shortcode');
```

## 5. CSS設計

### 5.1 ファイル名
`assets/css/password.css`

### 5.2 主要クラス
- `.toolzoo-password-container`: 全体コンテナ
- `.toolzoo-password-options`: オプション選択エリア
- `.toolzoo-password-slider`: スライダー
- `.toolzoo-password-checkboxes`: チェックボックスグループ
- `.toolzoo-password-list`: パスワードリスト（`<ol>`）
- `.toolzoo-password-item`: リスト項目（`<li>`）
- `.toolzoo-password-text`: パスワードテキスト
- `.toolzoo-copy-single-btn`: 個別コピーボタン
- `.toolzoo-copy-all-container`: すべてコピーボタンコンテナ
- `.toolzoo-btn`: ボタン共通スタイル
- `.toolzoo-btn-primary`: 主要ボタン
- `.toolzoo-btn.copied`: コピー成功時のスタイル

### 5.3 リストデザイン例
```css
.toolzoo-password-list {
  list-style: none;
  padding: 0;
  margin: 20px 0;
  max-height: 500px;
  overflow-y: auto;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.toolzoo-password-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
  transition: background-color 0.2s;
}

.toolzoo-password-item:last-child {
  border-bottom: none;
}

.toolzoo-password-item:hover {
  background-color: #f5f5f5;
}

.toolzoo-password-text {
  font-family: 'Courier New', Courier, monospace;
  font-size: 14px;
  color: #333;
  flex: 1;
  margin-right: 10px;
  word-break: break-all;
}

.toolzoo-copy-single-btn {
  padding: 5px 12px;
  font-size: 12px;
  background: #007cba;
  color: white;
  border: none;
  border-radius: 3px;
  cursor: pointer;
  white-space: nowrap;
}

.toolzoo-copy-single-btn:hover {
  background: #005a87;
}

.toolzoo-copy-single-btn.copied {
  background: #28a745;
}

.toolzoo-copy-all-container {
  text-align: center;
  margin-top: 15px;
}

.toolzoo-btn-primary {
  padding: 10px 20px;
  font-size: 14px;
  background: #0073aa;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.toolzoo-btn-primary:hover {
  background: #005a87;
}

.toolzoo-btn-primary.copied {
  background: #28a745;
}
```

### 5.4 レスポンシブ対応
```css
@media screen and (max-width: 768px) {
  .toolzoo-password-container {
    padding: 15px;
  }

  .toolzoo-password-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .toolzoo-password-text {
    margin-bottom: 8px;
    margin-right: 0;
    font-size: 12px;
  }

  .toolzoo-copy-single-btn {
    align-self: flex-end;
  }
}
```

## 6. JavaScript設計

### 6.1 ファイル名
`assets/js/password.js`

### 6.2 主要関数

#### 6.2.1 初期化
```javascript
document.addEventListener('DOMContentLoaded', function() {
  initPasswordGenerator();
});

function initPasswordGenerator() {
  // イベントリスナー設定
  setupEventListeners();

  // デフォルト設定で自動生成
  const defaultOptions = getOptions();
  generateAndDisplay(defaultOptions);
}
```

#### 6.2.2 イベントハンドラー設定
```javascript
function setupEventListeners() {
  // 生成ボタン
  const generateBtn = document.getElementById('toolzoo-generate-btn');
  generateBtn.addEventListener('click', function() {
    const options = getOptions();
    if (validateOptions(options)) {
      generateAndDisplay(options);
    }
  });

  // スライダー
  const slider = document.getElementById('toolzoo-password-length');
  slider.addEventListener('input', function() {
    document.getElementById('toolzoo-length-value').textContent = this.value;
  });

  // チェックボックス
  document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', validateOptions);
  });
}
```

#### 6.2.3 主要関数リスト
- `initPasswordGenerator()`: 初期化
- `setupEventListeners()`: イベントリスナー設定
- `getOptions()`: ユーザー設定取得
- `validateOptions(options)`: オプションバリデーション
- `generateAndDisplay(options)`: 生成と表示
- `generatePasswordList(length, options, count)`: パスワードリスト生成
- `generatePassword(length, options)`: 単一パスワード生成
- `validatePassword(password, options)`: パスワード検証
- `renderPasswordList(passwords)`: HTML生成
- `displayPasswordList(passwords)`: DOM更新
- `attachCopyEventListeners(passwords)`: コピーイベント設定
- `copySinglePassword(password, button)`: 個別コピー
- `copyAllPasswords(passwords)`: すべてコピー
- `showCopySuccess(button)`: コピー成功表示
- `showCopyAllSuccess()`: すべてコピー成功表示
- `fallbackCopyToClipboard(text, button)`: フォールバック

### 6.3 グローバル変数
```javascript
let currentPasswords = []; // 現在表示中のパスワードリスト
```

## 7. セキュリティ考慮事項

### 7.1 乱数生成
- `crypto.getRandomValues()` 使用（暗号学的に安全）
- フォールバック時の警告表示を検討

### 7.2 XSS対策
- PHP側で `esc_html()`, `esc_attr()` 使用
- JavaScript側で適切なエスケープ処理（`escapeHtml()` 関数）
- `textContent` 使用を推奨

### 7.3 パスワードの扱い
- 生成されたパスワードはサーバーに送信しない
- ブラウザのローカルストレージに保存しない
- 完全にクライアントサイドで処理
- メモリ上に一時的に保持（変数 `currentPasswords`）

## 8. テスト項目

### 8.1 機能テスト

#### 8.1.1 パスワード生成
- [ ] 20個のパスワードが生成される
- [ ] 各文字種別単独での生成
- [ ] 複数文字種別組み合わせでの生成
- [ ] 最小文字数（8文字）での生成
- [ ] 最大文字数（64文字）での生成
- [ ] 全文字種別未選択時のバリデーション
- [ ] 生成されたパスワードに指定文字種別が含まれるか
- [ ] 生成ボタンを複数回押した場合の動作
- [ ] 各パスワードがユニークか（重複チェック）

#### 8.1.2 コピー機能
- [ ] 個別コピーボタンの動作
- [ ] すべてコピーボタンの動作
- [ ] コピー成功時のフィードバック表示
- [ ] 複数のコピーボタンを連続でクリックした場合
- [ ] クリップボードに正しい内容がコピーされるか

#### 8.1.3 UI動作
- [ ] 初期表示でパスワードが自動生成される
- [ ] スライダーで文字数が変更できる
- [ ] チェックボックスで文字種別が選択できる
- [ ] リストがスクロール可能

### 8.2 ブラウザ互換性テスト
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] クリップボードAPIの動作確認
- [ ] フォールバック実装の動作確認

### 8.3 レスポンシブテスト
- [ ] スマートフォン (320px~): リスト表示が適切
- [ ] タブレット (768px~): レイアウトが崩れない
- [ ] デスクトップ (1024px~): 見やすい表示

### 8.4 アクセシビリティテスト
- [ ] キーボード操作（Tab、Enter）
- [ ] スクリーンリーダー対応
- [ ] フォーカス表示
- [ ] ボタンのラベルが適切

### 8.5 パフォーマンステスト
- [ ] 20個生成の処理時間（1秒以内推奨）
- [ ] メモリリークがない
- [ ] スクロールが滑らか

## 9. 将来の拡張案

### 9.1 機能拡張
- **生成数のカスタマイズ**: 10個、20個、50個から選択
- **パスワード再生成**: 個別に再生成ボタンを配置
- **お気に入り機能**: 気に入ったパスワードをマーク
- **除外文字設定**: 紛らわしい文字の除外（0/O, 1/l/Iなど）
- **カスタム文字セット**: ユーザー定義の文字セット
- **パスワード強度表示**: 各パスワードの強度を視覚化
- **エクスポート機能**: CSV、テキストファイルで保存
- **印刷機能**: 印刷用のレイアウト

### 9.2 UI改善
- **検索/フィルター**: 特定の文字を含むパスワードを検索
- **ソート機能**: 文字数、強度でソート
- **ページネーション**: 大量生成時の対応
- **ダークモード**: 暗い背景での表示

### 9.3 セキュリティ強化
- **パスワード履歴**: セッションストレージで一時保存
- **暗号化**: 履歴を暗号化して保存
- **自動クリア**: 一定時間後に自動クリア

## 10. 使用例

### 10.1 基本的な使い方
1. ページにアクセスすると20個のパスワードが自動表示される
2. 気に入ったパスワードの「コピー」ボタンをクリック
3. クリップボードにコピーされたパスワードを使用

### 10.2 カスタマイズ
1. 文字数スライダーで希望の文字数を選択
2. 文字種別のチェックボックスで使用する文字を選択
3. 「パスワード生成 (20個)」ボタンをクリック
4. 新しい20個のパスワードが生成される

### 10.3 複数パスワードの取得
1. 「すべてコピー」ボタンをクリック
2. 20個すべてのパスワードがクリップボードにコピーされる
3. テキストエディタなどに貼り付けて保存
