# 体積単位コンバーター 詳細設計

## 1. 機能概要

### 1.1 機能名
体積単位コンバーター（リットル・ガロン・升）

### 1.2 ショートコード
```
[toolzoo_volume_unit]
```

> **単位の訂正について**
> ご要望に「平方メートル (m²)」とありましたが、m² は**面積**の単位です。
> 体積の文脈でメートル法の代表単位として最も日常的な **リットル (L)** を採用しています。
> （1 L = 0.001 m³ = 1 dm³）

### 1.3 機能説明
リットル・ガロン（米国）・升の3単位を相互変換するツール。
ページ上部に3単位の基準値早見表を表示し、下部の自由入力欄でいずれかの値を入力すると
リアルタイムに他の2単位へ自動変換して表示する。
下部に日本（尺貫法）と米国（ヤード・ポンド法）の体積単位体系の解説テーブルも付属する。

### 1.4 単位の定義・換算基準

| 変換元 | 変換先 | 換算式 | 根拠 |
|--------|--------|--------|------|
| 1 米ガロン | リットル | × 3.785411784 | 国際定義（厳密値） |
| 1 リットル | 米ガロン | ÷ 3.785411784 ≈ × 0.26417 | 同上 |
| 1 升 | リットル | × 1.8039 | 日本計量法 |
| 1 リットル | 升 | ÷ 1.8039 ≈ × 0.55437 | 日本計量法 |
| 1 米ガロン | 升 | × 3.785411784 ÷ 1.8039 ≈ × 2.0985 | 上記組み合わせ |
| 1 升 | 米ガロン | × 1.8039 ÷ 3.785411784 ≈ × 0.47655 | 上記組み合わせ |

**定数値まとめ（JavaScript用）:**
```
L_PER_GAL  = 3.785411784          （米ガロン→リットル、厳密値）
GAL_PER_L  = 1 / 3.785411784      （≈ 0.26417）
L_PER_SHO  = 1.8039               （升→リットル、日本計量法）
SHO_PER_L  = 1 / 1.8039           （≈ 0.55437）
SHO_PER_GAL = 3.785411784 / 1.8039（≈ 2.09850）
GAL_PER_SHO = 1.8039 / 3.785411784（≈ 0.47655）
```

> **英ガロンについて**: 英国ガロン (UK gallon) = 4.54609 L であり米国ガロンと異なる。
> 本ツールでは日常で最も多用される **米国ガロン (US gallon)** を採用し、解説テーブルに英国ガロンの値を補足する。

---

## 2. UI設計

### 2.1 画面レイアウト

```
┌─────────────────────────────────────────────┐
│   体積単位コンバーター                        │
├─────────────────────────────────────────────┤
│ 基準値早見表                                 │
├──────────────┬──────────────┬───────────────┤
│  基準値       │  ガロン (gal)│  升           │
├──────────────┼──────────────┼───────────────┤
│  1 リットル  │  0.2642 gal │  0.5544 升    │
│  1 ガロン    │  —          │  2.0985 升    │
│  1 升        │  0.4765 gal │  —            │
└──────────────┴──────────────┴───────────────┘
│ ※ 1升 = 1.8039 L（日本計量法）              │
│ ※ 1米ガロン = 3.785411784 L（国際定義）     │
├─────────────────────────────────────────────┤
│ 自由変換（いずれかに入力すると自動変換）      │
│                                             │
│  リットル:  [       1.00000] L              │
│  ガロン:    [       0.26417] gal            │
│  升:        [       0.55437] 升             │
│                                             │
│  [ クリア ]                                 │
├─────────────────────────────────────────────┤
│ 日本の体積単位（尺貫法）                     │
├─────────────────────────────────────────────┤
│  1 勺 (shaku) = 1/10 合 ≈ 18.039 mL        │
│  1 合 (go)    = 10 勺 ≈ 180.39 mL          │
│  1 升 (sho)   = 10 合 ≈ 1.8039 L           │
│  1 斗 (to)    = 10 升 ≈ 18.039 L           │
│  1 石 (koku)  = 10 斗 ≈ 180.39 L           │
├─────────────────────────────────────────────┤
│ ヤード・ポンド法の体積単位（米国）           │
├─────────────────────────────────────────────┤
│  1 fl oz  = 29.5735 mL                     │
│  1 cup    = 8 fl oz = 236.588 mL           │
│  1 pint   = 2 cups = 0.473176 L            │
│  1 quart  = 2 pints = 0.946353 L           │
│  1 gallon = 4 quarts = 3.785412 L          │
│  （英国ガロン = 4.54609 L）                 │
└─────────────────────────────────────────────┘
```

