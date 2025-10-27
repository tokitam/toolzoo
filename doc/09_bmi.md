# BMI計算ツール 詳細設計

## 1. 機能概要

### 1.1 機能名
BMIの計算

### 1.2 ショートコード
```
[toolzoo_bmi]
```

### 1.3 機能説明
ユーザーが身長と体重を入力することで、BMI（体格指数）を自動計算して表示するツール。
BMIと共に適正体重も表示し、ユーザーが自分の健康状態を把握できるようにサポートします。
さらに、BMIについての説明と各BMI値の意味を下部に表示します。

### 1.4 表示内容
- 計算結果
  - BMI値（小数第1位）
  - 適正体重範囲（kg、小数第1位）
  - BMI評価（痩せ、普通体重、過体重、肥満）
- 説明セクション
  - BMI計算式の説明
  - BMI値と評価の対応表

## 2. UI設計

### 2.1 画面レイアウト
```
┌─────────────────────────────────────────────┐
│   BMIの計算                                  │
├─────────────────────────────────────────────┤
│                                             │
│ 身長：  [150] cm  ────────────────────────  │
│         (100-250cm)                         │
│                                             │
│ 体重：  [60]  kg  ────────────────────────  │
│         (20-200kg)                          │
│                                             │
│ [ 計算する ]                                 │
│                                             │
├─────────────────────────────────────────────┤
│ 計算結果                                     │
├─────────────────────────────────────────────┤
│                                             │
│ BMI: 26.7                                   │
│ 評価: 過体重                                │
│                                             │
│ 適正体重: 50.6 kg ~ 68.1 kg                │
│                                             │
├─────────────────────────────────────────────┤
│ BMIについて                                  │
├─────────────────────────────────────────────┤
│                                             │
│ 【BMI計算式】                               │
│ BMI = 体重(kg) ÷ 身長(m)²                   │
│                                             │
│ 【BMI評価】                                 │
│ BMI < 18.5       : 痩せ型                  │
│ 18.5 ≤ BMI < 25  : 普通体重                │
│ 25 ≤ BMI < 30    : 過体重                  │
│ 30 ≤ BMI         : 肥満                    │
│                                             │
│ 【適正体重について】                       │
│ 適正体重 = (身長m)² × 22 を目安とします    │
│ 一般的な健康指標では BMI22が適正とされています。│
│ 適正体重範囲は BMI 18.5 ~ 25 に                │
│ 対応する体重の幅を表示しています。               │
│                                             │
└─────────────────────────────────────────────┘
```

### 2.2 UI要素詳細

#### 2.2.1 身長入力欄
- **タイプ**: テキスト入力 + スピナー
- **ID**: `toolzoo-bmi-height`
- **単位**: cm
- **範囲**: 100 ~ 250cm
- **デフォルト値**: 170
- **ステップ**: 0.5
- **バリデーション**: 100以上250以下の数値

#### 2.2.2 体重入力欄
- **タイプ**: テキスト入力 + スピナー
- **ID**: `toolzoo-bmi-weight`
- **単位**: kg
- **範囲**: 20 ~ 200kg
- **デフォルト値**: 60
- **ステップ**: 0.5
- **バリデーション**: 20以上200以下の数値

#### 2.2.3 計算ボタン
- **テキスト**: "計算する"
- **ID**: `toolzoo-bmi-calculate-btn`
- **動作**: クリック時に BMI と適正体重を計算して表示

#### 2.2.4 計算結果表示エリア
- **ID**: `toolzoo-bmi-results-container`
- **初期状態**: 非表示
- **表示内容**:
  - BMI値（`toolzoo-bmi-value`）: 例「26.7」
  - BMI評価（`toolzoo-bmi-category`）: 「痩せ型」「普通体重」「過体重」「肥満」
  - 適正体重範囲（`toolzoo-bmi-ideal-weight`）: 例「50.6 kg ~ 68.1 kg」

#### 2.2.5 説明セクション
- **ID**: `toolzoo-bmi-info-section`
- **構成**:
  - BMI計算式
  - BMI評価表
  - 適正体重についての説明

## 3. 機能仕様

