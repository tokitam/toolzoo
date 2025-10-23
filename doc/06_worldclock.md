# 世界時計機能 詳細設計書

## 1. 機能概要

### 1.1 ショートコード名
`[toolzoo_worldclock]`

### 1.2 目的
世界の主要30都市の現在時刻をリアルタイムで表示するツール。ユーザーのタイムゾーンを基準に、東回りで都市を並べ替えて表示する。

### 1.3 利用シーン
- グローバルビジネスサイト
- 国際会議・イベントページ
- 海外取引先との連絡時の時差確認
- 旅行・観光サイト

### 1.4 主な特徴
- **30都市対応**: 世界の主要都市の時刻を表示
- **リアルタイム更新**: JavaScriptで毎秒更新
- **自動ソート**: ユーザーのタイムゾーンから東回りに並べ替え
- **見やすい表示**: 都市名、国名、現在時刻、日付、GMT差分を表示
- **レスポンシブ**: モバイル・タブレット対応

## 2. 表示する都市一覧（30都市）

| 都市名 | 国名 | タイムゾーン | GMT差分 |
|--------|------|-------------|---------|
| ロンドン | イギリス | Europe/London | GMT+0/+1 |
| ニューヨーク | アメリカ | America/New_York | GMT-5/-4 |
| 東京 | 日本 | Asia/Tokyo | GMT+9 |
| パリ | フランス | Europe/Paris | GMT+1/+2 |
| シンガポール | シンガポール | Asia/Singapore | GMT+8 |
| アムステルダム | オランダ | Europe/Amsterdam | GMT+1/+2 |
| ソウル | 韓国 | Asia/Seoul | GMT+9 |
| ドバイ | アラブ首長国連邦 | Asia/Dubai | GMT+4 |
| メルボルン | オーストラリア | Australia/Melbourne | GMT+10/+11 |
| ベルリン | ドイツ | Europe/Berlin | GMT+1/+2 |
| コペンハーゲン | デンマーク | Europe/Copenhagen | GMT+1/+2 |
| シドニー | オーストラリア | Australia/Sydney | GMT+10/+11 |
| ウィーン | オーストリア | Europe/Vienna | GMT+1/+2 |
| マドリード | スペイン | Europe/Madrid | GMT+1/+2 |
| 上海 | 中国 | Asia/Shanghai | GMT+8 |
| ストックホルム | スウェーデン | Europe/Stockholm | GMT+1/+2 |
| 北京 | 中国 | Asia/Shanghai | GMT+8 |
| 香港 | 中国 | Asia/Hong_Kong | GMT+8 |
| チューリッヒ | スイス | Europe/Zurich | GMT+1/+2 |
| フランクフルト | ドイツ | Europe/Berlin | GMT+1/+2 |
| ロサンゼルス | アメリカ | America/Los_Angeles | GMT-8/-7 |
| バルセロナ | スペイン | Europe/Madrid | GMT+1/+2 |
| トロント | カナダ | America/Toronto | GMT-5/-4 |
| ブリュッセル | ベルギー | Europe/Brussels | GMT+1/+2 |
| シカゴ | アメリカ | America/Chicago | GMT-6/-5 |
| ジュネーブ | スイス | Europe/Zurich | GMT+1/+2 |
| サンフランシスコ | アメリカ | America/Los_Angeles | GMT-8/-7 |
| ダブリン | アイルランド | Europe/Dublin | GMT+0/+1 |
| ボストン | アメリカ | America/New_York | GMT-5/-4 |
| イスタンブール | トルコ | Europe/Istanbul | GMT+3 |

## 3. UI/UX設計

### 3.1 レイアウト構成

