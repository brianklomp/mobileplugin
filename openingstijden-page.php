<?php
/**
 * Opening Hours Settings Page for ADREMM Clock Plugin
 */
if ( ! defined('ABSPATH') ) exit;

$settings = wp_parse_args(get_option('adremm_clock_settings', array()), adremm_clock_get_default_settings());
$days = array(
    'mon' => 'Maandag',
    'tue' => 'Dinsdag',
    'wed' => 'Woensdag',
    'thu' => 'Donderdag',
    'fri' => 'Vrijdag',
    'sat' => 'Zaterdag',
    'sun' => 'Zondag'
);

$opening_hours = !empty($settings['opening_hours']) ? json_decode($settings['opening_hours'], true) : array();
?>
<div class="wrap adremm-clock-v2">
    <h1><?php _e('ADREMM Klok – Openingstijden', 'adremm-clock-plugin'); ?></h1>
    <p><?php _e('Stel hier de openingstijden in voor de Status-melding op de klok.', 'adremm-clock-plugin'); ?></p>

    <form method="post" action="options.php">
        <?php settings_fields('adremm_clock_options'); ?>

        <!-- We need to pass all settings because register_setting is for the whole array -->
        <?php foreach ($settings as $key => $val): ?>
            <?php if ($key !== 'opening_hours'): ?>
                <input type="hidden" name="adremm_clock_settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($val); ?>">
            <?php endif; ?>
        <?php endforeach; ?>

        <table class="widefat fixed striped" style="max-width: 1000px; margin-top: 20px;">
            <thead>
                <tr>
                    <th><?php _e('Dag', 'adremm-clock-plugin'); ?></th>
                    <th><?php _e('Openingstijden', 'adremm-clock-plugin'); ?></th>
                    <th><?php _e('Pauze', 'adremm-clock-plugin'); ?></th>
                    <th><?php _e('Opties', 'adremm-clock-plugin'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($days as $key => $label):
                    $day_data = isset($opening_hours[$key]) ? $opening_hours[$key] : array();
                    $open = isset($day_data['open']) ? $day_data['open'] : '09:00';
                    $close = isset($day_data['close']) ? $day_data['close'] : '18:00';
                    $is_closed = !empty($day_data['is_closed']);
                    $is_koopavond = !empty($day_data['is_koopavond']);
                    $break_start = isset($day_data['break_start']) ? $day_data['break_start'] : '';
                    $break_end = isset($day_data['break_end']) ? $day_data['break_end'] : '';
                    $break_label = isset($day_data['break_label']) ? $day_data['break_label'] : '';
                ?>
                <tr>
                    <td><strong><?php echo $label; ?></strong></td>
                    <td>
                        <input type="time" name="adremm_h[<?php echo $key; ?>][open]" value="<?php echo esc_attr($open); ?>" <?php disabled($is_closed); ?>> -
                        <input type="time" name="adremm_h[<?php echo $key; ?>][close]" value="<?php echo esc_attr($close); ?>" <?php disabled($is_closed); ?>>
                    </td>
                    <td>
                        Van <input type="time" name="adremm_h[<?php echo $key; ?>][break_start]" value="<?php echo esc_attr($break_start); ?>" <?php disabled($is_closed); ?>>
                        Tot <input type="time" name="adremm_h[<?php echo $key; ?>][break_end]" value="<?php echo esc_attr($break_end); ?>" <?php disabled($is_closed); ?>>
                        Label <input type="text" name="adremm_h[<?php echo $key; ?>][break_label]" value="<?php echo esc_attr($break_label); ?>" placeholder="Pauze" <?php disabled($is_closed); ?>>
                    </td>
                    <td>
                        <label><input type="checkbox" name="adremm_h[<?php echo $key; ?>][is_closed]" value="1" <?php checked($is_closed); ?>> Gesloten</label><br>
                        <label><input type="checkbox" name="adremm_h[<?php echo $key; ?>][is_koopavond]" value="1" <?php checked($is_koopavond); ?>> Koopavond</label>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top:40px; background:#fff; padding:20px; border:1px solid #ccd0d4;">
            <h3><?php _e('Uitzonderlijke Dagen (Feestdagen)', 'adremm-clock-plugin'); ?></h3>
            <div id="exceptional-days-wrap">
                <!-- JS handles this -->
            </div>
            <button type="button" class="button" id="add-exceptional-day"><?php _e('Voeg dag toe', 'adremm-clock-plugin'); ?></button>
            <input type="hidden" name="adremm_clock_settings[exceptional_days]" id="adremm_exceptional_json" value="<?php echo esc_attr($settings['exceptional_days']); ?>">
        </div>

        <!-- We'll use a hidden field to store the JSON string to keep things compatible with our main settings array -->
        <input type="hidden" name="adremm_clock_settings[opening_hours]" id="adremm_opening_hours_json" value="<?php echo esc_attr($settings['opening_hours']); ?>">

        <p class="submit">
            <?php submit_button(__('Openingstijden Opslaan', 'adremm-clock-plugin'), 'primary', 'submit', false); ?>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    var exceptionalDays = JSON.parse($('#adremm_exceptional_json').val() || '[]');

    function renderEx() {
        var html = '';
        exceptionalDays.forEach(function(ex, i) {
            html += '<div style="display:flex; gap:10px; margin-bottom:10px;">';
            html += '<input type="date" value="'+ex.date+'" class="ex-date" data-idx="'+i+'">';
            html += '<input type="text" value="'+ex.label+'" class="ex-label" data-idx="'+i+'" placeholder="Omschrijving">';
            html += '<select class="ex-status" data-idx="'+i+'"><option value="open" '+(ex.status==='open'?'selected':'')+'>Open</option><option value="closed" '+(ex.status==='closed'?'selected':'')+'>Gesloten</option></select>';
            html += '<button type="button" class="button remove-ex" data-idx="'+i+'">Verwijder</button>';
            html += '</div>';
        });
        $('#exceptional-days-wrap').html(html);
        $('#adremm_exceptional_json').val(JSON.stringify(exceptionalDays));
    }
    renderEx();

    $('#add-exceptional-day').on('click', function() {
        exceptionalDays.push({date: '', label: '', status: 'closed'});
        renderEx();
    });

    $(document).on('change', '.ex-date, .ex-label, .ex-status', function() {
        var idx = $(this).data('idx');
        if ($(this).hasClass('ex-date')) exceptionalDays[idx].date = $(this).val();
        if ($(this).hasClass('ex-label')) exceptionalDays[idx].label = $(this).val();
        if ($(this).hasClass('ex-status')) exceptionalDays[idx].status = $(this).val();
        $('#adremm_exceptional_json').val(JSON.stringify(exceptionalDays));
    });

    $(document).on('click', '.remove-ex', function() {
        exceptionalDays.splice($(this).data('idx'), 1);
        renderEx();
    });

    $('form').on('submit', function() {
        var hours = {};
        $('tbody tr').each(function() {
            var $row = $(this);
            var dayMatch = $row.find('input[name*="adremm_h"]').attr('name').match(/\[(.*?)\]/);
            if (dayMatch) {
                var dayKey = dayMatch[1];
                hours[dayKey] = {
                    open: $row.find('input[name*="[open]"]').val(),
                    close: $row.find('input[name*="[close]"]').val(),
                    break_start: $row.find('input[name*="[break_start]"]').val(),
                    break_end: $row.find('input[name*="[break_end]"]').val(),
                    break_label: $row.find('input[name*="[break_label]"]').val(),
                    is_closed: $row.find('input[name*="[is_closed]"]').is(':checked'),
                    is_koopavond: $row.find('input[name*="[is_koopavond]"]').is(':checked')
                };
            }
        });
        $('#adremm_opening_hours_json').val(JSON.stringify(hours));
    });

    $('input[name*="[is_closed]"]').on('change', function() {
        var $row = $(this).closest('tr');
        $row.find('input[type="time"], input[type="text"]').prop('disabled', $(this).is(':checked'));
    }).trigger('change');
});
</script>
