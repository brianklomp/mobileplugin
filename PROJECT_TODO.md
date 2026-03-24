# ADREMM Klok TO-DO List

This list summarizes the core requirements gathered from the last 7 prompts to ensure high-fidelity implementation.

## 1. General & Architecture
- [ ] **Standalone Plugin:** Ensure the plugin is entirely independent of previous 'mobile plugin' codebases.
- [ ] **Versioning:** Maintain version 1.2.7 in `adremm-clock-plugin.php` and `readme.txt` to ensure installation overwrites previous versions.
- [ ] **Menu Structure:** Keep the "Instellingen", "Openingstijden", and "Mobiel & Tablet" submenus separate as requested.

## 2. Digital Themes
- [ ] **Wall Clock (Muurklok):**
    - [ ] Implementation of a seamless rolling 9-to-0 transition (no shooting back).
    - [ ] Ensure the clock fits strictly on one line.
    - [ ] Apply 35% fade gradients (top/bottom).
- [ ] **Matrix Theme:**
    - [ ] Redesign font effect to use "rounded lines" (Looksky style).
    - [ ] Ensure vertical centering of colons and glitch effects are functional.
- [ ] **Vintage Radiowekker:**
    - [ ] Maintain Nixie tube style, radio streams, and scaling logic.
    - [ ] Ensure side controls (volume, power) are functional.

## 3. Analog Themes
- [ ] **Mondriaan Theme:**
    - [ ] Circular white face with 8px black border.
    - [ ] Vertical black bar at 12 o'clock, grey square at 9 o'clock.
    - [ ] Blue center hub with yellow dot.
    - [ ] Precise hand alignment: Blue (hour), Yellow (minute), Red (second).
- [ ] **Global Analog Logic:**
    - [ ] Support for 'Overshoot' and 'Middenringen' toggles.

## 4. UI/UX & Positioning
- [ ] **Joystick Positioning:**
    - [ ] 3x3 grid selector with [HD], [FT], and [X] labels.
    - [ ] Tooltips on hover.
- [ ] **Collapsible Tab:**
    - [ ] Dimension: strictly 25px x 100px.
    - [ ] **Opposite Snapping:** Clocks floating on the right must collapse to a tab on the LEFT edge.
- [ ] **Admin Live Preview:**
    - [ ] Real-time WYSIWYG updates for *every* setting without refresh.
    - [ ] Direct Google Font preview styling in select options.
    - [ ] Perfect row alignment for inputs and color pickers.

## 5. Logic & Compatibility
- [ ] **Opening Hours:** Reliable multi-slot engine with Koopdag support.
- [ ] **Scaling:** Three tiers (Small 280px, Normal 380px, Large 480px) using `--panel-scale`.