### 3.1 BMI計算アルゴリズム

#### 3.1.1 計算式
```
BMI = 体重(kg) ÷ 身長(m)²
```

#### 3.1.2 処理フロー
```
1. ユーザー入力を取得
   ↓
2. 入力値をバリデーション
   - 身長: 100 ~ 250cm
   - 体重: 20 ~ 200kg
   ↓
3. 入力が正常な場合:
   a. 身長をメートルに変換（cm ÷ 100）
   b. BMI = 体重 ÷ (身長m)²
   c. BMI値を小数第1位で四捨五入
   ↓
4. BMI評価を判定
   - BMI < 18.5: 痩せ型
   - 18.5 ≤ BMI < 25: 普通体重
   - 25 ≤ BMI < 30: 過体重
   - 30 ≤ BMI: 肥満
   ↓
5. 適正体重を計算
   - 最小適正体重 = (身長m)² × 18.5
   - 最大適正体重 = (身長m)² × 25
   ↓
6. 結果を画面に表示
   ↓
7. エラーの場合はエラーメッセージを表示
```

#### 3.1.3 実装方法（JavaScript）
```javascript
function calculateBMI() {
  const heightCm = parseFloat(document.getElementById('toolzoo-bmi-height').value);
  const weightKg = parseFloat(document.getElementById('toolzoo-bmi-weight').value);

  // バリデーション
  if (!validateInput(heightCm, weightKg)) {
    showError('身長は100～250cm、体重は20～200kgで入力してください。');
    return;
  }

  // BMI計算
  const heightM = heightCm / 100;
  const bmi = weightKg / (heightM * heightM);

  // 評価判定
  let category;
  if (bmi < 18.5) {
    category = '痩せ型';
  } else if (bmi < 25) {
    category = '普通体重';
  } else if (bmi < 30) {
    category = '過体重';
  } else {
    category = '肥満';
  }

  // 適正体重計算
  const minIdealWeight = heightM * heightM * 18.5;
  const maxIdealWeight = heightM * heightM * 25;

  // 結果表示
  displayResults(bmi, category, minIdealWeight, maxIdealWeight);
}

function validateInput(height, weight) {
  return !isNaN(height) && !isNaN(weight) &&
         height >= 100 && height <= 250 &&
         weight >= 20 && weight <= 200;
}

function displayResults(bmi, category, minWeight, maxWeight) {
  document.getElementById('toolzoo-bmi-value').textContent = bmi.toFixed(1);
  document.getElementById('toolzoo-bmi-category').textContent = category;
  document.getElementById('toolzoo-bmi-ideal-weight').textContent =
    `${minWeight.toFixed(1)} kg ~ ${maxWeight.toFixed(1)} kg`;

  document.getElementById('toolzoo-bmi-results-container').style.display = 'block';
  hideError();
}

function showError(message) {
  const errorDiv = document.getElementById('toolzoo-bmi-error-message');
  errorDiv.textContent = message;
  errorDiv.style.display = 'block';
  document.getElementById('toolzoo-bmi-results-container').style.display = 'none';
}

function hideError() {
  document.getElementById('toolzoo-bmi-error-message').style.display = 'none';
}
```

### 3.2 イベントハンドラー

#### 3.2.1 イベントリスナー設定
```javascript
document.addEventListener('DOMContentLoaded', function() {
  initBMICalculator();
});

function initBMICalculator() {
  // 計算ボタンのイベント
  const calculateBtn = document.getElementById('toolzoo-bmi-calculate-btn');
  calculateBtn.addEventListener('click', calculateBMI);

  // Enterキーで計算
  const heightInput = document.getElementById('toolzoo-bmi-height');
  const weightInput = document.getElementById('toolzoo-bmi-weight');

  heightInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      calculateBMI();
    }
  });

  weightInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      calculateBMI();
    }
  });

  // 初期値で自動計算（オプション）
  // calculateBMI();
}
```

