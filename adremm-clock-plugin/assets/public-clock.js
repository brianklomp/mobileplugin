/**
 * ADREMM Clock Frontend Logic
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const $root = $('#adremm-clock-root');
        if (!$root.length) return;

        const $panel = $root.find('.adremm-clock-panel');
        const $tab = $root.find('.adremm-clock-tab');
        const $closeBtn = $root.find('.adremm-clock-close-btn');
        const $time = $root.find('.time');
        const $date = $root.find('.date');

        // Initial tab setup based on position
        let tabClass = 'tab-right';
        if ($root.hasClass('adremm-clock-pos-top-left') ||
            $root.hasClass('adremm-clock-pos-middle-left') ||
            $root.hasClass('adremm-clock-pos-bottom-left')) {
            tabClass = 'tab-left';
        } else if ($root.hasClass('adremm-clock-pos-top-center')) {
            tabClass = 'tab-top';
        } else if ($root.hasClass('adremm-clock-pos-bottom-center')) {
            tabClass = 'tab-bottom';
        }
        $tab.addClass(tabClass);

        // Position the tab vertically if it's side-aligned
        if (tabClass === 'tab-left' || tabClass === 'tab-right') {
            const rootTop = $root.css('top');
            const rootBottom = $root.css('bottom');
            if (rootTop !== 'auto') $tab.css('top', rootTop);
            else if (rootBottom !== 'auto') $tab.css('bottom', rootBottom);
            else $tab.css('top', '50%').css('margin-top', '-50px');
        } else {
            // Center top/bottom
            $tab.css('left', '50%').css('margin-left', '-50px');
        }

        // Clock Update
        function updateClock() {
            const now = new Date();
            const locale = (typeof adremmClockData !== 'undefined') ? adremmClockData.locale : 'nl-NL';
            $time.text(now.toLocaleTimeString(locale));
            $date.text(now.toLocaleDateString(locale, { weekday: 'long', day: 'numeric', month: 'long' }));
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Toggle Panel -> Tab
        $closeBtn.on('click', function() {
            $panel.fadeOut(300, function() {
                $tab.fadeIn(300);
            });
        });

        // Toggle Tab -> Panel
        $tab.on('click', function() {
            $tab.fadeOut(300, function() {
                $panel.fadeIn(300);
            });
        });
    });

})(jQuery);
