# 年号表示機能 詳細設計

## 1. 機能概要

### 1.1 機能名
年号一覧表示ツール (Japanese Era List / Nengo)

### 1.2 ショートコード
```
[toolzoo_nengo]
```

### 1.3 機能説明
1868年（明治元年）から2025年までの西暦と日本の年号（明治・大正・昭和・平成・令和）を一覧表で表示するツール。
各年の西暦と対応する年号を並べて表示し、日本の年号を簡単に確認できる。

### 1.4 表示内容
- 対象期間: 1868年～2025年（158年分）
- 表示項目: 西暦、明治、大正、昭和、平成、令和
- 該当しない年号は空白または「-」で表示

## 2. UI設計

### 2.1 画面レイアウト
```
┌─────────────────────────────────────────────────────────┐
│   年号一覧表                                             │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ 西暦  │ 明治  │ 大正  │ 昭和  │ 平成  │ 令和           │
│───────┼───────┼───────┼───────┼───────┼──────────────│
│ 1868  │ 元年  │   -   │   -   │   -   │   -          │
│ 1869  │  2    │   -   │   -   │   -   │   -          │
│ 1870  │  3    │   -   │   -   │   -   │   -          │
│  ...  │  ...  │  ...  │  ...  │  ...  │  ...         │
│ 1912  │  45   │ 元年  │   -   │   -   │   -          │
│ 1913  │   -   │  2    │   -   │   -   │   -          │
│  ...  │  ...  │  ...  │  ...  │  ...  │  ...         │
│ 1926  │   -   │  15   │ 元年  │   -   │   -          │
│ 1927  │   -   │   -   │  2    │   -   │   -          │
│  ...  │  ...  │  ...  │  ...  │  ...  │  ...         │
│ 1989  │   -   │   -   │  64   │ 元年  │   -          │
│ 1990  │   -   │   -   │   -   │  2    │   -          │
│  ...  │  ...  │  ...  │  ...  │  ...  │  ...         │
│ 2019  │   -   │   -   │   -   │  31   │ 元年         │
│ 2020  │   -   │   -   │   -   │   -   │  2           │
│  ...  │  ...  │  ...  │  ...  │  ...  │  ...         │
│ 2025  │   -   │   -   │   -   │   -   │  7           │
│                                                         │
│                                           [ページトップへ] │
└─────────────────────────────────────────────────────────┘
```

### 2.2 UI要素詳細

#### 2.2.1 テーブル構造
- **ID**: `toolzoo-nengo-list-table`
- **カラム数**: 6列（西暦、明治、大正、昭和、平成、令和）
- **行数**: 158行（1868年～2025年）+ ヘッダー行
- **スタイル**: 固定ヘッダー（スクロール時もヘッダーが見える）

#### 2.2.2 テーブルヘッダー
| カラム | 幅 | 説明 |
|--------|-----|------|
| 西暦 | 80px | 西暦年（例: 2024） |
| 明治 | 60px | 明治年号（例: 33） |
| 大正 | 60px | 大正年号（例: 9） |
| 昭和 | 60px | 昭和年号（例: 25） |
| 平成 | 60px | 平成年号（例: 12） |
| 令和 | 60px | 令和年号（例: 6） |

**合計幅**: 約380px（モバイルでも横スクロール可能）

#### 2.2.3 データ表示ルール
1. **元年表示**: 各年号の開始年は「元年」と表示
2. **2年目以降**: 年号年を数値のみで表示（例: 2, 3, 4...）
3. **該当なし**: 「-」または空白セル
4. **改元年**: 複数年号が該当する年は両方表示
   - 例: 1912年 → 明治「45」、大正「元年」
   - 例: 2019年 → 平成「31」、令和「元年」

#### 2.2.4 スタイリング
- **交互行背景色**: 見やすさ向上のためストライプ表示
- **現在年ハイライト**: 現在の年を目立たせる（背景色または枠線）
- **固定ヘッダー**: スクロール時もカラム名が見える
- **中央寄せ**: 年号列は中央寄せ
- **右寄せ**: 西暦列は右寄せ