### 3.3 入力値の自動調整
```javascript
// 身長の入力値が範囲外の場合は自動修正
document.getElementById('toolzoo-bmi-height').addEventListener('blur', function() {
  let value = parseFloat(this.value);
  if (value < 100) this.value = 100;
  if (value > 250) this.value = 250;
});

// 体重の入力値が範囲外の場合は自動修正
document.getElementById('toolzoo-bmi-weight').addEventListener('blur', function() {
  let value = parseFloat(this.value);
  if (value < 20) this.value = 20;
  if (value > 200) this.value = 200;
});
```

### 3.4 BMI評価の色分け
```javascript
function getColorForCategory(category) {
  const colors = {
    '痩せ型': '#0066cc',      // 青
    '普通体重': '#00aa00',    // 緑
    '過体重': '#ffaa00',      // オレンジ
    '肥満': '#dd0000'         // 赤
  };
  return colors[category] || '#000000';
}

function applyColorToResult(category) {
  const color = getColorForCategory(category);
  document.getElementById('toolzoo-bmi-category').style.color = color;
  // またはカテゴリクラスを追加
  document.getElementById('toolzoo-bmi-results-container').className =
    `toolzoo-bmi-results toolzoo-bmi-${category}`;
}
```

## 4. PHPクラス設計

### 4.1 クラス名
`Toolzoo_BMI_Calculator`

### 4.2 ファイルパス
`includes/class-bmi-calculator.php`

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
  - `wp_enqueue_style('toolzoo-bmi-css')`
  - `wp_enqueue_script('toolzoo-bmi-js')`

### 4.4 ショートコードハンドラー
```php
function toolzoo_bmi_shortcode($atts) {
    $calculator = new Toolzoo_BMI_Calculator();
    return $calculator->render();
}
add_shortcode('toolzoo_bmi', 'toolzoo_bmi_shortcode');
```

## 5. CSS設計

### 5.1 ファイル名
`assets/css/bmi.css`

### 5.2 主要クラス
- `.toolzoo-bmi-container`: 全体コンテナ
- `.toolzoo-bmi-header`: ヘッダー
- `.toolzoo-bmi-input-group`: 入力グループ
- `.toolzoo-bmi-input-wrapper`: 入力フィールドラッパー
- `.toolzoo-bmi-input-label`: 入力ラベル
- `.toolzoo-bmi-input-field`: 入力フィールド
- `.toolzoo-bmi-input-unit`: 単位表示
- `.toolzoo-bmi-button`: ボタン
- `.toolzoo-bmi-results-container`: 結果コンテナ
- `.toolzoo-bmi-result-item`: 結果アイテム
- `.toolzoo-bmi-category`: カテゴリ表示
- `.toolzoo-bmi-category.toolzoo-bmi-痩せ型`: 痩せ型スタイル
- `.toolzoo-bmi-category.toolzoo-bmi-普通体重`: 普通体重スタイル
- `.toolzoo-bmi-category.toolzoo-bmi-過体重`: 過体重スタイル
- `.toolzoo-bmi-category.toolzoo-bmi-肥満`: 肥満スタイル
- `.toolzoo-bmi-info-section`: 説明セクション
- `.toolzoo-bmi-error`: エラーメッセージ