### 2.2 UI要素詳細

#### 2.2.1 早見表（上部リファレンス）
- **タイプ**: 静的テーブル
- **ID**: `toolzoo-volume-reference-table`
- **内容**: 1L・1ガロン・1升を基準にした3×3の換算表
- **小数桁数**: 小数第4位

#### 2.2.2 リットル入力欄
- **タイプ**: number input
- **ID**: `toolzoo-volume-liter`
- **単位**: L
- **デフォルト値**: 1
- **min**: 0, **step**: any

#### 2.2.3 ガロン入力欄
- **タイプ**: number input
- **ID**: `toolzoo-volume-gal`
- **単位**: gal
- **デフォルト値**: 0.26417（1Lから自動算出）
- **min**: 0, **step**: any

#### 2.2.4 升入力欄
- **タイプ**: number input
- **ID**: `toolzoo-volume-sho`
- **単位**: 升
- **デフォルト値**: 0.55437（1Lから自動算出）
- **min**: 0, **step**: any

#### 2.2.5 クリアボタン
- **テキスト**: "クリア"
- **ID**: `toolzoo-volume-clear-btn`

#### 2.2.6 解説セクション（下部）
- 日本の体積単位（尺貫法）：勺・合・升・斗・石
- ヤード・ポンド法（米国）：fl oz・cup・pint・quart・gallon（英国ガロンの補足も）

---

## 3. 機能仕様

### 3.1 換算アルゴリズム

#### 3.1.1 基本換算式
```
// 入力がリットルの場合
gal = liter / 3.785411784
sho = liter / 1.8039

// 入力がガロンの場合
liter = gal × 3.785411784
sho   = gal × 3.785411784 / 1.8039

// 入力が升の場合
liter = sho × 1.8039
gal   = sho × 1.8039 / 3.785411784
```

#### 3.1.2 処理フロー（リアルタイム変換）
```
1. 各入力欄に input イベントリスナー登録
   ↓
2. ユーザーがいずれかの入力欄を編集
   ↓
3. 入力値をリットルに一旦変換（共通ベース）
   ↓
4. バリデーション
   - 空欄 or 非数値 → 他の欄をクリア
   - 0未満 → 0 に補正
   ↓
5. リットル値から他の全単位を計算
   ↓
6. 丸めて各入力欄に書き込む（isUpdating フラグで無限ループ防止）
```

#### 3.1.3 実装方針（データ駆動型）
weight_unit・length_unit と同じ UNITS 配列方式を採用:
```javascript
var UNITS = [
    { key: 'liter', id: 'toolzoo-volume-liter', literPerUnit: 1,            decimals: 5 },
    { key: 'gal',   id: 'toolzoo-volume-gal',   literPerUnit: 3.785411784,  decimals: 5 },
    { key: 'sho',   id: 'toolzoo-volume-sho',   literPerUnit: 1.8039,       decimals: 5 },
];
```
変換は `L値 = 入力値 × literPerUnit` → `他単位値 = L値 / 他単位の literPerUnit`

### 3.2 表示精度

| 単位 | 小数桁数 |
|------|--------|
| リットル | 5桁（例: 1.00000） |
| ガロン | 5桁（例: 0.26417） |
| 升 | 5桁（例: 0.55437） |

---

## 4. 追加提案

### 4.1 ミリリットル(mL)を変換欄に追加（推奨）
料理・飲料の文脈では mL が最も日常的。`UNITS` 配列に1行追加するだけで対応可能。

| 追加単位 | literPerUnit | 備考 |
|---------|-------------|------|
| mL (ミリリットル) | 0.001 | 1 mL = 0.001 L |

例：自由変換欄を4行（mL・L・gal・升）にする案:
```
  mL:      [    1000.00000] mL
  リットル: [       1.00000] L
  ガロン:   [       0.26417] gal
  升:       [       0.55437] 升
```

### 4.2 身近な体積の目安（サブ情報）
```
【よくある体積の目安】
・缶ビール（1本）: 350 mL = 0.0925 gal = 0.194 升
・ペットボトル（1本）: 500 mL = 0.132 gal = 0.277 升
・1升瓶（日本酒）: 1.8039 L = 0.477 gal = 1 升
・1ガロン（牛乳）: 3.785 L = 2.099 升
```

### 4.3 米国ガロン／英国ガロン切替（将来拡張）
ガロンには米 (3.785L) と英 (4.546L) の2種類がある。
将来的にラジオボタンで切替できる UI を追加できる。

### 4.4 編集中フィールドのハイライト
weight_unit・length_unit と同様に、編集中の入力欄を青くハイライト。