#### 2.2.5 ナビゲーション機能
- **ページトップへボタン**: リストの下部に配置
- **年代ジャンプ機能（オプション）**:
  - 特定の年代に素早く移動
  - 例: [明治] [大正] [昭和] [平成] [令和] ボタン

## 3. 機能仕様

### 3.1 年号データ

#### 3.1.1 年号期間定義
```javascript
const eraData = [
  {
    name: '明治',
    nameEn: 'Meiji',
    startYear: 1868,
    endYear: 1912
  },
  {
    name: '大正',
    nameEn: 'Taisho',
    startYear: 1912,
    endYear: 1926
  },
  {
    name: '昭和',
    nameEn: 'Showa',
    startYear: 1926,
    endYear: 1989
  },
  {
    name: '平成',
    nameEn: 'Heisei',
    startYear: 1989,
    endYear: 2019
  },
  {
    name: '令和',
    nameEn: 'Reiwa',
    startYear: 2019,
    endYear: null  // 現在進行中
  }
];
```

#### 3.1.2 改元年の扱い
改元があった年は、両方の年号を表示する:

| 西暦 | 状況 | 表示内容 |
|------|------|----------|
| 1912年 | 明治45年 / 大正元年 | 明治: 45、大正: 元年 |
| 1926年 | 大正15年 / 昭和元年 | 大正: 15、昭和: 元年 |
| 1989年 | 昭和64年 / 平成元年 | 昭和: 64、平成: 元年 |
| 2019年 | 平成31年 / 令和元年 | 平成: 31、令和: 元年 |

**注**: 実際には年途中で改元されているが、このツールでは簡略化のため年単位で表示

### 3.2 データ生成アルゴリズム

#### 3.2.1 処理フロー
```
1. 表示年範囲を設定（1868-2025）
   ↓
2. 各年について年号データを生成
   ↓
3. テーブルHTMLを生成
   ↓
4. DOMに挿入
   ↓
5. スタイル適用・現在年ハイライト
```

#### 3.2.2 データ生成関数（擬似コード）
```javascript
function generateNengoList(startYear, endYear) {
  const rows = [];

  for (let year = startYear; year <= endYear; year++) {
    const row = {
      seireki: year,
      meiji: calculateNengo(year, 'meiji'),
      taisho: calculateNengo(year, 'taisho'),
      showa: calculateNengo(year, 'showa'),
      heisei: calculateNengo(year, 'heisei'),
      reiwa: calculateNengo(year, 'reiwa')
    };
    rows.push(row);
  }

  return rows;
}

function calculateNengo(year, eraName) {
  const era = eraData.find(e => e.nameEn.toLowerCase() === eraName);

  if (!era) return '-';

  // 年号範囲外の場合
  if (year < era.startYear || (era.endYear && year > era.endYear)) {
    return '-';
  }

  // 年号年を計算
  const nengoYear = year - era.startYear + 1;

  // 元年表示
  if (nengoYear === 1) {
    return '元年';
  }

  return nengoYear.toString();
}

function renderTable(rows) {
  let html = '<table class="toolzoo-nengo-list-table">';

  // ヘッダー
  html += '<thead><tr>';
  html += '<th>西暦</th>';
  html += '<th>明治</th>';
  html += '<th>大正</th>';
  html += '<th>昭和</th>';
  html += '<th>平成</th>';
  html += '<th>令和</th>';
  html += '</tr></thead>';

  // ボディ
  html += '<tbody>';
  const currentYear = new Date().getFullYear();

  for (const row of rows) {
    const isCurrentYear = row.seireki === currentYear;
    const rowClass = isCurrentYear ? ' class="current-year"' : '';

    html += `<tr${rowClass}>`;
    html += `<td class="seireki">${row.seireki}</td>`;
    html += `<td class="nengo">${row.meiji}</td>`;
    html += `<td class="nengo">${row.taisho}</td>`;
    html += `<td class="nengo">${row.showa}</td>`;
    html += `<td class="nengo">${row.heisei}</td>`;
    html += `<td class="nengo">${row.reiwa}</td>`;
    html += '</tr>';
  }

  html += '</tbody>';
  html += '</table>';

  return html;
}
```

