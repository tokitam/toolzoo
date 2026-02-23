/**
 * ToolZoo - Length Unit Converter
 * 各種長さ単位の相互変換（メートル経由で一括計算）
 */

(function () {
    'use strict';

    /**
     * 単位定義テーブル
     * metersPerUnit: 1単位あたりのメートル数（厳密値）
     * decimals: 表示桁数
     */
    var UNITS = [
        // ---- メートル法 ----
        { key: 'km',    id: 'toolzoo-length-km',    metersPerUnit: 1000,          decimals: 7 },
        { key: 'meter', id: 'toolzoo-length-meter',  metersPerUnit: 1,             decimals: 5 },
        { key: 'cm',    id: 'toolzoo-length-cm',     metersPerUnit: 0.01,          decimals: 3 },
        // ---- ヤード・ポンド法 ----
        { key: 'mile',  id: 'toolzoo-length-mile',   metersPerUnit: 1609.344,      decimals: 7 },
        { key: 'feet',  id: 'toolzoo-length-feet',   metersPerUnit: 0.3048,        decimals: 5 },
        { key: 'inch',  id: 'toolzoo-length-inch',   metersPerUnit: 0.0254,        decimals: 4 },
        // ---- 尺貫法 ----
        { key: 'ri',    id: 'toolzoo-length-ri',     metersPerUnit: 129600 / 33,   decimals: 7 },
        { key: 'sun',   id: 'toolzoo-length-sun',    metersPerUnit: 1 / 33,        decimals: 4 },
        { key: 'bu',    id: 'toolzoo-length-bu',     metersPerUnit: 1 / 330,       decimals: 3 },
        { key: 'rin',   id: 'toolzoo-length-rin',    metersPerUnit: 1 / 3300,      decimals: 2 },
    ];

    // 無限ループ防止フラグ
    var isUpdating = false;

    /**
     * 初期化
     */
    function init() {
        // ウィジェットが存在しない場合はスキップ
        if (!document.getElementById('toolzoo-length-meter')) {
            return;
        }

        // 各フィールドにイベント設定
        UNITS.forEach(function (unit) {
            var el = document.getElementById(unit.id);
            if (!el) return;

            el.addEventListener('input', function () {
                if (isUpdating) return;
                setActiveField(unit.key);
                var val = validateInput(this.value);
                if (val !== null) {
                    convertFromUnit(unit, val);
                } else {
                    clearAllExcept(unit.key);
                }
            });

            el.addEventListener('blur', function () {
                el.classList.remove('active');
            });
        });

        // クリアボタン
        var clearBtn = document.getElementById('toolzoo-length-clear-btn');
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                isUpdating = true;
                UNITS.forEach(function (u) {
                    var el = document.getElementById(u.id);
                    if (el) {
                        el.value = '';
                        el.classList.remove('active');
                    }
                });
                isUpdating = false;
                var meterEl = document.getElementById('toolzoo-length-meter');
                if (meterEl) meterEl.focus();
            });
        }

        // 初期値 1m で全欄を計算済みの状態にする
        var meterUnit = getUnit('meter');
        setActiveField('meter');
        convertFromUnit(meterUnit, 1);
    }

    /**
     * 入力値のバリデーション
     * @param {string} value
     * @returns {number|null} 有効な数値 or null
     */
    function validateInput(value) {
        if (value === '' || value === null) return null;
        var num = parseFloat(value);
        if (isNaN(num)) return null;
        if (num < 0) return 0;
        return num;
    }

    /**
     * 指定単位の値からメートルに換算し、他全単位へ変換して表示
     * @param {{ metersPerUnit: number }} sourceUnit
     * @param {number} value
     */
    function convertFromUnit(sourceUnit, value) {
        var meters = value * sourceUnit.metersPerUnit;
        isUpdating = true;
        UNITS.forEach(function (u) {
            if (u.key === sourceUnit.key) return;
            var el = document.getElementById(u.id);
            if (el) {
                el.value = (meters / u.metersPerUnit).toFixed(u.decimals);
            }
        });
        isUpdating = false;
    }

    /**
     * 指定キー以外の全フィールドをクリア
     * @param {string} activeKey
     */
    function clearAllExcept(activeKey) {
        isUpdating = true;
        UNITS.forEach(function (u) {
            if (u.key === activeKey) return;
            var el = document.getElementById(u.id);
            if (el) el.value = '';
        });
        isUpdating = false;
    }

    /**
     * 編集中フィールドをハイライト、他は解除
     * @param {string} activeKey
     */
    function setActiveField(activeKey) {
        UNITS.forEach(function (u) {
            var el = document.getElementById(u.id);
            if (!el) return;
            if (u.key === activeKey) {
                el.classList.add('active');
            } else {
                el.classList.remove('active');
            }
        });
    }

    /**
     * UNITS から key で単位定義を取得
     * @param {string} key
     * @returns {object}
     */
    function getUnit(key) {
        for (var i = 0; i < UNITS.length; i++) {
            if (UNITS[i].key === key) return UNITS[i];
        }
        return UNITS[0];
    }

    // DOM 読み込み完了後に初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
