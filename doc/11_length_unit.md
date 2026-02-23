# 長さ単位変換ツール 詳細設計

## 1. 機能概要

### 1.1 機能名
長さ単位変換（メートル・フィート・寸）

### 1.2 ショートコード
```
[toolzoo_length_unit]
```

### 1.3 機能説明
メートル・フィート・寸（日本伝統尺貫法）の3単位を相互変換するツール。
ページ上部に1メートルを基準とした早見表を表示し、下部の自由入力欄で任意の値を入力すると
リアルタイムに他2単位へ自動変換して表示する。
尺貫法の単位体系の説明も付属する。

### 1.4 単位の定義・換算基準

| 変換元 | 変換先 | 換算式 | 根拠 |
|--------|--------|--------|------|
| 1 メートル | フィート | × 1/0.3048 ≈ 3.28084 | 国際ヤード・ポンド協定（1959年）|
| 1 メートル | 寸       | × 33 = 33.0000 | 日本計量法（1寸 = 1/33 m）|
| 1 フィート | メートル | × 0.3048 | 国際ヤード・ポンド協定 |
| 1 フィート | 寸       | × 0.3048 × 33 ≈ 10.0584 | 上記組み合わせ |
| 1 寸       | メートル | × 1/33 ≈ 0.030303 | 日本計量法 |
| 1 寸       | フィート | ÷ 0.3048 × (1/33) ≈ 0.099419 | 上記組み合わせ |

**定数値まとめ:**
- `FEET_PER_METER = 1 / 0.3048`（≈ 3.280839895...）
- `SUN_PER_METER = 33`（厳密値）
- `METER_PER_FEET = 0.3048`（厳密値）
- `SUN_PER_FEET = 0.3048 × 33`（≈ 10.0584）
- `METER_PER_SUN = 1 / 33`（≈ 0.030303...）
- `FEET_PER_SUN = 1 / (33 × 0.3048)`（≈ 0.099419...）

---

## 2. UI設計

### 2.1 画面レイアウト

```
┌─────────────────────────────────────────────┐
│   長さ単位変換                               │
├─────────────────────────────────────────────┤
│ 基準値早見表                                 │
├─────────────┬─────────────┬─────────────────┤
│  基準値      │  フィート    │  寸              │
├─────────────┼─────────────┼─────────────────┤
│  1 メートル  │  3.2808 ft  │  33.0000 寸      │
│  1 フィート  │  ─          │  10.0584 寸      │
│  1 寸        │  0.0994 ft  │  ─              │
└─────────────┴─────────────┴─────────────────┘
│ ※ 1寸 = 1/33 m（日本計量法）                │
│ ※ 1フィート = 0.3048 m（国際定義）           │
├─────────────────────────────────────────────┤
│ 自由変換（いずれかに入力すると自動変換）      │
│                                             │
│  メートル:  [       1.000] m               │
│  フィート:  [       3.281] ft              │
│  寸    :   [      33.000] 寸              │
│                                             │
│  [ クリア ]                                 │
├─────────────────────────────────────────────┤
│ 尺貫法 単位体系                              │
├─────────────────────────────────────────────┤
│  1 厘 = 1/10 分 ≈ 0.303 mm                 │
│  1 分 = 1/10 寸 ≈ 3.03 mm                  │
│  1 寸 = 1/10 尺 ≈ 30.3 mm                  │
│  1 尺 = 10 寸 ≈ 30.3 cm                    │
│  1 間 = 6 尺 ≈ 181.8 cm                    │
│  1 丈 = 10 尺 ≈ 3.03 m                     │
│  1 町 = 60 間 ≈ 109.1 m                    │
│  1 里 = 36 町 ≈ 3.927 km                   │
└─────────────────────────────────────────────┘
```

### 2.2 UI要素詳細

#### 2.2.1 早見表（上部リファレンス）
- **タイプ**: 静的テーブル（HTML固定表示）
- **ID**: `toolzoo-length-reference-table`
- **内容**:
  - 行1: 1メートル → フィート / 寸
  - 行2: 1フィート → 寸（メートルは定義）
  - 行3: 1寸 → フィート（メートルは定義）
- **小数桁数**: 小数第4位

#### 2.2.2 メートル入力欄
- **タイプ**: number input
- **ID**: `toolzoo-length-meter`
- **単位**: m
- **デフォルト値**: 1
- **min**: 0
- **step**: 任意（小数可）
- **バリデーション**: 0以上の数値

