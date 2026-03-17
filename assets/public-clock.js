/**
 * Frontend JavaScript for ADREMM Clock Plugin
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const $root = $('#adremm-clock-root');
        if (!$root.length) return;

        const $panel = $root.find('.adremm-clock-panel');
        const $closeBtn = $root.find('.adremm-clock-close-btn');
        const $tab = $root.find('.adremm-clock-tab');
        const $timeDisplay = $root.find('.time');
        const $dateDisplay = $root.find('.date');

        // Determine tab position based on root class
        let tabPos = 'tab-right';
        if ($root.hasClass('adremm-clock-pos-top-left') ||
            $root.hasClass('adremm-clock-pos-middle-left') ||
            $root.hasClass('adremm-clock-pos-bottom-left')) {
            tabPos = 'tab-left';
        }
        $tab.addClass(tabPos);

        // Update Clock
        function updateClock() {
            const now = new Date();
            const locale = (typeof adremmClockData !== 'undefined' && adremmClockData.locale) ? adremmClockData.locale : 'nl-NL';
            const timeStr = now.toLocaleTimeString(locale);
            const dateStr = now.toLocaleDateString(locale, { weekday: 'long', day: 'numeric', month: 'long' });

            $timeDisplay.text(timeStr);
            $dateDisplay.text(dateStr);
        }

        setInterval(updateClock, 1000);
        updateClock();

        // Close Logic (Hide panel, Show tab)
        $closeBtn.on('click', function() {
            $panel.fadeOut(300, function() {
                $tab.fadeIn(300);
            });
        });

        // Open Logic (Hide tab, Show panel)
        $tab.on('click', function() {
            $tab.fadeOut(300, function() {
                $panel.fadeIn(300);
            });
        });

        // Handle specific positioning for tab if center
        if ($root.hasClass('adremm-clock-pos-top-center')) {
            $tab.css({ top: '0', left: '50%', transform: 'translateX(-50%) rotate(90deg)', width: '100px', height: '25px' });
            $tab.find('span').css('transform', 'none');
            $tab.removeClass('tab-left tab-right');
        } else if ($root.hasClass('adremm-clock-pos-bottom-center')) {
            $tab.css({ bottom: '0', left: '50%', transform: 'translateX(-50%) rotate(90deg)', width: '100px', height: '25px' });
            $tab.find('span').css('transform', 'none');
            $tab.removeClass('tab-left tab-right');
        } else {
            // Standard vertical tabs on the sides
            // Use CSS for top positioning to avoid fragile offset().top
            if ($root.hasClass('adremm-clock-pos-top-left') || $root.hasClass('adremm-clock-pos-top-right')) {
                $tab.css('top', '20px');
            } else if ($root.hasClass('adremm-clock-pos-middle-left') || $root.hasClass('adremm-clock-pos-middle-right')) {
                $tab.css('top', '50%');
                $tab.css('margin-top', '-50px');
            } else if ($root.hasClass('adremm-clock-pos-bottom-left') || $root.hasClass('adremm-clock-pos-bottom-right')) {
                $tab.css('bottom', '20px');
            }
        }
    });

})(jQuery);
