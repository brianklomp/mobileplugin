/**
 * Live Preview JavaScript for ADREMM Clock Plugin
 */
jQuery(document).ready(function($) {
    const $preview = $('#adremm-clock-preview');
    const $themeSelect = $('#clock-theme-select');
    const $fontSelect = $('#clock-font-select');
    const $bgColorInput = $('input[name="adremm_clock_settings[bg_color]"]');
    const $textColorInput = $('input[name="adremm_clock_settings[text_color]"]');

    // Update Theme
    $themeSelect.on('change', function() {
        const theme = $(this).val();
        $preview.removeClass('theme-modern theme-classic theme-digital').addClass('theme-' + theme);
    });

    // Update Position Preview
    $('input[name="adremm_clock_settings[position]"]').on('change', function() {
        const pos = $(this).val();
        console.log('Position changed to:', pos);
        // You could add a visual indicator of the position in the preview panel here if desired
    });

    // Update Font
    $fontSelect.on('change', function() {
        const font = $(this).val();
        $preview.css('font-family', font);

        // Dynamically load Google Font for preview
        if (font !== 'inherit') {
            const fontUrl = 'https://fonts.googleapis.com/css2?family=' + font.replace(/ /g, '+') + '&display=swap';
            if (!$('link[href="' + fontUrl + '"]').length) {
                $('head').append('<link rel="stylesheet" href="' + fontUrl + '">');
            }
        }
    });

    // Update Colors
    function updateColors() {
        $preview.css({
            'background-color': $bgColorInput.val(),
            'color': $textColorInput.val()
        });
    }

    // Initialize Color Picker if available
    if ($.isFunction($.fn.wpColorPicker)) {
        $('.color-picker').wpColorPicker({
            change: function(event, ui) {
                setTimeout(updateColors, 10);
            },
            alpha: true
        });
    } else {
        $bgColorInput.on('input', updateColors);
        $textColorInput.on('input', updateColors);
    }

    // Initial load of font if not standard
    $fontSelect.trigger('change');

    // Simple Clock for Preview
    function updatePreviewTime() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('nl-NL');
        const dateStr = now.toLocaleDateString('nl-NL', { weekday: 'long', day: 'numeric', month: 'long' });
        $preview.find('.time').text(timeStr);
        $preview.find('.date').text(dateStr);
    }
    setInterval(updatePreviewTime, 1000);
    updatePreviewTime();
});