#### 2.2.3 フィート入力欄
- **タイプ**: number input
- **ID**: `toolzoo-length-feet`
- **単位**: ft
- **デフォルト値**: 3.2808（1mから自動算出）
- **min**: 0
- **step**: 任意
- **バリデーション**: 0以上の数値

#### 2.2.4 寸入力欄
- **タイプ**: number input
- **ID**: `toolzoo-length-sun`
- **単位**: 寸
- **デフォルト値**: 33（1mから自動算出）
- **min**: 0
- **step**: 任意
- **バリデーション**: 0以上の数値

#### 2.2.5 クリアボタン
- **テキスト**: "クリア"
- **ID**: `toolzoo-length-clear-btn`
- **動作**: 全入力欄を空にする

#### 2.2.6 尺貫法説明セクション
- **ID**: `toolzoo-length-shakkan-section`
- **内容**: 厘・分・寸・尺・間・丈・町・里の単位体系とメートル換算
- **形式**: テーブル

---

## 3. 機能仕様

### 3.1 変換アルゴリズム

#### 3.1.1 基本変換式
```
// 入力単位がメートルの場合
feet = meter × (1 / 0.3048)
sun  = meter × 33

// 入力単位がフィートの場合
meter = feet × 0.3048
sun   = feet × 0.3048 × 33

// 入力単位が寸の場合
meter = sun / 33
feet  = sun / (33 × 0.3048)
```

#### 3.1.2 処理フロー（リアルタイム変換）
```
1. 各入力欄に `input` イベントリスナーを登録
   ↓
2. ユーザーがいずれかの入力欄を編集
   ↓
3. 編集された入力欄を「基準」として特定
   ↓
4. 入力値を数値に変換（parseFloat）
   ↓
5. バリデーション
   - 数値でない → 他の欄をクリアして終了
   - 0未満 → 0 に修正
   ↓
6. 基準単位に応じて他2単位を計算
   ↓
7. 結果を小数第4位で四捨五入して表示
   ↓
8. 他の入力欄に書き込む
   （書き込み中は `input` イベントが再発火しないよう制御）
```

#### 3.1.3 無限ループ防止
```javascript
let isUpdating = false; // フラグで制御

inputElement.addEventListener('input', function() {
  if (isUpdating) return;
  isUpdating = true;
  // 計算と他欄への書き込み
  isUpdating = false;
});
```

### 3.2 表示精度

| 単位 | 小数桁数 |
|------|--------|
| メートル | 第5位まで（例: 0.03030） |
| フィート | 第4位まで（例: 3.2808） |
| 寸 | 第4位まで（例: 33.0000） |

※ 桁数は JavaScript の `toFixed()` で制御。

### 3.3 入力値のバリデーション

```javascript
function validateLengthInput(value) {
  const num = parseFloat(value);
  if (isNaN(num)) return null;         // 数値以外 → null
  if (num < 0) return 0;               // 負値 → 0に補正
  return num;
}
```

### 3.4 早見表の表示値（固定）

| 基準値 | フィート換算値 | 寸換算値 |
|--------|--------------|--------|
| 1 メートル | 3.2808 ft | 33.0000 寸 |
| 1 フィート | ─ | 10.0584 寸 |
| 1 寸 | 0.0994 ft | ─ |

---

## 4. 追加提案

### 4.1 身近な長さの例示（サブ情報）
入力欄の下部または説明セクションに身近な参考値を表示。
ユーザーがスケールを直感的に理解しやすくなる。

```
【身近な長さの目安】
・畳の短辺（京間）: 約 3 尺 = 90.9 cm = 2.98 ft
・成人男性の平均身長: 約 5 尺 7 寸 = 172.7 cm = 5.67 ft
・6フィート（米国で言う背の高い人）: ≈ 182.9 cm ≈ 60.3 寸
```

### 4.2 小数桁数切り替えボタン（UI案）
「3桁 / 6桁」のトグルボタンで表示精度を切り替え可能にする。
精度が必要な用途（建築・加工など）向け。

### 4.3 入力単位の強調表示
現在編集中の入力欄を視覚的に強調（ハイライト）する。
「どの単位を基準に変換しているか」がひと目で分かる。

---

## 5. PHPクラス設計

### 5.1 クラス名
`Toolzoo_Length_Unit`

### 5.2 ファイルパス
`includes/class-length-unit.php`

### 5.3 メソッド一覧

#### 5.3.1 __construct()
- **説明**: コンストラクタ
- **処理**: 初期化処理

