=== ADREMM Klok ===
Contributors: ADREMM
Tags: clock, widget, opening hours, radio
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.3.6
License: GPLv2 or later

Een uiterst gebruiksvriendelijke, meertalige klokplugin met live previews, openingstijden en schaalbare weergave.

== Changelog ==

= 1.3.6 =
* Refined Matrix theme with perspective-warped "gebogen strepen" (curved scanlines).
* Enhanced Matrix glitch with step-based clipping and RGB shift.
* Corrected Analog hand styles: Rectangle is now sharp, Arrow has a triangular head, and Point uses a diamond shape.
* Improved hand positioning and layering for Heart and Steampunk styles.
* Fixed Pixels theme: decoupled it from Matrix effects and ensured it uses Silkscreen.
* Synchronized Admin Live Preview for 1:1 visual fidelity.

= 1.3.5 =
* Matrix theme overhaul: implemented Dr Glitch font and curved scanline effects.
* Refined Matrix animation: glitches now only trigger on digits that change.
* Corrected Analog hand styles (Rectangle, Heart, Arrow, Steampunk) for greater precision.
* Fixed Pixels theme: decoupled it from Matrix font and ensured it uses Silkscreen.
* Synchronized Admin Live Preview for 1:1 fidelity with all theme refinements.

= 1.3.4 =
* Fixed Radio (Alarm) theme scaling: nixie tubes now fit correctly within Small (280px) panels.
* Implemented Dutch layout for Minimalist (Design) theme: [DA] [HH] [MM] [SS] with labels.
* Added 'tick-flash' pulse effect for digital themes triggered on every second change.
* Synchronized Admin Live Preview with the new Minimalist layout and second-change flash.

= 1.3.3 =
* Critical fix: corrected analog clock hand alignment using top:50% and transform pivot points.
* Implemented requested "opposite snapping" for collapsible tabs: clocks on the right dock to the left edge.
* Refined Wall Clock theme with precise 35% fade gradients and single-line layout.
* Synchronized Admin Live Preview for 1:1 fidelity with frontend logic.
* Incremented version to 1.3.3 to ensure clean WordPress install.


Deze ADREMM klok plugin is een meertalige plugin voor mijn users.
Hij heeft 3 thema's en kan op 8 wijzes getoond worden op de front.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to ADREMM Klok in the admin menu to configure.

== Changelog ==

= 1.3.0 =
* Major release addressing fidelity issues and synchronization.
* Fixed Admin Preview duplication bug for center rings.
* Implemented functional toggles for 'Overshoot' and 'Middenringen' (center rings).
* Corrected Analog hand center alignment and pivot points.
* Fixed Digital Dimensions: width and height settings are now strictly respected.
* Refined Wall Clock (Muurklok) with a 35% top/bottom fade effect.
* Redesigned Matrix theme with a "curved stripe" scanline effect.
* Incremented version to 1.3.0 to ensure installation overwrites previous versions.

= 1.2.7 =
* Restored separate "Mobiel & Tablet" submenu for better organizational clarity.
* Fixed "Wall Clock" (Muurklok) rolling animation: implemented seamless 9-to-0 transition.
* Redesigned Matrix theme with "rounded lines" (Looksky style) for a modern high-fidelity look.
* Synchronized Mondriaan analog design with 8px borders and forced hand colors in preview.
* Incremented version to ensure installation overwrites previous versions.

= 1.2.6 =
* Improved admin settings alignment to match high-fidelity design requirements.
* Fixed row spacing and horizontal alignment for input groups and color pickers.
* Ensured complete Admin Live Preview synchronization across all tabs and settings.
* Incremented version to ensure installation overwrites previous versions.

= 1.2.5 =
* Unified mobile and tablet settings into a single "Responsiviteit" tab on the main settings page.
* Removed redundant "Mobiel & Tablet" submenu for a cleaner admin interface.
* Optimized Admin Live Preview to correctly reflect Bar vs Floating Panel layouts in real-time.
* Validated all PHP scripts for syntax accuracy and version persistence.

= 1.2.4 =
* Refined collapsible tab snapping logic: if the clock floats on the right, it now correctly docks to the left edge when closed.
* Improved Admin Live Preview to sync between Bar (Header/Footer) and Floating Panel layouts.
* Added support for toggling different analog notation types (Roman numerals, Arabic numbers, dots) in the admin preview.
* Synchronized hand thickness and scaling logic across all themes.

