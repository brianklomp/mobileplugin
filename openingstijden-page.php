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
if (!is_array($opening_hours)) $opening_hours = array();
?>
<div class="wrap adremm-clock-v2">
    <h1><?php _e('ADREMM Klok – Openingstijden', 'adremm-clock-plugin'); ?></h1>
    <p><?php _e('Stel hier meerdere tijdvensters per dag in. Gebruik de "Kopieer" knop om tijden snel over te nemen.', 'adremm-clock-plugin'); ?></p>

    <form method="post" action="options.php" id="adremm-hours-form">
        <?php settings_fields('adremm_clock_options'); ?>

        <?php foreach ($settings as $key => $val): ?>
            <?php if ($key !== 'opening_hours' && $key !== 'exceptional_days'): ?>
                <input type="hidden" name="adremm_clock_settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($val); ?>">
            <?php endif; ?>
        <?php endforeach; ?>

        <div class="hours-container" style="max-width: 1000px; margin-top: 20px;">
            <?php foreach($days as $key => $label):
                $slots = isset($opening_hours[$key]) ? $opening_hours[$key] : array();
                $is_closed = false;
                if (isset($slots['is_closed'])) {
                    $is_closed = $slots['is_closed'];
                }
                if (empty($slots) || (isset($slots['is_closed']) && count($slots) == 1)) {
                    if (!$is_closed) $slots = array(array('open' => '09:00', 'close' => '18:00'));
                }
            ?>
            <div class="day-row" data-day="<?php echo $key; ?>" style="background:#fff; padding:15px; border:1px solid #ccd0d4; margin-bottom:10px; border-radius:8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <strong style="font-size:16px;"><?php echo $label; ?></strong>
                    <div>
                        <label style="cursor:pointer; font-weight:600;"><input type="checkbox" class="day-closed-toggle" <?php checked($is_closed); ?>> <?php _e('Gesloten', 'adremm-clock-plugin'); ?></label>
                        <button type="button" class="button button-secondary copy-day" style="margin-left:20px;"><?php _e('Kopieer naar alle dagen', 'adremm-clock-plugin'); ?></button>
                    </div>
                </div>

                <div class="slots-wrap" <?php if($is_closed) echo 'style="display:none;"'; ?>>
                    <div class="slots-list">
                        <?php
                        if (is_array($slots)) {
                            foreach($slots as $idx => $slot):
                                if ($idx === 'is_closed') continue;
                                if (!isset($slot['open'])) continue;
                        ?>
                        <div class="slot-item" style="display:inline-flex; gap:10px; align-items:center; margin-right:15px; margin-bottom:10px; background:#f0f0f1; padding:8px 12px; border-radius:4px;">
                            <input type="time" class="slot-open" value="<?php echo esc_attr($slot['open']); ?>">
                            <span>tot</span>
                            <input type="time" class="slot-close" value="<?php echo esc_attr($slot['close']); ?>">
                            <button type="button" class="remove-slot" style="color:#d63638; cursor:pointer; background:none; border:none; font-size:20px; padding:0; line-height:1;">&times;</button>
                        </div>
                        <?php endforeach; } ?>
                    </div>
                    <button type="button" class="button button-link add-slot" style="color:#2271b1; font-weight:600; text-decoration:none;">+ <?php _e('Tijdvenster toevoegen', 'adremm-clock-plugin'); ?></button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top:40px; background:#fff; padding:25px; border:1px solid #ccd0d4; max-width: 1100px; border-radius:8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px;"><?php _e('Uitzonderlijke Dagen (Speciale dagen)', 'adremm-clock-plugin'); ?></h3>
            <p class="description"><?php _e('Stel afwijkende tijden in. Je kunt hier ook een tijdelijke positie voor de klok kiezen.', 'adremm-clock-plugin'); ?></p>
            <div id="exceptional-days-wrap" style="margin:20px 0;"></div>
            <button type="button" class="button button-primary" id="add-exceptional-day"><?php _e('Voeg speciale dag toe', 'adremm-clock-plugin'); ?></button>
            <input type="hidden" name="adremm_clock_settings[exceptional_days]" id="adremm_exceptional_json" value="<?php echo esc_attr($settings['exceptional_days']); ?>">
        </div>

        <input type="hidden" name="adremm_clock_settings[opening_hours]" id="adremm_opening_hours_json" value="<?php echo esc_attr($settings['opening_hours']); ?>">

        <div style="margin-top:30px;">
            <?php submit_button(__('Openingstijden Opslaan', 'adremm-clock-plugin'), 'primary button-large', 'submit', false); ?>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    function serializeHours() {
        var hours = {};
        $('.day-row').each(function() {
            var day = $(this).data('day');
            var isClosed = $(this).find('.day-closed-toggle').is(':checked');
            if (isClosed) {
                hours[day] = { is_closed: true };
            } else {
                var slots = [];
                $(this).find('.slot-item').each(function() {
                    slots.push({
                        open: $(this).find('.slot-open').val(),
                        close: $(this).find('.slot-close').val()
                    });
                });
                hours[day] = slots;
            }
        });
        $('#adremm_opening_hours_json').val(JSON.stringify(hours));
    }

    $(document).on('click', '.add-slot', function() {
        var $list = $(this).siblings('.slots-list');
        var html = '<div class="slot-item" style="display:inline-flex; gap:10px; align-items:center; margin-right:15px; margin-bottom:10px; background:#f0f0f1; padding:8px 12px; border-radius:4px;">' +
                   '<input type="time" class="slot-open" value="09:00"> ' +
                   '<span>tot</span>' +
                   '<input type="time" class="slot-close" value="18:00">' +
                   '<button type="button" class="remove-slot" style="color:#d63638; cursor:pointer; background:none; border:none; font-size:20px; padding:0; line-height:1;">&times;</button>' +
                   '</div>';
        $list.append(html);
    });

    $(document).on('click', '.remove-slot', function() {
        $(this).closest('.slot-item').remove();
    });

    $(document).on('change', '.day-closed-toggle', function() {
        $(this).closest('.day-row').find('.slots-wrap').toggle(!$(this).is(':checked'));
    });

    $('.copy-day').on('click', function() {
        if(!confirm('Weet je zeker dat je de tijden van deze dag naar ALLE andere dagen wilt kopiëren?')) return;
        var $row = $(this).closest('.day-row');
        var isClosed = $row.find('.day-closed-toggle').is(':checked');

        // Collect current values from original row
        var slots = [];
        $row.find('.slot-item').each(function() {
            slots.push({
                open: $(this).find('.slot-open').val(),
                close: $(this).find('.slot-close').val()
            });
        });

        $('.day-row').not($row).each(function() {
            var $targetRow = $(this);
            $targetRow.find('.day-closed-toggle').prop('checked', isClosed);
            $targetRow.find('.slots-wrap').toggle(!isClosed);

            var $list = $targetRow.find('.slots-list');
            $list.empty();
            slots.forEach(function(slot) {
                var html = '<div class="slot-item" style="display:inline-flex; gap:10px; align-items:center; margin-right:15px; margin-bottom:10px; background:#f0f0f1; padding:8px 12px; border-radius:4px;">' +
                           '<input type="time" class="slot-open" value="'+slot.open+'"> ' +
                           '<span>tot</span>' +
                           '<input type="time" class="slot-close" value="'+slot.close+'">' +
                           '<button type="button" class="remove-slot" style="color:#d63638; cursor:pointer; background:none; border:none; font-size:20px; padding:0; line-height:1;">&times;</button>' +
                           '</div>';
                $list.append(html);
            });
        });
        serializeHours();
    });

    // Special Days Logic
    var exceptionalDays = JSON.parse($('#adremm_exceptional_json').val() || '[]');
    var positions = {
        'inherit': 'Standaard positie',
        'top-left': 'Linksboven', 'top-center': 'Boven (HD)', 'top-right': 'Rechtsboven',
        'middle-left': 'Midden links', 'middle-right': 'Midden rechts',
        'bottom-left': 'Linksonder', 'bottom-center': 'Onder (FT)', 'bottom-right': 'Rechtsonder'
    };

    function renderEx() {
        var html = '';
        exceptionalDays.forEach(function(ex, i) {
            html += '<div class="ex-item" style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:15px; background:#f9f9f9; padding:15px; border-radius:6px; border-left:4px solid #2271b1; align-items:center;">';
            html += '<div><strong>Datum:</strong><br><input type="date" value="'+(ex.date||'')+'" class="ex-date" data-idx="'+i+'" style="width:140px;"></div>';
            html += '<div><strong>Label/Status:</strong><br><input type="text" value="'+(ex.label||'')+'" class="ex-label" data-idx="'+i+'" placeholder="bijv. Vakantie" style="width:150px;"></div>';
            html += '<div><strong>Open van:</strong><br><input type="time" value="'+(ex.open||'09:00')+'" class="ex-open" data-idx="'+i+'"></div>';
            html += '<div><strong>tot:</strong><br><input type="time" value="'+(ex.close||'18:00')+'" class="ex-close" data-idx="'+i+'"></div>';
            html += '<div><strong>Positie:</strong><br><select class="ex-pos" data-idx="'+i+'">';
            for(var p in positions) {
                html += '<option value="'+p+'" '+(ex.pos===p?'selected':'')+'>'+positions[p]+'</option>';
            }
            html += '</select></div>';
            html += '<div><strong>Status:</strong><br><select class="ex-status" data-idx="'+i+'"><option value="open" '+(ex.status==='open'?'selected':'')+'>Open</option><option value="closed" '+(ex.status==='closed'?'selected':'')+'>Gesloten</option></select></div>';
            html += '<div style="margin-left:auto;"><button type="button" class="button remove-ex" data-idx="'+i+'" style="color:#d63638; border-color:#d63638;">&times;</button></div>';
            html += '</div>';
        });
        $('#exceptional-days-wrap').html(html);
        $('#adremm_exceptional_json').val(JSON.stringify(exceptionalDays));
    }
    renderEx();

    $('#add-exceptional-day').on('click', function() {
        exceptionalDays.push({date: '', label: '', status: 'closed', open: '09:00', close: '18:00', pos: 'inherit'});
        renderEx();
    });

    $(document).on('change', '.ex-date, .ex-label, .ex-status, .ex-open, .ex-close, .ex-pos', function() {
        var idx = $(this).data('idx');
        exceptionalDays[idx].date = $('.ex-date[data-idx="'+idx+'"]').val();
        exceptionalDays[idx].label = $('.ex-label[data-idx="'+idx+'"]').val();
        exceptionalDays[idx].status = $('.ex-status[data-idx="'+idx+'"]').val();
        exceptionalDays[idx].open = $('.ex-open[data-idx="'+idx+'"]').val();
        exceptionalDays[idx].close = $('.ex-close[data-idx="'+idx+'"]').val();
        exceptionalDays[idx].pos = $('.ex-pos[data-idx="'+idx+'"]').val();
        $('#adremm_exceptional_json').val(JSON.stringify(exceptionalDays));
    });

    $(document).on('click', '.remove-ex', function() {
        exceptionalDays.splice($(this).data('idx'), 1);
        renderEx();
    });

    $('#adremm-hours-form').on('submit', function() {
        serializeHours();
    });
});
</script>