### 3.3 初期表示

#### 3.3.1 デフォルト動作
- ページ読み込み時に全データを表示
- 現在年を自動でハイライト
- 現在年付近が画面中央に来るようにスクロール位置を調整

```javascript
document.addEventListener('DOMContentLoaded', function() {
  initNengoList();
  scrollToCurrentYear();
});

function scrollToCurrentYear() {
  const currentYearRow = document.querySelector('.current-year');
  if (currentYearRow) {
    currentYearRow.scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });
  }
}
```

### 3.4 パフォーマンス最適化

#### 3.4.1 大量データの扱い
158行のテーブルは比較的小規模なため、以下の最適化で十分:

1. **一括生成**: 初回ロード時にすべてのHTMLを生成
2. **CSS最適化**: GPU アクセラレーション活用
3. **遅延レンダリング（将来対応）**: データ範囲を拡大する場合に検討

#### 3.4.2 仮想スクロール（将来対応）
データ量が増えた場合（例: 1000年分表示）は仮想スクロールを実装:
- 表示領域内の行のみDOMに追加
- スクロールに応じて動的に行を追加/削除

## 4. PHPクラス設計

### 4.1 クラス名
`Toolzoo_Nengo_List`

### 4.2 ファイルパス
`includes/class-nengo-list.php`

### 4.3 メソッド一覧

#### 4.3.1 __construct()
- **説明**: コンストラクタ
- **処理**: 初期化処理

#### 4.3.2 render()
- **説明**: HTMLを生成して返却
- **戻り値**: string (HTML)
- **処理**:
  - コンテナHTMLの生成
  - CSS/JSのエンキュー

#### 4.3.3 enqueue_assets()
- **説明**: CSS/JSを読み込み
- **処理**:
  - `wp_enqueue_style('toolzoo-nengo-css')`
  - `wp_enqueue_script('toolzoo-nengo-js')`

#### 4.3.4 generate_nengo_data()
- **説明**: サーバーサイドで年号データを生成（オプション）
- **戻り値**: array
- **処理**:
  - PHPで年号リストを生成
  - `wp_localize_script` でJavaScriptに渡す
  - または、直接HTMLテーブルを生成

### 4.4 ショートコードハンドラー
```php
function toolzoo_nengo_shortcode($atts) {
    $list = new Toolzoo_Nengo_List();
    return $list->render();
}
add_shortcode('toolzoo_nengo', 'toolzoo_nengo_shortcode');
```

### 4.5 データ生成オプション

#### オプション1: JavaScript側で生成（推奨）
- メリット: サーバー負荷が少ない、動的処理が容易
- デメリット: JavaScript無効時に表示不可

#### オプション2: PHP側で生成
- メリット: JavaScript無効でも表示可能、SEO対策
- デメリット: サーバー負荷増、HTML サイズ増

**推奨**: JavaScript側で生成し、noscript タグで代替メッセージを表示

## 5. CSS設計

### 5.1 ファイル名
`assets/css/nengo.css`

### 5.2 主要クラス
- `.toolzoo-nengo-container`: 全体コンテナ
- `.toolzoo-nengo-list-table`: テーブル本体
- `.toolzoo-nengo-list-table thead`: 固定ヘッダー
- `.toolzoo-nengo-list-table tbody`: テーブルボディ
- `.toolzoo-nengo-list-table td.seireki`: 西暦セル
- `.toolzoo-nengo-list-table td.nengo`: 年号セル
- `.toolzoo-nengo-list-table tr.current-year`: 現在年の行
- `.toolzoo-nengo-era-jump`: 年代ジャンプボタン
- `.toolzoo-nengo-scroll-top`: トップへボタン