### 5.3 スタイル例
```css
.toolzoo-bmi-container {
  max-width: 600px;
  margin: 20px 0;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #f9f9f9;
}

.toolzoo-bmi-header h3 {
  margin: 0 0 20px 0;
  font-size: 24px;
  color: #333;
}

.toolzoo-bmi-input-group {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 20px;
}

.toolzoo-bmi-input-wrapper {
  display: flex;
  align-items: center;
  gap: 10px;
}

.toolzoo-bmi-input-label {
  min-width: 60px;
  font-weight: bold;
  color: #333;
}

.toolzoo-bmi-input-field {
  width: 100px;
  padding: 8px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
  text-align: center;
}

.toolzoo-bmi-input-field:focus {
  outline: none;
  border-color: #0066cc;
  box-shadow: 0 0 5px rgba(0, 102, 204, 0.3);
}

.toolzoo-bmi-input-unit {
  font-size: 14px;
  color: #666;
  min-width: 30px;
}

.toolzoo-bmi-button {
  padding: 10px 30px;
  font-size: 14px;
  font-weight: bold;
  color: white;
  background: #0066cc;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.2s;
}

.toolzoo-bmi-button:hover {
  background: #0052a3;
}

.toolzoo-bmi-results-container {
  display: none;
  margin-top: 20px;
  padding: 20px;
  background: white;
  border: 2px solid #0066cc;
  border-radius: 8px;
}

.toolzoo-bmi-result-item {
  margin: 10px 0;
  font-size: 16px;
}

.toolzoo-bmi-result-item strong {
  min-width: 80px;
  display: inline-block;
  color: #333;
}

.toolzoo-bmi-value {
  font-size: 28px;
  font-weight: bold;
  color: #0066cc;
}

.toolzoo-bmi-category {
  font-size: 18px;
  font-weight: bold;
  padding: 8px 12px;
  border-radius: 4px;
  display: inline-block;
}

.toolzoo-bmi-category.toolzoo-bmi-痩せ型 {
  background: rgba(0, 102, 204, 0.1);
  color: #0066cc;
}

.toolzoo-bmi-category.toolzoo-bmi-普通体重 {
  background: rgba(0, 170, 0, 0.1);
  color: #00aa00;
}

.toolzoo-bmi-category.toolzoo-bmi-過体重 {
  background: rgba(255, 170, 0, 0.1);
  color: #ffaa00;
}

.toolzoo-bmi-category.toolzoo-bmi-肥満 {
  background: rgba(221, 0, 0, 0.1);
  color: #dd0000;
}

.toolzoo-bmi-ideal-weight {
  font-size: 16px;
  color: #666;
}

.toolzoo-bmi-info-section {
  margin-top: 30px;
  padding-top: 20px;
  border-top: 2px solid #ddd;
}

.toolzoo-bmi-info-section h4 {
  margin-top: 15px;
  margin-bottom: 10px;
  color: #333;
  font-size: 16px;
}

.toolzoo-bmi-info-section p {
  margin: 5px 0;
  color: #666;
  font-size: 14px;
  line-height: 1.6;
}

.toolzoo-bmi-info-table {
  width: 100%;
  margin: 10px 0;
  border-collapse: collapse;
}

.toolzoo-bmi-info-table th,
.toolzoo-bmi-info-table td {
  padding: 8px 12px;
  text-align: left;
  border-bottom: 1px solid #eee;
  font-size: 14px;
}

.toolzoo-bmi-info-table th {
  background: #f5f5f5;
  font-weight: bold;
}

.toolzoo-bmi-error {
  display: none;
  margin-bottom: 15px;
  padding: 12px;
  background: #ffebee;
  color: #c62828;
  border: 1px solid #ef5350;
  border-radius: 4px;
  font-size: 14px;
}

@media screen and (max-width: 600px) {
  .toolzoo-bmi-container {
    padding: 15px;
  }

  .toolzoo-bmi-input-wrapper {
    flex-wrap: wrap;
  }

  .toolzoo-bmi-input-field {
    width: 80px;
  }
}
```

## 6. JavaScript設計

### 6.1 ファイル名
`assets/js/bmi.js`

### 6.2 主要関数

#### 6.2.1 初期化
```javascript
document.addEventListener('DOMContentLoaded', function() {
  initBMICalculator();
});

function initBMICalculator() {
  setupEventListeners();
}
```

#### 6.2.2 イベントハンドラー設定
```javascript
function setupEventListeners() {
  // 計算ボタン
  const calculateBtn = document.getElementById('toolzoo-bmi-calculate-btn');
  if (calculateBtn) {
    calculateBtn.addEventListener('click', calculateBMI);
  }

  // 身長入力フィールド
  const heightInput = document.getElementById('toolzoo-bmi-height');
  if (heightInput) {
    heightInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') calculateBMI();
    });
    heightInput.addEventListener('blur', function() {
      constrainValue(this, 100, 250);
    });
  }

  // 体重入力フィールド
  const weightInput = document.getElementById('toolzoo-bmi-weight');
  if (weightInput) {
    weightInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') calculateBMI();
    });
    weightInput.addEventListener('blur', function() {
      constrainValue(this, 20, 200);
    });
  }
}
```

