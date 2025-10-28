# IP-CHECKER ショートコード 設計方針

## 1. 機能概要

### 1.1 機能名
IP-CHECKER（IPアドレスチェッカー）

### 1.2 ショートコード
```
[toolzoo_ip]
```

### 1.3 機能説明
アクセスしたユーザーのIPアドレスとゲートウェイ情報を表示するシンプルなツール。
ネットワーク診断やアクセス元の確認に使用できます。

### 1.4 表示内容
- アクセス元IPアドレス
- アクセス元ドメイン（DNS逆引き）
- ゲートウェイ情報
- その他のサーバー情報（オプション）

## 2. UI設計

### 2.1 画面レイアウト
```
┌─────────────────────────────────────────────┐
│   IP-CHECKER                                │
├─────────────────────────────────────────────┤
│                                             │
│ Your IP Address:  192.168.1.100             │
│                                             │
│ Your Domain:      example.local             │
│                                             │
│ Gateway:          192.168.1.1               │
│                                             │
│ User Agent:       Mozilla/5.0...            │
│                                             │
└─────────────────────────────────────────────┘
```

### 2.2 UI要素詳細

#### 2.2.1 IPアドレス表示
- **ラベル**: "Your IP Address" または "あなたのIPアドレス"
- **値**: IPv4 または IPv6アドレス
- **形式**: テキスト表示（コピー可能）
- **ID**: `toolzoo-ip-address`

#### 2.2.2 ドメイン表示
- **ラベル**: "Your Domain" または "あなたのドメイン"
- **値**: DNS逆引きで取得したドメイン名（ホスト名）
- **形式**: テキスト表示（コピー可能、取得失敗時は「Not Available」）
- **ID**: `toolzoo-ip-domain`
- **取得方法**: `gethostbyaddr()` を使用したDNS逆引き

#### 2.2.3 ゲートウェイ表示
- **ラベル**: "Gateway" または "ゲートウェイ"
- **値**: ゲートウェイインターフェース情報
- **形式**: テキスト表示
- **ID**: `toolzoo-ip-gateway`

#### 2.2.3 User Agent表示（追加情報）
- **ラベル**: "User Agent" または "ユーザーエージェント"
- **値**: ブラウザ情報
- **形式**: テキスト表示（スクロール可能）
- **ID**: `toolzoo-ip-useragent`

#### 2.2.4 その他の情報
- **Server Port**: `$_SERVER['SERVER_PORT']`
- **Request Method**: `$_SERVER['REQUEST_METHOD']`
- **Protocol**: `$_SERVER['SERVER_PROTOCOL']`

## 3. 機能仕様

### 3.1 IPアドレス検出アルゴリズム

#### 3.1.1 取得方法
```
1. クライアントのIPアドレスを取得
   - 直接接続: $_SERVER['REMOTE_ADDR']
   - プロキシ経由: $_SERVER['HTTP_X_FORWARDED_FOR']
   - CloudFlare経由: $_SERVER['HTTP_CF_CONNECTING_IP']

2. IPの検証
   - filter_var() でIP形式を検証
   - 有効なIPアドレスのみ表示

3. IPv4/IPv6の判定
   - FILTER_VALIDATE_IP で判定
   - アドレスファミリーを表示
```

#### 3.1.2 セキュアな実装
```php
function get_client_ip() {
    // CloudFlare
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    // プロキシ
    else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    }
    // 直接接続
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // IP検証
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }

    return 'Unknown';
}
```

### 3.2 ドメイン情報の取得

```php
function get_domain_by_ip($ip) {
    // DNS逆引きで取得
    $domain = gethostbyaddr($ip);

    // IP自身が返ってきた場合は取得失敗
    if ($domain === $ip) {
        return 'Not Available';
    }

    // セキュリティ: ドメイン名が正しい形式かチェック
    if (preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9]([a-zA-Z0-9\-]*[a-zA-Z0-9])?$/', $domain)) {
        return esc_html($domain);
    }

    return 'Not Available';
}
```

**注意点:**
- `gethostbyaddr()` はDNS逆引きをするため、ネットワーク遅延の可能性がある
- タイムアウト設定を検討する必要がある
- 返り値がIPアドレスと同じ場合は逆引き失敗と判定

### 3.3 ゲートウェイ情報の取得

```php
function get_gateway_info() {
    $gateway_interface = $_SERVER['GATEWAY_INTERFACE'] ?? 'Not Available';

    // ゲートウェイインターフェースの解析
    // 例: "CGI/1.1" → CGI バージョン 1.1

    return $gateway_interface;
}
```