```
┌─────────────────────────────────────────────────────────────┐
│                     世界時計                                  │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ 🕐 東京（日本）                                       │   │
│  │    現在時刻: 15:30:45                                │   │
│  │    日付: 2025年10月23日                              │   │
│  │    GMT+9                                             │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ 🕐 ソウル（韓国）                                     │   │
│  │    現在時刻: 15:30:45                                │   │
│  │    日付: 2025年10月23日                              │   │
│  │    GMT+9                                             │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ 🕐 シンガポール（シンガポール）                       │   │
│  │    現在時刻: 14:30:45                                │   │
│  │    日付: 2025年10月23日                              │   │
│  │    GMT+8                                             │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ... (27都市続く)                                           │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 3.2 各都市カードの構成

```
┌─────────────────────────────────────────┐
│ 🕐 東京（日本）             ← 時計アイコン + 都市名（国名）
├─────────────────────────────────────────┤
│ 現在時刻: 15:30:45          ← HH:MM:SS形式
│ 日付: 2025年10月23日        ← YYYY年MM月DD日形式
│ GMT+9                       ← GMT差分
└─────────────────────────────────────────┘
```

### 3.3 表示順序のロジック

1. **ユーザーのタイムゾーンを取得**
   - JavaScriptで `Intl.DateTimeFormat().resolvedOptions().timeZone` を使用

2. **ユーザーの都市を一番上に表示**
   - 30都市の中にユーザーのタイムゾーンと一致する都市があれば最上位に

3. **東回りでソート**
   - ユーザーのGMT差分を基準に、東側（+方向）から順に表示
   - GMT差分が同じ場合は都市名の五十音順

### 3.4 リアルタイム更新

- JavaScriptで毎秒（1000ms）更新
- `setInterval` を使用して時刻を更新
- 日付が変わった場合も自動的に更新

## 4. 技術仕様

### 4.1 PHPクラス設計

#### 4.1.1 クラス名
`Toolzoo_Worldclock`

#### 4.1.2 ファイルパス
`includes/class-worldclock.php`（新規作成）

#### 4.1.3 クラス構造

```php
<?php
/**
 * World Clock Class
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_Worldclock class
 */
