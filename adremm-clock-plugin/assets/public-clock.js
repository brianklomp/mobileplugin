/**
 * ADREMM Clock Frontend Logic V4
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

            const handSweep = (typeof adremmClockData !== 'undefined') ? adremmClockData.handSweep : 'smooth';
            const locale = (typeof adremmClockData !== 'undefined') ? adremmClockData.locale : 'nl-NL';

            // Analog Sweep Logic
            let secDeg = s * 6;
            if (handSweep === 'smooth') secDeg = s * 6 + ms * 0.006;
            else if (handSweep === 'classy') secDeg = s * 6 + ms * 0.003;

            const minDeg = m * 6 + s * 0.1;
            const hourDeg = (h % 12) * 30 + m * 0.5;

            $root.find('.h-sec').css('transform', `rotate(${secDeg}deg)`);
            $root.find('.h-min').css('transform', `rotate(${minDeg}deg)`);
            $root.find('.h-hour').css('transform', `rotate(${hourDeg}deg)`);

            // Digital (Update every second or when ms is low)
            if (handSweep === 'ticking' || ms < 100) {
                const $timeRow = $root.find('.time-row');
                const $timeTarget = $root.find('.time-digital');

                const hh = String(h).padStart(2, '0');
                const mm = String(m).padStart(2, '0');
                const ss = String(s).padStart(2, '0');

                if ($timeRow.hasClass('digital-style-wall')) {
                    const digits = (hh + mm).split('');
                    let html = '';
                    digits.forEach((d, i) => {
                        html += `<div class="digit-col"><span style="transform: translateY(-${parseInt(d) * 32}px)">0\n1\n2\n3\n4\n5\n6\n7\n8\n9</span></div>`;
                        if (i === 1) html += '<span>:</span>';
                    });
                    $timeTarget.html(`${html}<span class="sec" style="opacity:${(ms > 500 ? 0.4 : 1)}">:${ss}</span>`);
                } else if ($timeRow.hasClass('digital-style-blocks')) {
                    $timeTarget.html(`<span class="b">${hh}</span>:<span class="b">${mm}</span>:<span class="b">${ss}</span>`);
                } else if ($timeRow.hasClass('digital-style-alarm')) {
                    $timeTarget.html(`<span class="time-digit-tube">${hh}</span>:<span class="time-digit-tube">${mm}</span>:<span class="time-digit-tube">${ss}</span>`);
                } else if ($timeRow.hasClass('digital-style-design')) {
                    const dayName = now.toLocaleDateString(locale, { weekday: 'short' });
                    $timeTarget.html(`<span class="day">${dayName}</span> ${hh}:${mm}<span class="sec">${ss}</span>`);
                } else if ($timeRow.hasClass('digital-style-pixels')) {
                    $timeTarget.html(`<span>${hh}</span><span>${mm}</span><span>${ss}</span>`);
                } else {
                    $timeTarget.text(`${hh}:${mm}:${ss}`);
                }

                $root.find('.date-text').text(now.toLocaleDateString(locale, { weekday: 'long', day: 'numeric', month: 'long' }));
            }
        }

        const interval = (typeof adremmClockData !== 'undefined' && (adremmClockData.handSweep === 'smooth' || adremmClockData.handSweep === 'classy')) ? 50 : 1000;
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

        // Radio Feature
        if (typeof adremmClockData !== 'undefined' && adremmClockData.radioEnabled === 'yes') {
            const streams = {
                techno: 'https://mediaserv38.live-streams.nl:18002/techno',
                disco: 'http://caster04.streampakket.com:8047/stream',
                hits: 'https://zwollefm.beheerstream.nl/8018/stream',
                concert: 'https://ice.cr5.streamzilla.xlcdn.com:8000/sz=RCOLiveWebradio=mp3-192',
                classics: 'https://streams.pinguinradio.com/PinguinClassics192.mp3',
                blues: 'https://19003.live.streamtheworld.com/SP_R2406394_SC'
            };
            const streamUrl = streams[adremmClockData.radioChannel] || streams.hits;
            window.adremmRadio = new Audio(streamUrl);

            $root.find('.time-row.digital-style-alarm').css('cursor', 'pointer').on('click', function() {
                if (window.adremmRadio.paused) {
                    window.adremmRadio.play().catch(e => console.log('Autoplay blocked'));
                    $(this).css('box-shadow', '0 0 30px rgba(255,102,0,0.8)');
                } else {
                    window.adremmRadio.pause();
                    $(this).css('box-shadow', '');
                }
            });
        }

        // Toggle Logic
        // Radio toggle UI
        $root.on('click', '#adremm-radio-toggle', function(e) {
            e.stopPropagation();
            $(this).toggleClass('on');
            if ($(this).hasClass('on')) {
                if (window.adremmRadio) window.adremmRadio.play().catch(e => console.log('Autoplay blocked'));
            } else {
                if (window.adremmRadio) window.adremmRadio.pause();
            }
        });

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
                 $container.fadeOut(300, function() {
                     $tab.css('display', 'flex').hide().fadeIn(300);
                 });
                 if (window.adremmRadio) window.adremmRadio.pause();
             });
             $tab.on('click', function() {
                 $tab.fadeOut(300, function() { $container.fadeIn(300); });
             });
        }
    });

})(jQuery);