### 4.5 コピーボタン
各変換結果の横にコピーボタンを設置すると、Webアプリ等での利用時に便利。

---

## 5. PHPクラス設計

### 5.1 クラス名
`Toolzoo_Volume_Unit`

### 5.2 ファイルパス
`includes/class-volume-unit.php`

### 5.3 メソッド一覧

#### 5.3.1 render()
- CSS/JS エンキュー
- HTML テンプレート生成（早見表 + 入力エリア + 説明セクション）

#### 5.3.2 enqueue_assets()
- `wp_enqueue_style('toolzoo-volume-unit-css')`
- `wp_enqueue_script('toolzoo-volume-unit-js')`

### 5.4 ショートコードハンドラー
```php
add_shortcode('toolzoo_volume_unit', array($this, 'volume_unit_shortcode'));

public function volume_unit_shortcode($atts) {
    $converter = new Toolzoo_Volume_Unit();
    return $converter->render();
}
```

---

## 6. CSS設計

### 6.1 ファイル名
`assets/css/volume-unit.css`

### 6.2 主要クラス
- `.toolzoo-volume-container`: 全体コンテナ
- `.toolzoo-volume-header`: ヘッダー
- `.toolzoo-volume-reference`: 早見表セクション
- `.toolzoo-volume-reference-table`: 早見表テーブル
- `.toolzoo-volume-converter`: 変換入力セクション
- `.toolzoo-volume-input-row`: 入力行
- `.toolzoo-volume-input-label`: 単位ラベル（`width: 7.5em; flex-shrink: 0` でラベル幅統一）
- `.toolzoo-volume-input-field`: 入力フィールド（右揃え）
- `.toolzoo-volume-input-field.active`: 編集中ハイライト
- `.toolzoo-volume-input-unit`: 単位テキスト
- `.toolzoo-volume-clear-btn`: クリアボタン
- `.toolzoo-volume-shakkan`: 尺貫法説明セクション
- `.toolzoo-volume-imperial`: ヤード・ポンド法説明セクション
- `.toolzoo-volume-note`: 補足注記

### 6.3 スタイル方針
- weight_unit.css・length_unit.css のスタイルを踏襲（コンテナ幅 600px・色・フォント）
- 入力フィールドは右揃え
- ラベル幅は `7.5em` に固定（全角4文字「リットル」以上に対応）

---

## 7. JavaScript設計

### 7.1 ファイル名
`assets/js/volume-unit.js`

### 7.2 単位定義テーブル
```javascript
var UNITS = [
    { key: 'liter', id: 'toolzoo-volume-liter', literPerUnit: 1,            decimals: 5 },
    { key: 'gal',   id: 'toolzoo-volume-gal',   literPerUnit: 3.785411784,  decimals: 5 },
    { key: 'sho',   id: 'toolzoo-volume-sho',   literPerUnit: 1.8039,       decimals: 5 },
];
```

### 7.3 主要関数
- `init()`: 初期化・イベント設定
- `validateInput(value)`: 入力値検証
- `convertFromUnit(sourceUnit, value)`: 指定単位→L換算→全単位へ変換
- `clearAllExcept(activeKey)`: 指定欄以外をクリア
- `setActiveField(activeKey)`: ハイライト制御
- `getUnit(key)`: UNITS から key で定義を取得

---

## 8. HTML構造（概略）

