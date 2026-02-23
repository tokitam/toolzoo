/**
 * ToolZoo - Weight Unit Converter
 * キログラム・ポンド・貫の相互変換（kg経由で一括計算）
 */

(function () {
    'use strict';

    /**
     * 単位定義テーブル
     * kgPerUnit: 1単位あたりのキログラム数（厳密値）
     * decimals: 表示桁数
     */
    var UNITS = [
        { key: 'kg',  id: 'toolzoo-weight-kg',  kgPerUnit: 1,           decimals: 5 },
        { key: 'lb',  id: 'toolzoo-weight-lb',  kgPerUnit: 0.45359237,  decimals: 5 },
        { key: 'kan', id: 'toolzoo-weight-kan', kgPerUnit: 3.75,         decimals: 5 },
    ];

    // 無限ループ防止フラグ
    var isUpdating = false;

    /**
     * 初期化
     */
    function init() {
        if (!document.getElementById('toolzoo-weight-kg')) {
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
        var clearBtn = document.getElementById('toolzoo-weight-clear-btn');
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
                var kgEl = document.getElementById('toolzoo-weight-kg');
                if (kgEl) kgEl.focus();
            });
        }

        // 初期値 1kg で全欄を計算済みの状態にする
        setActiveField('kg');
        convertFromUnit(getUnit('kg'), 1);
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
     * 指定単位の値から kg に換算し、他全単位へ変換して表示
     * @param {{ kgPerUnit: number }} sourceUnit
     * @param {number} value
     */
    function convertFromUnit(sourceUnit, value) {
        var kg = value * sourceUnit.kgPerUnit;
        isUpdating = true;
        UNITS.forEach(function (u) {
            if (u.key === sourceUnit.key) return;
            var el = document.getElementById(u.id);
            if (el) {
                el.value = (kg / u.kgPerUnit).toFixed(u.decimals);
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
