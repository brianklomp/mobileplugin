/**
 * ADREMM Clock Admin Preview Logic V3 - FULL UPGRADE
 */
jQuery(document).ready(function($) {
    const $form = $('#adremm-clock-form');
    const $liveView = $('#clock-live-view');

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
    });

    // Color Pickers
    if ($.isFunction($.fn.wpColorPicker)) {
        $('.adremm-color-picker').wpColorPicker({
            change: function(event, ui) {
                setTimeout(updatePreview, 5);
            },
            clear: updatePreview,
            alpha: true
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

    // Dynamic Updates
    function updatePreview() {
        const getVal = (name) => $form.find(`[name="adremm_clock_settings[${name}]"]`).val();
        const getRadioVal = (name) => $form.find(`[name="adremm_clock_settings[${name}]"]:checked`).val();

        const s = {
            theme_font: getVal('theme_font'),
            bg_color: getVal('bg_color'),
            text_color: getVal('text_color'),
            panel_size: getVal('panel_size'),
            show_analog: getRadioVal('show_analog'),
            analog_bg_image: getVal('analog_bg_image'),
            analog_bg_color: getVal('analog_bg_color'),
            analog_ring_color: getVal('analog_ring_color'),
            analog_ring_size: getVal('analog_ring_size'),
            show_digital: getRadioVal('show_digital'),
            digital_color: getVal('digital_color'),
            digital_font: getVal('digital_font'),
            show_status: getRadioVal('show_status'),
            text_open: getVal('text_open'),
            color_open: getVal('color_open'),
            font_status: getVal('font_status'),
            show_date: getRadioVal('show_date'),
            color_date: getVal('color_date'),
            font_date: getVal('font_date'),
            extra_message: $form.find('textarea[name="adremm_clock_settings[extra_message]"]').val(),
            extra_color: getVal('extra_color'),
            extra_font_size: getVal('extra_font_size'),
            extra_marquee: getRadioVal('extra_marquee'),
            hand_hour_color: getVal('hand_hour_color'),
            hand_min_color: getVal('hand_min_color'),
            hand_sec_color: getVal('hand_sec_color')
        };

        const $container = $('#adremm-clock-live-preview'); // Note: Make sure ID matches or use $liveView

        // Root styles
        $liveView.css({
            'background-color': s.bg_color,
            'color': s.text_color,
            'font-family': s.theme_font
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
            $liveView.find('.hand.hour').css('background-color', s.hand_hour_color);
            $liveView.find('.hand.min').css('background-color', s.hand_min_color);
            $liveView.find('.hand.sec').css('background-color', s.hand_sec_color);
        } else {
            $analog.hide();
        }

        // Digital
        const $time = $liveView.find('.preview-time');
        if (s.show_digital === 'yes') {
            $time.show().css({
                'color': s.digital_color,
                'font-family': s.digital_font
            });
        } else {
            $time.hide();
        }

        // Status
        const $status = $liveView.find('.preview-status');
        if (s.show_status === 'yes') {
            $status.show().text(s.text_open).css({
                'color': s.color_open,
                'font-family': s.font_status
            });
        } else {
            $status.hide();
        }

        // Date
        const $date = $liveView.find('.preview-date');
        if (s.show_date === 'yes') {
            $date.show().css({
                'color': s.color_date,
                'font-family': s.font_date
            });
        } else {
            $date.hide();
        }

        // Extra
        const $extra = $liveView.find('.preview-extra');
        $extra.text(s.extra_message).css({
            'color': s.extra_color,
            'font-size': s.extra_font_size + 'px'
        });
        if (s.extra_marquee === 'yes') $extra.addClass('marquee-preview');
        else $extra.removeClass('marquee-preview');

        // Size classes (visual feedback in preview)
        $liveView.removeClass('size-small size-normal size-large').addClass('size-' + s.panel_size);
    }

    // Font Preload
    $('.adremm-font-select option').each(function() {
        const f = $(this).val();
        if (f && f !== 'inherit') {
            const url = 'https://fonts.googleapis.com/css2?family=' + f.replace(/ /g, '+') + '&display=swap';
            if (!$('link[href="' + url + '"]').length) $('head').append('<link rel="stylesheet" href="' + url + '">');
        }
    });

    $form.find('input, select, textarea').on('input change', updatePreview);

    function animateClock() {
        const now = new Date();
        const s = now.getSeconds();
        const m = now.getMinutes();
        const h = now.getHours();
        $liveView.find('.hand.sec').css('transform', `rotate(${s * 6}deg)`);
        $liveView.find('.hand.min').css('transform', `rotate(${m * 6 + s * 0.1}deg)`);
        $liveView.find('.hand.hour').css('transform', `rotate(${h * 30 + m * 0.5}deg)`);
        $liveView.find('.preview-time').text(now.toLocaleTimeString('nl-NL'));
    }
    setInterval(animateClock, 1000);
    animateClock();
    updatePreview();
});
