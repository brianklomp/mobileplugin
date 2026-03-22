/**
 * ADREMM Clock Admin Preview Logic V6 - SYNCED CLASSES
 */
jQuery(document).ready(function($) {
    const $form = $('#adremm-clock-form, #adremm-clock-mobile-form');
    const $liveView = $('#clock-live-view, #clock-mobile-view');

    // Style font select options
    $('.adremm-font-select option').each(function() {
        const font = $(this).val();
        if (font && font !== 'Thema' && font !== 'inherit') {
            $(this).css('font-family', font);
        }
    });

    $('.adremm-nav-tabs .nav-tab').on('click', function(e) {
        e.preventDefault();
        const target = $(this).attr('href');
        $('.adremm-nav-tabs .nav-tab').removeClass('is-active');
        $(this).addClass('is-active');
        $('.adremm-panel').removeClass('is-active');
        $(target).addClass('is-active');
    });

    // Handle percentage inputs for analog scale
    $form.on('input', 'input[name="adremm_clock_settings[analog_not_scale_pct]"]', function() {
        const val = parseFloat($(this).val()) / 100;
        $form.find('input[name="adremm_clock_settings[analog_not_scale]"]').val(val);
    });
    $form.on('input', 'input[name="adremm_clock_settings[analog_hand_scale_pct]"]', function() {
        const val = parseFloat($(this).val()) / 100;
        $form.find('input[name="adremm_clock_settings[analog_hand_scale]"]').val(val);
    });

    $form.on('input', 'input[name="adremm_clock_settings[extra_speed]"]', function() {
        const ms = (21 - $(this).val()) * 100;
        $('#extra-speed-val').text(ms + ' ms');
    });

    $form.on('change', 'select[name="adremm_clock_settings[analog_theme]"]', function() {
        updatePreview();
    });

    $('.joy-item').on('click', function() {
        $('.joy-item').removeClass('active');
        $(this).addClass('active');
        $(this).find('input').prop('checked', true).trigger('change');
    });

    if ($.isFunction($.fn.wpColorPicker)) {
        $('.adremm-color-picker').wpColorPicker({
            alpha: true,
            change: function(event, ui) {
                const color = ui.color.to_s('rgba');
                $(this).val(color);
                setTimeout(updatePreview, 20);
            }
        });
    }

    const getVal = (name) => $form.find(`[name="adremm_clock_settings[${name}]"]`).val();
    const getRadioVal = (name) => $form.find(`[name="adremm_clock_settings[${name}]"]:checked`).val();

    function updateWidthLogic() {
        const pos = getRadioVal('position');
        const isBar = (pos === 'top-center' || pos === 'bottom-center');
        if (isBar) {
            $liveView.removeClass('adremm-clock-panel').addClass('adremm-clock-bar');
        } else {
            $liveView.removeClass('adremm-clock-bar').addClass('adremm-clock-panel');
        }

        const size = getVal('panel_size');
        const customOverride = $form.find('input[name="adremm_clock_settings[panel_custom_override]"]').is(':checked');
        const $customInput = $form.find('input[name="adremm_clock_settings[panel_width_custom]"]');
        const $hiddenWidth = $form.find('input[name="adremm_clock_settings[panel_width]"]');

        let width = 380;
        let scale = 1.0;
        if (customOverride) {
            $customInput.prop('disabled', false);
            width = parseFloat($customInput.val()) || 380;
            scale = 1.0;
        } else {
            $customInput.prop('disabled', true);
            if (size === 'small') { width = 280; scale = 0.73; }
            else if (size === 'normal') { width = 380; scale = 1.0; }
            else if (size === 'large') { width = 480; scale = 1.26; }
        }
        $hiddenWidth.val(width);

        // Apply to preview container immediately
        $liveView.removeClass('panel-size-small panel-size-normal panel-size-large custom-width-active');
        $liveView.addClass('panel-size-' + size);
        if (customOverride) $liveView.addClass('custom-width-active');

        return { width, scale, customOverride };
    }

    function updatePreview() {
        const { width, scale, customOverride } = updateWidthLogic();

        // Initial setup of header if not exists
        if ($liveView.find('.adremm-clock-header').length === 0) {
            $liveView.find('.adremm-clock-container').prepend('<div class="adremm-clock-header" style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:15px; order:-10; width:100%;"><div class="header-status-left" style="flex:1; text-align:left;"></div></div>');
        }

        const s = {
            theme_mode: getRadioVal('theme_mode'),
            theme_font: getVal('theme_font'),
            bg_color: getVal('bg_color'),
            text_color: getVal('text_color'),
            panel_size: getVal('panel_size'),
            panel_width: width,
            custom_width_active: customOverride,
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
            analog_hand_scale: getVal('analog_hand_scale'),
            analog_theme: getVal('analog_theme'),
            show_digital: getRadioVal('show_digital'),
            digital_color: getVal('digital_color'),
            digital_font: getVal('digital_font'),
            digital_style: getVal('digital_style'),
            digital_width: getVal('digital_width'),
            digital_height: getVal('digital_height'),
            digital_font_size: getVal('digital_font_size'),
            digital_font_weight: getVal('digital_font_weight'),
            digital_italic: $form.find('[name="adremm_clock_settings[digital_italic]"]').is(':checked') ? 'yes' : 'no',
            digital_border_size: getVal('digital_border_size'),
            digital_border_color: getVal('digital_border_color'),
            digital_border_radius: getVal('digital_border_radius'),
            digital_font_url: getVal('digital_font_url'),
            digital_glow: $form.find('[name="adremm_clock_settings[digital_glow]"]').is(':checked') ? 'yes' : 'no',
            digital_glow_color: getVal('digital_glow_color'),
            digital_glow_spread: getVal('digital_glow_spread'),
            minimalist_padding: getVal('minimalist_padding'),
            minimalist_radius: getVal('minimalist_radius'),
            minimalist_bg: getVal('minimalist_bg'),
            vintage_logo: getVal('vintage_logo'),
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
            analog_center_ring: $form.find('[name="adremm_clock_settings[analog_center_ring]"]').is(':checked') ? 'yes' : 'no',
            analog_center_ring_size: getVal('analog_center_ring_size'),
            analog_center_ring_color: getVal('analog_center_ring_color'),
            analog_overshoot: $form.find('[name="adremm_clock_settings[analog_overshoot]"]').is(':checked') ? 'yes' : 'no',

            hand_hour_color: getVal('hand_hour_color'),
            hand_hour_thick: getVal('hand_hour_thick'),
            hand_hour_len: getVal('hand_hour_len'),
            hand_hour_style: getVal('hand_hour_style'),
            hand_hour_center_ring: $form.find('[name="adremm_clock_settings[hand_hour_center_ring]"]').is(':checked') ? 'yes' : 'no',
            hand_hour_center_size: getVal('hand_hour_center_size'),
            hand_hour_center_color: getVal('hand_hour_center_color'),

            hand_min_color: getVal('hand_min_color'),
            hand_min_thick: getVal('hand_min_thick'),
            hand_min_len: getVal('hand_min_len'),
            hand_min_style: getVal('hand_min_style'),
            hand_min_center_ring: $form.find('[name="adremm_clock_settings[hand_min_center_ring]"]').is(':checked') ? 'yes' : 'no',
            hand_min_center_size: getVal('hand_min_center_size'),
            hand_min_center_color: getVal('hand_min_center_color'),

            hand_sec_color: getVal('hand_sec_color'),
            hand_sec_thick: getVal('hand_sec_thick'),
            hand_sec_len: getVal('hand_sec_len'),
            hand_sec_style: getVal('hand_sec_style'),
            hand_sec_center_ring: $form.find('[name="adremm_clock_settings[hand_sec_center_ring]"]').is(':checked') ? 'yes' : 'no',
            hand_sec_center_size: getVal('hand_sec_center_size'),
            hand_sec_center_color: getVal('hand_sec_center_color'),
            hand_sweep: getVal('hand_sweep'),
            show_close_x: $form.find('[name="adremm_clock_settings[show_close_x]"]').is(':checked') ? 'yes' : 'no',
            close_x_size: getVal('close_x_size'),
            color_close_x: getVal('color_close_x'),
            show_close_label: $form.find('[name="adremm_clock_settings[show_close_label]"]').is(':checked') ? 'yes' : 'no',
            close_label: getVal('close_label'),
            color_close_label: getVal('color_close_label'),
            close_label_font_size: getVal('close_label_font_size'),
        };

        $liveView.removeClass('theme-mode-light theme-mode-dark theme-mode-auto');
        $liveView.addClass('theme-mode-' + s.theme_mode);

        const liveStyles = {
            '--user-bg': s.bg_color || 'transparent',
            '--user-text': s.text_color || '#000',
            'font-family': (s.theme_font && s.theme_font !== 'inherit') ? `"${s.theme_font}"` : 'inherit',
            '--panel-scale': scale,
            '--panel-width': width + 'px',
            '--digital-font-size-base': s.digital_font_size + 'px',
            '--digital-width-final': (s.digital_style === 'custom') ? (s.digital_width * scale) + 'px' : '100%',
            '--digital-height-final': (s.digital_style === 'custom') ? (s.digital_height * scale) + 'px' : 'auto'
        };
        $liveView.css(liveStyles);

        // Set system font preview globally in preview container
        if (s.theme_font && s.theme_font !== 'inherit') {
            $('head').append(`<link href="https://fonts.googleapis.com/css2?family=${s.theme_font.replace(/ /g, '+')}&display=swap" rel="stylesheet">`);
        }

        // Digital Glitch Mock
        if (s.digital_style === 'blocks') {
            const isGlitch = Math.random() > 0.9;
            $liveView.find('.preview-time span.b').toggleClass('glitch', isGlitch);
        }

        // Analog
        const $analog = $liveView.find('.analog-preview');
        const $face = $liveView.find('.clock-face');
        if (s.show_analog === 'yes') {
            $analog.show();
            $face.removeClass('theme-mondriaan');
            if (s.analog_theme === 'mondriaan') {
                $face.addClass('theme-mondriaan');
                if ($face.find('.mondriaan-elements').length === 0) {
                    $face.append('<div class="mondriaan-elements"><div class="mondriaan-mark-12"></div><div class="mondriaan-mark-9"></div><div class="mondriaan-hub"></div></div>');
                }
                // Force colors for Mondriaan
                s.hand_hour_color = '#2a3492';
                s.hand_min_color = '#ffff00';
                s.hand_sec_color = '#ff3b30';
            } else {
                $face.find('.mondriaan-elements').remove();
            }

            $face.css({
                'background-image': s.analog_bg_image ? `url(${s.analog_bg_image})` : 'none',
                'background-color': (s.analog_theme === 'mondriaan') ? '#fff' : (s.analog_bg_image ? 'transparent' : s.analog_bg_color),
                'border-color': (s.analog_theme === 'mondriaan') ? '#000' : s.analog_ring_color,
                'border-width': (s.analog_theme === 'mondriaan') ? '6px' : s.analog_ring_size + 'px'
            });

            // Center Ring - now handled inside hand divs in preview for 1:1
            $face.find('.center-ring').remove();

            // Notations
            if ($face.find('.hour-notations').length === 0) {
                $face.prepend('<div class="notations hour-notations"></div><div class="notations min-notations"></div>');
            }
            $face.find('.hour-notations').html('').css({ 'color': s.analog_hour_color, '--not-thick': s.analog_hour_thick + 'px', '--not-len': s.analog_hour_length + 'px', 'transform': `scale(${s.analog_not_scale})` });
            for(let i=1; i<=12; i++) $face.find('.hour-notations').append(`<i style="transform: rotate(${i*30}deg)"></i>`);

            $face.find('.min-notations').html('').css({ 'color': s.analog_min_color, '--not-thick': s.analog_min_thick + 'px', '--not-len': s.analog_min_length + 'px', 'transform': `scale(${s.analog_not_scale})` });
            for(let i=1; i<=60; i++) if(i%5!==0) $face.find('.min-notations').append(`<i style="transform: rotate(${i*6}deg)"></i>`);

            const overshootClass = (s.analog_overshoot === 'yes') ? ' has-overshoot' : '';

            ['hour', 'min', 'sec'].forEach(h => {
                const $h = $liveView.find('.h-' + h);
                $h.attr('class', 'h-' + h + ' ' + s['hand_' + h + '_style'] + overshootClass).css({
                    'background-color': s['hand_' + h + '_color'],
                    'color': s['hand_' + h + '_color'],
                    'width': s['hand_' + h + '_thick'] + 'px',
                    'height': s['hand_' + h + '_len'] + '%',
                    '--adremm-scale': s.analog_hand_scale
                });

                // Remove existing center ring to prevent duplication
                $h.find('.center-ring').remove();

                if (s['hand_' + h + '_center_ring'] === 'yes') {
                    $h.append(`<div class="center-ring" style="width:${s['hand_' + h + '_center_size']}px; height:${s['hand_' + h + '_center_size']}px; background:${s['hand_' + h + '_center_color']};"></div>`);
                }
            });
        } else { $analog.hide(); }

        // Digital
        const $timeWrap = $liveView.find('.clock-info');
        let $time = $timeWrap.find('.preview-time');
        if ($time.length === 0) { $time = $('<div class="preview-time"></div>'); $timeWrap.prepend($time); }
        $time.parent().attr('class', 'time-row digital-style-' + s.digital_style + (s.digital_glow === 'yes' ? ' has-glow' : ''));

        if (s.show_digital === 'yes') {
            let fontFamily = (s.digital_font && s.digital_font !== 'inherit') ? `"${s.digital_font}"` : 'inherit';
            if (s.digital_style === 'custom' && s.digital_font_url) {
                const customFontName = 'CustomPreviewFont';
                const style = document.createElement('style');
                style.innerHTML = `@font-face { font-family: "${customFontName}"; src: url("${s.digital_font_url}"); }`;
                document.head.appendChild(style);
                fontFamily = `"${customFontName}"`;
            }

            $time.parent().show().css({
                'color': s.digital_color,
                'font-family': fontFamily,
                'font-size': (s.digital_style === 'custom' || s.digital_style === 'pixels' || s.digital_style === 'blocks') ? s.digital_font_size + 'px' : '',
                'font-weight': s.digital_font_weight,
                'font-style': (s.digital_italic === 'yes') ? 'italic' : 'normal',
                'border': (s.digital_style === 'custom') ? s.digital_border_size + 'px solid ' + s.digital_border_color : '',
                'border-radius': (s.digital_style === 'custom') ? s.digital_border_radius + 'px' : ''
            });

            const now = new Date();
            const hh = String(now.getHours()).padStart(2, '0');
            const mm = String(now.getMinutes()).padStart(2, '0');
            const ss = String(now.getSeconds()).padStart(2, '0');

            // Second-change pulse
            if ($time.attr('data-ss') !== ss) {
                $time.addClass('tick-flash');
                setTimeout(() => $time.removeClass('tick-flash'), 300);
                $time.attr('data-ss', ss);
            }

            if (s.digital_style === 'alarm') {
                const logoHtml = s.vintage_logo ? `<img src="${s.vintage_logo}" class="logo-circle" style="object-fit:cover;">` : `<div class="logo-circle">A</div>`;
                $time.html(`<div class="radio-vintage-body"><div class="nixie-tubes"><div class="nixie-tube">${hh[0]}</div><div class="nixie-tube">${hh[1]}</div><div class="nixie-tube-gap">:</div><div class="nixie-tube">${mm[0]}</div><div class="nixie-tube">${mm[1]}</div></div><div class="radio-scale-container" style="height:80px;"><div class="scale-indicator"></div><div class="radio-scale-scroll-v" style="padding-top:30px;"></div></div><div class="radio-side-panel">${logoHtml}<div class="radio-controls-grid"><div class="power-btn on">X</div><div class="volume-knob on"><div class="knob-line"></div></div></div></div></div>`);
                const $scroll = $time.find('.radio-scale-scroll-v');
                for(let i=0; i<=60; i++) $scroll.append(`<div class="scale-mark"><span>-</span>${String(i).padStart(2, '0')}</div>`);
                $scroll.css('transform', `translateY(${-now.getSeconds() * 20}px)`);
            } else if (s.digital_style === 'blocks') {
                $time.html(`<span class="b">${hh}</span><span class="sep">:</span><span class="b">${mm}</span><span class="sep">:</span><span class="b">${ss}</span>`);
            } else if (s.digital_style === 'wall') {
                if ($time.find('.digit-col').length === 0) {
                    const digits = (hh + mm + ss).split('');
                    let html = '';
                    digits.forEach((d, i) => { html += `<div class="digit-col" data-prev="-1"><span>0\n1\n2\n3\n4\n5\n6\n7\n8\n9\n0</span></div>`; if (i === 1 || i === 3) html += '<div class="sep">:</div>'; });
                    $time.html(html).css('display', 'flex');
                }
            } else if (s.digital_style === 'design') {
                const dayName = now.toLocaleDateString('nl-NL', { weekday: 'short' }).toUpperCase().substring(0,2);
                $time.html(`<div class="minimalist-container" style="background:var(--user-bg-final); padding:10px; border-radius:8px; display:flex; font-family:monospace; align-items:center; justify-content:center; gap:15px; width:100%;">
                    <div class="mini-col" style="display:flex; flex-direction:column; align-items:center;">
                        <span style="color:var(--user-text-final); font-size:32px; font-weight:bold; line-height:1;">${dayName}</span>
                        <span style="color:var(--user-text-final); font-size:10px; opacity:0.6; text-transform:uppercase; margin-top:4px;">dag</span>
                    </div>
                    <div class="mini-col" style="display:flex; flex-direction:column; align-items:center;">
                        <span style="color:var(--user-text-final); font-size:32px; font-weight:bold; line-height:1;">${hh}</span>
                        <span style="color:var(--user-text-final); font-size:10px; opacity:0.6; text-transform:uppercase; margin-top:4px;">uur</span>
                    </div>
                    <div class="mini-col" style="display:flex; flex-direction:column; align-items:center;">
                        <span style="color:var(--user-text-final); font-size:32px; font-weight:bold; line-height:1;">${mm}</span>
                        <span style="color:var(--user-text-final); font-size:10px; opacity:0.6; text-transform:uppercase; margin-top:4px;">min</span>
                    </div>
                    <div class="mini-col" style="display:flex; flex-direction:column; align-items:center;">
                        <span style="color:var(--user-text-final); font-size:32px; font-weight:bold; line-height:1;">${ss}</span>
                        <span style="color:var(--user-text-final); font-size:10px; opacity:0.6; text-transform:uppercase; margin-top:4px;">sec</span>
                    </div>
                </div>`);
            } else { $time.text(`${hh}:${mm}:${ss}`); }
        } else { $time.parent().hide(); }

        const $status = $liveView.find('.preview-status');
        const $headerStatusContainer = $liveView.find('.header-status-left');

        $status.text(s.text_open).css({ 'color': s.color_open, 'font-family': (s.font_status && s.font_status !== 'inherit') ? `"${s.font_status}"` : 'inherit' });

        // Match new template logic
        if (s.status_pos === 'above_digital' || s.status_pos === 'above_analog') {
            if ($headerStatusContainer.find('.preview-status').length === 0) {
                $headerStatusContainer.append($status);
            }
            $status.css('order', '0');
        } else {
            const $info = $liveView.find('.clock-info');
            if ($info.find('.preview-status').length === 0) {
                $info.append($status);
            }
            if (s.status_pos === 'below_analog') $status.css('order', '12');
            else if (s.status_pos === 'below_digital') $status.css('order', '22');
            else if (s.status_pos === 'below_date') $status.css('order', '32');
            else $status.css('order', '21');
        }

        updateCloseBtn(s);
        updateTabPreview(s);
    }

    function updateTabPreview(s) {
        let $tab = $liveView.find('.adremm-clock-tab');
        if ($tab.length === 0) {
            $tab = $('<div class="adremm-clock-tab" style="display:none;"><span class="tab-label"></span></div>');
            $liveView.append($tab);
        }

        $tab.removeClass('tab-left tab-right');
        const pos = getRadioVal('position');
        const isLeftSide = pos.includes('left');
        // Opposite snapping for preview too
        $tab.addClass(isLeftSide ? 'tab-right' : 'tab-left');

        $tab.find('.tab-label').text(s.tab_text || 'KLOK');
        $tab.css({
            'background-color': s.tab_bg,
            'color': s.tab_color,
            'font-family': s.tab_font
        });
    }

    $form.on('input change keyup click mouseup', 'input, select, textarea', function() {
        if ($(this).hasClass('adremm-font-select')) {
            const font = $(this).val();
            $(this).css('font-family', (font === 'Thema' || font === 'inherit') ? 'inherit' : font);
        }
        // Force refresh for all changes immediately
        updatePreview();
    });

    // Special trigger for color picker changes which don't always fire 'change' correctly
    $(document).on('wpcolorpicker:change', function(e, ui) {
        updatePreview();
    });

    function updateCloseBtn(s) {
        let $header = $liveView.find('.adremm-clock-header');
        let $btn = $header.find('.preview-close');
        if ($btn.length === 0) { $btn = $('<div class="preview-close"></div>'); $header.append($btn); }
        $btn.html('').attr('style', 'display:flex; align-items:center; gap:5px; z-index:20; margin-left:auto;');
        if (s.show_close_label === 'yes') $btn.append(`<span class="close-label" style="color:${s.color_close_label}; font-size:${s.close_label_font_size}px;">${s.close_label}</span>`);
        if (s.show_close_x === 'yes') $btn.append(`<span class="close-x" style="font-size:${s.close_x_size}px; color:${s.color_close_x}; line-height:1;">&times;</span>`);
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
        $liveView.find('.h-sec').css('--adremm-rotate', `${secDeg}deg`);
        $liveView.find('.h-min').css('--adremm-rotate', `${m * 6 + s * 0.1}deg`);
        $liveView.find('.h-hour').css('--adremm-rotate', `${h * 30 + m * 0.5}deg`);

        if (getVal('digital_style') === 'wall') {
            const digits = (String(h).padStart(2, '0') + String(m).padStart(2, '0') + String(s).padStart(2, '0')).split('');
            const $cols = $liveView.find('.digit-col');
            const digitHeight = $cols.first().height() || 60;

            $cols.each(function(i) {
                const d = parseInt(digits[i]);
                const prev = parseInt($(this).attr('data-prev'));
                const $span = $(this).find('span');
                if (prev !== d) {
                    let targetIdx = d;
                    if (prev === 9 && d === 0) targetIdx = 10;
                    $span.css({ 'transition': 'transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)', 'transform': `translateY(-${targetIdx * digitHeight}px)` });
                    if (targetIdx === 10) {
                        setTimeout(() => { $span.css({ 'transition': 'none', 'transform': 'translateY(0)' }); }, 600);
                    }
                    $(this).attr('data-prev', d);
                }
            });
        }
    }
    setInterval(animateClock, 50);
    updatePreview();
});
