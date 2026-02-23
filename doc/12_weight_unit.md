# 重量単位コンバーター 詳細設計

## 1. 機能概要

### 1.1 機能名
重量単位コンバーター（キログラム・ポンド・貫）

### 1.2 ショートコード
```
[toolzoo_weight_unit]
```

> **注意**: ショートコード名は `weight_unit`（重量）です。`wait_unit` は typo の可能性があるためこちらを採用しています。実装前に確認してください。

### 1.3 機能説明
キログラム・ポンド・貫の3単位を相互変換するツール。
ページ上部に3単位の基準値早見表を表示し、下部の自由入力欄でいずれかの値を入力すると
リアルタイムに他の2単位へ自動変換して表示する。
下部に各単位系の体系説明も付属する。

### 1.4 単位の定義・換算基準

| 変換元 | 変換先 | 換算式 | 根拠 |
|--------|--------|--------|------|
| 1 ポンド | キログラム | × 0.45359237 | 国際ヤード・ポンド協定（1959年）厳密値 |
| 1 キログラム | ポンド | ÷ 0.45359237 ≈ × 2.20462 | 同上 |
| 1 貫 | キログラム | × 3.75 | 日本計量法（厳密値） |
| 1 キログラム | 貫 | ÷ 3.75 = × 4/15 ≈ × 0.26667 | 日本計量法 |
| 1 ポンド | 貫 | × 0.45359237 ÷ 3.75 ≈ × 0.12096 | 上記組み合わせ |
| 1 貫 | ポンド | × 3.75 ÷ 0.45359237 ≈ × 8.26733 | 上記組み合わせ |

**定数値まとめ（JavaScript用）:**
```
KG_PER_LB  = 0.45359237          （厳密値）
LB_PER_KG  = 1 / 0.45359237      （≈ 2.20462262）
KG_PER_KAN = 3.75                 （厳密値）
KAN_PER_KG = 1 / 3.75             （= 4/15 ≈ 0.26667）
LB_PER_KAN = 3.75 / 0.45359237   （≈ 8.26733）
KAN_PER_LB = 0.45359237 / 3.75   （≈ 0.12096）
```

---

## 2. UI設計

### 2.1 画面レイアウト

```
┌─────────────────────────────────────────────┐
│   重量単位コンバーター                        │
├─────────────────────────────────────────────┤
│ 基準値早見表                                 │
├──────────────┬──────────────┬───────────────┤
│  基準値       │  ポンド (lb) │  貫           │
├──────────────┼──────────────┼───────────────┤
│  1 キログラム │  2.2046 lb  │  0.2667 貫    │
│  1 ポンド    │  —          │  0.1210 貫    │
│  1 貫        │  8.2673 lb  │  —            │
└──────────────┴──────────────┴───────────────┘
│ ※ 1貫 = 3.75 kg（日本計量法）               │
│ ※ 1ポンド = 0.45359237 kg（国際定義）        │
├─────────────────────────────────────────────┤
│ 自由変換（いずれかに入力すると自動変換）      │
│                                             │
│  キログラム:  [       1.000] kg             │
│  ポンド:      [       2.205] lb             │
│  貫:          [       0.267] 貫             │
│                                             │
│  [ クリア ]                                 │
├─────────────────────────────────────────────┤
│ 日本の重量単位（尺貫法）                     │
├─────────────────────────────────────────────┤
│  1 毛 = 1/1000 匁 ≈ 3.75 mg               │
│  1 厘 = 1/100 匁 ≈ 37.5 mg               │
│  1 分 = 1/10 匁 ≈ 375 mg                 │
│  1 匁 = 3.75 g                            │
│  1 両 = 10 匁 = 37.5 g                    │
│  1 斤 = 160 匁 = 600 g                    │
│  1 貫 = 1000 匁 = 3.75 kg                 │
│  1 斤（パン）= 1ポンド = 453.6 g           │
├─────────────────────────────────────────────┤
│ ヤード・ポンド法の重量単位                   │
├─────────────────────────────────────────────┤
│  1 グレーン (gr) = 64.799 mg              │
│  1 ドラム (dr) = 27.34 gr = 1.772 g       │
│  1 オンス (oz) = 16 dr = 28.35 g          │
│  1 ポンド (lb) = 16 oz = 453.59 g         │
│  1 ストーン (st) = 14 lb = 6.350 kg       │
│  1 ハンドレッドウェイト (cwt) = 100 lb     │
│  1 トン（米） = 2000 lb = 907.185 kg      │
│  1 トン（英） = 2240 lb = 1016.047 kg     │
└─────────────────────────────────────────────┘
```

