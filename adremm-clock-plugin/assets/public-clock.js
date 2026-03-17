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
            const locale = (typeof adremmClockData !== 'undefined') ? adremmClockData.locale : 'nl-NL';

            // Analog
            const s = now.getSeconds();
            const m = now.getMinutes();
            const h = now.getHours();

            // Sweep Logic
            // Note: Sweep style 'smooth' handled by CSS transition or sub-second interval
            $root.find('.h-sec').css('transform', `rotate(${s * 6}deg)`);
            $root.find('.h-min').css('transform', `rotate(${m * 6 + s * 0.1}deg)`);
            $root.find('.h-hour').css('transform', `rotate(${h * 30 + m * 0.5}deg)`);

            // Digital
            $root.find('.time-digital').text(now.toLocaleTimeString(locale));
            $root.find('.date-text').text(now.toLocaleDateString(locale, { weekday: 'long', day: 'numeric', month: 'long' }));
        }

        setInterval(updateClock, 1000);
        updateClock();

        // Panel sizing & effects
        if (typeof adremmClockData !== 'undefined') {
            $root.addClass('panel-size-' + adremmClockData.panelSize);
            if (adremmClockData.handSweep === 'smooth') {
                $root.find('.h-hour, .h-min, .h-sec').css('transition', 'transform 0.5s cubic-bezier(0.4, 2.08, 0.55, 0.44)');
            }
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
             if ($root.hasClass('adremm-clock-pos-top-left') ||
                 $root.hasClass('adremm-clock-pos-middle-left') ||
                 $root.hasClass('adremm-clock-pos-bottom-left')) {
                 tabPos = 'tab-left';
             }
             $tab.addClass(tabPos);

             $closeBtn.on('click', function() {
                 $container.fadeOut(300, function() { $tab.fadeIn(300); });
             });
             $tab.on('click', function() {
                 $tab.fadeOut(300, function() { $container.fadeIn(300); });
             });
        }
    });

})(jQuery);