#### 5.3.2 render()
- **説明**: HTML を生成して返却
- **戻り値**: string (HTML)
- **処理**:
  - CSS/JS のエンキュー
  - HTML テンプレートの生成（早見表 + 入力エリア + 説明セクション）

#### 5.3.3 enqueue_assets()
- **説明**: CSS/JS を読み込み
- **処理**:
  - `wp_enqueue_style('toolzoo-length-unit-css')`
  - `wp_enqueue_script('toolzoo-length-unit-js')`

### 5.4 ショートコードハンドラー
```php
function toolzoo_length_unit_shortcode($atts) {
    $tool = new Toolzoo_Length_Unit();
    return $tool->render();
}
add_shortcode('toolzoo_length_unit', 'toolzoo_length_unit_shortcode');
```

---

## 6. CSS設計

### 6.1 ファイル名
`assets/css/length-unit.css`

### 6.2 主要クラス
- `.toolzoo-length-container`: 全体コンテナ
- `.toolzoo-length-header`: ヘッダー（タイトル）
- `.toolzoo-length-reference`: 早見表セクション
- `.toolzoo-length-reference-table`: 早見表テーブル
- `.toolzoo-length-converter`: 変換入力セクション
- `.toolzoo-length-input-row`: 入力行
- `.toolzoo-length-input-label`: 単位ラベル
- `.toolzoo-length-input-field`: 入力フィールド（編集中はハイライト）
- `.toolzoo-length-input-field.active`: 編集中のフィールド
- `.toolzoo-length-input-unit`: 単位テキスト（m / ft / 寸）
- `.toolzoo-length-clear-btn`: クリアボタン
- `.toolzoo-length-shakkan`: 尺貫法説明セクション
- `.toolzoo-length-shakkan-table`: 尺貫法テーブル
- `.toolzoo-length-note`: 補足注記

### 6.3 スタイル方針
- コンテナ最大幅: 600px
- 入力フィールド: 右揃え（数値の視認性向上）
- 編集中フィールド: ボーダー色を強調（`#0066cc`）
- レスポンシブ: 600px以下でラベルを上部に移動

---

## 7. JavaScript設計

### 7.1 ファイル名
`assets/js/length-unit.js`

### 7.2 定数定義
```javascript
const TOOLZOO_LENGTH = {
  FEET_PER_METER: 1 / 0.3048,          // ≈ 3.280839895
  SUN_PER_METER:  33,                  // 厳密値
  METER_PER_FEET: 0.3048,              // 厳密値
  SUN_PER_FEET:   0.3048 * 33,         // ≈ 10.0584
  METER_PER_SUN:  1 / 33,              // ≈ 0.030303
  FEET_PER_SUN:   1 / (33 * 0.3048),  // ≈ 0.099419
};
```

### 7.3 主要関数一覧

#### 7.3.1 初期化
```javascript
document.addEventListener('DOMContentLoaded', function() {
  initLengthUnitConverter();
});

function initLengthUnitConverter() {
  setupInputListeners();
  // デフォルト値1mで初期表示
  convertFromMeter(1);
}
```

#### 7.3.2 イベントリスナー設定
```javascript
function setupInputListeners() {
  const meterInput = document.getElementById('toolzoo-length-meter');
  const feetInput  = document.getElementById('toolzoo-length-feet');
  const sunInput   = document.getElementById('toolzoo-length-sun');
  const clearBtn   = document.getElementById('toolzoo-length-clear-btn');

  meterInput.addEventListener('input', function() {
    if (isUpdating) return;
    const val = validateLengthInput(this.value);
    if (val !== null) convertFromMeter(val);
    else clearOthers('meter');
    setActiveField('meter');
  });

  feetInput.addEventListener('input', function() {
    if (isUpdating) return;
    const val = validateLengthInput(this.value);
    if (val !== null) convertFromFeet(val);
    else clearOthers('feet');
    setActiveField('feet');
  });

  sunInput.addEventListener('input', function() {
    if (isUpdating) return;
    const val = validateLengthInput(this.value);
    if (val !== null) convertFromSun(val);
    else clearOthers('sun');
    setActiveField('sun');
  });

  clearBtn.addEventListener('click', clearAll);
}
```