### 2.2 UI要素詳細

#### 2.2.1 早見表（上部リファレンス）
- **タイプ**: 静的テーブル
- **ID**: `toolzoo-weight-reference-table`
- **内容**: 1kg・1lb・1貫を基準にした3×3の換算表
- **小数桁数**: 小数第4位

#### 2.2.2 キログラム入力欄
- **タイプ**: number input
- **ID**: `toolzoo-weight-kg`
- **単位**: kg
- **デフォルト値**: 1
- **min**: 0, **step**: any

#### 2.2.3 ポンド入力欄
- **タイプ**: number input
- **ID**: `toolzoo-weight-lb`
- **単位**: lb
- **デフォルト値**: 2.2046（1kgから自動算出）
- **min**: 0, **step**: any

#### 2.2.4 貫入力欄
- **タイプ**: number input
- **ID**: `toolzoo-weight-kan`
- **単位**: 貫
- **デフォルト値**: 0.2667（1kgから自動算出）
- **min**: 0, **step**: any

#### 2.2.5 クリアボタン
- **テキスト**: "クリア"
- **ID**: `toolzoo-weight-clear-btn`

#### 2.2.6 解説セクション（下部）
- 日本の重量単位（尺貫法）：毛・厘・分・匁・両・斤・貫
- ヤード・ポンド法：グレーン・ドラム・オンス・ポンド・ストーン・トン

---

## 3. 機能仕様

### 3.1 換算アルゴリズム

#### 3.1.1 基本換算式
```
// 入力がキログラムの場合
lb  = kg / 0.45359237
kan = kg / 3.75

// 入力がポンドの場合
kg  = lb × 0.45359237
kan = lb × 0.45359237 / 3.75

// 入力が貫の場合
kg  = kan × 3.75
lb  = kan × 3.75 / 0.45359237
```

#### 3.1.2 処理フロー（リアルタイム変換）
```
1. 各入力欄に input イベントリスナー登録
   ↓
2. ユーザーがいずれかの入力欄を編集
   ↓
3. 入力値をキログラムに一旦変換（共通ベース）
   ↓
4. バリデーション
   - 空欄 or 非数値 → 他の欄をクリア
   - 0未満 → 0 に補正
   ↓
5. キログラム値から他の全単位を計算
   ↓
6. 丸めて各入力欄に書き込む（isUpdating フラグで無限ループ防止）
```

#### 3.1.3 実装方針（データ駆動型）
length_unit と同じ UNITS 配列方式を採用:
```javascript
var UNITS = [
    { key: 'kg',  id: 'toolzoo-weight-kg',  kgPerUnit: 1,            decimals: 5 },
    { key: 'lb',  id: 'toolzoo-weight-lb',  kgPerUnit: 0.45359237,   decimals: 5 },
    { key: 'kan', id: 'toolzoo-weight-kan', kgPerUnit: 3.75,          decimals: 5 },
];
```
変換は `kg値 = 入力値 × kgPerUnit` → `他単位値 = kg値 / 他単位の kgPerUnit`

### 3.2 表示精度

| 単位 | 小数桁数 |
|------|--------|
| キログラム | 5桁（例: 1.00000） |
| ポンド | 5桁（例: 2.20462） |
| 貫 | 5桁（例: 0.26667） |

---

## 4. 追加提案

### 4.1 グラム・トン・オンス・匁を変換欄に追加（将来拡張）
自由変換欄にさらに以下の単位を追加することで利便性が向上する。

| 追加単位 | kgPerUnit | 備考 |
|---------|-----------|------|
| グラム (g) | 0.001 | メートル法 |
| トン (t) | 1000 | メートル法 |
| オンス (oz) | 0.028349523125 | 1 oz = 1/16 lb |
| 匁 (monme) | 0.00375 | 尺貫法：1匁 = 1/1000 貫 |