### 5.3 テーブルデザイン例
```css
.toolzoo-nengo-list-table {
  width: 100%;
  max-width: 500px;
  margin: 0 auto;
  border-collapse: collapse;
  font-size: 14px;
}

.toolzoo-nengo-list-table thead {
  position: sticky;
  top: 0;
  background: #333;
  color: #fff;
  z-index: 10;
}

.toolzoo-nengo-list-table th {
  padding: 10px 5px;
  text-align: center;
  border: 1px solid #ddd;
}

.toolzoo-nengo-list-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

.toolzoo-nengo-list-table tbody tr:hover {
  background-color: #f0f0f0;
}

.toolzoo-nengo-list-table td {
  padding: 8px 5px;
  text-align: center;
  border: 1px solid #ddd;
}

.toolzoo-nengo-list-table td.seireki {
  font-weight: bold;
  text-align: right;
  padding-right: 10px;
}

.toolzoo-nengo-list-table tr.current-year {
  background-color: #ffffcc;
  font-weight: bold;
  border: 2px solid #ff9900;
}
```

### 5.4 レスポンシブ対応
```css
@media screen and (max-width: 768px) {
  .toolzoo-nengo-container {
    overflow-x: auto;
  }

  .toolzoo-nengo-list-table {
    font-size: 12px;
  }

  .toolzoo-nengo-list-table th,
  .toolzoo-nengo-list-table td {
    padding: 6px 3px;
  }
}
```

### 5.5 印刷スタイル
```css
@media print {
  .toolzoo-nengo-scroll-top,
  .toolzoo-nengo-era-jump {
    display: none;
  }

  .toolzoo-nengo-list-table thead {
    position: static;
  }
}
```

## 6. JavaScript設計

### 6.1 ファイル名
`assets/js/nengo.js`

### 6.2 主要関数

#### 6.2.1 初期化
```javascript
document.addEventListener('DOMContentLoaded', function() {
  initNengoList();
});

function initNengoList() {
  // データ生成
  const startYear = 1868;
  const endYear = 2025;
  const data = generateNengoList(startYear, endYear);

  // テーブル生成
  const html = renderTable(data);

  // DOM挿入
  const container = document.getElementById('toolzoo-nengo-table-container');
  container.innerHTML = html;

  // 現在年にスクロール
  scrollToCurrentYear();

  // イベントリスナー設定
  setupEventListeners();
}
```

#### 6.2.2 主要関数リスト
- `initNengoList()`: 初期化
- `generateNengoList(startYear, endYear)`: データ生成
- `calculateNengo(year, eraName)`: 年号計算
- `renderTable(rows)`: テーブルHTML生成
- `scrollToCurrentYear()`: 現在年へスクロール
- `setupEventListeners()`: イベントリスナー設定
- `jumpToEra(eraName)`: 年代ジャンプ
- `scrollToTop()`: トップへスクロール

#### 6.2.3 年代ジャンプ機能
```javascript
function jumpToEra(eraName) {
  const eraStartYear = {
    'meiji': 1868,
    'taisho': 1912,
    'showa': 1926,
    'heisei': 1989,
    'reiwa': 2019
  };

  const targetYear = eraStartYear[eraName];
  const targetRow = document.querySelector(`tr[data-year="${targetYear}"]`);

  if (targetRow) {
    targetRow.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}
```

### 6.3 データ管理
年号データは JavaScript内で定数として定義。

## 7. セキュリティ考慮事項

### 7.1 XSS対策
- PHP側で `esc_html()`, `esc_attr()` 使用
- JavaScript側で `textContent` 使用（静的データのみなので問題なし）
- ユーザー入力は受け付けないため、入力検証不要

### 7.2 データの整合性
- 年号データは定数として管理
- 改元情報の正確性を担保

## 8. テスト項目

### 8.1 機能テスト

#### 8.1.1 データ表示
- [ ] 1868年～2025年の全データが表示される
- [ ] 各年号の元年が「元年」と表示される
- [ ] 2年目以降が数値で表示される
- [ ] 該当しない年号が「-」で表示される

#### 8.1.2 改元年の表示
- [ ] 1912年: 明治45年 + 大正元年
- [ ] 1926年: 大正15年 + 昭和元年
- [ ] 1989年: 昭和64年 + 平成元年
- [ ] 2019年: 平成31年 + 令和元年

#### 8.1.3 UI機能
- [ ] 現在年がハイライト表示される
- [ ] 初期表示で現在年付近にスクロールされる
- [ ] 固定ヘッダーが機能する
- [ ] トップへボタンが機能する
- [ ] 年代ジャンプボタンが機能する（実装する場合）

