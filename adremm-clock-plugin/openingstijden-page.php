<?php
/**
 * Openingstijden Admin View for ADREMM Clock Plugin
 */
if ( ! defined('ABSPATH') ) exit;

// We include this within settings-page.php or as a standalone page.
// For now, let's create the logic that will be included in the tab 'Openingstijden' (if added)
// or as the standalone page content.

?>
<div class="adremm-openingstijden-wrap">
    <h3><?php _e('Wekelijkse Openingstijden', 'adremm-clock-plugin'); ?></h3>
    <p class="description"><?php _e('Stel hier de standaard openingstijden per dag in.', 'adremm-clock-plugin'); ?></p>

    <div class="days-container">
        <?php
        $days = array('Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag');
        foreach($days as $day): ?>
            <div class="day-row" data-day="<?php echo esc_attr($day); ?>">
                <div class="day-header">
                    <strong><?php echo $day; ?></strong>
                    <label><input type="checkbox" class="is-closed"> Gesloten</label>
                </div>
                <div class="time-slots">
                    <div class="slot">
                        <input type="time" value="09:00"> tot <input type="time" value="18:00">
                        <button type="button" class="button remove-slot">Verwijderen</button>
                    </div>
                </div>
                <button type="button" class="button add-slot">+ Tijdsblok toevoegen</button>
                <div class="extra-note">
                    <label>Extra bericht (bijv. "Koopavond"):</label>
                    <input type="text" class="regular-text" placeholder="Bericht...">
                </div>
                <button type="button" class="button copy-to-all">Kopieer naar alle dagen</button>
            </div>
        <?php endforeach; ?>
    </div>

    <hr>

    <h3><?php _e('Uitzonderingen', 'adremm-clock-plugin'); ?></h3>
    <div class="exceptions-container">
         <div class="exception-row">
            <input type="date">
            <label><input type="checkbox"> Gesloten</label>
            <div class="exc-slots">
                 <input type="time" value="09:00"> tot <input type="time" value="17:00">
            </div>
            <button type="button" class="button remove-exc">Verwijderen</button>
         </div>
    </div>
    <button type="button" class="button add-exception">+ Nieuwe uitzondering</button>
</div>

<style>
.adremm-openingstijden-wrap { background: #fff; padding: 20px; border: 1px solid #ccc; }
.day-row { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
.day-header { display: flex; gap: 20px; align-items: center; margin-bottom: 10px; font-size: 16px; }
.time-slots { margin-bottom: 10px; }
.slot { margin-bottom: 5px; display: flex; align-items: center; gap: 10px; }
.extra-note { margin: 10px 0; }
.exceptions-container { margin-bottom: 15px; }
.exception-row { display: flex; align-items: center; gap: 15px; margin-bottom: 10px; padding: 10px; background: #f9f9f9; border-radius: 4px; }
</style>