### 4.2 身近な重さの例示（サブ情報）
```
【身近な重さの目安】
・500mlペットボトル（水）: 約 500g = 1.102 lb = 0.133 貫
・体重60kg: 60 kg = 132.28 lb = 16 貫
・1貫（寿司の「1貫」は別の用法）: 3.75 kg = 8.27 lb
```

### 4.3 編集中フィールドのハイライト
length_unit と同様に、編集中の入力欄を青くハイライト。

---

## 5. PHPクラス設計

### 5.1 クラス名
`Toolzoo_Weight_Unit`

### 5.2 ファイルパス
`includes/class-weight-unit.php`

### 5.3 メソッド一覧

#### 5.3.1 render()
- CSS/JS エンキュー
- HTML テンプレート生成（早見表 + 入力エリア + 説明セクション）

#### 5.3.2 enqueue_assets()
- `wp_enqueue_style('toolzoo-weight-unit-css')`
- `wp_enqueue_script('toolzoo-weight-unit-js')`

### 5.4 ショートコードハンドラー
```php
add_shortcode('toolzoo_weight_unit', array($this, 'weight_unit_shortcode'));

public function weight_unit_shortcode($atts) {
    $converter = new Toolzoo_Weight_Unit();
    return $converter->render();
}
```

---

## 6. CSS設計

### 6.1 ファイル名
`assets/css/weight-unit.css`

### 6.2 主要クラス
- `.toolzoo-weight-container`: 全体コンテナ
- `.toolzoo-weight-header`: ヘッダー
- `.toolzoo-weight-reference`: 早見表セクション
- `.toolzoo-weight-reference-table`: 早見表テーブル
- `.toolzoo-weight-converter`: 変換入力セクション
- `.toolzoo-weight-input-row`: 入力行
- `.toolzoo-weight-input-label`: 単位ラベル
- `.toolzoo-weight-input-field`: 入力フィールド
- `.toolzoo-weight-input-field.active`: 編集中ハイライト
- `.toolzoo-weight-input-unit`: 単位テキスト
- `.toolzoo-weight-clear-btn`: クリアボタン
- `.toolzoo-weight-shakkan`: 尺貫法説明セクション
- `.toolzoo-weight-imperial`: ヤード・ポンド法説明セクション
- `.toolzoo-weight-note`: 補足注記

### 6.3 スタイル方針
- length_unit.css のスタイルを踏襲（コンテナ幅・色・フォント）
- 入力フィールドは右揃え

---

## 7. JavaScript設計

### 7.1 ファイル名
`assets/js/weight-unit.js`

### 7.2 単位定義テーブル
```javascript
var UNITS = [
    { key: 'kg',  id: 'toolzoo-weight-kg',  kgPerUnit: 1,           decimals: 5 },
    { key: 'lb',  id: 'toolzoo-weight-lb',  kgPerUnit: 0.45359237,  decimals: 5 },
    { key: 'kan', id: 'toolzoo-weight-kan', kgPerUnit: 3.75,         decimals: 5 },
];
```

### 7.3 主要関数
- `init()`: 初期化・イベント設定
- `validateInput(value)`: 入力値検証
- `convertFromUnit(sourceUnit, value)`: 指定単位→kg換算→全単位へ変換
- `clearAllExcept(activeKey)`: 指定欄以外をクリア
- `setActiveField(activeKey)`: ハイライト制御

---

## 8. HTML構造（概略）

