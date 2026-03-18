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

        <table class="widefat fixed striped" style="max-width: 600px; margin-top: 20px;">
            <thead>
                <tr>
                    <th><?php _e('Dag', 'adremm-clock-plugin'); ?></th>
                    <th><?php _e('Open', 'adremm-clock-plugin'); ?></th>
                    <th><?php _e('Gesloten', 'adremm-clock-plugin'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($days as $key => $label):
                    $open = $opening_hours[$key]['open'] ?? '09:00';
                    $close = $opening_hours[$key]['close'] ?? '18:00';
                    $is_closed = !empty($opening_hours[$key]['is_closed']);
                ?>
                <tr>
                    <td><strong><?php echo $label; ?></strong></td>
                    <td>
                        <input type="time" name="adremm_clock_opening_hours[<?php echo $key; ?>][open]" value="<?php echo esc_attr($open); ?>" <?php disabled($is_closed); ?>>
                    </td>
                    <td>
                        <input type="time" name="adremm_clock_opening_hours[<?php echo $key; ?>][close]" value="<?php echo esc_attr($close); ?>" <?php disabled($is_closed); ?>>
                        <label style="margin-left:10px;">
                            <input type="checkbox" name="adremm_clock_opening_hours[<?php echo $key; ?>][is_closed]" value="1" <?php checked($is_closed); ?>>
                            <?php _e('Gesloten', 'adremm-clock-plugin'); ?>
                        </label>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- We'll use a hidden field to store the JSON string to keep things compatible with our main settings array -->
        <input type="hidden" name="adremm_clock_settings[opening_hours]" id="adremm_opening_hours_json" value="<?php echo esc_attr($settings['opening_hours']); ?>">

        <p class="submit">
            <?php submit_button(__('Openingstijden Opslaan', 'adremm-clock-plugin'), 'primary', 'submit', false); ?>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('form').on('submit', function() {
        var hours = {};
        $('tr').each(function() {
            var $row = $(this);
            var dayKey = $row.find('input[type="time"]').first().attr('name');
            if (dayKey) {
                dayKey = dayKey.match(/\[(.*?)\]/)[1];
                hours[dayKey] = {
                    open: $row.find('input[name*="[open]"]').val(),
                    close: $row.find('input[name*="[close]"]').val(),
                    is_closed: $row.find('input[type="checkbox"]').is(':checked')
                };
            }
        });
        $('#adremm_opening_hours_json').val(JSON.stringify(hours));
    });

    $('input[type="checkbox"]').on('change', function() {
        var $row = $(this).closest('tr');
        $row.find('input[type="time"]').prop('disabled', $(this).is(':checked'));
    });
});
</script>