### 3.4 その他のサーバー情報

```php
function get_server_info() {
    return array(
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Not Available',
        'server_port' => $_SERVER['SERVER_PORT'] ?? 'Not Available',
        'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
        'server_protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'Not Available',
        'remote_port' => $_SERVER['REMOTE_PORT'] ?? 'Not Available',
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Not Available',
    );
}
```

### 3.5 プロキシ検出

```php
function is_behind_proxy() {
    return !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ||
           !empty($_SERVER['HTTP_CF_CONNECTING_IP']) ||
           !empty($_SERVER['HTTP_X_REAL_IP']) ||
           !empty($_SERVER['HTTP_VIA']);
}
```

## 4. PHPクラス設計

### 4.1 クラス名
`Toolzoo_IP_Checker`

### 4.2 ファイルパス
`includes/class-ip-checker.php`

### 4.3 メソッド一覧

#### 4.3.1 __construct()
- **説明**: コンストラクタ
- **処理**: 初期化処理

#### 4.3.2 render()
- **説明**: HTMLを生成して返却
- **戻り値**: string (HTML)
- **処理**:
  - IPアドレス取得
  - ゲートウェイ情報取得
  - HTMLテンプレート生成
  - CSS/JSのエンキュー

#### 4.3.3 get_client_ip()
- **説明**: クライアントのIPアドレスを取得
- **戻り値**: string (IPアドレス or "Unknown")
- **処理**: プロキシ対応、IP検証

#### 4.3.4 get_domain_by_ip()
- **説明**: IPアドレスからドメイン名を取得
- **パラメータ**: string $ip (IPアドレス)
- **戻り値**: string (ドメイン名 or "Not Available")
- **処理**: DNS逆引き、ドメイン形式検証

#### 4.3.5 get_gateway_info()
- **説明**: ゲートウェイ情報を取得
- **戻り値**: string (ゲートウェイ情報)

#### 4.3.6 get_server_info()
- **説明**: その他のサーバー情報を取得
- **戻り値**: array

#### 4.3.7 is_ipv4()
- **説明**: IPv4アドレスかどうかを判定
- **パラメータ**: string $ip
- **戻り値**: boolean

#### 4.3.8 is_ipv6()
- **説明**: IPv6アドレスかどうかを判定
- **パラメータ**: string $ip
- **戻り値**: boolean

#### 4.3.9 is_private_ip()
- **説明**: プライベートIPかどうかを判定
- **パラメータ**: string $ip
- **戻り値**: boolean

#### 4.3.10 enqueue_assets()
- **説明**: CSS/JSを読み込み
- **処理**:
  - `wp_enqueue_style('toolzoo-ip-checker-css')`
  - `wp_enqueue_script('toolzoo-ip-checker-js')`（オプション）

### 4.4 ショートコードハンドラー
```php
function toolzoo_ip_shortcode($atts) {
    $checker = new Toolzoo_IP_Checker();
    return $checker->render();
}
add_shortcode('toolzoo_ip', 'toolzoo_ip_shortcode');
```

## 5. CSS設計

### 5.1 ファイル名
`assets/css/ip-checker.css`

### 5.2 主要クラス
- `.toolzoo-ip-checker-container`: 全体コンテナ
- `.toolzoo-ip-checker-header`: ヘッダー
- `.toolzoo-ip-checker-content`: コンテンツエリア
- `.toolzoo-ip-checker-item`: 情報アイテム
- `.toolzoo-ip-checker-label`: ラベル
- `.toolzoo-ip-checker-value`: 値表示（コピー可能）
- `.toolzoo-ip-checker-value.private`: プライベートIP用スタイル
- `.toolzoo-ip-checker-value.public`: パブリックIP用スタイル
- `.toolzoo-ip-checker-copy-btn`: コピーボタン
- `.toolzoo-ip-checker-info`: 補足情報