```html
<div class="toolzoo-weight-container" id="toolzoo-weight-unit">

  <!-- ヘッダー -->
  <div class="toolzoo-weight-header">
    <h3>重量単位コンバーター</h3>
  </div>

  <!-- 早見表 -->
  <div class="toolzoo-weight-reference">
    <h4>基準値早見表</h4>
    <table class="toolzoo-weight-reference-table">
      <thead>
        <tr><th>基準値</th><th>ポンド (lb)</th><th>貫</th></tr>
      </thead>
      <tbody>
        <tr><td>1 キログラム</td><td>2.2046 lb</td><td>0.2667 貫</td></tr>
        <tr><td>1 ポンド</td><td>—</td><td>0.1210 貫</td></tr>
        <tr><td>1 貫</td><td>8.2673 lb</td><td>—</td></tr>
      </tbody>
    </table>
    <p class="toolzoo-weight-note">
      ※ 1貫 = 3.75 kg（日本計量法） / 1ポンド = 0.45359237 kg（国際定義）
    </p>
  </div>

  <!-- 自由変換 -->
  <div class="toolzoo-weight-converter">
    <h4>自由変換</h4>
    <p class="toolzoo-weight-converter-hint">いずれかに入力すると他の単位へ自動変換します</p>

    <div class="toolzoo-weight-input-row">
      <label for="toolzoo-weight-kg">キログラム</label>
      <input type="number" id="toolzoo-weight-kg" min="0" step="any" value="1">
      <span>kg</span>
    </div>

    <div class="toolzoo-weight-input-row">
      <label for="toolzoo-weight-lb">ポンド</label>
      <input type="number" id="toolzoo-weight-lb" min="0" step="any">
      <span>lb</span>
    </div>

    <div class="toolzoo-weight-input-row">
      <label for="toolzoo-weight-kan">貫</label>
      <input type="number" id="toolzoo-weight-kan" min="0" step="any">
      <span>貫</span>
    </div>

    <button id="toolzoo-weight-clear-btn">クリア</button>
  </div>

  <!-- 尺貫法 重量単位 -->
  <div class="toolzoo-weight-shakkan">
    <h4>日本の重量単位（尺貫法）</h4>
    <table>...</table>
    <p class="toolzoo-weight-note">※ 1匁 = 3.75 g（日本計量法による定義）</p>
  </div>

  <!-- ヤード・ポンド法 重量単位 -->
  <div class="toolzoo-weight-imperial">
    <h4>ヤード・ポンド法の重量単位</h4>
    <table>...</table>
    <p class="toolzoo-weight-note">※ 1ポンド = 0.45359237 kg（1959年 国際定義）</p>
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
- [ ] 1 kg → 2.20462 lb（小数第5位）
- [ ] 1 kg → 0.26667 貫（小数第5位）
- [ ] 1 lb → 0.45359 kg
- [ ] 1 lb → 0.12096 貫
- [ ] 1 貫 → 3.75000 kg（厳密値）
- [ ] 1 貫 → 8.26733 lb

### 10.2 リアルタイム変換テスト
- [ ] kg 入力で lb・貫が即時更新
- [ ] lb 入力で kg・貫が即時更新
- [ ] 貫 入力で kg・lb が即時更新
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
2. ⬜ `includes/class-weight-unit.php` を実装
3. ⬜ `assets/css/weight-unit.css` を実装
4. ⬜ `assets/js/weight-unit.js` を実装
5. ⬜ `includes/class-constants.php` にツール定義を追加
   - `id`: `'weight'`
   - `slug`: `'weight'`
   - `shortcode`: `'[toolzoo_weight_unit]'`
   - `emoji`: `'⚖️'`（BMI と区別するため別 emoji を検討）
6. ⬜ `includes/class-toolzoo.php` にショートコード登録・リライトルール追加
7. ⬜ `languages/toolzoo.pot` / `toolzoo-ja.po` に翻訳追加
8. ⬜ `doc/01_overview.md` の機能一覧・ディレクトリ構造を更新

---

## 12. 将来の拡張案

### 12.1 変換単位の追加
下記を `UNITS` 配列に追加するだけで対応可能（データ駆動型設計のため）:

| 追加単位 | kgPerUnit | グループ |
|---------|-----------|---------|
| グラム (g) | 0.001 | メートル法 |
| トン (t) | 1000 | メートル法 |
| オンス (oz) | 0.028349523125 | ヤード・ポンド法 |
| ストーン (st) | 6.35029318 | ヤード・ポンド法 |
| 匁 (monme) | 0.00375 | 尺貫法 |

### 12.2 プリセット入力
「体重60kg」「1袋（1kg）」「1ダース（卵：600g）」などをワンクリックでセット。

### 12.3 コピーボタン
各変換結果の横にコピーボタンを設置。

### 12.4 length_unit との統合ページ
長さ・重量を1ページにまとめた「単位変換ハブ」ページへの誘導リンクを追加。
