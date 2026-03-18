/**
 * ADREMM Clock Frontend Logic V3
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const $root = $('#adremm-clock-wrapper');
        if (!$root.length) return;

        const $container = $root.find('.adremm-clock-container');
        const $tab = $root.find('.adremm-clock-tab');
        const $closeBtn = $root.find('.adremm-clock-close');

        // Update Clock Elements
        function updateClock() {
            const now = new Date();
            const ms = now.getMilliseconds();
            const s = now.getSeconds();
            const m = now.getMinutes();
            const h = now.getHours();

            const isSmooth = (typeof adremmClockData !== 'undefined' && adremmClockData.handSweep === 'smooth');
            const locale = (typeof adremmClockData !== 'undefined') ? adremmClockData.locale : 'nl-NL';

            // Analog Sweep Logic
            const secDeg = isSmooth ? (s * 6 + ms * 0.006) : (s * 6);
            const minDeg = m * 6 + s * 0.1;
            const hourDeg = (h % 12) * 30 + m * 0.5;

            $root.find('.h-sec').css('transform', `rotate(${secDeg}deg)`);
            $root.find('.h-min').css('transform', `rotate(${minDeg}deg)`);
            $root.find('.h-hour').css('transform', `rotate(${hourDeg}deg)`);

            // Digital (Update every second or when ms is low)
            if (!isSmooth || ms < 100) {
                $root.find('.time-digital').text(now.toLocaleTimeString(locale));
                $root.find('.date-text').text(now.toLocaleDateString(locale, { weekday: 'long', day: 'numeric', month: 'long' }));
            }
        }

        const interval = (typeof adremmClockData !== 'undefined' && adremmClockData.handSweep === 'smooth') ? 50 : 1000;
        setInterval(updateClock, interval);
        updateClock();

        // Panel sizing & effects
        if (typeof adremmClockData !== 'undefined') {
            $root.addClass('panel-size-' + adremmClockData.panelSize);
            if (adremmClockData.extraMarquee === 'yes') {
                $root.find('.extra-row').addClass('marquee');
                const speed = 21 - parseInt(adremmClockData.extraSpeed);
                $root.find('.extra-row').css('--marquee-speed', speed + 's');
            }
        }

        // Toggle Logic
        if ($root.hasClass('adremm-clock-panel')) {
             // Tab positioning logic
             let tabPos = 'tab-right';
             // If selected position is on the left side, tab also goes to the left.
             if ($root.hasClass('adremm-clock-pos-top-left') ||
                 $root.hasClass('adremm-clock-pos-middle-left') ||
                 $root.hasClass('adremm-clock-pos-bottom-left')) {
                 tabPos = 'tab-left';
             }
             $tab.addClass(tabPos);

             $closeBtn.on('click', function() {
                 $container.fadeOut(300, function() {
                     $tab.css('display', 'flex').hide().fadeIn(300);
                 });
             });
             $tab.on('click', function() {
                 $tab.fadeOut(300, function() { $container.fadeIn(300); });
             });
        }
    });

})(jQuery);
