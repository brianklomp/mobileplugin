/**
 * ADREMM Clock Frontend Logic V2
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
            $root.find('.h-sec').css('transform', `rotate(${s * 6}deg)`);
            $root.find('.h-min').css('transform', `rotate(${m * 6 + s * 0.1}deg)`);
            $root.find('.h-hour').css('transform', `rotate(${h * 30 + m * 0.5}deg)`);

            // Digital
            $root.find('.time-digital').text(now.toLocaleTimeString(locale));
            $root.find('.date-text').text(now.toLocaleDateString(locale, { weekday: 'long', day: 'numeric', month: 'long' }));
        }

        setInterval(updateClock, 1000);
        updateClock();

        // Layout handling
        if ($root.hasClass('adremm-clock-panel')) {
            // Tab positioning logic
            let tabPos = 'tab-right';
            if ($root.hasClass('adremm-clock-pos-top-left') ||
                $root.hasClass('adremm-clock-pos-middle-left') ||
                $root.hasClass('adremm-clock-pos-bottom-left')) {
                tabPos = 'tab-left';
            }
            $tab.addClass(tabPos);

            // Match vertical position
            const rootTop = $root.css('top');
            const rootBottom = $root.css('bottom');
            if (rootTop !== 'auto') $tab.css('top', rootTop);
            else if (rootBottom !== 'auto') $tab.css('bottom', rootBottom);
            else $tab.css('top', '50%').css('margin-top', '-50px');

            // Toggle Logic
            $closeBtn.on('click', function() {
                $container.fadeOut(300, function() { $tab.fadeIn(300); });
            });
            $tab.on('click', function() {
                $tab.fadeOut(300, function() { $container.fadeIn(300); });
            });
        }
    });

})(jQuery);
