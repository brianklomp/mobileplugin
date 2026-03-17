/**
 * ADREMM Clock Admin Preview Logic
 */
jQuery(document).ready(function($) {
    const $preview = $('#adremm-clock-live-preview');
    const $form = $('#adremm-clock-form');

    // Tab Navigation
    $('.adremm-clock-tab-link').on('click', function(e) {
        e.preventDefault();
        const target = $(this).attr('href');
        $('.adremm-clock-tab-link').removeClass('is-active');
        $(this).addClass('is-active');
        $('.adremm-clock-tab-content').removeClass('is-active');
        $(target).addClass('is-active');
    });

    // Theme Update
    $form.find('input[name="adremm_clock_settings[theme]"]').on('change', function() {
        const theme = $(this).val();
        $preview.removeClass('theme-modern theme-classic theme-digital').addClass('theme-' + theme);
    });

    // Font Update
    function loadGoogleFont(font) {
        if (font) {
            const fontUrl = 'https://fonts.googleapis.com/css2?family=' + font.replace(/ /g, '+') + '&display=swap';
            if (!$('link[href="' + fontUrl + '"]').length) {
                $('head').append('<link rel="stylesheet" href="' + fontUrl + '">');
            }
        }
    }

    $('#clock-font-select').on('change', function() {
        const font = $(this).val();
        $preview.css('font-family', font);
        loadGoogleFont(font);
    }).trigger('change');

    // Preload all fonts in dropdown for visual selection
    $('#clock-font-select option').each(function() {
        loadGoogleFont($(this).val());
    });

    // Color Update Function
    function updatePreviewColors() {
        const bg = $form.find('input[name="adremm_clock_settings[bg_color]"]').val();
        const text = $form.find('input[name="adremm_clock_settings[text_color]"]').val();
        $preview.css({
            'background-color': bg,
            'color': text
        });
    }

    // Initialize Alpha Color Picker
    if ($.isFunction($.fn.wpColorPicker)) {
        $('.adremm-color-picker').wpColorPicker({
            change: function(event, ui) {
                // Use a slight timeout to ensure val() is updated
                setTimeout(updatePreviewColors, 5);
            },
            clear: updatePreviewColors,
            alpha: true
        });
    }

    // Position indicator (visual feedback in preview column if needed)
    $form.find('input[name="adremm_clock_settings[position]"]').on('change', function() {
        const pos = $(this).val();
        // Visual feedback logic here if desired
    });

    // Clock update for preview
    function updateTime() {
        const now = new Date();
        $preview.find('.time').text(now.toLocaleTimeString('nl-NL'));
        $preview.find('.date').text(now.toLocaleDateString('nl-NL', { weekday: 'long', day: 'numeric', month: 'long' }));
    }
    setInterval(updateTime, 1000);
    updateTime();

    // Initial color setup
    updatePreviewColors();
});