= 1.2.3 =
* Unified mobile and desktop settings into a single "Responsiviteit" (Responsiveness) tab.
* Fixed collapsible tab snapping logic: right-positioned clocks now dock to the left edge when closed.
* Improved Admin Live Preview synchronization for all themes and Google Fonts.
* Incremented version to ensure installation overwrites previous versions.

= 1.1.7 =
* Improved Mondriaan analog clock design to match provided artwork (circular face, 12 o'clock bar, 9 o'clock square).
* Refined digital clock sizing for Small (280px), Normal (380px), and Large (480px) panels.
* Fixed Admin Live Preview reliability and synchronization.
* Corrected activation redirect logic.
* Incremented version for release 1.1.7 to ensure WordPress update overwrite.

= 1.1.6 =
* Overhauled Mondriaan analog clock design with 8px lines and precise block placement.
* Standardized collapsible tab to 25px x 100px.
* Improved admin joystick UI with specific [HD], [FT], [X] labels and tooltips.
* Fixed snapping logic: closed clock from the right docks to the left edge as a 25x100px tab.
* Finalized version for release 1.1.6 to ensure clean WordPress install.

= 1.1.5 =
* Fixed analog hand alignment to precise center.
* Perfected Wall Clock fade gradients (top/bottom) and layout.
* Improved Mondriaan analog clock design (8px borders and blocks).
* Refined digital clock sizing for better fit in Small/Normal/Large panels.
* Fixed admin live preview reliability.
* Incremented version to ensure installation overwrites previous versions.

= 1.1.4 =
* Incremented version to ensure installation overwrites previous versions.
* Fixed scaling logic to strictly follow 280px/380px/480px sizes.
* Switched failing 'Pixels' font to reliable Google Font 'Silkscreen'.
* Perfected Wall Clock 30% top/bottom fade effect.
* Improved Mondriaan analog clock design with 8px lines and better proportions.
* Fixed JavaScript serialization bug in Opening Hours.
* Synchronized Admin Live Preview for all recent theme refinements.
* Resolved potential PHP warnings in status logic.

= 1.1.2 =
* Fixed PHP warnings in status logic by adding robust existence checks.
* Restored 'Koopdag' (late night shopping) feature with toggle and label support.
* Refined Matrix theme alignment (centered colons) and Wall Clock alignment (single line HH:MM:SS).
* Redesigned Mondriaan analog clock with thicker lines and improved proportions.
* Integrated grid-based position selector with [HD], [FT], and [X] labels.
* Improved admin live preview fidelity for all themes.
* Incremented version for better installation/update persistence.

= 1.1.1 =
* Fixed plugin installation/overwrite behavior by bumping version to 1.1.1.
* Improved activation redirect logic using the 'activated_plugin' hook.

= 1.1.0 =
* Unified settings page (merged Mobile settings into Responsiviteit tab).
* Simplified navigation by removing separate "Mobiel & Tablet" submenu.
* Cleaned up repository and removed redundant files.
* Enhanced Matrix grid and Wall Clock visual fidelity.
* Improved Minimalist styling options (padding, background, radius).

= 1.0.7 =
* Implemented standard sizing system (250/350/450px) with scaling.
* Restored Nixie and Matrix grid effects.
* Added custom logo support for Vintage theme.
* Added fade effect to Wall Clock digits.
* Added styling options (padding, background) to Minimalist clock.
* Fixed live preview reliability.

= 1.0.6 =
* Restored Nixie tube grid (raster) with improved visibility.
* Fixed installation issue by incrementing version to 1.0.6.

= 1.0.5 =
* Overhauled architecture to Singleton class.
* Enhanced Vintage Radio (v2.3) with side controls.
* Added 6-digit HH:MM:SS Wall Clock theme.
* Implemented position-aware transform scaling.
* Fixed analog hand color inheritance.
* Improved opening hours engine (multi-slot, special days).

= 1.0.2 =
* Added support for decimal values in settings (step="0.1").
* Implemented volume control for Vintage Radio theme.
* Fixed redirection logic on activation.
* Enhanced theme fidelity (glitch effects, color corrections).
* Fixed hand style clipping.

= 1.0.0 =
* Initial release.