#### 6.2.3 主要関数リスト
- `initBMICalculator()`: 初期化
- `setupEventListeners()`: イベントリスナー設定
- `calculateBMI()`: BMI計算と結果表示
- `validateInput(height, weight)`: 入力値バリデーション
- `displayResults(bmi, category, minWeight, maxWeight)`: 結果表示
- `getCategory(bmi)`: BMI評価判定
- `getColorForCategory(category)`: 評価に対応する色取得
- `showError(message)`: エラーメッセージ表示
- `hideError()`: エラーメッセージ非表示
- `constrainValue(input, min, max)`: 入力値を指定範囲に制限

### 6.3 グローバル変数
```javascript
// 特になし（ステートレス設計）
```

## 7. HTML構造

### 7.1 基本構造
```html
<div class="toolzoo-bmi-container" id="toolzoo-bmi-calculator">
  <div class="toolzoo-bmi-header">
    <h3>BMIの計算</h3>
  </div>

  <div id="toolzoo-bmi-error-message" class="toolzoo-bmi-error"></div>

  <div class="toolzoo-bmi-input-group">
    <div class="toolzoo-bmi-input-wrapper">
      <label class="toolzoo-bmi-input-label" for="toolzoo-bmi-height">身長</label>
      <input
        type="number"
        id="toolzoo-bmi-height"
        class="toolzoo-bmi-input-field"
        min="100"
        max="250"
        value="170"
        step="0.5"
      >
      <span class="toolzoo-bmi-input-unit">cm</span>
    </div>

    <div class="toolzoo-bmi-input-wrapper">
      <label class="toolzoo-bmi-input-label" for="toolzoo-bmi-weight">体重</label>
      <input
        type="number"
        id="toolzoo-bmi-weight"
        class="toolzoo-bmi-input-field"
        min="20"
        max="200"
        value="60"
        step="0.5"
      >
      <span class="toolzoo-bmi-input-unit">kg</span>
    </div>

    <button id="toolzoo-bmi-calculate-btn" class="toolzoo-bmi-button">
      計算する
    </button>
  </div>

  <div id="toolzoo-bmi-results-container" class="toolzoo-bmi-results-container">
    <div class="toolzoo-bmi-result-item">
      <strong>BMI:</strong>
      <span id="toolzoo-bmi-value" class="toolzoo-bmi-value">--</span>
    </div>
    <div class="toolzoo-bmi-result-item">
      <strong>評価:</strong>
      <span id="toolzoo-bmi-category" class="toolzoo-bmi-category">--</span>
    </div>
    <div class="toolzoo-bmi-result-item">
      <strong>適正体重:</strong>
      <span id="toolzoo-bmi-ideal-weight" class="toolzoo-bmi-ideal-weight">--</span>
    </div>
  </div>

  <div class="toolzoo-bmi-info-section">
    <h4>BMIについて</h4>

    <h5>BMI計算式</h5>
    <p>BMI = 体重(kg) ÷ 身長(m)²</p>

    <h5>BMI評価</h5>
    <table class="toolzoo-bmi-info-table">
      <thead>
        <tr>
          <th>BMI値</th>
          <th>評価</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>BMI &lt; 18.5</td>
          <td>痩せ型</td>
        </tr>
        <tr>
          <td>18.5 ≤ BMI &lt; 25</td>
          <td>普通体重</td>
        </tr>
        <tr>
          <td>25 ≤ BMI &lt; 30</td>
          <td>過体重</td>
        </tr>
        <tr>
          <td>30 ≤ BMI</td>
          <td>肥満</td>
        </tr>
      </tbody>
    </table>

    <h5>適正体重について</h5>
    <p>
      適正体重は BMI が 18.5 ～ 25 の範囲に対応する体重です。
      一般的には BMI が 22 の場合の体重が最も健康的とされています。
    </p>
  </div>
</div>
```

## 8. セキュリティ考慮事項

### 8.1 入力値バリデーション
- 数値型入力フィールドを使用（HTML5の type="number"）
- JavaScript側でも数値チェック
- 範囲チェック（身長100-250、体重20-200）

### 8.2 XSS対策
- PHP側で `esc_html()`, `esc_attr()` 使用
- JavaScript側で `textContent` 使用

