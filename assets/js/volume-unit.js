/**
 * ToolZoo - Volume Unit Converter
 * リットル・ガロン・升の相互変換（L経由で一括計算）
 */

(function () {
    'use strict';

    /**
     * 単位定義テーブル
     * literPerUnit: 1単位あたりのリットル数（厳密値）
     * decimals: 表示桁数
     */
    var UNITS = [
        { key: 'liter', id: 'toolzoo-volume-liter', literPerUnit: 1,            decimals: 5 },
        { key: 'gal',   id: 'toolzoo-volume-gal',   literPerUnit: 3.785411784,  decimals: 5 },
        { key: 'sho',   id: 'toolzoo-volume-sho',   literPerUnit: 1.8039,       decimals: 5 },
    ];

    // 無限ループ防止フラグ
    var isUpdating = false;

    /**
     * 初期化
     */
    function init() {
        if (!document.getElementById('toolzoo-volume-liter')) {
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
        var clearBtn = document.getElementById('toolzoo-volume-clear-btn');
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
                var literEl = document.getElementById('toolzoo-volume-liter');
                if (literEl) literEl.focus();
            });
        }

        // 初期値 1L で全欄を計算済みの状態にする
        setActiveField('liter');
        convertFromUnit(getUnit('liter'), 1);
    }

    /**
     * 入力値のバリデーション
     * @param {string} value
     * @returns {number|null}
     */
    function validateInput(value) {
        if (value === '' || value === null) return null;
        var num = parseFloat(value);
        if (isNaN(num)) return null;
        if (num < 0) return 0;
        return num;
    }

    /**
     * 指定単位の値から L に換算し、他全単位へ変換して表示
     * @param {{ literPerUnit: number }} sourceUnit
     * @param {number} value
     */
    function convertFromUnit(sourceUnit, value) {
        var liters = value * sourceUnit.literPerUnit;
        isUpdating = true;
        UNITS.forEach(function (u) {
            if (u.key === sourceUnit.key) return;
            var el = document.getElementById(u.id);
            if (el) {
                el.value = (liters / u.literPerUnit).toFixed(u.decimals);
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
