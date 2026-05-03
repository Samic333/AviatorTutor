-- =============================================================================
-- AviatorTutor — Phase 3: ATA 22 Autoflight — interactive diagram with 14
-- hotspots and 3 states (Normal / Auto Disengage / Mistrim).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'autoflight' LIMIT 1);

SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES
    (@system_id,
     'Q400 AFCS Architecture & Disengage Logic',
     'Interactive block diagram of the Automatic Flight Control System. Sensors (AHRS 1/2 + ADC 1/2) feed two Flight Guidance Modules: FGM1 commands the AP/YD actuators (APAU 1/2) while FGM2 monitors. The FCECU prioritises manual trim over AFCS commands. The Flight Guidance Control Panel is the cockpit interface; the glareshield AP DISENG warning lights flash for automatic disengagements; the AP DIS pushbuttons on each control wheel are the acknowledge path. The FMA strip at the bottom shows the white-armed / green-active mode convention.',
     '/assets/aircraft/q400/autoflight-flow.svg',
     NULL,
     1,
     'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — AHRS 1 / 2',
 'Two Attitude Heading Reference Systems. Both must be valid AND not disagree for AP to engage. AHRS monitoring trip is one of the external sources of an AP INHIBIT message.',
 9.58, 20.00, 'component', '#fbbf24', 'sensors'),

(@diagram_id, '2 — ADC 1 / 2',
 'Two Air Data Computers. Both must be valid AND not disagree for AP to engage. ADU monitoring trip is another external AP INHIBIT source.',
 9.58, 40.00, 'component', '#fbbf24', 'sensors'),

(@diagram_id, '3 — FGM 1 (Commands)',
 'Flight Guidance Module 1: the channel that COMMANDS the AP and YD actuators. Processes Flight Director outputs, Auto Pitch Trim, mode logic. Without FGM1 there is no AP/YD output.',
 30.42, 34.16, 'component', '#22d3ee', 'fgm'),

(@diagram_id, '4 — FGM 2 (Monitors)',
 'Flight Guidance Module 2: the channel that MONITORS FGM1 commands and APAU performance. Detects miscompares and disconnects the AP if necessary. Also monitors AP roll-servo torque to drive the Mistrim message.',
 30.42, 55.83, 'component', '#a78bfa', 'fgm'),

(@diagram_id, '5 — APAU 1',
 'Autopilot Actuator Unit 1: pitch and roll servos that move the elevator and aileron under FGM1 commands. Disengage immediately if a torque mismatch is detected by FGM2.',
 49.16, 36.66, 'component', '#22d3ee', 'actuator'),

(@diagram_id, '6 — APAU 2',
 'Autopilot Actuator Unit 2: yaw damper actuator driving the rudder for stability augmentation. YD failure disengages AP.',
 49.16, 55.00, 'component', '#22d3ee', 'actuator'),

(@diagram_id, '7 — FCECU',
 'Flight Control Electronic Control Unit: receives pitch trim commands. Prioritises manual pitch trim from the wheel switch over AFCS commands — manual trim with AP engaged disengages the AP.',
 67.91, 36.66, 'component', '#22c55e', 'controls'),

(@diagram_id, '8 — Control Surfaces',
 'Elevator, ailerons, spoilers, rudder. The actual surfaces moved by APAUs through the FCECU.',
 67.91, 55.00, 'component', '#22c55e', 'controls'),

(@diagram_id, '9 — FGCP (Flight Guidance Control Panel)',
 'Glareshield panel. AP and YD pushbuttons; mode buttons (HDG, NAV, APPR, BC, IAS, VS, VNAV, ALT, ALT SEL); HSI SEL; NAV SOURCE; STBY; rotary knobs for course / heading / altitude / IAS / VS.',
 87.50, 24.16, 'component', '#7dd3fc', 'cockpit'),

(@diagram_id, '10 — AP DISENG Warning Light (Capt)',
 'Red glareshield segment. Flashes for AUTOMATIC AP disengagement. Stays dark for MANUAL disengagement. Both segments lit means an FGM power-source failure caused the disengagement.',
 82.50, 45.00, 'component', '#dc2626', 'warning'),

(@diagram_id, '11 — AP DISENG Warning Light (FO)',
 'Mirror segment for the FO side. Same flashing logic as the captain''s light.',
 92.50, 45.00, 'component', '#dc2626', 'warning'),

(@diagram_id, '12 — AP DIS Pushbuttons (Control Wheels)',
 'One on each control wheel. Functions: disengage AP, ACKNOWLEDGE auto-disengagement warnings (stops flashing, silences aural), reset YD disengage PFD annunciation. AP cannot re-engage while warnings are active.',
 87.50, 61.66, 'component', '#7a8aa6', 'cockpit'),

(@diagram_id, '13 — FMA: Active Mode (GREEN)',
 'Top of PFD. GREEN colour indicates a mode is currently ACTIVE — flying the aircraft. Examples: HDG, NAV, ALT, VNAV PATH, VS. Pilot callout: "white-to-green is the call."',
 26.66, 81.16, 'note', '#22c55e', 'fma'),

(@diagram_id, '14 — FMA: MISTRIM (AMBER)',
 'Annunciation only — TRIM L WING DN or TRIM R WING DN. AP does NOT auto-disengage. NO automatic roll trim. Crew must disengage cleanly with hands on the wheel, trim laterally, re-engage.',
 92.50, 81.16, 'note', '#fbbf24', 'fma');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id,
 'normal',
 'Normal Operations',
 'AP and YD engaged. FMA shows green active modes, white armed modes. Both FGMs operational, both APAUs commanded, no warnings. Pitch trim auto-tracking. Default colour scheme on all hotspots.',
 JSON_OBJECT()),

(@diagram_id,
 'auto_disengage',
 'Automatic AP Disengagement',
 'A failure or external condition has caused AUTO disengagement. Both AP DISENG glareshield segments are flashing red, the PFD shows flashing amber AP DISENGAGED, the continuous aural tone is sounding. Crew action: hands on controls, press AP DIS to acknowledge, cross-check, identify cause, attempt re-engagement after warnings reset.',
 JSON_OBJECT(
   'h10', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h11', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true)
 )),

(@diagram_id,
 'mistrim',
 'Roll Mistrim Annunciation',
 'AP roll-servo torque exceeded threshold; FGMs commanded amber MISTRIM [TRIM L/R WING DN] on the PFD. AP stays engaged. There is NO automatic roll trim. Crew must disengage AP CLEANLY with hands on the wheel, trim laterally until message clears, re-engage. Never carry a Mistrim through to a low-altitude AP disengagement.',
 JSON_OBJECT(
   'h14', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h4',  JSON_OBJECT('color_hex', '#fbbf24', 'pulse', false)
 ));

SELECT
    @diagram_id AS diagram_id,
    (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
    (SELECT COUNT(*) FROM diagram_states   WHERE diagram_id = @diagram_id) AS states_inserted;
