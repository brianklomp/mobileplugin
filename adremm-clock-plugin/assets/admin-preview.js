/**
 * ADREMM Clock Admin Preview Logic V3 - FULL UPGRADE
 */
jQuery(document).ready(function($) {
    const $form = $('#adremm-clock-form, #adremm-clock-mobile-form');
    const $liveView = $('#clock-live-view, #clock-mobile-view');

    // Main Tab Switching
    $('.adremm-nav-tabs .nav-tab').on('click', function(e) {
        e.preventDefault();
        const target = $(this).attr('href');
        $('.adremm-nav-tabs .nav-tab').removeClass('is-active');
        $(this).addClass('is-active');
        $('.adremm-panel').removeClass('is-active');
        $(target).addClass('is-active');
    });

    // Sub-Tab Switching (Tijdpaneel)
    $('.sub-tab-btn').on('click', function() {
        const target = $(this).data('sub');
        $('.sub-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.adremm-sub-panel').removeClass('active');
        $('#' + target).addClass('active');
    });

    // Joystick selection
    $('.joy-item').on('click', function() {
        $('.joy-item').removeClass('active');
        $(this).addClass('active');
        $(this).find('input').prop('checked', true).trigger('change');
    });

    // Color Pickers with enhanced Alpha support
    if ($.isFunction($.fn.wpColorPicker)) {
        $('.adremm-color-picker').wpColorPicker({
            alpha: true,
            change: function(event, ui) {
                const color = ui.color.to_s('rgba');
                $(this).val(color);
                setTimeout(updatePreview, 20);
            },
            clear: function() {
                $(this).val('transparent');
                setTimeout(updatePreview, 20);
            }
        });
    }

    // Media Upload
    $('.adremm-media-upload').on('click', function(e) {
        e.preventDefault();
        const $input = $(this).siblings('.adremm-media-url');
        const frame = wp.media({ title: 'Kies achtergrond', button: { text: 'Gebruik deze' }, multiple: false });
        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();
            $input.val(attachment.url).trigger('change');
        });
        frame.open();
    });

    // Helpers
    const getVal = (name) => $form.find(`[name="adremm_clock_settings[${name}]"]`).val();
    const getRadioVal = (name) => $form.find(`[name="adremm_clock_settings[${name}]"]:checked`).val();
    const getCheckboxVal = (name) => $form.find(`[name="adremm_clock_settings[${name}]"]:checked`).length > 0 ? 'yes' : 'no';

    // Force Color Pickers to trigger preview on manual input as well
    $form.on('keyup', '.adremm-color-picker', function() {
        updatePreview();
    });

    // Dynamic Updates
    function updatePreview() {
        const s = {
            theme_mode: getRadioVal('theme_mode'),
            theme_font: getVal('theme_font'),
            bg_color: getVal('bg_color'),
            text_color: getVal('text_color'),
            panel_size: getVal('panel_size'),
            panel_shadow: getVal('panel_shadow'),
            panel_border: getVal('panel_border'),
            show_analog: getRadioVal('show_analog'),
            analog_bg_image: getVal('analog_bg_image'),
            analog_bg_color: getVal('analog_bg_color'),
            analog_ring_color: getVal('analog_ring_color'),
            analog_ring_size: getVal('analog_ring_size'),
            analog_not_scale: getVal('analog_not_scale'),
            analog_hour_color: getVal('analog_hour_color'),
            analog_hour_thick: getVal('analog_hour_thick'),
            analog_hour_length: getVal('analog_hour_length'),
            analog_min_color: getVal('analog_min_color'),
            analog_min_thick: getVal('analog_min_thick'),
            analog_min_length: getVal('analog_min_length'),
            analog_not_above: $form.find('[name="adremm_clock_settings[analog_not_above]"]').is(':checked') ? 'yes' : 'no',
            show_digital: getRadioVal('show_digital'),
            digital_color: getVal('digital_color'),
            digital_font: getVal('digital_font'),
            digital_style: getVal('digital_style'),
            digital_width: getVal('digital_width'),
            digital_height: getVal('digital_height'),
            digital_show_sec: $form.find('[name="adremm_clock_settings[digital_show_sec]"]').is(':checked') ? 'yes' : 'no',
            digital_glow: $form.find('[name="adremm_clock_settings[digital_glow]"]').is(':checked') ? 'yes' : 'no',
            digital_glow_color: getVal('digital_glow_color'),
            digital_glow_spread: getVal('digital_glow_spread'),
            digital_orientation: getVal('digital_orientation'),
            radio_enabled: $form.find('[name="adremm_clock_settings[radio_enabled]"]').is(':checked') ? 'yes' : 'no',
            text_open: getVal('text_open'),
            color_open: getVal('color_open'),
            font_status: getVal('font_status'),
            status_pos: getVal('status_pos'),
            color_date: getVal('color_date'),
            font_date: getVal('font_date'),
            extra_message: $form.find('textarea[name="adremm_clock_settings[extra_message]"]').val(),
            extra_color: getVal('extra_color'),
            extra_font_size: getVal('extra_font_size'),
            extra_marquee: getRadioVal('extra_marquee'),
            hand_hour_color: getVal('hand_hour_color'),
            hand_hour_thick: getVal('hand_hour_thick'),
            hand_hour_len: getVal('hand_hour_len'),
            hand_hour_style: getVal('hand_hour_style'),
            hand_min_color: getVal('hand_min_color'),
            hand_min_thick: getVal('hand_min_thick'),
            hand_min_len: getVal('hand_min_len'),
            hand_min_style: getVal('hand_min_style'),
            hand_sec_color: getVal('hand_sec_color'),
            hand_sec_thick: getVal('hand_sec_thick'),
            hand_sec_len: getVal('hand_sec_len'),
            hand_sec_style: getVal('hand_sec_style'),
            hand_sweep: getVal('hand_sweep'),
            show_close_x: $form.find('[name="adremm_clock_settings[show_close_x]"]').is(':checked') ? 'yes' : 'no',
            close_x_size: getVal('close_x_size'),
            color_close_x: getVal('color_close_x'),
            show_close_label: $form.find('[name="adremm_clock_settings[show_close_label]"]').is(':checked') ? 'yes' : 'no',
            close_label: getVal('close_label'),
            color_close_label: getVal('color_close_label')
        };

        // Root styles & Theme classes
        $liveView.removeClass('theme-mode-light theme-mode-dark theme-mode-auto');
        $liveView.addClass('theme-mode-' + s.theme_mode);

        $liveView.css({
            '--user-bg': (s.bg_color && s.bg_color !== 'transparent') ? s.bg_color : 'transparent',
            '--user-text': s.text_color || '#000000',
            'background-color': 'var(--adremm-clock-bg)',
            'color': 'var(--adremm-clock-text)',
            'font-family': (s.theme_font && s.theme_font !== 'inherit') ? `"${s.theme_font}"` : 'inherit',
            'box-shadow': s.panel_shadow || 'none',
            'border': s.panel_border || 'none'
        });

        // Preload fonts
        [s.theme_font, s.digital_font, s.font_status, s.font_date].forEach(font => {
            if (font && font !== 'inherit') {
                const url = 'https://fonts.googleapis.com/css2?family=' + font.replace(/ /g, '+') + '&display=swap';
                if (!$('link[href="' + url + '"]').length) $('head').append('<link rel="stylesheet" href="' + url + '">');
            }
        });

        // Analog
        const $analog = $liveView.find('.analog-preview');
        const $face = $liveView.find('.clock-face');
        if (s.show_analog === 'yes') {
            $analog.show();
            $face.css({
                'background-image': s.analog_bg_image ? `url(${s.analog_bg_image})` : 'none',
                'background-color': s.analog_bg_image ? 'transparent' : s.analog_bg_color,
                'border-color': s.analog_ring_color,
                'border-width': s.analog_ring_size + 'px'
            });

            // Notations
            const $notations = $face.find('.notations');
            if ($notations.length === 0) {
                // Initialize preview notation structure if missing
                let html = '<div class="notations hour-notations"></div><div class="notations min-notations"></div>';
                $face.prepend(html);
            }

            const $hNots = $face.find('.hour-notations');
            $hNots.html('');
            for(let i=1; i<=12; i++) {
                $hNots.append(`<i style="transform: rotate(${i*30}deg); transform-origin: center 40px;"></i>`);
            }
            $hNots.css({
                'color': s.analog_hour_color,
                '--not-thick': s.analog_hour_thick + 'px',
                '--not-len': s.analog_hour_length + 'px',
                'display': 'block',
                'z-index': (s.analog_not_above === 'yes' ? 20 : 5),
                'transform': `scale(${s.analog_not_scale})`
            });
            if (s.analog_not_above === 'yes') $hNots.addClass('above'); else $hNots.removeClass('above');

            const $mNots = $face.find('.min-notations');
            $mNots.html('');
            for(let i=1; i<=60; i++) {
                if(i%5!==0) $mNots.append(`<i style="transform: rotate(${i*6}deg); transform-origin: center 40px;"></i>`);
            }
            $mNots.css({
                'color': s.analog_min_color,
                '--not-thick': s.analog_min_thick + 'px',
                '--not-len': s.analog_min_length + 'px',
                'display': 'block',
                'z-index': (s.analog_not_above === 'yes' ? 20 : 5),
                'transform': `scale(${s.analog_not_scale})`
            });
            if (s.analog_not_above === 'yes') $mNots.addClass('above'); else $mNots.removeClass('above');

            $liveView.find('.hand.hour').attr('class', 'hand hour ' + s.hand_hour_style).css({
                'background-color': (s.hand_hour_style === 'steampunk' ? 'transparent' : s.hand_hour_color),
                'color': s.hand_hour_color,
                'width': s.hand_hour_thick + 'px',
                'height': s.hand_hour_len + '%'
            });
            $liveView.find('.hand.min').attr('class', 'hand min ' + s.hand_min_style).css({
                'background-color': (s.hand_min_style === 'steampunk' ? 'transparent' : s.hand_min_color),
                'color': s.hand_min_color,
                'width': s.hand_min_thick + 'px',
                'height': s.hand_min_len + '%'
            });
            $liveView.find('.hand.sec').attr('class', 'hand sec ' + s.hand_sec_style).css({
                'background-color': (s.hand_sec_style === 'steampunk' ? 'transparent' : s.hand_sec_color),
                'color': s.hand_sec_color,
                'width': s.hand_sec_thick + 'px',
                'height': s.hand_sec_len + '%'
            });
        } else {
            $analog.hide();
        }

        // Digital
        const $timeWrap = $liveView.find('.clock-info');
        let $time = $timeWrap.find('.preview-time');
        if ($time.length === 0) {
            $time = $('<div class="preview-time"></div>');
            $timeWrap.prepend($time);
        }

        $time.parent().removeClass('digital-style-alarm digital-style-wall digital-style-custom digital-style-blocks digital-style-dots digital-style-design has-glow');
        $time.parent().addClass('time-row'); // Ensure base class

        if (s.show_digital === 'yes') {
            $time.parent().show().addClass('digital-style-' + s.digital_style);
            if (s.digital_glow === 'yes') $time.parent().addClass('has-glow');

            $time.parent().css({
                'color': s.digital_color,
                'font-family': (s.digital_font && s.digital_font !== 'inherit') ? `"${s.digital_font}"` : 'inherit',
                '--digital-glow-color': s.digital_glow_color,
                '--digital-glow-spread': s.digital_glow_spread + 'px'
            });

            // Update content based on style
            const now = new Date();
            const hh = String(now.getHours()).padStart(2, '0');
            const mm = String(now.getMinutes()).padStart(2, '0');
            const ss = String(now.getSeconds()).padStart(2, '0');

            if (s.digital_style === 'blocks') {
                $time.html(`<span class="b">${hh}</span>:<span class="b">${mm}</span>:<span class="b">${ss}</span>`);
            } else if (s.digital_style === 'wall') {
                const digits = (hh + mm).split('');
                let html = '';
                digits.forEach((d, i) => {
                    html += `<div class="digit-col"><span style="transform: translateY(-${parseInt(d) * 32}px)">0\n1\n2\n3\n4\n5\n6\n7\n8\n9</span></div>`;
                    if (i === 1) html += '<span>:</span>';
                });
                $time.html(`${html}<span class="sec" style="opacity:0.4">:${ss}</span>`);
            } else if (s.digital_style === 'design') {
                $time.parent().toggleClass('vertical', s.digital_orientation === 'vertical');
                $time.html(`<span class="day">MA</span> ${hh}:${mm}<span class="sec">${ss}</span>`);
            } else if (s.digital_style === 'alarm') {
                $time.html(`${hh}:${mm}:${ss}`);
            } else {
                $time.text(`${hh}:${mm}:${ss}`);
            }
        } else {
            $time.parent().hide();
        }

        // Status & Date positioning in preview
        const $info = $liveView.find('.clock-info');
        const $pStatus = $info.find('.preview-status');
        const $pDate = $info.find('.preview-date');
        const $pTime = $info.find('.time-row');

        // Reset positions
        $pStatus.detach();
        $pDate.detach();
        $pTime.detach();

        if (s.status_pos === 'above_digital') {
            $info.append($pStatus).append($pTime).append($pDate);
        } else if (s.status_pos === 'below_digital') {
            $info.append($pTime).append($pStatus).append($pDate);
        } else if (s.status_pos === 'below_date') {
            $info.append($pTime).append($pDate).append($pStatus);
        } else {
             $info.append($pTime).append($pStatus).append($pDate);
        }

        $pStatus.text(s.text_open).css({
            'color': s.color_open,
            'font-family': (s.font_status && s.font_status !== 'inherit' && s.font_status !== 'Thema') ? `"${s.font_status}"` : 'inherit'
        });
        $pDate.css({
            'color': s.color_date,
            'font-family': (s.font_date && s.font_date !== 'inherit') ? `"${s.font_date}"` : 'inherit'
        });

        // Extra
        const $extra = $liveView.find('.preview-extra');
        $extra.text(s.extra_message).css({
            'color': s.extra_color,
            'font-size': s.extra_font_size + 'px',
            'display': s.extra_message ? 'block' : 'none'
        });
        if (s.extra_marquee === 'yes') $extra.addClass('marquee-preview');
        else $extra.removeClass('marquee-preview');

        // Size classes (visual feedback in preview)
        $liveView.removeClass('size-small size-normal size-large').addClass('size-' + s.panel_size);

        updateCloseBtn(s);

        // Ensure radio on/off visual in preview
        if (s.digital_style === 'alarm') {
             $liveView.find('#adremm-radio-toggle').toggleClass('on', s.radio_enabled === 'yes');
        }
    }

    // Font Preload for select options and handle preview
    $('.adremm-font-select').on('change', function() {
        const font = $(this).val();
        $(this).css('font-family', (font && font !== 'inherit' && font !== 'Thema') ? `"${font}"` : 'inherit');
    }).each(function() {
        const $sel = $(this);
        $sel.find('option').each(function() {
            const f = $(this).val();
            if (f && f !== 'inherit' && f !== 'Thema') {
                $(this).css('font-family', `"${f}"`);
            }
        });
    }).trigger('change');

    $form.on('input change keyup', 'input, select, textarea', function() {
        updatePreview();
    });

    // Special listener for radio buttons and checkboxes to ensure they trigger on click
    $form.on('click', 'input[type="radio"], input[type="checkbox"]', function() {
        setTimeout(updatePreview, 10);
    });

    // Initial trigger to sync UI with loaded settings
        if ($('.nav-tab.is-active').length) $('.nav-tab.is-active').trigger('click');
        if ($('.sub-tab-btn.active').length) $('.sub-tab-btn.active').trigger('click');


        // Final force update
        setTimeout(updatePreview, 100);

    // Close preview button logic
    function updateCloseBtn(s) {
        let $btn = $liveView.find('.preview-close');
        if ($btn.length === 0) {
            $btn = $('<div class="preview-close"></div>');
            $liveView.prepend($btn);
        }
        $btn.html('');
        if (s.show_close_label === 'yes') {
            $btn.append(`<span class="close-label" style="color:${s.color_close_label}">${s.close_label}</span>`);
        }
        if (s.show_close_x === 'yes') {
            $btn.append(`<span class="close-x" style="font-size:${s.close_x_size}px; color:${s.color_close_x}">&times;</span>`);
        }
        if (s.show_close_x !== 'yes' && s.show_close_label !== 'yes') $btn.hide(); else $btn.show();
    }

    function animateClock() {
        const now = new Date();
        const s = now.getSeconds();
        const m = now.getMinutes();
        const h = now.getHours();
        const ms = now.getMilliseconds();

        const handSweep = getVal('hand_sweep');
        let secDeg = s * 6;
        if (handSweep === 'smooth') secDeg = s * 6 + ms * 0.006;
        if (handSweep === 'classy') secDeg = s * 6 + ms * 0.003;

        $liveView.find('.hand.sec').css('transform', `rotate(${secDeg}deg)`);
        $liveView.find('.hand.min').css('transform', `rotate(${m * 6 + s * 0.1}deg)`);
        $liveView.find('.hand.hour').css('transform', `rotate(${h * 30 + m * 0.5}deg)`);

        // Re-run the part of updatePreview that handles digital time logic
        // but only if we are in the middle of a second to show "on the fly" updates
        if (ms < 100) {
            const style = getVal('digital_style');
            const hh = String(h).padStart(2, '0');
            const mm = String(m).padStart(2, '0');
            const ss = String(s).padStart(2, '0');
            const $time = $liveView.find('.preview-time');

            if (style === 'blocks') {
                $time.html(`<span class="b">${hh}</span>:<span class="b">${mm}</span>:<span class="b">${ss}</span>`);
            } else if (style === 'wall') {
                const digits = (hh + mm).split('');
                let html = '';
                digits.forEach((d, i) => {
                    html += `<div class="digit-col"><span style="transform: translateY(-${parseInt(d) * 32}px)">0\n1\n2\n3\n4\n5\n6\n7\n8\n9</span></div>`;
                    if (i === 1) html += '<span>:</span>';
                });
                $time.html(`${html}<span class="sec" style="opacity:0.4">:${ss}</span>`);
            } else if (style === 'design') {
                $time.html(`<span class="day">MA</span> ${hh}:${mm}<span class="sec">${ss}</span>`);
            } else {
                $time.text(`${hh}:${mm}:${ss}`);
            }
        }
    }
    setInterval(animateClock, 1000);
    animateClock();
    updatePreview();
});
