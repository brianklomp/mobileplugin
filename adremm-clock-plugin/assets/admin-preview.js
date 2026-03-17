/**
 * ADREMM Clock Admin Preview Logic V2
 */
jQuery(document).ready(function($) {
    const $form = $('#adremm-clock-form');
    const $liveView = $('#clock-live-view');

    // Tab Switching
    $('.adremm-nav-tabs .nav-tab').on('click', function(e) {
        e.preventDefault();
        const target = $(this).attr('href');
        $('.nav-tab').removeClass('is-active');
        $(this).addClass('is-active');
        $('.adremm-panel').removeClass('is-active');
        $(target).addClass('is-active');
    });

    // Joystick selection
    $('.joy-btn').on('click', function() {
        if ($(this).hasClass('joy-center')) return;
        $('.joy-btn').removeClass('is-selected');
        $(this).addClass('is-selected');
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

    // Dynamic Updates
    function updatePreview() {
        const settings = {
            text_open: $('input[name="adremm_clock_settings[text_open]"]').val(),
            text_closed: $('input[name="adremm_clock_settings[text_closed]"]').val(),
            show_status: $('input[name="adremm_clock_settings[show_status]"]:checked').val(),
            show_date: $('input[name="adremm_clock_settings[show_date]"]:checked').val(),
            color_open: $('input[name="adremm_clock_settings[color_open]"]').val(),
            color_closed: $('input[name="adremm_clock_settings[color_closed]"]').val(),
            color_date: $('input[name="adremm_clock_settings[color_date]"]').val(),
            extra_message: $('textarea[name="adremm_clock_settings[extra_message]"]').val(),
            extra_color: $('input[name="adremm_clock_settings[extra_color]"]').val(),
            font_status: $('select[name="adremm_clock_settings[font_status]"]').val(),
        };

        const $status = $liveView.find('.preview-status');
        const $date = $liveView.find('.preview-date');
        const $extra = $liveView.find('.preview-extra');

        // Update Status
        if (settings.show_status === 'yes') {
            $status.show().text(settings.text_open).css({
                'color': settings.color_open,
                'font-family': settings.font_status
            });
        } else {
            $status.hide();
        }

        // Update Date
        if (settings.show_date === 'yes') {
            $date.show().css('color', settings.color_date);
        } else {
            $date.hide();
        }

        // Update Extra
        $extra.text(settings.extra_message).css('color', settings.extra_color);

        // Load fonts if changed
        if (settings.font_status) {
            const fontUrl = 'https://fonts.googleapis.com/css2?family=' + settings.font_status.replace(/ /g, '+') + '&display=swap';
            if (!$('link[href="' + fontUrl + '"]').length) {
                $('head').append('<link rel="stylesheet" href="' + fontUrl + '">');
            }
        }
    }

    // Font Preload for visual dropdown
    $('.adremm-font-select option').each(function() {
        const font = $(this).val();
        if (font) {
            const fontUrl = 'https://fonts.googleapis.com/css2?family=' + font.replace(/ /g, '+') + '&display=swap';
            if (!$('link[href="' + fontUrl + '"]').length) {
                $('head').append('<link rel="stylesheet" href="' + fontUrl + '">');
            }
        }
    });

    // Bind all inputs for live update
    $form.find('input, select, textarea').on('input change', updatePreview);

    // Initial analog clock animation for preview
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