class Toolzoo_Worldclock {
    /**
     * Generate HTML output
     *
     * @return string HTML output
     */
    public function render() {
        // Enqueue CSS/JS
        $this->enqueue_assets();

        // Generate HTML
        ob_start();
        ?>
        <div class="toolzoo-worldclock-container" id="toolzoo-worldclock">
            <div class="toolzoo-worldclock-header">
                <h3><?php esc_html_e('World Clock', 'toolzoo'); ?></h3>
                <p class="toolzoo-worldclock-description">
                    <?php esc_html_e('Current time in 30 major cities around the world, sorted from your timezone eastward.', 'toolzoo'); ?>
                </p>
            </div>

            <div class="toolzoo-worldclock-list" id="toolzoo-worldclock-list">
                <!-- Dynamically generated by JavaScript -->
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get cities list
     *
     * @return array Array of city information
     */
    private function get_cities_list() {
        return array(
            array(
                'city'     => __('London', 'toolzoo'),
                'country'  => __('United Kingdom', 'toolzoo'),
                'timezone' => 'Europe/London',
            ),
            array(
                'city'     => __('New York', 'toolzoo'),
                'country'  => __('United States', 'toolzoo'),
                'timezone' => 'America/New_York',
            ),
            array(
                'city'     => __('Tokyo', 'toolzoo'),
                'country'  => __('Japan', 'toolzoo'),
                'timezone' => 'Asia/Tokyo',
            ),
            // ... 残りの27都市
        );
    }

    /**
     * Enqueue CSS/JS
     */
    private function enqueue_assets() {
        // CSS
        wp_enqueue_style(
            'toolzoo-worldclock-css',
            TOOLZOO_PLUGIN_URL . 'assets/css/worldclock.css',
            array(),
            TOOLZOO_VERSION
        );

        // JavaScript
        wp_enqueue_script(
            'toolzoo-worldclock-js',
            TOOLZOO_PLUGIN_URL . 'assets/js/worldclock.js',
            array(),
            TOOLZOO_VERSION,
            true
        );

        // Pass cities data to JavaScript
        wp_localize_script(
            'toolzoo-worldclock-js',
            'toolzooWorldclockData',
            array(
                'cities' => $this->get_cities_list(),
                'labels' => array(
                    'currentTime' => __('Current Time:', 'toolzoo'),
                    'date'        => __('Date:', 'toolzoo'),
                ),
            )
        );
    }
}
```

### 4.2 JavaScriptロジック

#### 4.2.1 ファイル名
`assets/js/worldclock.js`（新規作成）

#### 4.2.2 主要機能

```javascript
/**
 * World Clock JavaScript
 */
(function() {
    'use strict';

    // Cities data (from PHP via wp_localize_script)
    const cities = toolzooWorldclockData.cities;
    const labels = toolzooWorldclockData.labels;

    // Get user's timezone
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    /**
     * Get GMT offset for a timezone
     */
    function getGMTOffset(timezone) {
        const now = new Date();
        const formatter = new Intl.DateTimeFormat('en', {
            timeZone: timezone,
            timeZoneName: 'short'
        });

        const parts = formatter.formatToParts(now);
        const timeZoneName = parts.find(part => part.type === 'timeZoneName').value;

        // Parse GMT offset from timezone name (e.g., "GMT+9")
        const match = timeZoneName.match(/GMT([+-]\d+)/);
        return match ? parseInt(match[1]) : 0;
    }

    /**
     * Sort cities by GMT offset (eastward from user's timezone)
     */
    function sortCities(cities) {
        const userOffset = getGMTOffset(userTimezone);

        return cities.slice().sort((a, b) => {
            const offsetA = getGMTOffset(a.timezone);
            const offsetB = getGMTOffset(b.timezone);

            // Calculate relative offset from user
            let relativeA = offsetA - userOffset;
            let relativeB = offsetB - userOffset;

            // Normalize to 0-23 range (eastward)
            if (relativeA < 0) relativeA += 24;
            if (relativeB < 0) relativeB += 24;

            // Sort by relative offset (ascending for eastward)
            if (relativeA !== relativeB) {
                return relativeA - relativeB;
            }

            // If same offset, sort by city name
            return a.city.localeCompare(b.city);
        });
    }

    /**
     * Format time for a timezone
     */
    function formatTime(timezone) {
        const now = new Date();

        const timeFormatter = new Intl.DateTimeFormat('ja-JP', {
            timeZone: timezone,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });

        const dateFormatter = new Intl.DateTimeFormat('ja-JP', {
            timeZone: timezone,
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        return {
            time: timeFormatter.format(now),
            date: dateFormatter.format(now)
        };
    }

    /**
     * Get GMT offset string
     */
    function getGMTOffsetString(timezone) {
        const offset = getGMTOffset(timezone);
        const sign = offset >= 0 ? '+' : '';
        return `GMT${sign}${offset}`;
    }

    /**
     * Render city card
     */
    function renderCityCard(city) {
        const timeInfo = formatTime(city.timezone);
        const gmtOffset = getGMTOffsetString(city.timezone);
        const isUserCity = city.timezone === userTimezone;

        return `
            <div class="toolzoo-worldclock-card ${isUserCity ? 'toolzoo-worldclock-current' : ''}">
                <div class="toolzoo-worldclock-city-header">
                    <span class="toolzoo-worldclock-icon">🕐</span>
                    <h4 class="toolzoo-worldclock-city-name">
                        ${city.city}（${city.country}）
                        ${isUserCity ? '<span class="toolzoo-worldclock-badge">現在地</span>' : ''}
                    </h4>
                </div>
                <div class="toolzoo-worldclock-time-info">
                    <div class="toolzoo-worldclock-time">
                        ${labels.currentTime} <strong>${timeInfo.time}</strong>
                    </div>
                    <div class="toolzoo-worldclock-date">
                        ${labels.date} ${timeInfo.date}
                    </div>
                    <div class="toolzoo-worldclock-gmt">
                        ${gmtOffset}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Update all clocks
     */
    function updateClocks() {
        const container = document.getElementById('toolzoo-worldclock-list');
        if (!container) return;

        const sortedCities = sortCities(cities);
        container.innerHTML = sortedCities.map(city => renderCityCard(city)).join('');
    }

    /**
     * Initialize
     */
    function init() {
        // Initial render
        updateClocks();

        // Update every second
        setInterval(updateClocks, 1000);
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
```

### 4.3 CSS設計

#### 4.3.1 ファイル名
`assets/css/worldclock.css`（新規作成）

#### 4.3.2 主要スタイル

```css
/**
 * World Clock Styles
 */

/* ===================================
   Container
   =================================== */
.toolzoo-worldclock-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* ===================================
   Header
   =================================== */
.toolzoo-worldclock-header {
    text-align: center;
    margin-bottom: 30px;
}

.toolzoo-worldclock-header h3 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 10px 0;
    color: #333;
}

.toolzoo-worldclock-description {
    font-size: 14px;
    color: #666;
    margin: 0;
}

/* ===================================
   City List
   =================================== */
.toolzoo-worldclock-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

/* ===================================
   City Card
   =================================== */
.toolzoo-worldclock-card {
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 16px;
    transition: all 0.3s ease;
}

.toolzoo-worldclock-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

/* Current location highlight */
.toolzoo-worldclock-current {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-color: #667eea;
}

.toolzoo-worldclock-current .toolzoo-worldclock-city-name,
.toolzoo-worldclock-current .toolzoo-worldclock-time-info {
    color: #fff;
}

/* ===================================
   City Header
   =================================== */
.toolzoo-worldclock-city-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.toolzoo-worldclock-icon {
    font-size: 24px;
    line-height: 1;
}

.toolzoo-worldclock-city-name {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

.toolzoo-worldclock-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.3);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

/* ===================================
   Time Info
   =================================== */
.toolzoo-worldclock-time-info {
    font-size: 14px;
    line-height: 1.8;
    color: #555;
}

.toolzoo-worldclock-time strong {
    font-size: 18px;
    font-weight: 700;
    color: #333;
}

.toolzoo-worldclock-current .toolzoo-worldclock-time strong {
    color: #fff;
}

.toolzoo-worldclock-gmt {
    margin-top: 4px;
    font-size: 12px;
    font-weight: 500;
    color: #999;
}

.toolzoo-worldclock-current .toolzoo-worldclock-gmt {
    color: rgba(255, 255, 255, 0.8);
}

/* ===================================
   Responsive
   =================================== */
@media screen and (max-width: 768px) {
    .toolzoo-worldclock-list {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 12px;
    }

    .toolzoo-worldclock-card {
        padding: 12px;
    }
}

@media screen and (max-width: 480px) {
    .toolzoo-worldclock-list {
        grid-template-columns: 1fr;
    }

    .toolzoo-worldclock-header h3 {
        font-size: 24px;
    }
}

/* ===================================
   Accessibility
   =================================== */
@media (prefers-reduced-motion: reduce) {
    .toolzoo-worldclock-card {
        transition: none;
    }
}

@media (prefers-contrast: high) {
    .toolzoo-worldclock-card {
        border: 3px solid #000;
    }
}
```

## 5. ショートコード登録

`includes/class-toolzoo.php` に追加：

```php
/**
 * Register shortcodes
 */
private function register_shortcodes() {
    add_shortcode('toolzoo_password', array($this, 'password_shortcode'));
    add_shortcode('toolzoo_nengo', array($this, 'nengo_shortcode'));
    add_shortcode('toolzoo_all', array($this, 'all_shortcode'));
    add_shortcode('toolzoo_worldclock', array($this, 'worldclock_shortcode')); // 追加
}

/**
 * World clock shortcode
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
public function worldclock_shortcode($atts) {
    $worldclock = new Toolzoo_Worldclock();
    return $worldclock->render();
}
```

## 6. class-all-shortcode.php への追加

ツール一覧に世界時計を追加：

```php
private function get_tools_list() {
    return array(
        array(
            'id'          => 'password',
            'name'        => __('Password Generator', 'toolzoo'),
            'description' => __('A tool that generates 20 random passwords at once...', 'toolzoo'),
            'icon'        => '🔒',
            'slug'        => 'password',
        ),
        array(
            'id'          => 'nengo',
            'name'        => __('Japanese Era List', 'toolzoo'),
            'description' => __('Displays a correspondence table...', 'toolzoo'),
            'icon'        => '📅',
            'slug'        => 'nengo',
        ),
        array(
            'id'          => 'worldclock',
            'name'        => __('World Clock', 'toolzoo'),
            'description' => __('Displays current time in 30 major cities around the world. Automatically sorted from your timezone eastward. Updates every second.', 'toolzoo'),
            'icon'        => '🌍',
            'slug'        => 'worldclock',
        ),
    );
}
```

```php
private function render_single_tool($tool_id) {
    // ... existing code ...

    switch ($tool_id) {
        case 'password':
            $generator = new Toolzoo_Password_Generator();
            return $generator->render();

        case 'nengo':
            $list = new Toolzoo_Nengo_List();
            return $list->render();

        case 'worldclock':
            $worldclock = new Toolzoo_Worldclock();
            return $worldclock->render();

        default:
            return '<p>' . esc_html__('Tool not available.', 'toolzoo') . '</p>';
    }
}
```

## 7. 翻訳ファイル

`languages/toolzoo-ja.po` に追加：

```po
#: includes/class-worldclock.php
msgid "World Clock"
msgstr "世界時計"

msgid "Current time in 30 major cities around the world, sorted from your timezone eastward."
msgstr "世界の主要30都市の現在時刻を表示します。あなたのタイムゾーンから東回りに並べ替えられます。"

msgid "Current Time:"
msgstr "現在時刻:"

msgid "Date:"
msgstr "日付:"

msgid "London"
msgstr "ロンドン"

msgid "United Kingdom"
msgstr "イギリス"

msgid "New York"
msgstr "ニューヨーク"

msgid "United States"
msgstr "アメリカ"

msgid "Tokyo"
msgstr "東京"

msgid "Japan"
msgstr "日本"

# ... 残りの27都市
```

## 8. セキュリティ考慮事項

### 8.1 出力のエスケープ
- すべてのテキスト出力: `esc_html()`
- 翻訳関数を使用: `__()`

### 8.2 JavaScriptのセキュリティ
- `wp_localize_script()` でデータを安全に渡す
- ユーザー入力は受け付けない（時刻は全てJavaScriptで生成）

## 9. パフォーマンス最適化

### 9.1 効率的な更新
- 必要な要素のみを更新
- `setInterval` を1つだけ使用
- メモリリークを防ぐ

### 9.2 初期表示の高速化
- CSSは最小限
- JavaScriptは非同期読み込み

## 10. アクセシビリティ

### 10.1 セマンティックHTML
- 適切な見出しレベル
- リストまたはグリッド構造

### 10.2 リアルタイム更新の配慮
- スクリーンリーダーに過度な更新を通知しない
- `aria-live="off"` を使用

## 11. テスト項目

### 11.1 機能テスト
- [ ] 30都市すべてが表示される
- [ ] 時刻が毎秒更新される
- [ ] ユーザーのタイムゾーンが最上位に表示される
- [ ] 東回りに正しく並べ替えられる
- [ ] GMT差分が正しく表示される
- [ ] 日付が正しく表示される

### 11.2 表示テスト
- [ ] デスクトップで正しく表示される
- [ ] タブレットで正しく表示される
- [ ] スマートフォンで正しく表示される

### 11.3 タイムゾーンテスト
- [ ] 日本から見て正しく動作する
- [ ] アメリカから見て正しく動作する
- [ ] ヨーロッパから見て正しく動作する

## 12. 実装手順

### Phase 1: 基本構造の作成
1. `includes/class-worldclock.php` ファイル作成
2. 基本的なクラス構造とメソッド実装
3. ショートコード登録

### Phase 2: JavaScript実装
1. `assets/js/worldclock.js` ファイル作成
2. タイムゾーン処理の実装
3. ソート機能の実装
4. リアルタイム更新機能の実装

### Phase 3: CSS実装
1. `assets/css/worldclock.css` ファイル作成
2. グリッドレイアウトの実装
3. カードスタイリング
4. レスポンシブ対応

### Phase 4: 統合とテスト
1. ツール一覧への追加
2. 翻訳ファイルの更新
3. 動作確認

## 13. 使用例

### 13.1 基本的な使用

```
[toolzoo_worldclock]
```

### 13.2 固定ページへの追加

固定ページ「世界時計」を作成：
- タイトル: 世界時計
- スラッグ: `worldclock`
- 親ページ: ToolZoo
- 本文: `[toolzoo_worldclock]`
- URL: `/toolzoo/worldclock/`

## 14. 将来の拡張案

### 14.1 機能追加
- **都市の追加/削除**: 管理画面から都市を追加・削除
- **12時間表示/24時間表示の切り替え**: ユーザー設定
- **検索機能**: 都市名で検索
- **お気に入り機能**: よく見る都市をピン留め
- **タイムゾーンコンバーター**: 特定の時刻を複数都市で表示

### 14.2 UI改善
- **アナログ時計表示**: デジタル/アナログ切り替え
- **カラーコーディング**: 昼/夜で色分け
- **天気情報**: 天気APIと連携

## 15. 関連ドキュメント

- [01_overview.md](./01_overview.md) - プラグイン全体の概要
- [02_password.md](./02_password.md) - パスワード生成機能の詳細
- [03_nengo.md](./03_nengo.md) - 年号一覧機能の詳細
- [04_admin_page.md](./04_admin_page.md) - 管理画面ページの詳細
- [05_toolzoo_all_shortcode.md](./05_toolzoo_all_shortcode.md) - ツール一覧ショートコードの詳細

## 16. まとめ

`[toolzoo_worldclock]` ショートコードは、世界の主要30都市の現在時刻をリアルタイムで表示する便利なツールです。

**主な特徴:**
- 30都市の時刻表示
- ユーザーのタイムゾーンから東回りに自動ソート
- 毎秒リアルタイム更新
- レスポンシブデザイン
- 見やすいカード形式

**使用例:**
```
[toolzoo_worldclock]
```

このツールにより、グローバルなビジネスや国際的なコミュニケーションが容易になります。
