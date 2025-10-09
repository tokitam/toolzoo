/**
 * 年号一覧表示ツール JavaScript
 *
 * @package ToolZoo
 */

(function() {
    'use strict';

    // 年号データ定義
    const eraData = [
        {
            name: '明治',
            nameEn: 'meiji',
            startYear: 1868,
            endYear: 1912
        },
        {
            name: '大正',
            nameEn: 'taisho',
            startYear: 1912,
            endYear: 1926
        },
        {
            name: '昭和',
            nameEn: 'showa',
            startYear: 1926,
            endYear: 1989
        },
        {
            name: '平成',
            nameEn: 'heisei',
            startYear: 1989,
            endYear: 2019
        },
        {
            name: '令和',
            nameEn: 'reiwa',
            startYear: 2019,
            endYear: null
        }
    ];

    /**
     * 初期化
     */
    document.addEventListener('DOMContentLoaded', function() {
        initNengoList();
    });

    /**
     * 年号一覧の初期化
     */
    function initNengoList() {
        // データ生成
        const startYear = 1868;
        const endYear = new Date().getFullYear();
        const data = generateNengoList(startYear, endYear);

        // テーブル生成
        displayNengoTable(data);

        // イベントリスナー設定
        setupEventListeners();

        // 現在年にスクロール
        setTimeout(function() {
            scrollToCurrentYear();
        }, 100);
    }

    /**
     * 年号リストデータを生成
     */
    function generateNengoList(startYear, endYear) {
        const rows = [];

        for (let year = startYear; year <= endYear; year++) {
            const row = {
                seireki: year,
                meiji: calculateNengo(year, 'meiji'),
                taisho: calculateNengo(year, 'taisho'),
                showa: calculateNengo(year, 'showa'),
                heisei: calculateNengo(year, 'heisei'),
                reiwa: calculateNengo(year, 'reiwa'),
                isKaigenYear: isKaigenYear(year),
                isCurrentYear: year === new Date().getFullYear()
            };
            rows.push(row);
        }

        return rows;
    }

    /**
     * 年号を計算
     */
    function calculateNengo(year, eraNameEn) {
        const era = eraData.find(function(e) {
            return e.nameEn === eraNameEn;
        });

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

    /**
     * 改元年かどうかを判定
     */
    function isKaigenYear(year) {
        const kaiGenYears = [1868, 1912, 1926, 1989, 2019];
        return kaiGenYears.indexOf(year) !== -1;
    }

    /**
     * テーブルを表示
     */
    function displayNengoTable(rows) {
        const container = document.getElementById('toolzoo-nengo-table-container');
        if (!container) return;

        container.innerHTML = renderTable(rows);
    }

    /**
     * テーブルHTMLを生成
     */
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

        rows.forEach(function(row) {
            const rowClasses = [];
            if (row.isKaigenYear) rowClasses.push('kaigen-year');
            if (row.isCurrentYear) rowClasses.push('current-year');

            const classAttr = rowClasses.length > 0 ? ' class="' + rowClasses.join(' ') + '"' : '';
            const idAttr = ' id="year-' + row.seireki + '"';

            html += '<tr' + classAttr + idAttr + ' data-year="' + row.seireki + '">';
            html += '<td class="seireki">' + row.seireki + '</td>';
            html += '<td class="nengo">' + escapeHtml(row.meiji) + '</td>';
            html += '<td class="nengo">' + escapeHtml(row.taisho) + '</td>';
            html += '<td class="nengo">' + escapeHtml(row.showa) + '</td>';
            html += '<td class="nengo">' + escapeHtml(row.heisei) + '</td>';
            html += '<td class="nengo">' + escapeHtml(row.reiwa) + '</td>';
            html += '</tr>';
        });

        html += '</tbody>';
        html += '</table>';

        return html;
    }

    /**
     * HTMLエスケープ
     */
    function escapeHtml(text) {
        if (typeof text !== 'string') {
            text = String(text);
        }
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /**
     * イベントリスナーを設定
     */
    function setupEventListeners() {
        // 年代ジャンプボタン
        const jumpButtons = document.querySelectorAll('.toolzoo-nengo-jump-btn');
        jumpButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const era = this.getAttribute('data-era');
                jumpToEra(era);
            });
        });

        // トップへスクロールボタン
        const scrollTopBtn = document.getElementById('toolzoo-scroll-top-btn');
        if (scrollTopBtn) {
            scrollTopBtn.addEventListener('click', function() {
                scrollToTop();
            });
        }
    }

    /**
     * 現在年へスクロール
     */
    function scrollToCurrentYear() {
        const currentYear = new Date().getFullYear();
        const targetRow = document.getElementById('year-' + currentYear);

        if (targetRow) {
            targetRow.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    }

    /**
     * 指定年代へジャンプ
     */
    function jumpToEra(eraNameEn) {
        const eraStartYear = {
            'meiji': 1868,
            'taisho': 1912,
            'showa': 1926,
            'heisei': 1989,
            'reiwa': 2019
        };

        const targetYear = eraStartYear[eraNameEn];
        if (!targetYear) return;

        const targetRow = document.getElementById('year-' + targetYear);

        if (targetRow) {
            targetRow.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // ハイライト効果
            highlightRow(targetRow);
        }
    }

    /**
     * 行をハイライト
     */
    function highlightRow(row) {
        row.style.transition = 'background-color 0.5s';
        const originalBg = row.style.backgroundColor;
        row.style.backgroundColor = '#fff9c4';

        setTimeout(function() {
            row.style.backgroundColor = originalBg;
        }, 1500);
    }

    /**
     * トップへスクロール
     */
    function scrollToTop() {
        const container = document.getElementById('toolzoo-nengo-table-container');
        if (container) {
            container.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // ページ全体もトップへ
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

})();