### 5.3 デザイン
```css
.toolzoo-ip-checker-container {
  max-width: 600px;
  margin: 20px 0;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #f9f9f9;
  font-family: Arial, sans-serif;
}

.toolzoo-ip-checker-item {
  display: flex;
  align-items: center;
  margin: 15px 0;
  padding-bottom: 15px;
  border-bottom: 1px solid #eee;
}

.toolzoo-ip-checker-label {
  min-width: 150px;
  font-weight: 600;
  color: #333;
}

.toolzoo-ip-checker-value {
  flex: 1;
  font-family: 'Courier New', monospace;
  font-size: 14px;
  color: #0066cc;
  padding: 8px;
  background: white;
  border-radius: 4px;
  border: 1px solid #ddd;
  word-break: break-all;
  user-select: all;
}

.toolzoo-ip-checker-value.private {
  color: #ff8800;
  border-color: #ffccaa;
  background: #fffbf5;
}

.toolzoo-ip-checker-value.public {
  color: #00aa00;
  border-color: #aaffaa;
  background: #f5fff5;
}

.toolzoo-ip-checker-copy-btn {
  margin-left: 10px;
  padding: 6px 12px;
  font-size: 12px;
  background: #0066cc;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.2s;
}

.toolzoo-ip-checker-copy-btn:hover {
  background: #0052a3;
}
```

## 6. JavaScript設計

### 6.1 ファイル名
`assets/js/ip-checker.js`（オプション）

### 6.2 機能
- IPアドレスのコピー機能
- User Agentのコピー機能
- コピー成功時のフィードバック表示

```javascript
document.addEventListener('DOMContentLoaded', function() {
  setupCopyButtons();
});

function setupCopyButtons() {
  document.querySelectorAll('.toolzoo-ip-checker-copy-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const value = this.previousElementSibling.textContent;
      copyToClipboard(value, this);
    });
  });
}

async function copyToClipboard(text, button) {
  try {
    await navigator.clipboard.writeText(text);
    showCopySuccess(button);
  } catch (err) {
    fallbackCopyToClipboard(text, button);
  }
}

function showCopySuccess(button) {
  const originalText = button.textContent;
  button.textContent = 'Copied!';
  button.classList.add('copied');

  setTimeout(() => {
    button.textContent = originalText;
    button.classList.remove('copied');
  }, 2000);
}
```

## 7. HTML構造

### 7.1 基本構造
```html
<div class="toolzoo-ip-checker-container">
  <div class="toolzoo-ip-checker-header">
    <h3>IP-CHECKER</h3>
  </div>

  <div class="toolzoo-ip-checker-content">
    <div class="toolzoo-ip-checker-item">
      <span class="toolzoo-ip-checker-label">Your IP Address:</span>
      <span class="toolzoo-ip-checker-value public">192.168.1.100</span>
      <button class="toolzoo-ip-checker-copy-btn">Copy</button>
    </div>

    <div class="toolzoo-ip-checker-item">
      <span class="toolzoo-ip-checker-label">Your Domain:</span>
      <span class="toolzoo-ip-checker-value">example.local</span>
      <button class="toolzoo-ip-checker-copy-btn">Copy</button>
    </div>

    <div class="toolzoo-ip-checker-item">
      <span class="toolzoo-ip-checker-label">Gateway:</span>
      <span class="toolzoo-ip-checker-value">CGI/1.1</span>
      <button class="toolzoo-ip-checker-copy-btn">Copy</button>
    </div>

    <div class="toolzoo-ip-checker-item">
      <span class="toolzoo-ip-checker-label">User Agent:</span>
      <span class="toolzoo-ip-checker-value">Mozilla/5.0...</span>
      <button class="toolzoo-ip-checker-copy-btn">Copy</button>
    </div>

    <div class="toolzoo-ip-checker-item">
      <span class="toolzoo-ip-checker-label">Server Port:</span>
      <span class="toolzoo-ip-checker-value">443</span>
    </div>

    <div class="toolzoo-ip-checker-item">
      <span class="toolzoo-ip-checker-label">Request Method:</span>
      <span class="toolzoo-ip-checker-value">GET</span>
    </div>
  </div>

  <div class="toolzoo-ip-checker-info">
    <p>Note: This tool displays your network connection information for diagnostic purposes.</p>
  </div>
</div>
```

## 8. セキュリティ考慮事項

### 8.1 IPアドレス情報の取り扱い
- **検証**: フィルターを使用してIP形式を検証
- **ログ**: 取得したIPアドレスをログに記録しない
- **キャッシュ**: 動的に生成し、キャッシュしない

### 8.2 プロキシ検出
- CloudFlare、AWS等のプロキシを適切に処理
- HTTPヘッダーの信頼性を考慮

### 8.3 プライベートIP判定
- RFC1918（プライベートアドレス範囲）を判定
  - 10.0.0.0/8
  - 172.16.0.0/12
  - 192.168.0.0/16
  - 127.0.0.0/8（ループバック）
  - ::1/128（IPv6ループバック）
  - fc00::/7（IPv6プライベート）