```html
<div class="toolzoo-volume-container" id="toolzoo-volume-unit">

  <!-- ヘッダー -->
  <div class="toolzoo-volume-header">
    <h3>体積単位コンバーター</h3>
  </div>

  <!-- 早見表 -->
  <div class="toolzoo-volume-reference">
    <h4>基準値早見表</h4>
    <table class="toolzoo-volume-reference-table">
      <thead>
        <tr><th>基準値</th><th>ガロン (gal)</th><th>升</th></tr>
      </thead>
      <tbody>
        <tr><td>1 リットル</td><td>0.2642 gal</td><td>0.5544 升</td></tr>
        <tr><td>1 ガロン</td><td>—</td><td>2.0985 升</td></tr>
        <tr><td>1 升</td><td>0.4765 gal</td><td>—</td></tr>
      </tbody>
    </table>
    <p class="toolzoo-volume-note">
      ※ 1升 = 1.8039 L（日本計量法） / 1米ガロン = 3.785411784 L（国際定義）
    </p>
  </div>

  <!-- 自由変換 -->
  <div class="toolzoo-volume-converter">
    <h4>自由変換</h4>
    <p class="toolzoo-volume-converter-hint">いずれかに入力すると他の単位へ自動変換します</p>

    <div class="toolzoo-volume-input-row">
      <label for="toolzoo-volume-liter">リットル</label>
      <input type="number" id="toolzoo-volume-liter" min="0" step="any" value="1">
      <span>L</span>
    </div>

    <div class="toolzoo-volume-input-row">
      <label for="toolzoo-volume-gal">ガロン</label>
      <input type="number" id="toolzoo-volume-gal" min="0" step="any">
      <span>gal</span>
    </div>

    <div class="toolzoo-volume-input-row">
      <label for="toolzoo-volume-sho">升</label>
      <input type="number" id="toolzoo-volume-sho" min="0" step="any">
      <span>升</span>
    </div>

    <button id="toolzoo-volume-clear-btn">クリア</button>
  </div>

  <!-- 尺貫法 体積単位 -->
  <div class="toolzoo-volume-shakkan">
    <h4>日本の体積単位（尺貫法）</h4>
    <table class="toolzoo-volume-info-table">...</table>
    <p class="toolzoo-volume-note">※ 1升 = 1.8039 L（日本計量法による定義）</p>
  </div>

  <!-- ヤード・ポンド法 体積単位 -->
  <div class="toolzoo-volume-imperial">
    <h4>米国の体積単位</h4>
    <table class="toolzoo-volume-info-table">...</table>
    <p class="toolzoo-volume-note">※ 米国ガロン = 3.785411784 L / 英国ガロン = 4.54609 L</p>
  </div>

</div>
```

---

## 9. セキュリティ考慮事項
- 全変換処理はクライアント側 JavaScript のみで完結（サーバー送信なし）
- PHP 側出力は静的テンプレート。動的値には `esc_html()` / `esc_attr()` を使用
- XSS 対策：JS は `textContent` / `value` への代入のみ

---

## 10. テスト項目

### 10.1 変換精度テスト
- [ ] 1 L → 0.26417 gal（小数第5位）
- [ ] 1 L → 0.55437 升（小数第5位）
- [ ] 1 gal → 3.78541 L
- [ ] 1 gal → 2.09850 升
- [ ] 1 升 → 1.80390 L（厳密値）
- [ ] 1 升 → 0.47655 gal

### 10.2 リアルタイム変換テスト
- [ ] L 入力で gal・升が即時更新
- [ ] gal 入力で L・升が即時更新
- [ ] 升 入力で L・gal が即時更新
- [ ] 無限ループが発生しない

### 10.3 入力バリデーション
- [ ] 空欄入力で他欄がクリアされる
- [ ] 負数入力で 0 に補正される
- [ ] 非数値入力で他欄がクリアされる

### 10.4 ブラウザ互換性テスト
- [ ] Chrome / Firefox / Safari / Edge

### 10.5 レスポンシブテスト
- [ ] 320px〜（スマートフォン）

---

## 11. 実装手順

1. ✅ この設計書を作成
2. ⬜ `includes/class-volume-unit.php` を実装
3. ⬜ `assets/css/volume-unit.css` を実装
4. ⬜ `assets/js/volume-unit.js` を実装
5. ⬜ `includes/class-constants.php` にツール定義を追加
   - `id`: `'volume'`
   - `slug`: `'volume'`
   - `shortcode`: `'[toolzoo_volume_unit]'`
   - `emoji`: `'🧴'`
6. ⬜ `includes/class-toolzoo.php` にショートコード登録・リライトルール追加
7. ⬜ `languages/toolzoo.pot` / `toolzoo-ja.po` に翻訳追加
8. ⬜ `doc/01_overview.md` の機能一覧を更新

---

## 12. 将来の拡張案

### 12.1 変換単位の追加
UNITS 配列に追加するだけで対応可能（データ駆動型設計のため）:

| 追加単位 | literPerUnit | グループ |
|---------|-------------|---------|
| mL (ミリリットル) | 0.001 | メートル法 |
| m³ (立方メートル) | 1000 | メートル法 |
| 英国ガロン (UK gal) | 4.54609 | ヤード・ポンド法 |
| fl oz (液量オンス・米) | 0.0295735296 | ヤード・ポンド法 |
| 合 (go) | 0.18039 | 尺貫法 |
| 斗 (to) | 18.039 | 尺貫法 |

### 12.2 プリセット入力
「缶ビール 350mL」「ペットボトル 500mL」「1升瓶」などをワンクリックでセット。

### 12.3 コピーボタン
各変換結果の横にコピーボタンを設置。

### 12.4 他の単位変換ツールとのリンク
長さ・重量・体積をまとめた「単位変換ハブ」ページへの誘導。