#### 7.3.3 変換関数
```javascript
function convertFromMeter(meter) {
  isUpdating = true;
  document.getElementById('toolzoo-length-feet').value =
    (meter * TOOLZOO_LENGTH.FEET_PER_METER).toFixed(4);
  document.getElementById('toolzoo-length-sun').value =
    (meter * TOOLZOO_LENGTH.SUN_PER_METER).toFixed(4);
  isUpdating = false;
}

function convertFromFeet(feet) {
  isUpdating = true;
  document.getElementById('toolzoo-length-meter').value =
    (feet * TOOLZOO_LENGTH.METER_PER_FEET).toFixed(5);
  document.getElementById('toolzoo-length-sun').value =
    (feet * TOOLZOO_LENGTH.SUN_PER_FEET).toFixed(4);
  isUpdating = false;
}

function convertFromSun(sun) {
  isUpdating = true;
  document.getElementById('toolzoo-length-meter').value =
    (sun * TOOLZOO_LENGTH.METER_PER_SUN).toFixed(5);
  document.getElementById('toolzoo-length-feet').value =
    (sun * TOOLZOO_LENGTH.FEET_PER_SUN).toFixed(4);
  isUpdating = false;
}
```

#### 7.3.4 その他のユーティリティ関数
- `validateLengthInput(value)`: 入力値検証（null or 数値）
- `setActiveField(fieldName)`: 編集中フィールドのハイライト
- `clearAll()`: 全フィールドをクリア
- `clearOthers(activeField)`: 入力値が無効な場合に他フィールドをクリア

---

## 8. HTML構造

### 8.1 基本構造
```html
<div class="toolzoo-length-container" id="toolzoo-length-unit">

  <!-- ヘッダー -->
  <div class="toolzoo-length-header">
    <h3>長さ単位変換</h3>
  </div>

  <!-- 早見表 -->
  <div class="toolzoo-length-reference">
    <h4>基準値早見表</h4>
    <table class="toolzoo-length-reference-table">
      <thead>
        <tr>
          <th>基準値</th>
          <th>フィート (ft)</th>
          <th>寸</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1 メートル</td>
          <td>3.2808 ft</td>
          <td>33.0000 寸</td>
        </tr>
        <tr>
          <td>1 フィート</td>
          <td>—</td>
          <td>10.0584 寸</td>
        </tr>
        <tr>
          <td>1 寸</td>
          <td>0.0994 ft</td>
          <td>—</td>
        </tr>
      </tbody>
    </table>
    <p class="toolzoo-length-note">
      ※ 1寸 = 1/33 m（日本計量法） / 1フィート = 0.3048 m（国際定義）
    </p>
  </div>

  <!-- 自由変換エリア -->
  <div class="toolzoo-length-converter">
    <h4>自由変換（いずれかに入力すると自動変換）</h4>

    <div class="toolzoo-length-input-row">
      <label class="toolzoo-length-input-label" for="toolzoo-length-meter">
        メートル
      </label>
      <input
        type="number"
        id="toolzoo-length-meter"
        class="toolzoo-length-input-field"
        min="0"
        step="any"
        value="1"
      >
      <span class="toolzoo-length-input-unit">m</span>
    </div>

    <div class="toolzoo-length-input-row">
      <label class="toolzoo-length-input-label" for="toolzoo-length-feet">
        フィート
      </label>
      <input
        type="number"
        id="toolzoo-length-feet"
        class="toolzoo-length-input-field"
        min="0"
        step="any"
        value="3.2808"
      >
      <span class="toolzoo-length-input-unit">ft</span>
    </div>

    <div class="toolzoo-length-input-row">
      <label class="toolzoo-length-input-label" for="toolzoo-length-sun">
        寸
      </label>
      <input
        type="number"
        id="toolzoo-length-sun"
        class="toolzoo-length-input-field"
        min="0"
        step="any"
        value="33.0000"
      >
      <span class="toolzoo-length-input-unit">寸</span>
    </div>

    <button id="toolzoo-length-clear-btn" class="toolzoo-length-clear-btn">
      クリア
    </button>
  </div>

  <!-- 尺貫法 説明セクション -->
  <div class="toolzoo-length-shakkan" id="toolzoo-length-shakkan">
    <h4>尺貫法 単位体系</h4>
    <table class="toolzoo-length-shakkan-table">
      <thead>
        <tr>
          <th>単位</th>
          <th>関係</th>
          <th>メートル換算</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>1 厘（りん）</td><td>= 1/10 分</td><td>≈ 0.303 mm</td></tr>
        <tr><td>1 分（ぶ）</td><td>= 1/10 寸</td><td>≈ 3.03 mm</td></tr>
        <tr><td>1 寸（すん）</td><td>= 1/10 尺</td><td>≈ 30.3 mm</td></tr>
        <tr><td>1 尺（しゃく）</td><td>= 10 寸</td><td>≈ 303.0 mm (30.3 cm)</td></tr>
        <tr><td>1 間（けん）</td><td>= 6 尺</td><td>≈ 1818.2 mm (181.8 cm)</td></tr>
        <tr><td>1 丈（じょう）</td><td>= 10 尺</td><td>≈ 3030.3 mm (3.03 m)</td></tr>
        <tr><td>1 町（ちょう）</td><td>= 60 間</td><td>≈ 109.09 m</td></tr>
        <tr><td>1 里（り）</td><td>= 36 町</td><td>≈ 3927.3 m (3.927 km)</td></tr>
      </tbody>
    </table>
    <p class="toolzoo-length-note">
      ※ 1尺 = 10/33 m（日本計量法による定義）
    </p>
  </div>

</div>
```