### 8.2 ブラウザ互換性テスト
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge

### 8.3 レスポンシブテスト
- [ ] スマートフォン (320px~): 横スクロール可能
- [ ] タブレット (768px~): 適切なサイズで表示
- [ ] デスクトップ (1024px~): 中央寄せで表示

### 8.4 パフォーマンステスト
- [ ] 初期ロード時間が2秒以内
- [ ] スクロールが滑らか
- [ ] メモリリークがない

### 8.5 アクセシビリティテスト
- [ ] キーボードでスクロール可能
- [ ] スクリーンリーダー対応（テーブルの適切なマークアップ）
- [ ] コントラスト比が十分

## 9. 表示データ仕様

### 9.1 完全なデータマッピング（抜粋）

| 西暦 | 明治 | 大正 | 昭和 | 平成 | 令和 |
|------|------|------|------|------|------|
| 1868 | 元年 | - | - | - | - |
| 1869 | 2 | - | - | - | - |
| ... | ... | ... | ... | ... | ... |
| 1911 | 44 | - | - | - | - |
| 1912 | 45 | 元年 | - | - | - |
| 1913 | - | 2 | - | - | - |
| ... | ... | ... | ... | ... | ... |
| 1925 | - | 14 | - | - | - |
| 1926 | - | 15 | 元年 | - | - |
| 1927 | - | - | 2 | - | - |
| ... | ... | ... | ... | ... | ... |
| 1988 | - | - | 63 | - | - |
| 1989 | - | - | 64 | 元年 | - |
| 1990 | - | - | - | 2 | - |
| ... | ... | ... | ... | ... | ... |
| 2018 | - | - | - | 30 | - |
| 2019 | - | - | - | 31 | 元年 |
| 2020 | - | - | - | - | 2 |
| 2021 | - | - | - | - | 3 |
| 2022 | - | - | - | - | 4 |
| 2023 | - | - | - | - | 5 |
| 2024 | - | - | - | - | 6 |
| 2025 | - | - | - | - | 7 |

### 9.2 各年号の年数
- **明治**: 1868-1912年（45年間）
- **大正**: 1912-1926年（15年間）
- **昭和**: 1926-1989年（64年間）
- **平成**: 1989-2019年（31年間）
- **令和**: 2019年-現在（7年目）

## 10. 将来の拡張案

### 10.1 機能拡張
- **検索機能**: 特定の年または年号年で検索
- **フィルタリング**: 特定の年号のみ表示
- **年齢計算**: 生まれ年を選択すると現在の年齢を表示
- **期間指定**: 表示する年の範囲をカスタマイズ
- **データ範囲拡張**: 2026年以降も自動追加
- **詳細情報**: 各年のクリックで歴史的出来事を表示

### 10.2 UI拡張
- **カード表示**: テーブル以外の表示形式
- **タイムライン表示**: 縦スクロールのタイムライン
- **年代ビジュアライゼーション**: グラフィカルな表現
- **印刷最適化**: PDF出力機能
- **CSV/Excelエクスポート**: データのダウンロード

### 10.3 データ拡張
- **月日情報**: 改元日の詳細情報を表示
- **歴史的出来事**: 各年の主要な出来事
- **旧暦対応**: 旧暦と新暦の対照表
- **他の年号**: 江戸時代以前の年号

### 10.4 国際化
- **英語対応**: ローマ字表記
- **多言語対応**: 各国語での表示

## 11. 参考資料

### 11.1 公式情報源
- 国立国会図書館: 年号一覧
- 内閣府: 元号について
- Wikipedia: 元号一覧

### 11.2 改元年の詳細
- **1912年**: 7月30日改元（明治45年/大正元年）
- **1926年**: 12月25日改元（大正15年/昭和元年）
- **1989年**: 1月8日改元（昭和64年/平成元年）
- **2019年**: 5月1日改元（平成31年/令和元年）

### 11.3 注意事項
このツールでは簡略化のため、改元があった年は年単位で両方の年号を表示しています。
実際には年途中で改元されているため、厳密な日付管理が必要な場合は別途確認が必要です。