### 8.4 XSS対策
- PHP側で `esc_html()`, `esc_attr()` 使用
- JavaScript側で `textContent` 使用

### 8.5 ユーザーエージェント
- ユーザーエージェントはユーザーが送信した情報なので、エスケープして表示

## 9. テスト項目

### 9.1 機能テスト

#### 9.1.1 IP取得
- [ ] IPv4アドレスが正しく表示される
- [ ] IPv6アドレスが正しく表示される
- [ ] プロキシ経由でのアクセスで正しいIPが取得される
- [ ] CloudFlareを経由している場合の処理
- [ ] 無効なIPアドレスが表示されない

#### 9.1.2 ドメイン取得
- [ ] DNS逆引きでドメイン名が取得される
- [ ] DNS逆引き失敗時は「Not Available」と表示される
- [ ] ドメイン名が正しくエスケープされている
- [ ] ドメイン形式が正しく検証されている
- [ ] IPv6アドレスのドメインも取得できる

#### 9.1.3 ゲートウェイ表示
- [ ] ゲートウェイ情報が表示される
- [ ] 利用不可の場合は「Not Available」と表示

#### 9.1.4 User Agent表示
- [ ] ユーザーエージェントが正しく表示される
- [ ] 特殊文字が正しくエスケープされている

#### 9.1.5 その他の情報
- [ ] サーバーポート番号が表示される
- [ ] リクエストメソッドが表示される
- [ ] サーバープロトコルが表示される

#### 9.1.6 プライベートIP判定
- [ ] 192.168.x.x がプライベートIPとして表示される
- [ ] 10.x.x.x がプライベートIPとして表示される
- [ ] 172.16.x.x ～ 172.31.x.x がプライベートIPとして表示される
- [ ] 127.x.x.x（ループバック）が判定される

#### 9.1.7 コピー機能
- [ ] IPアドレスをコピーできる
- [ ] ドメイン名をコピーできる
- [ ] User Agentをコピーできる
- [ ] コピー成功時にボタンテキストが変更される

### 9.2 セキュリティテスト
- [ ] XSS脆弱性がない
- [ ] SQLインジェクションの危険性がない（PHPのみ処理）
- [ ] IPアドレスが適切に検証されている

### 9.3 ブラウザ互換性テスト
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] モバイルブラウザ

### 9.4 レスポンシブテスト
- [ ] スマートフォン (320px~)
- [ ] タブレット (768px~)
- [ ] デスクトップ (1024px~)

### 9.5 環境テスト
- [ ] WordPress 管理画面で表示確認
- [ ] 記事・ページに埋め込み
- [ ] キャッシュプラグイン有効時の動作

## 10. 将来の拡張案

### 10.1 機能拡張
- **IP地理情報**: IPアドレスから国、都市を表示
- **DNS逆引き**: ホスト名の取得と表示
- **IPランクチェック**: ブラックリスト確認
- **IPレポート**: アクセスログの記録と統計
- **VPN検出**: VPN接続の検出
- **VPN/プロキシ判定**: 接続タイプの判定

### 10.2 UI改善
- **リアルタイム更新**: ページリロード時の更新
- **QRコード**: IPアドレスのQRコード表示
- **地図表示**: GeoIPを使用した地図表示
- **JSON形式**: JSON形式でのエクスポート

### 10.3 管理機能
- **アクセスログ記録**: データベースにアクセスを記録
- **ログ分析**: アクセス統計の表示
- **制限設定**: 表示可能なユーザーの制限

## 11. 使用例

### 11.1 基本的な使い方
1. ページに `[toolzoo_ip]` を挿入
2. IPアドレス、ゲートウェイ、ユーザーエージェント等が表示される
3. 必要に応じてコピーボタンでテキストをコピー

### 11.2 応用例
- **ネットワーク診断ページ**: 複数の診断ツール（IP確認、DNS等）をまとめたページ
- **サポートページ**: ユーザーサポート時に環境情報を提供
- **デバッグページ**: 開発者向けの環境確認ページ

## 12. 実装順序

1. ✅ この設計方針書を作成
2. ⬜ `class-ip-checker.php` を実装
3. ⬜ `assets/css/ip-checker.css` を実装
4. ⬜ `assets/js/ip-checker.js` を実装（オプション）
5. ⬜ `class-constants.php` にツール定義を追加
6. ⬜ `class-toolzoo.php` にショートコード登録
7. ⬜ `languages/toolzoo.pot`, `toolzoo-ja.po` に翻訳追加
