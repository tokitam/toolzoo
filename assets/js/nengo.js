/**
 * Japanese Era List Display Tool JavaScript
 *
 * @package ToolZoo
 */

(function() {
    'use strict';

    // Era data definition
    const eraData = [
        {
            name: 'Meiji',
            nameEn: 'meiji',
            startYear: 1868,
            endYear: 1912
        },
        {
            name: 'Taisho',
            nameEn: 'taisho',
            startYear: 1912,
            endYear: 1926
        },
        {
            name: 'Showa',
            nameEn: 'showa',
            startYear: 1926,
            endYear: 1989
        },
        {
            name: 'Heisei',
            nameEn: 'heisei',
            startYear: 1989,
            endYear: 2019
        },
        {
            name: 'Reiwa',
            nameEn: 'reiwa',
            startYear: 2019,
            endYear: null
        }
    ];

    /**
     * Initialize
     */
    document.addEventListener('DOMContentLoaded', function() {
        initNengoList();
    });

    /**
     * Initialize era list
     */
    function initNengoList() {
        // Generate data
        const startYear = 1868;
        const endYear = new Date().getFullYear();
        const data = generateNengoList(startYear, endYear);

        // Generate table
        displayNengoTable(data);

        // Setup event listeners
        setupEventListeners();

        // Scroll to current year
        setTimeout(function() {
            scrollToCurrentYear();
        }, 100);
    }

    /**
     * Generate era list data
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
     * Calculate era year
     */
    function calculateNengo(year, eraNameEn) {
        const era = eraData.find(function(e) {
            return e.nameEn === eraNameEn;
        });

        if (!era) return '-';

        // If outside era range
        if (year < era.startYear || (era.endYear && year > era.endYear)) {
            return '-';
        }

        // Calculate era year
        const nengoYear = year - era.startYear + 1;

        // Display first year
        if (nengoYear === 1) {
            return typeof toolzooNengoL10n !== 'undefined' && toolzooNengoL10n.yearOne
                ? toolzooNengoL10n.yearOne
                : 'Year 1';
        }

        return nengoYear.toString();
    }

    /**
     * Check if it's an era change year
     */
    function isKaigenYear(year) {
        const kaiGenYears = [1868, 1912, 1926, 1989, 2019];
        return kaiGenYears.indexOf(year) !== -1;
    }

    /**
     * Display table
     */
    function displayNengoTable(rows) {
        const container = document.getElementById('toolzoo-nengo-table-container');
        if (!container) return;

        container.innerHTML = renderTable(rows);
    }

    /**
     * Generate table HTML
     */
    function renderTable(rows) {
        let html = '<table class="toolzoo-nengo-list-table">';

        // Get localized strings with fallbacks
        const l10n = typeof toolzooNengoL10n !== 'undefined' ? toolzooNengoL10n : {};
        const westernCalendar = l10n.westernCalendar || 'Western Calendar';
        const meiji = l10n.meiji || 'Meiji';
        const taisho = l10n.taisho || 'Taisho';
        const showa = l10n.showa || 'Showa';
        const heisei = l10n.heisei || 'Heisei';
        const reiwa = l10n.reiwa || 'Reiwa';

        // Header
        html += '<thead><tr>';
        html += '<th>' + escapeHtml(westernCalendar) + '</th>';
        html += '<th>' + escapeHtml(meiji) + '</th>';
        html += '<th>' + escapeHtml(taisho) + '</th>';
        html += '<th>' + escapeHtml(showa) + '</th>';
        html += '<th>' + escapeHtml(heisei) + '</th>';
        html += '<th>' + escapeHtml(reiwa) + '</th>';
        html += '</tr></thead>';

        // Body
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
     * HTML escape
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
     * Setup event listeners
     */
    function setupEventListeners() {
        // Era jump buttons
        const jumpButtons = document.querySelectorAll('.toolzoo-nengo-jump-btn');
        jumpButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const era = this.getAttribute('data-era');
                jumpToEra(era);
            });
        });

        // Scroll to top button
        const scrollTopBtn = document.getElementById('toolzoo-scroll-top-btn');
        if (scrollTopBtn) {
            scrollTopBtn.addEventListener('click', function() {
                scrollToTop();
            });
        }
    }

    /**
     * Scroll to current year
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
     * Jump to specified era
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

            // Highlight effect
            highlightRow(targetRow);
        }
    }

    /**
     * Highlight row
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
     * Scroll to top
     */
    function scrollToTop() {
        const container = document.getElementById('toolzoo-nengo-table-container');
        if (container) {
            container.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Also scroll entire page to top
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

})();
