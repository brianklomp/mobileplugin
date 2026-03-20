=== ADREMM Klok ===
Contributors: ADREMM
Tags: clock, widget, opening hours, radio
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.1.2
License: GPLv2 or later

Een uiterst gebruiksvriendelijke, meertalige klokplugin met live previews, openingstijden en schaalbare weergave.

== Description ==

Deze ADREMM klok plugin is een meertalige plugin voor mijn users.
Hij heeft 3 thema's en kan op 8 wijzes getoond worden op de front.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to ADREMM Klok in the admin menu to configure.

== Changelog ==

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
