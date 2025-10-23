/**
 * World Clock JavaScript
 */
(function() {
    'use strict';

    // Check if data is available
    if (typeof toolzooWorldclockData === 'undefined') {
        console.error('toolzooWorldclockData is not defined');
        return;
    }

    // Cities data (from PHP via wp_localize_script)
    const cities = toolzooWorldclockData.cities;
    const labels = toolzooWorldclockData.labels;

    // Get user's timezone
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    /**
     * Get GMT offset for a timezone
     */
    function getGMTOffset(timezone) {
        try {
            const now = new Date();
            const utcDate = new Date(now.toLocaleString('en-US', { timeZone: 'UTC' }));
            const tzDate = new Date(now.toLocaleString('en-US', { timeZone: timezone }));
            const offset = (tzDate - utcDate) / (1000 * 60 * 60);
            return Math.round(offset);
        } catch (e) {
            console.error('Error getting GMT offset for ' + timezone, e);
            return 0;
        }
    }

    /**
     * Sort cities by GMT offset (eastward from user's timezone)
     */
    function sortCities(cities) {
        const userOffset = getGMTOffset(userTimezone);

        return cities.slice().sort(function(a, b) {
            const offsetA = getGMTOffset(a.timezone);
            const offsetB = getGMTOffset(b.timezone);

            // Calculate relative offset from user
            let relativeA = offsetA - userOffset;
            let relativeB = offsetB - userOffset;

            // Normalize to 0-23 range (eastward)
            if (relativeA < 0) relativeA += 24;
            if (relativeB < 0) relativeB += 24;

            // Sort by relative offset (ascending for eastward)
            if (relativeA !== relativeB) {
                return relativeA - relativeB;
            }

            // If same offset, sort by city name
            return a.city.localeCompare(b.city);
        });
    }

    /**
     * Format time for a timezone
     */
    function formatTime(timezone) {
        try {
            const now = new Date();

            const timeFormatter = new Intl.DateTimeFormat('ja-JP', {
                timeZone: timezone,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });

            const dateFormatter = new Intl.DateTimeFormat('ja-JP', {
                timeZone: timezone,
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            return {
                time: timeFormatter.format(now),
                date: dateFormatter.format(now)
            };
        } catch (e) {
            console.error('Error formatting time for ' + timezone, e);
            return {
                time: '--:--:--',
                date: '----'
            };
        }
    }

    /**
     * Get GMT offset string
     */
    function getGMTOffsetString(timezone) {
        const offset = getGMTOffset(timezone);
        const sign = offset >= 0 ? '+' : '';
        return 'GMT' + sign + offset;
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Render city card
     */
    function renderCityCard(city) {
        const timeInfo = formatTime(city.timezone);
        const gmtOffset = getGMTOffsetString(city.timezone);
        const isUserCity = city.timezone === userTimezone;

        const cityName = escapeHtml(city.city);
        const countryName = escapeHtml(city.country);
        const currentTime = escapeHtml(labels.currentTime);
        const dateLabel = escapeHtml(labels.date);
        const yourLocation = escapeHtml(labels.yourLocation);

        let html = '<div class="toolzoo-worldclock-card';
        if (isUserCity) {
            html += ' toolzoo-worldclock-current';
        }
        html += '">';

        html += '<div class="toolzoo-worldclock-city-header">';
        html += '<span class="toolzoo-worldclock-icon">🕐</span>';
        html += '<h4 class="toolzoo-worldclock-city-name">';
        html += cityName + '（' + countryName + '）';
        if (isUserCity) {
            html += '<span class="toolzoo-worldclock-badge">' + yourLocation + '</span>';
        }
        html += '</h4>';
        html += '</div>';

        html += '<div class="toolzoo-worldclock-time-info">';
        html += '<div class="toolzoo-worldclock-time">';
        html += currentTime + ' <strong>' + escapeHtml(timeInfo.time) + '</strong>';
        html += '</div>';
        html += '<div class="toolzoo-worldclock-date">';
        html += dateLabel + ' ' + escapeHtml(timeInfo.date);
        html += '</div>';
        html += '<div class="toolzoo-worldclock-gmt">';
        html += escapeHtml(gmtOffset);
        html += '</div>';
        html += '</div>';

        html += '</div>';

        return html;
    }

    /**
     * Update all clocks
     */
    function updateClocks() {
        const container = document.getElementById('toolzoo-worldclock-list');
        if (!container) return;

        const sortedCities = sortCities(cities);
        const html = sortedCities.map(function(city) {
            return renderCityCard(city);
        }).join('');

        container.innerHTML = html;
    }

    /**
     * Initialize
     */
    function init() {
        // Initial render
        updateClocks();

        // Update every second
        setInterval(updateClocks, 1000);
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