---

## 9. セキュリティ考慮事項

### 9.1 入力値の安全な処理
- 全変換処理はクライアント側 JavaScript のみで完結（サーバー送信なし）
- JavaScript の `parseFloat()` で数値変換、`isNaN()` でバリデーション
- 結果表示は `value` プロパティへの代入（XSS の心配なし）

### 9.2 PHP 側の出力
- HTML は静的な固定テンプレート
- 動的な値を出力する場合は `esc_html()`, `esc_attr()` を使用

---

## 10. テスト項目

### 10.1 変換精度テスト
- [ ] 1m → 3.2808 ft（小数第4位）
- [ ] 1m → 33.0000 寸（小数第4位）
- [ ] 1ft → 0.30480 m（小数第5位）
- [ ] 1ft → 10.0584 寸（小数第4位）
- [ ] 1寸 → 0.03030 m（小数第5位）
- [ ] 1寸 → 0.0994 ft（小数第4位）
- [ ] 大きな値（例: 1000m）で精度を確認
- [ ] 小さな値（例: 0.001m）で精度を確認

### 10.2 リアルタイム変換動作テスト
- [ ] メートル入力欄の変更で他2欄が即時更新される
- [ ] フィート入力欄の変更で他2欄が即時更新される
- [ ] 寸入力欄の変更で他2欄が即時更新される
- [ ] 無限ループが発生しない
- [ ] 入力値削除時に他の欄がクリアされる

### 10.3 入力値バリデーションテスト
- [ ] 文字列入力時に他欄が空になる
- [ ] 負数入力時に 0 に補正される（または他欄をクリア）
- [ ] 0 入力時に変換が正常に動作する
- [ ] 小数点を含む値が正しく変換される

### 10.4 クリアボタンテスト
- [ ] クリアボタンで全欄が空になる

### 10.5 ブラウザ互換性テスト
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] モバイルブラウザ（iOS Safari / Android Chrome）

### 10.6 レスポンシブテスト
- [ ] スマートフォン (320px~): 入力欄の配置確認
- [ ] タブレット (768px~): レイアウト確認
- [ ] デスクトップ (1024px~): 全体の見やすさ確認

---

## 11. 実装手順

1. ⬜ この設計書を作成 ✅
2. ⬜ `includes/class-length-unit.php` を実装
3. ⬜ `assets/css/length-unit.css` を実装
4. ⬜ `assets/js/length-unit.js` を実装
5. ⬜ `includes/class-constants.php` にツール定義を追加
   - `id`: `'length'`
   - `slug`: `'length'`
   - `shortcode`: `'[toolzoo_length_unit]'`
6. ⬜ `includes/class-toolzoo.php` にショートコード登録・リライトルール追加
7. ⬜ `toolzoo.php` に変更がある場合は修正
8. ⬜ `doc/01_overview.md` の機能一覧・ディレクトリ構造を更新

---

## 12. 将来の拡張案

### 12.1 単位の追加
- インチ（in）: 1 ft = 12 in
- ヤード（yd）: 1 yd = 3 ft
- マイル（mi）: 1 mi = 1760 yd
- センチメートル・ミリメートル（表示専用）
- 尺貫法のより細かい単位（分・厘）

### 12.2 プリセット入力
よく使う値をワンクリックでセット（例: 「1間 (6尺)」「6フィート」「170cm」）。

### 12.3 履歴表示
直近の変換履歴を最大5件表示し、再利用できるようにする。

### 12.4 コピーボタン
各変換結果の横にコピーボタンを設置（IP-CHECKERと同様の実装）。

### 12.5 表示精度のトグル
「標準（4桁）/ 精密（8桁）」を切り替えるボタンを追加。