### 8.3 計算のセキュリティ
- クライアント側のみで処理（サーバーに送信しない）
- 機密情報なし

## 9. テスト項目

### 9.1 機能テスト

#### 9.1.1 BMI計算
- [ ] 正常な値で BMI が正しく計算される
- [ ] 身長100cm、体重20kgでの計算
- [ ] 身長250cm、体重200kgでの計算
- [ ] 小数点を含む値での計算
- [ ] 計算結果が小数第1位で表示される

#### 9.1.2 評価判定
- [ ] BMI < 18.5 で「痩せ型」と表示
- [ ] 18.5 ≤ BMI < 25 で「普通体重」と表示
- [ ] 25 ≤ BMI < 30 で「過体重」と表示
- [ ] 30 ≤ BMI で「肥満」と表示

#### 9.1.3 適正体重計算
- [ ] 適正体重の最小値が正しく計算される
- [ ] 適正体重の最大値が正しく計算される
- [ ] 範囲が正しく表示される

#### 9.1.4 入力値バリデーション
- [ ] 身長が100未満の場合はエラー表示
- [ ] 身長が250を超える場合はエラー表示
- [ ] 体重が20未満の場合はエラー表示
- [ ] 体重が200を超える場合はエラー表示
- [ ] 空欄でのエラー処理
- [ ] 非数値入力でのエラー処理

#### 9.1.5 UI操作
- [ ] 計算ボタンクリックで計算実行
- [ ] Enterキーで計算実行
- [ ] 入力フィールドの自動修正

### 9.2 ブラウザ互換性テスト
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] number入力タイプの動作確認

### 9.3 レスポンシブテスト
- [ ] スマートフォン (320px~): 入力フィールドの配置
- [ ] タブレット (768px~): レイアウトが崩れない
- [ ] デスクトップ (1024px~): 見やすい表示

### 9.4 アクセシビリティテスト
- [ ] キーボード操作（Tab、Enter）
- [ ] ラベルと入力フィールドの関連付け
- [ ] エラーメッセージが読み取り可能

### 9.5 計算精度テスト
- [ ] 浮動小数点演算の精度確認
- [ ] 丸め誤差の確認

## 10. 将来の拡張案

### 10.1 機能拡張
- **身長・体重の単位切り替え**: ポンド、インチ等
- **履歴機能**: 過去の計算結果を表示
- **グラフ表示**: BMI推移のグラフ化
- **目標設定**: 目標BMIに対する必要体重変化量の計算
- **多言語対応**: 英語、中国語等
- **年齢別標準**: 年代別の適正BMI

### 10.2 UI改善
- **表示形式の選択**: シンプル/詳細表示
- **自動計算**: 入力値変更時に自動計算
- **リセットボタン**: 入力値をリセット
- **お気に入り**: 複数パターンを保存

### 10.3 統合機能
- **健康管理ツール**: 他の健康指標との連携
- **データベース**: 複数ユーザーの管理
- **レポート出力**: PDF形式での結果出力

## 11. 使用例

### 11.1 基本的な使い方
1. ページにアクセス
2. 身長（170cm）と体重（60kg）を入力
3. 「計算する」ボタンをクリック
4. BMI（20.8）と評価（普通体重）が表示される
5. 適正体重範囲が表示される

### 11.2 複数パターンの計算
1. 身長・体重を変更
2. 「計算する」ボタンをクリック
3. 新しい結果が表示される

### 11.3 キーボード操作
1. 身長フィールドで Enterキーを押す
2. 自動的に計算が実行される

## 12. 定数定義

Constants クラスに以下を追加予定：
```php
const TOOL_BMI = array(
    'name' => 'BMIの計算',
    'slug' => 'toolzoo_bmi',
    'description' => '身長と体重を入力して BMI と適正体重を計算します',
    'height_min' => 100,
    'height_max' => 250,
    'weight_min' => 20,
    'weight_max' => 200,
    'bmi_underweight' => 18.5,
    'bmi_normal_max' => 25,
    'bmi_overweight_max' => 30,
    'ideal_bmi' => 22,
);
```
