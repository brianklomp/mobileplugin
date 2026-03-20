/**
 * ADREMM Clock Frontend Logic V6
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const $root = $('#adremm-clock-wrapper');
        if (!$root.length) return;

        const $container = $root.find('.adremm-clock-container');
        const $tab = $root.find('.adremm-clock-tab');
        const $closeBtn = $root.find('.adremm-clock-close');

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
            if (handSweep === 'smooth') {
                secDeg = s * 6 + ms * 0.006;
            } else if (handSweep === 'classy') {
                // Faster ticks (e.g. 5 times per second)
                const fastTick = Math.floor(ms / 200) * (6 / 5);
                secDeg = s * 6 + fastTick;
            }

            const minDeg = m * 6 + s * 0.1;
            const hourDeg = (h % 12) * 30 + m * 0.5;

            $root.find('.h-sec').css('transform', `rotate(${secDeg}deg)`);
            $root.find('.h-min').css('transform', `rotate(${minDeg}deg)`);
            $root.find('.h-hour').css('transform', `rotate(${hourDeg}deg)`);

            // Digital Update
            const $timeRow = $root.find('.time-row');
            const $timeTarget = $root.find('.time-digital');

            const hh = String(h).padStart(2, '0');
            const mm = String(m).padStart(2, '0');
            const ss = String(s).padStart(2, '0');

            if ($timeRow.hasClass('digital-style-alarm')) {
                $root.find('#nixie-h1').text(hh[0]);
                $root.find('#nixie-h2').text(hh[1]);
                $root.find('#nixie-m1').text(mm[0]);
                $root.find('#nixie-m2').text(mm[1]);

                const $scaleV = $root.find('.radio-scale-scroll-v');
                if ($scaleV.length) {
                    const itemHeight = 20;
                    const secVal = s + ms/1000;
                    const offset = secVal * itemHeight;
                    // Reset transition at the start of a minute to avoid "shooting back"
                    if (s === 0 && ms < 100) {
                        $scaleV.css('transition', 'none');
                    } else {
                        $scaleV.css('transition', 'transform 0.05s linear');
                    }
                    $scaleV.css('transform', `translateY(${-offset}px)`);
                }
            } else if ($timeRow.hasClass('digital-style-wall')) {
                 const digits = (hh + mm + ss).split('');
                 const $cols = $timeTarget.find('.digit-col');
                 const digitHeight = 60;
                 if ($cols.length === 0) {
                     let html = '';
                     digits.forEach((d, i) => {
                         html += `<div class="digit-col"><span>0\n1\n2\n3\n4\n5\n6\n7\n8\n9\n0</span></div>`;
                         if (i === 1 || i === 3) html += '<div class="sep">:</div>';
                     });
                     $timeTarget.html(html);
                 }
                 $timeTarget.find('.digit-col').each(function(i) {
                     const d = parseInt(digits[i]);
                     let offset = d * digitHeight;
                     $(this).find('span').css('transform', `translateY(-${offset}px)`);
                 });
            } else if (handSweep === 'ticking' || ms < 100) {
                if ($timeRow.hasClass('digital-style-blocks')) {
                    const isGlitch = Math.random() > 0.95;
                    const glitchClass = isGlitch ? ' glitch' : '';
                    $timeTarget.html(`<span class="b${glitchClass}">${hh}</span><span class="sep">:</span><span class="b${glitchClass}">${mm}</span><span class="sep">:</span><span class="b${glitchClass}">${ss}</span>`);
                } else if ($timeRow.hasClass('digital-style-design')) {
                    const dayName = now.toLocaleDateString(locale, { weekday: 'short' }).toUpperCase();
                    $timeTarget.html(`<div class="minimalist-container" style="background:#000; color:#fff; padding:15px; border-radius:8px; display:inline-block; font-family:monospace;">
                        <div class="time-main" style="font-size:42px; line-height:1; display:flex; align-items:center; gap:5px;">
                            <span style="color:#ff3b30; margin-right:5px;">${dayName}:</span><span>${hh}</span><span style="color:#666">:</span><span>${mm}</span><span style="color:#666">:</span><span>${ss}</span>
                        </div>
                        <div class="time-labels" style="color:#666; font-size:12px; display:flex; justify-content:flex-end; gap:15px; margin-top:5px; text-transform:uppercase;">
                            <span>uur</span><span>min</span><span>sec</span>
                        </div>
                    </div>`);
                } else if ($timeRow.hasClass('digital-style-pixels')) {
                    $timeTarget.html(`${hh}:${mm}:${ss}`);
                } else {
                    $timeTarget.text(`${hh}:${mm}:${ss}`);
                }
            }

            if (ms < 100) {
                $root.find('.date-text').text(now.toLocaleDateString(locale, { weekday: 'long', day: 'numeric', month: 'long' }));
            }
        }

        const interval = (typeof adremmClockData !== 'undefined' && (adremmClockData.handSweep === 'smooth' || adremmClockData.handSweep === 'classy')) ? 50 : 1000;
        setInterval(updateClock, interval);
        updateClock();

        // Marquee Logic (Lichtslang)
        if (typeof adremmClockData !== 'undefined' && adremmClockData.extraMarquee === 'yes') {
            const $marqueeRow = $root.find('.extra-row');
            const $marqueeSpan = $marqueeRow.find('span');
            if ($marqueeSpan.length) {
                $marqueeRow.css({ overflow: 'hidden', whiteSpace: 'nowrap', position: 'relative', width: '100%' });
                $marqueeSpan.css({ display: 'inline-block', position: 'relative' });

                let marqueePos = $marqueeRow.width();
                const marqueeSpeed = parseInt(adremmClockData.extraSpeed) || 5;

                function stepMarquee() {
                    marqueePos -= (marqueeSpeed / 10);
                    if (marqueePos < -$marqueeSpan.width()) {
                        marqueePos = $marqueeRow.width();
                    }
                    $marqueeSpan.css('transform', `translateX(${marqueePos}px)`);
                    requestAnimationFrame(stepMarquee);
                }
                stepMarquee();
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
            window.adremmRadio.volume = 0.8;

            $root.on('click', '#adremm-radio-power', function(e) {
                e.stopPropagation();
                $(this).toggleClass('on');
                if ($(this).hasClass('on')) {
                    window.adremmRadio.play().catch(e => console.log('Autoplay blocked'));
                } else {
                    window.adremmRadio.pause();
                }
            });

            // Volume Knob Logic
            let isDragging = false;
            let startY = 0;
            let currentVol = 0.8;

            $root.on('mousedown touchstart', '#adremm-radio-volume', function(e) {
                isDragging = true;
                startY = (e.type === 'touchstart') ? e.originalEvent.touches[0].clientY : e.clientY;
                e.preventDefault();
            });

            $(document).on('mousemove touchmove', function(e) {
                if (!isDragging) return;
                const y = (e.type === 'touchmove') ? e.originalEvent.touches[0].clientY : e.clientY;
                const delta = (startY - y) / 100;
                currentVol = Math.max(0, Math.min(1, currentVol + delta));
                window.adremmRadio.volume = currentVol;
                const rotation = (currentVol * 240) - 120; // -120 to +120
                $root.find('#adremm-radio-volume').css('transform', `rotate(${rotation}deg)`);
                startY = y;
            });

            $(document).on('mouseup touchend', function() {
                isDragging = false;
            });
        }

        if ($root.hasClass('adremm-clock-panel')) {
             let tabPos = 'tab-right';
             if ($root.hasClass('adremm-clock-pos-top-left') ||
                 $root.hasClass('adremm-clock-pos-middle-left') ||
                 $root.hasClass('adremm-clock-pos-bottom-left')) {
                 tabPos = 'tab-left';
             }
             $tab.addClass(tabPos);

             $closeBtn.on('click', function() {
                 $container.fadeOut(300, function() { $tab.css('display', 'flex').hide().fadeIn(300); });
             });
             $tab.on('click', function() {
                 $tab.fadeOut(300, function() { $container.fadeIn(300); });
             });
        }
    });

})(jQuery);
